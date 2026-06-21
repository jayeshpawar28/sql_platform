<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class SqlExecutorService
{
    public function executeSubmission($problem, $userQuery)
    {
        $dbPath = storage_path('app/temp_db_' . uniqid() . '.sqlite');
        
        // Ensure the file is created
        file_put_contents($dbPath, '');

        $connectionName = 'sqlite_sandbox';
        
        Config::set("database.connections.{$connectionName}", [
            'driver' => 'sqlite',
            'database' => $dbPath,
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ]);

        try {
            $db = DB::connection($connectionName);
            
            // 1. Run Schema and Seed
            // We expect $problem->default_schema to be something like:
            // "CREATE TABLE users (id INT, name TEXT); INSERT INTO users VALUES (1, 'Alice');"
            $statements = array_filter(array_map('trim', explode(';', $problem->default_schema)));
            foreach ($statements as $stmt) {
                if (!empty($stmt)) {
                    $db->statement($stmt);
                }
            }

            // 2. Validate User Query (Prevent DROP, INSERT, UPDATE, DELETE)
            $upperQuery = strtoupper($userQuery);
            $disallowedKeywords = ['DROP', 'INSERT', 'UPDATE', 'DELETE', 'ALTER', 'TRUNCATE', 'CREATE', 'REPLACE'];
            
            foreach ($disallowedKeywords as $keyword) {
                if (preg_match('/\b' . $keyword . '\b/', $upperQuery)) {
                    return [
                        'status' => 'error',
                        'message' => "Query contains forbidden keyword: $keyword",
                    ];
                }
            }

            if (!str_starts_with(trim($upperQuery), 'SELECT')) {
                return [
                    'status' => 'error',
                    'message' => 'Only SELECT queries are allowed.',
                ];
            }

            // 3. Execute User Query
            $results = $db->select($userQuery);
            $resultsArray = json_decode(json_encode($results), true);
            // dd($resultsArray);

            // 4. Compare with Expected Output
            $expectedOutput = json_decode($problem->expected_output, true);

            // dd($expectedOutput);

            $isCorrect = $this->compareResults($resultsArray, $expectedOutput);

            return [
                'status' => $isCorrect ? 'correct' : 'incorrect',
                'results' => $resultsArray,
                'expected' => $expectedOutput,
                'message' => $isCorrect ? 'Accepted!' : 'Wrong Answer.',
            ];

        } catch (QueryException $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'An unexpected error occurred.',
            ];
        } finally {
            // Disconnect and remove temp db
            DB::purge($connectionName);
            if (file_exists($dbPath)) {
                unlink($dbPath);
            }
        }
    }

    private function compareResults($actual, $expected)
    {
        if (count($actual) !== count($expected)) {
            return false;
        }

        // Simplistic comparison for now. A deeper comparison ignoring order might be needed depending on requirements,
        // but typically row order matters if ORDER BY is part of the problem.
        return $actual === $expected;
    }

    public function getSchemaDetails($problem)
    {
        $dbPath = storage_path('app/temp_db_schema_' . uniqid() . '.sqlite');
        file_put_contents($dbPath, '');
        $connectionName = 'sqlite_schema_' . uniqid();
        
        Config::set("database.connections.{$connectionName}", [
            'driver' => 'sqlite',
            'database' => $dbPath,
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ]);

        $schemaDetails = [];

        try {
            $db = DB::connection($connectionName);
            
            $statements = array_filter(array_map('trim', explode(';', $problem->default_schema)));
            foreach ($statements as $stmt) {
                if (!empty($stmt)) {
                    $db->statement($stmt);
                }
            }

            $tables = $db->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            
            foreach ($tables as $table) {
                $tableName = $table->name;
                
                $columnsResult = $db->select("PRAGMA table_info({$tableName})");
                $columns = array_map(function($col) {
                    return [
                        'name' => $col->name,
                        'type' => $col->type
                    ];
                }, $columnsResult);
                // dd($columns);

                $sampleData = json_decode(json_encode($db->select("SELECT * FROM {$tableName} LIMIT 3")), true);

                $schemaDetails[] = [
                    'table_name' => $tableName,
                    'columns' => $columns,
                    'sample_data' => $sampleData
                ];
            }

        } catch (\Exception $e) {
            Log::error("Error extracting schema: " . $e->getMessage());
        } finally {
            DB::purge($connectionName);
            if (file_exists($dbPath)) {
                unlink($dbPath);
            }
        }

        return $schemaDetails;
    }
}
