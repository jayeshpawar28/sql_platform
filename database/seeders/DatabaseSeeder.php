<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Problem;
use App\Models\Badge;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Test User
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // 5 Badges
        $badges = [
            ['name' => 'First Blood', 'description' => 'Solved your first problem', 'icon' => 'bi-droplet-fill'],
            ['name' => 'On Fire', 'description' => '7-day streak', 'icon' => 'bi-fire'],
            ['name' => 'JOIN Master', 'description' => 'Solved 5 JOIN problems', 'icon' => 'bi-diagram-3-fill'],
            ['name' => 'Speed Demon', 'description' => 'Solved a problem very quickly', 'icon' => 'bi-lightning-fill'],
            ['name' => 'Perfect 10', 'description' => 'Solved 10 problems', 'icon' => 'bi-10-circle-fill'],
        ];

        foreach ($badges as $badge) {
            Badge::create($badge);
        }

        // 3 Datasets and 10 Problems

        // Dataset 1: Employees
        $employeeSchema = "
            CREATE TABLE employees (id INTEGER PRIMARY KEY, name TEXT, salary INTEGER, department_id INTEGER);
            INSERT INTO employees VALUES (1, 'Alice', 60000, 1);
            INSERT INTO employees VALUES (2, 'Bob', 55000, 1);
            INSERT INTO employees VALUES (3, 'Charlie', 70000, 2);
            INSERT INTO employees VALUES (4, 'David', 45000, 2);
            INSERT INTO employees VALUES (5, 'Eve', 80000, 3);
            CREATE TABLE departments (id INTEGER PRIMARY KEY, name TEXT);
            INSERT INTO departments VALUES (1, 'Engineering');
            INSERT INTO departments VALUES (2, 'Sales');
            INSERT INTO departments VALUES (3, 'Management');
        ";

        Problem::create([
            'title' => 'Highest Paid Employee',
            'description' => 'Write a query to find the employee with the highest salary.',
            'difficulty' => 'easy',
            'topic' => 'Select',
            'default_schema' => $employeeSchema,
            'expected_output' => json_encode([['id' => 5, 'name' => 'Eve', 'salary' => 80000, 'department_id' => 3]]),
        ]);

        Problem::create([
            'title' => 'Average Salary by Department',
            'description' => 'Write a query to find the average salary for each department name.',
            'difficulty' => 'medium',
            'topic' => 'Aggregation',
            'default_schema' => $employeeSchema,
            'expected_output' => json_encode([
                ['name' => 'Engineering', 'avg_salary' => 57500],
                ['name' => 'Management', 'avg_salary' => 80000],
                ['name' => 'Sales', 'avg_salary' => 57500], // (70k+45k)/2
            ]),
        ]);

        Problem::create([
            'title' => 'Employees earning more than their manager',
            'description' => 'Write a query to find employees earning more than 60000 along with their department name.',
            'difficulty' => 'medium',
            'topic' => 'Join',
            'default_schema' => $employeeSchema,
            'expected_output' => json_encode([
                ['name' => 'Charlie', 'dept_name' => 'Sales'],
                ['name' => 'Eve', 'dept_name' => 'Management'],
            ]),
        ]);

        // Dataset 2: E-commerce
        $ecommerceSchema = "
            CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT, created_at DATE);
            INSERT INTO users VALUES (1, 'John', '2025-01-01');
            INSERT INTO users VALUES (2, 'Jane', '2025-01-02');
            CREATE TABLE orders (id INTEGER PRIMARY KEY, user_id INTEGER, amount DECIMAL(10,2), order_date DATE);
            INSERT INTO orders VALUES (101, 1, 150.00, '2025-01-10');
            INSERT INTO orders VALUES (102, 1, 200.50, '2025-01-15');
            INSERT INTO orders VALUES (103, 2, 50.00, '2025-01-20');
        ";

        Problem::create([
            'title' => 'Total Spent by User',
            'description' => 'Find the total amount spent by each user (user_id, total_spent).',
            'difficulty' => 'easy',
            'topic' => 'Aggregation',
            'default_schema' => $ecommerceSchema,
            'expected_output' => json_encode([
                ['user_id' => 1, 'total_spent' => 350.50],
                ['user_id' => 2, 'total_spent' => 50.00],
            ]),
        ]);

        Problem::create([
            'title' => 'Users without orders',
            'description' => 'Find users who have not placed any orders. In this dataset, all users have orders, so it should be empty.',
            'difficulty' => 'easy',
            'topic' => 'Join',
            'default_schema' => $ecommerceSchema . "INSERT INTO users VALUES (3, 'Ghost', '2025-02-01');",
            'expected_output' => json_encode([
                ['id' => 3, 'name' => 'Ghost', 'created_at' => '2025-02-01']
            ]),
        ]);

        Problem::create([
            'title' => 'Largest Order Details',
            'description' => 'Retrieve the order details (order id, amount) and the user name for the order with the largest amount.',
            'difficulty' => 'hard',
            'topic' => 'Select',
            'default_schema' => $ecommerceSchema,
            'expected_output' => json_encode([
                ['id' => 102, 'amount' => 200.50, 'name' => 'John']
            ]),
        ]);

        // Dataset 3: Hospital
        $hospitalSchema = "
            CREATE TABLE patients (patient_id INTEGER PRIMARY KEY, first_name TEXT, last_name TEXT, gender TEXT);
            INSERT INTO patients VALUES (1, 'Donald', 'Duck', 'M');
            INSERT INTO patients VALUES (2, 'Mickey', 'Mouse', 'M');
            INSERT INTO patients VALUES (3, 'Minnie', 'Mouse', 'F');
            CREATE TABLE admissions (patient_id INTEGER, admission_date DATE, discharge_date DATE, diagnosis TEXT);
            INSERT INTO admissions VALUES (1, '2025-03-01', '2025-03-05', 'Flu');
            INSERT INTO admissions VALUES (2, '2025-04-10', '2025-04-11', 'Checkup');
            INSERT INTO admissions VALUES (1, '2025-05-01', '2025-05-10', 'Pneumonia');
        ";

        Problem::create([
            'title' => 'Show all patients',
            'description' => 'Select first_name and last_name of all patients.',
            'difficulty' => 'easy',
            'topic' => 'Select',
            'default_schema' => $hospitalSchema,
            'expected_output' => json_encode([
                ['first_name' => 'Donald', 'last_name' => 'Duck'],
                ['first_name' => 'Mickey', 'last_name' => 'Mouse'],
                ['first_name' => 'Minnie', 'last_name' => 'Mouse'],
            ]),
        ]);

        Problem::create([
            'title' => 'Patient Admissions Count',
            'description' => 'Find the total number of admissions for each patient_id.',
            'difficulty' => 'medium',
            'topic' => 'Aggregation',
            'default_schema' => $hospitalSchema,
            'expected_output' => json_encode([
                ['patient_id' => 1, 'admissions_count' => 2],
                ['patient_id' => 2, 'admissions_count' => 1],
            ]),
        ]);

        Problem::create([
            'title' => 'Patients with multiple admissions',
            'description' => 'Find the names (first_name, last_name) of patients who have been admitted more than once.',
            'difficulty' => 'hard',
            'topic' => 'Join',
            'default_schema' => $hospitalSchema,
            'expected_output' => json_encode([
                ['first_name' => 'Donald', 'last_name' => 'Duck']
            ]),
        ]);

        Problem::create([
            'title' => 'Longest Stay Diagnosis',
            'description' => 'Which diagnosis resulted in the longest hospital stay? (Return diagnosis and days_stayed).',
            'difficulty' => 'hard',
            'topic' => 'Select',
            'default_schema' => $hospitalSchema,
            'expected_output' => json_encode([
                ['diagnosis' => 'Pneumonia', 'days_stayed' => 9]
            ]),
        ]);
    }
}
