<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Problem;
use App\Models\Submission;
use App\Models\UserProgress;
use App\Services\SqlExecutorService;
use Illuminate\Support\Facades\Auth;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Exceptions\PrismRateLimitedException;
use Prism\Prism\Exceptions\PrismProviderOverloadedException;

class SqlEditor extends Component
{
    public Problem $problem;
    public $query = '';
    public $results = null;
    public $expected = null;
    public $status = '';
    public $errorMessage = '';
    
    public $aiResponse = '';
    public $aiLoading = false;
    public $isSolved = false;
    public $schemaDetails = [];

    
    public function mount(Problem $problem)
    {
        // dd($problem);
        $this->problem = $problem;
        $this->query = "-- Write your query here";
        
        if (Auth::check()) {
            $this->isSolved = Submission::where('user_id', Auth::id())
                ->where('problem_id', $this->problem->id)
                ->where('status', 'correct')
                ->exists();
        }
            
        $this->schemaDetails = app(SqlExecutorService::class)->getSchemaDetails($problem);

        // dd($this->schemaDetails);
    }

    public function runQuery(SqlExecutorService $executor)
    {
        
        if (!Auth::check()) {
            $this->status = 'auth_required';
            return;
        }

        $this->reset(['results', 'expected', 'status', 'errorMessage', 'aiResponse']);
        
        if (empty(trim($this->query)) || $this->query == '-- Write your query here') {
            // dd($this->query);
            $this->status = 'error';
            $this->errorMessage = 'Query cannot be empty.';
            return;
        }

        $response = $executor->executeSubmission($this->problem, $this->query);
        
        $this->status = $response['status'];
        if ($this->status === 'error') {
            $this->errorMessage = $response['message'];
        } else {
            $this->results = $response['results'];
            $this->expected = $response['expected'];
        }
    }

    public function submitQuery(SqlExecutorService $executor)
    {
        if (!Auth::check()) {
            $this->status = 'auth_required';
            return;
        }

        $this->runQuery($executor);
        
        if ($this->status !== 'error') {
            Submission::create([
                'user_id' => Auth::id(),
                'problem_id' => $this->problem->id,
                'query' => $this->query,
                'status' => $this->status,
                'error_message' => $this->errorMessage,
            ]);

            if ($this->status === 'correct') {
                $this->isSolved = true;
                $this->updateProgress();
                
                session()->flash('success', 'Great job! You successfully solved the problem.');
                return $this->redirect(route('problems.index'), navigate: true);
            }
        }
    }

    public function resetEditor()
    {
        $this->query = "-- Write your query here";
        $this->reset(['results', 'expected', 'status', 'errorMessage', 'aiResponse']);
        $this->dispatch('updateEditor', query: $this->query);
    }

    private function ensureAiConfigured(): bool
    {
        $provider = config('services.ai.provider', 'gemini');
        
        $keyEnvVar = match($provider) {
            'gemini' => 'GEMINI_API_KEY',
            'openai' => 'OPENAI_API_KEY',
            'anthropic' => 'ANTHROPIC_API_KEY',
            'mistral' => 'MISTRAL_API_KEY',
            'groq' => 'GROQ_API_KEY',
            'xai' => 'XAI_API_KEY',
            'deepseek' => 'DEEPSEEK_API_KEY',
            'elevenlabs' => 'ELEVENLABS_API_KEY',
            'voyageai' => 'VOYAGEAI_API_KEY',
            'openrouter' => 'OPENROUTER_API_KEY',
            'perplexity' => 'PERPLEXITY_API_KEY',
            'z' => 'Z_API_KEY',
            default => null,
        };
        
        $apiKey = $keyEnvVar ? (config("prism.providers.{$provider}.api_key") ?: env($keyEnvVar)) : null;

        if (empty($apiKey)) {
            $this->aiResponse = "AI Assistant is not configured yet.\n\nTo enable AI-powered hints and explanation features, please get a free Gemini API key from Google AI Studio (https://aistudio.google.com/) and add it to your .env file:\n\nGEMINI_API_KEY=your_key_here";
            return false;
        }

        return true;
    }

    public function getHint()
    {
        $this->aiLoading = true;
        try {
            if (!$this->ensureAiConfigured()) {
                $this->aiLoading = false;
                return;
            }

            $prompt = "I am working on this SQL problem:\n" . 
                      "Title: {$this->problem->title}\n" .
                      "Description: {$this->problem->description}\n" .
                      "My current query: {$this->query}\n\n" .
                      "Give me a smart hint to help me solve it. DO NOT give me the exact answer. Nudge me in the right direction.";

            $response = Prism::text()
                ->using(config('services.ai.provider'), config('services.ai.model'))
                ->withClientOptions(['verify' => false])
                ->withPrompt($prompt)
                ->generate();

            $this->aiResponse = $response->text;
        } catch (PrismRateLimitedException $e) {
            $this->aiResponse = "⚠️ AI rate limit reached. The free Gemini tier allows a limited number of requests per minute. Please wait 30-60 seconds and try again.";
        } catch (PrismProviderOverloadedException $e) {
            $this->aiResponse = "⚠️ The AI provider is currently overloaded. Please try again in a few moments.";
        } catch (\Exception $e) {
            $this->aiResponse = "Error generating hint: " . $e->getMessage();
        }
        $this->aiLoading = false;
    }

    public function explainSolution()
    {
        if (!$this->isSolved) return;
        
        $this->aiLoading = true;
        try {
            if (!$this->ensureAiConfigured()) {
                $this->aiLoading = false;
                return;
            }

            $prompt = "I just solved this SQL problem:\n" . 
                      "Title: {$this->problem->title}\n" .
                      "Description: {$this->problem->description}\n" .
                      "My working query: {$this->query}\n\n" .
                      "Please explain why this solution works in simple terms.";

            $response = Prism::text()
                ->using(config('services.ai.provider'), config('services.ai.model'))
                ->withClientOptions(['verify' => false])
                ->withPrompt($prompt)
                ->generate();

            $this->aiResponse = $response->text;
        } catch (PrismRateLimitedException $e) {
            $this->aiResponse = "⚠️ AI rate limit reached. Please wait 30-60 seconds and try again.";
        } catch (PrismProviderOverloadedException $e) {
            $this->aiResponse = "⚠️ The AI provider is currently overloaded. Please try again in a few moments.";
        } catch (\Exception $e) {
            $this->aiResponse = "Error generating explanation: " . $e->getMessage();
        }
        $this->aiLoading = false;
    }

    public function optimizeQuery()
    {
        $this->aiLoading = true;
        try {
            if (!$this->ensureAiConfigured()) {
                $this->aiLoading = false;
                return;
            }

            $prompt = "I have this SQL query for a problem titled '{$this->problem->title}':\n" .
                      "Query: {$this->query}\n\n" .
                      "Can this be optimized for performance or readability? If so, show the optimized query and explain why.";

            $response = Prism::text()
                ->using(config('services.ai.provider'), config('services.ai.model'))
                ->withClientOptions(['verify' => false])
                ->withPrompt($prompt)
                ->generate();

            $this->aiResponse = $response->text;
        } catch (PrismRateLimitedException $e) {
            $this->aiResponse = "⚠️ AI rate limit reached. Please wait 30-60 seconds and try again.";
        } catch (PrismProviderOverloadedException $e) {
            $this->aiResponse = "⚠️ The AI provider is currently overloaded. Please try again in a few moments.";
        } catch (\Exception $e) {
            $this->aiResponse = "Error generating optimization: " . $e->getMessage();
        }
        $this->aiLoading = false;
    }



    private function updateProgress()
    {
        $progress = UserProgress::firstOrCreate(
            ['user_id' => Auth::id(), 'topic' => $this->problem->topic],
            ['problems_solved' => 0, 'total_problems' => Problem::where('topic', $this->problem->topic)->count()]
        );
        
        $solvedCount = Submission::where('user_id', Auth::id())
            ->where('status', 'correct')
            ->whereHas('problem', function($q) {
                $q->where('topic', $this->problem->topic);
            })->distinct('problem_id')->count();
            
        $progress->update(['problems_solved' => $solvedCount]);
    }

    public function redirectToLogin()
    {
        return redirect()->guest(route('login'));
    }

    public function redirectToRegister()
    {
        return redirect()->guest(route('register'));
    }

    public function render()
    {
        return view('livewire.sql-editor')->layout('layouts.app');
    }
}
