<div class="editor-layout d-flex w-100" style="height: calc(100vh - 75px);" id="main-split">
    
    <!-- LEFT PANEL: Problem Description & AI -->
    <div id="split-left" class="bg-body border-end d-flex flex-column h-100 overflow-hidden" wire:ignore.self>
        <div class="card-header bg-body border-bottom pt-3 pb-2 px-3 sticky-top z-1">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-body">{{ $problem->title }}</h5>
                <span class="badge {{ $problem->difficulty === 'easy' ? 'bg-success' : ($problem->difficulty === 'medium' ? 'bg-warning' : 'bg-danger') }}">
                    {{ ucfirst($problem->difficulty) }}
                </span>
            </div>
        </div>
        
        <div class="overflow-auto flex-grow-1 p-3">
            <p class="text-muted small mb-2 fw-semibold text-uppercase tracking-wider">Topic: {{ $problem->topic }}</p>
            <div class="problem-description mb-4 text-body" style="font-size: 0.95rem; line-height: 1.6;">
                {!! nl2br(e($problem->description)) !!}
            </div>

            <!-- Schema & Sample Data -->
            @if(!empty($schemaDetails))
                <div class="schema-section mt-4 mb-4">
                    <h6 class="fw-bold mb-3 border-bottom pb-2 text-muted small">DATABASE SCHEMA</h6>
                    <div class="accordion accordion-flush border rounded" id="schemaAccordion">
                        @foreach($schemaDetails as $index => $table)
                            <div class="accordion-item {{ !$loop->last ? 'border-bottom' : '' }}">
                                <h2 class="accordion-header" id="heading-{{ $index }}">
                                    <button class="accordion-button px-3 py-2 bg-body-tertiary fw-bold text-body shadow-none {{ $index !== 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse-{{ $index }}" style="font-size: 0.9rem;">
                                        <i class="bi bi-table me-2 text-primary"></i> <code class="text-body ms-2">{{ $table['table_name'] }}</code>
                                    </button>
                                </h2>
                                <div id="collapse-{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="heading-{{ $index }}">
                                    <div class="accordion-body px-3 pt-3 pb-3 bg-body">
                                        <!-- Columns -->
                                        <div class="table-responsive mb-3 rounded border">
                                            <table class="table table-sm table-borderless mb-0" style="font-size: 0.8rem;">
                                                <thead class="table-light border-bottom text-muted">
                                                    <tr>
                                                        <th class="fw-semibold">Column Name</th>
                                                        <th class="fw-semibold">Type</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($table['columns'] as $col)
                                                        <tr>
                                                            <td><code class="text-body fw-semibold">{{ $col['name'] }}</code></td>
                                                            <td class="text-muted">{{ $col['type'] }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <!-- Sample Data -->
                                        @if(!empty($table['sample_data']))
                                            <p class="mb-2 fw-semibold text-muted" style="font-size: 0.8rem;">Sample Data:</p>
                                            <div class="table-responsive rounded border">
                                                <table class="table table-sm table-striped table-borderless mb-0" style="font-size: 0.8rem;">
                                                    <thead class="table-light border-bottom text-muted">
                                                        <tr>
                                                            @foreach(array_keys($table['sample_data'][0]) as $header)
                                                                <th class="fw-semibold">{{ $header }}</th>
                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($table['sample_data'] as $row)
                                                            <tr>
                                                                @foreach($row as $val)
                                                                    <td>{{ $val }}</td>
                                                                @endforeach
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <!-- Expected Output -->
            @if($problem->expected_output)
                @php
                    $expectedOutputData = json_decode($problem->expected_output, true);
                @endphp
                @if(!empty($expectedOutputData))
                    <div class="expected-output-section mt-4 mb-4">
                        <h6 class="fw-bold mb-3 border-bottom pb-2 text-muted small">EXPECTED OUTPUT</h6>
                        <div class="table-responsive border rounded bg-body">
                            <table class="table table-sm table-striped table-borderless mb-0" style="font-size: 0.85rem;">
                                <thead class="table-success border-bottom">
                                    <tr>
                                        @foreach(array_keys($expectedOutputData[0]) as $header)
                                            <th class="fw-semibold">{{ $header }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expectedOutputData as $row)
                                        <tr>
                                            @foreach($row as $val)
                                                <td>{{ $val }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endif
            
            @if($isSolved)
                <div class="alert alert-success d-flex align-items-center mb-4 border-0 shadow-sm rounded-3">
                    <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                    <div>
                        <strong>Solved!</strong> You have successfully solved this problem.
                    </div>
                </div>
            @endif
            
            <?php /* ?>
            <div class="card bg-body-tertiary border-0 shadow-sm rounded-3 mt-4 mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3 text-body"><i class="bi bi-robot me-2 text-primary"></i> AI Assistant</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <button wire:click="getHint" class="btn btn-outline-info btn-sm rounded-pill fw-semibold" wire:loading.attr="disabled">
                            <i class="bi bi-lightbulb me-1"></i> Get Hint
                        </button>
                        <button wire:click="optimizeQuery" class="btn btn-outline-primary btn-sm rounded-pill fw-semibold" wire:loading.attr="disabled">
                            <i class="bi bi-lightning-charge me-1"></i> Optimize
                        </button>
                        @if($isSolved)
                            <button wire:click="explainSolution" class="btn btn-outline-success btn-sm rounded-pill fw-semibold" wire:loading.attr="disabled">
                                <i class="bi bi-book me-1"></i> Explain
                            </button>
                        @endif
                    </div>
                    
                    <!-- AI Loading State -->
                    <div wire:loading wire:target="getHint, optimizeQuery, explainSolution" class="mt-3 text-muted small fw-semibold">
                        <span class="spinner-border spinner-border-sm me-2 text-primary" role="status" aria-hidden="true"></span>
                        AI is thinking...
                    </div>
                    
                    @if($aiResponse)
                        <div class="mt-3 p-3 bg-body rounded-3 small border position-relative shadow-sm">
                            <button type="button" class="btn-close btn-close-sm position-absolute top-0 end-0 m-2" aria-label="Close" wire:click="$set('aiResponse', '')"></button>
                            <div style="font-size: 0.9rem; line-height: 1.5;" class="text-body">
                                {!! nl2br(e($aiResponse)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <?php */ ?>
        </div>
    </div>

    <!-- RIGHT PANEL: Editor + Results -->
    <div id="split-right" class="d-flex flex-column h-100 bg-dark overflow-hidden" wire:ignore.self>
        
        <!-- TOP: Monaco Editor -->
        <div id="split-top" class="d-flex flex-column overflow-hidden" wire:ignore.self>
            <div class="bg-dark text-white d-flex justify-content-between align-items-center py-2 px-3 border-bottom border-secondary">
                <span class="fw-bold small text-light"><i class="bi bi-code-slash me-2 text-primary"></i> SQL Editor</span>
                <div>
                    <button wire:click="resetEditor" class="btn btn-sm btn-outline-secondary me-2 rounded-1">Reset</button>
                    <button wire:click="runQuery" class="btn btn-sm btn-light me-2 rounded-1 fw-bold">Run Code</button>
                    <button wire:click="submitQuery" class="btn btn-sm btn-success rounded-1 fw-bold px-3">Submit</button>
                </div>
            </div>
            
            <div class="flex-grow-1 position-relative w-100 overflow-hidden">
                <div id="monaco-container" class="position-absolute top-0 bottom-0 start-0 end-0 w-100" wire:ignore></div>
                <!-- Hidden textarea to bind livewire model -->
                <textarea id="hidden-query" wire:model="query" style="display:none;"></textarea>
            </div>
        </div>
        
        <!-- BOTTOM: Results -->
        <div id="split-bottom" class="d-flex flex-column bg-body border-top overflow-hidden" wire:ignore.self>
            <div class="bg-body-tertiary pt-2 pb-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                <span class="fw-bold small text-secondary text-uppercase"><i class="bi bi-terminal me-2"></i> Results</span>
                @guest
                    <span class="small text-muted d-none d-md-inline">
                        You need to <a href="#" wire:click.prevent="redirectToLogin" class="text-primary text-decoration-none fw-bold">log in</a> / <a href="#" wire:click.prevent="redirectToRegister" class="text-primary text-decoration-none fw-bold">sign up</a> to run or submit
                    </span>
                @endguest
            </div>
            
            <div class="overflow-auto flex-grow-1 p-3">
                <div wire:loading wire:target="runQuery, submitQuery" class="w-100 h-100">
                    <div class="text-center py-5 h-100 d-flex flex-column justify-content-center align-items-center">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Executing...</span>
                        </div>
                        <h6 class="text-muted fw-semibold">Running query on sandbox DB...</h6>
                    </div>
                </div>
                
                <div wire:loading.remove wire:target="runQuery, submitQuery">
                    @if($status === 'error')
                        <div class="alert alert-danger mb-0 border-0 shadow-sm rounded-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                                <strong>Execution Error</strong>
                            </div>
                            <code class="text-danger d-block p-2 bg-body-tertiary rounded border text-wrap text-break">{{ $errorMessage }}</code>
                        </div>
                    @elseif($status === 'correct' || $status === 'incorrect')
                        <div class="alert {{ $status === 'correct' ? 'alert-success' : 'alert-danger' }} mb-4 border-0 shadow-sm rounded-3 py-2">
                            <h5 class="mb-0 d-flex align-items-center">
                                <i class="bi {{ $status === 'correct' ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} fs-4 me-2"></i>
                                <strong>{{ $status === 'correct' ? 'Accepted' : 'Wrong Answer' }}</strong>
                            </h5>
                        </div>
                        
                        @if($results)
                            <h6 class="fw-bold text-body small text-uppercase mb-2">Your Output</h6>
                            <div class="table-responsive border rounded bg-body shadow-sm mb-4">
                                <table class="table table-sm table-striped table-borderless mb-0" style="font-size: 0.85rem;">
                                    <thead class="table-light border-bottom">
                                        <tr>
                                            @foreach(array_keys($results[0] ?? []) as $header)
                                                <th class="fw-semibold text-muted">{{ $header }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($results as $row)
                                            <tr>
                                                @foreach($row as $col)
                                                    <td>{{ $col }}</td>
                                                @endforeach
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="100%" class="text-center py-3 text-muted">0 rows returned.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @endif
                        
                        @if($expected)
                            <h6 class="fw-bold text-muted small text-uppercase mb-2">Expected Output</h6>
                            <div class="table-responsive border rounded bg-body shadow-sm opacity-75">
                                <table class="table table-sm table-striped table-borderless mb-0" style="font-size: 0.85rem;">
                                    <thead class="table-light border-bottom">
                                        <tr>
                                            @foreach(array_keys($expected[0] ?? []) as $header)
                                                <th class="fw-semibold text-muted">{{ $header }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($expected as $row)
                                            <tr>
                                                @foreach($row as $col)
                                                    <td>{{ $col }}</td>
                                                @endforeach
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="100%" class="text-center py-3 text-muted">0 rows expected.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @elseif($status === 'auth_required')
                        <div class="alert alert-warning mb-0 border-0 shadow-sm rounded-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-lock-fill fs-5 me-2 text-warning"></i>
                                <strong>Authentication Required</strong>
                            </div>
                            <p class="mb-0 text-body">You need to <a href="#" wire:click.prevent="redirectToLogin" class="fw-bold text-decoration-none">log in</a> or <a href="#" wire:click.prevent="redirectToRegister" class="fw-bold text-decoration-none">sign up</a> to execute code on the sandbox servers.</p>
                        </div>
                    @else
                        <div class="text-center text-muted py-5 h-100 d-flex flex-column justify-content-center align-items-center">
                            <i class="bi bi-play-circle fs-1 mb-3 d-block opacity-25"></i>
                            <h6 class="fw-semibold">Run or submit your query to see results here.</h6>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hide the footer on this specific page */
    footer { display: none !important; }
    
    /* Gutter styles for Split.js */
    .gutter {
        background-color: #272d3;
        background-repeat: no-repeat;
        background-position: 50%;
        transition: background-color 0.2s;
    }
    .gutter:hover {
        background-color: #e2e8f0;
    }
    .gutter.gutter-horizontal {
        background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAeCAYAAADkftS9AAAAIklEQVQoU2M4c+bMfxAGAgYYmwGrIIiDjrELjpo5aiZeMwF+yNnOs5KSvgAAAABJRU5ErkJggg==');
        cursor: col-resize;
        border-left: 1px solid #e2e8f0;
        border-right: 1px solid #e2e8f0;
    }
    .gutter.gutter-vertical {
        background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAFAQMAAABo7865AAAABlBMVEVHcEzMzMzyCB2iAAAAAXRSTlMAQObYZgAAABBJREFUeF5jOAMEEAIEEFBgABpWAgR2X0lZAAAAAElFTkSuQmCC');
        cursor: row-resize;
        border-top: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
    }
    
    /* Minor scrollbar tweaks for editor panels */
    .overflow-auto::-webkit-scrollbar { width: 8px; height: 8px; }
    .overflow-auto::-webkit-scrollbar-track { background: transparent; }
    .overflow-auto::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 4px; }
    .overflow-auto::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }
</style>

@script
<script>
    // Load Split.js
    if (typeof Split === 'undefined') {
        const splitScript = document.createElement('script');
        splitScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/split.js/1.6.5/split.min.js';
        splitScript.onload = () => initSplitLayout();
        document.head.appendChild(splitScript);
    } else {
        initSplitLayout();
    }
    
    let editor;
    let horizontalSplit;
    let verticalSplit;

    function initSplitLayout() {
        // Initialize Resizable Panels
        if (document.getElementById('split-left') && document.getElementById('split-right')) {
            horizontalSplit = Split(['#split-left', '#split-right'], {
                sizes: [40, 60],
                minSize: [300, 300],
                gutterSize: 8,
                cursor: 'col-resize',
                onDrag: () => { if (editor) editor.layout(); }
            });
        }
        
        if (document.getElementById('split-top') && document.getElementById('split-bottom')) {
            verticalSplit = Split(['#split-top', '#split-bottom'], {
                direction: 'vertical',
                sizes: [50, 50],
                minSize: [100, 100],
                gutterSize: 8,
                cursor: 'row-resize',
                onDrag: () => { if (editor) editor.layout(); }
            });
        }
        
        // Window resize listener
        window.addEventListener('resize', () => {
            if (editor) editor.layout();
        });
        
        loadMonaco();
    }

    function loadMonaco() {
        // Load Monaco Editor via CDN
        const requireConfig = { paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs' } };
        
        if (typeof monaco === 'undefined') {
            const loaderScript = document.createElement('script');
            loaderScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs/loader.min.js';
            loaderScript.onload = () => {
                window.require.config(requireConfig);
                window.require(['vs/editor/editor.main'], function() {
                    initMonaco();
                });
            };
            document.head.appendChild(loaderScript);
        } else {
            initMonaco();
        }
    }

    function initMonaco() {
        const container = document.getElementById('monaco-container');
        const hiddenQuery = document.getElementById('hidden-query');
        
        if (container && hiddenQuery) {
            
            // Register SQL Autocompletion Provider if not already registered
            if (!window.sqlCompletionRegistered) {
                // Get schema details passed from Laravel
                const schemaData = @json($schemaDetails ?? []);
                
                monaco.languages.registerCompletionItemProvider('sql', {
                    provideCompletionItems: function(model, position) {
                        const word = model.getWordUntilPosition(position);
                        const range = {
                            startLineNumber: position.lineNumber,
                            endLineNumber: position.lineNumber,
                            startColumn: word.startColumn,
                            endColumn: word.endColumn
                        };
                        
                        const keywords = [
                            'SELECT', 'FROM', 'WHERE', 'INSERT', 'INTO', 'VALUES', 'UPDATE', 'SET', 'DELETE',
                            'JOIN', 'INNER JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'FULL JOIN', 'ON', 'AS', 'AND', 'OR',
                            'NOT', 'GROUP BY', 'ORDER BY', 'HAVING', 'LIMIT', 'OFFSET', 'UNION', 'ALL', 'ANY',
                            'BETWEEN', 'EXISTS', 'IN', 'LIKE', 'IS NULL', 'IS NOT NULL', 'COUNT', 'SUM', 'AVG', 'MIN', 'MAX',
                            'CREATE', 'TABLE', 'ALTER', 'DROP', 'INDEX', 'PRIMARY KEY', 'FOREIGN KEY', 'REFERENCES'
                        ];

                        const suggestions = keywords.map(keyword => {
                            return {
                                label: keyword,
                                kind: monaco.languages.CompletionItemKind.Keyword,
                                detail: 'Keyword',
                                insertText: keyword,
                                range: range
                            };
                        });
                        
                        // Add Tables and Columns from Schema to Autocomplete
                        schemaData.forEach(table => {
                            suggestions.push({
                                label: table.table_name,
                                kind: monaco.languages.CompletionItemKind.Class,
                                detail: 'Table',
                                insertText: table.table_name,
                                range: range
                            });
                            
                            table.columns.forEach(col => {
                                // Prevent duplicate column suggestions if multiple tables have the same column name
                                if (!suggestions.find(s => s.label === col.name && s.kind === monaco.languages.CompletionItemKind.Field)) {
                                    suggestions.push({
                                        label: col.name,
                                        kind: monaco.languages.CompletionItemKind.Field,
                                        detail: 'Column (' + col.type + ')',
                                        insertText: col.name,
                                        range: range
                                    });
                                }
                            });
                        });

                        return { suggestions: suggestions };
                    }
                });
                window.sqlCompletionRegistered = true;
            }

            editor = monaco.editor.create(container, {
                value: hiddenQuery.value,
                language: 'sql',
                theme: 'vs-dark',
                automaticLayout: true,
                minimap: { enabled: false },
                fontSize: 14,
                scrollBeyondLastLine: false,
                suggestOnTriggerCharacters: true,
                quickSuggestions: true,
                wordWrap: 'on'
            });
            
            // Force layout calculation after slight delay to ensure container is fully rendered
            setTimeout(() => { editor.layout(); }, 100);

            // Update livewire component when editor content changes
            editor.onDidChangeModelContent(() => {
                // We use vanilla JS to dispatch event to Livewire 3
                $wire.set('query', editor.getValue(), false);
            });
        }
    }
    
    // Listen for reset events from Livewire
    Livewire.on('updateEditor', (data) => {
        if (editor) {
            editor.setValue(data.query);
        }
    });
</script>
@endscript
