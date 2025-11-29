@extends('layouts.tenant.student')

@section('title', 'Edit Note')

@section('content')
    <div class="container-fluid h-100 d-flex flex-column">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('tenant.student.notes.index', ['view' => 'personal']) }}"
                            class="text-decoration-none">{{ __('Notes') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ __('Edit Note') }}</li>
                </ol>
            </nav>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="toggleResearchPanel()">
                    <i class="bi bi-robot me-1"></i> {{ __('AI Research Assistant') }}
                </button>
                <form action="{{ route('tenant.student.notes.personal.destroy', $personalNote->id) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this note?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-trash me-1"></i> {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="row flex-grow-1 g-3">
            <!-- Editor Column -->
            <div class="col-lg-8 col-xl-8 d-flex flex-column transition-all" id="editorColumn">
                <div class="card border-0 shadow-sm flex-grow-1 d-flex flex-column">
                    <div class="card-body p-0 d-flex flex-column">
                        <form action="{{ route('tenant.student.notes.personal.update', $personalNote->id) }}" method="POST"
                            class="h-100 d-flex flex-column" id="noteForm">
                            @csrf
                            @method('PUT')

                            <!-- Top Controls -->
                            <div class="p-3 border-bottom bg-light d-flex gap-3 align-items-center flex-wrap">
                                <div class="flex-grow-1">
                                    <input type="text"
                                        class="form-control form-control-lg border-0 bg-transparent fw-bold px-0"
                                        id="title" name="title" value="{{ old('title', $personalNote->title) }}"
                                        required placeholder="{{ __('Untitled Note') }}" style="box-shadow: none;">
                                </div>

                                <div class="d-flex gap-2 align-items-center">
                                    <select class="form-select form-select-sm" id="subject_id" name="subject_id"
                                        style="max-width: 150px;">
                                        <option value="">{{ __('No Subject') }}</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}"
                                                {{ old('subject_id', $personalNote->subject_id) == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm border dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">
                                            <i class="bi bi-palette"></i>
                                        </button>
                                        <div class="dropdown-menu p-2" style="min-width: 200px;">
                                            <label class="small text-muted mb-2 ms-1">Note Color</label>
                                            <div class="d-flex gap-1 flex-wrap">
                                                @foreach (['#ffffff', '#f28b82', '#fbbc04', '#fff475', '#ccff90', '#a7ffeb', '#cbf0f8', '#aecbfa', '#d7aefb', '#fdcfe8', '#e6c9a8', '#e8eaed'] as $color)
                                                    <div class="form-check form-check-inline m-0 p-0">
                                                        <input class="btn-check" type="radio" name="color"
                                                            id="color_{{ $loop->index }}" value="{{ $color }}"
                                                            {{ old('color', $personalNote->color) == $color ? 'checked' : '' }}>
                                                        <label
                                                            class="btn btn-sm border rounded-circle p-0 d-flex align-items-center justify-content-center"
                                                            for="color_{{ $loop->index }}"
                                                            style="width: 24px; height: 24px; background-color: {{ $color }}; cursor: pointer;">
                                                            <i class="bi bi-check text-dark d-none small"></i>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm border dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">
                                            <i class="bi bi-share"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <h6 class="dropdown-header">Share Note Content</h6>
                                            </li>
                                            <li><a class="dropdown-item" href="#" onclick="shareNote('email')"><i
                                                        class="bi bi-envelope me-2"></i>Email</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="shareNote('whatsapp')"><i
                                                        class="bi bi-whatsapp me-2"></i>WhatsApp</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="shareNote('twitter')"><i
                                                        class="bi bi-twitter-x me-2"></i>X (Twitter)</a></li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li><a class="dropdown-item" href="#" onclick="copyToClipboard()"><i
                                                        class="bi bi-clipboard me-2"></i>Copy Text</a></li>
                                        </ul>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-sm px-3">
                                        <i class="bi bi-save me-1"></i> {{ __('Update') }}
                                    </button>
                                </div>
                            </div>

                            <!-- Editor Area -->
                            <div class="flex-grow-1 d-flex flex-column">
                                <div id="quill-editor" style="flex: 1; min-height: 400px;"></div>
                                <input type="hidden" id="content" name="content" required
                                    value="{{ old('content', $personalNote->content) }}">
                            </div>

                            <!-- Bottom Tags -->
                            <div class="p-3 border-top bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="input-group input-group-sm" style="max-width: 70%;">
                                        <span class="input-group-text border-0 bg-transparent"><i
                                                class="bi bi-tags"></i></span>
                                        <input type="text" class="form-control border-0 bg-transparent" id="tags"
                                            name="tags"
                                            value="{{ old('tags', is_array($personalNote->tags) ? implode(', ', $personalNote->tags) : '') }}"
                                            placeholder="{{ __('Add tags separated by commas (e.g. exam, important, chapter1)') }}">
                                    </div>
                                    <div class="small text-muted">
                                        <i class="bi bi-clock me-1"></i> Last saved:
                                        {{ $personalNote->updated_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Research Assistant Column -->
            <div class="col-lg-4 col-xl-4 d-none h-100" id="researchColumn">
                <div class="card border-0 shadow-sm h-100 d-flex flex-column">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-robot me-2"></i>Research Assistant</h6>
                        <button type="button" class="btn-close btn-sm" onclick="toggleResearchPanel()"></button>
                    </div>
                    <div class="card-body p-3 d-flex flex-column overflow-hidden">
                        <!-- Search Tabs -->
                        <ul class="nav nav-pills nav-fill mb-3" id="researchTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active py-1 small" id="wiki-tab" data-bs-toggle="tab"
                                    data-bs-target="#wiki" type="button" role="tab">
                                    <i class="bi bi-book me-1"></i> Wikipedia
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-1 small google-pill" id="google-tab" data-bs-toggle="tab"
                                    data-bs-target="#google" type="button" role="tab">
                                    <span class="google-pill-text">
                                        <span style="color: #4285F4;">G</span><span style="color: #DB4437;">o</span><span
                                            style="color: #F4B400;">o</span><span style="color: #4285F4;">g</span><span
                                            style="color: #0F9D58;">l</span><span style="color: #DB4437;">e</span>
                                    </span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-1 small" id="ai-tab" data-bs-toggle="tab"
                                    data-bs-target="#ai" type="button" role="tab">
                                    <i class="bi bi-stars me-1 text-warning"></i> Ask AI
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content flex-grow-1 overflow-hidden d-flex flex-column" id="researchTabContent">
                            <!-- Wikipedia Tab -->
                            <div class="tab-pane fade show active h-100 d-flex flex-column" id="wiki"
                                role="tabpanel">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" id="wikiSearchInput"
                                        placeholder="Search Wikipedia..." onkeypress="handleEnter(event, 'wiki')">
                                    <button class="btn btn-outline-primary" type="button" onclick="searchWikipedia()">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                                <div id="wikiResults" class="flex-grow-1 overflow-auto custom-scrollbar pe-2">
                                    <div class="text-center text-muted mt-5">
                                        <i class="bi bi-search fs-1 opacity-25"></i>
                                        <p class="small mt-2">Search for topics to add to your notes.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Google Tab -->
                            <div class="tab-pane fade h-100 d-flex flex-column" id="google" role="tabpanel">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" id="googleSearchInput"
                                        placeholder="Search Google..." onkeypress="handleEnter(event, 'google')">
                                    <button class="btn btn-primary" type="button" onclick="searchGoogle()"
                                        style="background-color: #4285F4; border-color: #4285F4;">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                                <div class="flex-grow-1 overflow-auto custom-scrollbar">
                                    <div class="alert alert-light border text-center small">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> Google Search opens in a new tab.
                                    </div>
                                    <div id="googleRecent" class="mt-3">
                                        <h6 class="small fw-bold text-muted text-uppercase">Recent Searches</h6>
                                        <ul class="list-group list-group-flush small" id="googleHistoryList">
                                            <!-- Populated by JS -->
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Ask AI Tab -->
                            <div class="tab-pane fade h-100 d-flex flex-column" id="ai" role="tabpanel">
                                <div class="mb-3">
                                    <select class="form-select form-select-sm" id="aiModelSelect">
                                        <option value="gemini-pro-3">Gemini 3 Pro (Preview)</option>
                                        <option value="chatgpt-5.1">ChatGPT 5.1</option>
                                        <option value="claude-opus-4.5">Claude Opus 4.5</option>
                                        <option value="perplexity">Perplexity</option>
                                    </select>
                                </div>
                                <div id="aiChatHistory"
                                    class="flex-grow-1 overflow-auto custom-scrollbar mb-3 p-2 border rounded bg-light">
                                    <div class="text-center text-muted mt-5">
                                        <i class="bi bi-stars fs-1 opacity-25 text-warning"></i>
                                        <p class="small mt-2">Select a model and ask anything!</p>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="aiChatInput" placeholder="Ask AI..."
                                        onkeypress="handleEnter(event, 'ai')">
                                    <button class="btn btn-primary" type="button" onclick="sendAiMessage()">
                                        <i class="bi bi-send"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Google Fonts: Quicksand -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Quill CSS/JS -->
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <style>
        #quill-editor {
            background: white;
            display: flex;
            flex-direction: column;
        }

        #quill-editor .ql-toolbar {
            border: none;
            border-bottom: 1px solid #dee2e6;
            background: #f8f9fa;
            padding: 12px 16px;
        }

        #quill-editor .ql-container {
            border: none;
            flex: 1;
            font-size: 16px;
            font-family: 'Quicksand', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        #quill-editor .ql-editor {
            padding: 20px;
            min-height: 400px;
        }

        #quill-editor .ql-editor.ql-blank::before {
            color: #adb5bd;
            font-style: normal;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        .wiki-result-item {
            font-size: 0.9rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .wiki-result-item:last-child {
            border-bottom: none;
        }

        .wiki-title {
            font-weight: 600;
            color: #0d6efd;
            cursor: pointer;
        }

        .wiki-snippet {
            color: #6c757d;
            margin-bottom: 5px;
        }

        .nav-pills .nav-link.google-pill.active {
            background-color: #fff;
            border: 1px solid #dadce0;
            box-shadow: 0 1px 2px rgba(60, 64, 67, 0.3), 0 1px 3px 1px rgba(60, 64, 67, 0.15);
        }

        .google-pill-text {
            font-weight: 500;
            letter-spacing: 0.2px;
        }
    </style>

    @push('scripts')
        <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
        <script>
            // Initialize Quill editor
            var quill = new Quill('#quill-editor', {
                theme: 'snow',
                placeholder: 'Start typing your notes here...',
                modules: {
                    toolbar: [
                        [{
                            'header': [1, 2, 3, 4, 5, 6, false]
                        }],
                        [{
                            'font': []
                        }],
                        [{
                            'size': ['small', false, 'large', 'huge']
                        }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{
                            'color': []
                        }, {
                            'background': []
                        }],
                        [{
                            'script': 'sub'
                        }, {
                            'script': 'super'
                        }],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }],
                        [{
                            'indent': '-1'
                        }, {
                            'indent': '+1'
                        }],
                        [{
                            'align': []
                        }],
                        ['blockquote', 'code-block'],
                        ['link', 'image', 'video'],
                        ['clean']
                    ]
                }
            });

            // Load existing content
            const existingContent = document.getElementById('content').value;
            if (existingContent) {
                quill.root.innerHTML = existingContent;
            }

            // Sync Quill content to hidden input on form submit
            document.getElementById('noteForm').addEventListener('submit', function(e) {
                document.getElementById('content').value = quill.root.innerHTML;
            });

            // Color picker logic
            document.querySelectorAll('input[name="color"]').forEach(function(radio) {
                radio.addEventListener('change', function() {
                    document.querySelectorAll('.btn-check + label i').forEach(function(icon) {
                        icon.classList.add('d-none');
                    });
                    this.nextElementSibling.querySelector('i').classList.remove('d-none');
                });
            });

            // Initialize color selection
            const selectedColor = "{{ old('color', $personalNote->color) }}";
            if (selectedColor) {
                const selectedRadio = document.querySelector(`input[name="color"][value="${selectedColor}"]`);
                if (selectedRadio) {
                    selectedRadio.checked = true;
                    selectedRadio.nextElementSibling.querySelector('i').classList.remove('d-none');
                }
            }

            function toggleResearchPanel() {
                const researchCol = document.getElementById('researchColumn');
                const editorCol = document.getElementById('editorColumn');

                if (researchCol.classList.contains('d-none')) {
                    // Show it
                    researchCol.classList.remove('d-none');
                    editorCol.classList.remove('col-12');
                    editorCol.classList.add('col-lg-8', 'col-xl-8');
                } else {
                    // Hide it
                    researchCol.classList.add('d-none');
                    editorCol.classList.remove('col-lg-8', 'col-xl-8');
                    editorCol.classList.add('col-12');
                }
            }

            // Initialize state
            document.addEventListener('DOMContentLoaded', function() {
                const editorCol = document.getElementById('editorColumn');
                editorCol.classList.add('col-12');
                editorCol.classList.remove('col-lg-8', 'col-xl-8');
            });

            function handleEnter(e, type) {
                if (e.key === 'Enter') {
                    if (type === 'wiki') searchWikipedia();
                    if (type === 'google') searchGoogle();
                    if (type === 'ai') sendAiMessage();
                }
            }

            function sendAiMessage() {
                const input = document.getElementById('aiChatInput');
                const message = input.value.trim();
                const model = document.getElementById('aiModelSelect').value;
                const historyDiv = document.getElementById('aiChatHistory');

                if (!message) return;

                // Clear input
                input.value = '';

                // Remove placeholder if exists
                if (historyDiv.querySelector('.text-center')) {
                    historyDiv.innerHTML = '';
                }

                // Append User Message
                const userDiv = document.createElement('div');
                userDiv.className = 'd-flex justify-content-end mb-2';
                userDiv.innerHTML = `
                <div class="bg-primary text-white p-2 rounded-3 small" style="max-width: 85%;">
                    ${escapeHtml(message)}
                </div>
            `;
                historyDiv.appendChild(userDiv);
                historyDiv.scrollTop = historyDiv.scrollHeight;

                // Append Loading Indicator
                const loadingDiv = document.createElement('div');
                loadingDiv.className = 'd-flex justify-content-start mb-2';
                loadingDiv.id = 'ai-loading';
                loadingDiv.innerHTML = `
                <div class="bg-white border p-2 rounded-3 small text-muted">
                    <span class="spinner-dots">Thinking...</span>
                </div>
            `;
                historyDiv.appendChild(loadingDiv);
                historyDiv.scrollTop = historyDiv.scrollHeight;

                // Send Request
                fetch('{{ route('tenant.student.notes.ai-chat') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            message: message,
                            model: model
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Remove loading
                        document.getElementById('ai-loading').remove();

                        // Append AI Response
                        const aiDiv = document.createElement('div');
                        aiDiv.className = 'd-flex justify-content-start mb-2';
                        aiDiv.innerHTML = `
                    <div class="bg-white border p-2 rounded-3 small" style="max-width: 90%;">
                        <div class="fw-bold text-primary mb-1" style="font-size: 0.75rem;">${data.model_name}</div>
                        ${data.response}
                        <div class="mt-1 pt-1 border-top">
                            <button class="btn btn-link btn-sm p-0 text-decoration-none" style="font-size: 0.7rem;" onclick="insertContent('AI Response', '${escapeHtml(data.raw_response)}')">
                                <i class="bi bi-plus-circle me-1"></i> Insert
                            </button>
                        </div>
                    </div>
                `;
                        historyDiv.appendChild(aiDiv);
                        historyDiv.scrollTop = historyDiv.scrollHeight;
                    })
                    .catch(error => {
                        document.getElementById('ai-loading').remove();
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'text-center text-danger small mb-2';
                        errorDiv.innerText = 'Error communicating with AI.';
                        historyDiv.appendChild(errorDiv);
                    });
            }

            function searchWikipedia() {
                const query = document.getElementById('wikiSearchInput').value;
                if (!query) return;

                const resultsDiv = document.getElementById('wikiResults');
                resultsDiv.innerHTML =
                    '<div class="text-center mt-4"><div class="spinner-border text-primary" role="status"></div></div>';

                fetch(
                        `https://en.wikipedia.org/w/api.php?action=query&list=search&srsearch=${encodeURIComponent(query)}&format=json&origin=*`)
                    .then(response => response.json())
                    .then(data => {
                        resultsDiv.innerHTML = '';
                        if (data.query.search.length === 0) {
                            resultsDiv.innerHTML = '<div class="text-center text-muted mt-4">No results found.</div>';
                            return;
                        }

                        data.query.search.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'wiki-result-item';
                            div.innerHTML = `
                            <div class="wiki-title" onclick="insertContent('${escapeHtml(item.title)}', '${escapeHtml(item.snippet)}')">${item.title} <i class="bi bi-plus-circle-fill small ms-1"></i></div>
                            <div class="wiki-snippet">${item.snippet}...</div>
                            <a href="https://en.wikipedia.org/?curid=${item.pageid}" target="_blank" class="small text-decoration-none">Read more <i class="bi bi-box-arrow-up-right"></i></a>
                        `;
                            resultsDiv.appendChild(div);
                        });
                    })
                    .catch(error => {
                        resultsDiv.innerHTML = '<div class="text-center text-danger mt-4">Error fetching results.</div>';
                    });
            }

            function searchGoogle() {
                const query = document.getElementById('googleSearchInput').value;
                if (!query) return;

                // Add to history
                const historyList = document.getElementById('googleHistoryList');
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center px-0';
                li.innerHTML =
                    `<span>${query}</span> <button class="btn btn-sm btn-link p-0" onclick="window.open('https://www.google.com/search?q=${encodeURIComponent(query)}', '_blank')"><i class="bi bi-arrow-right"></i></button>`;
                historyList.prepend(li);

                // Open in new tab
                window.open(`https://www.google.com/search?q=${encodeURIComponent(query)}`, '_blank');
            }

            function insertContent(title, snippet) {
                // Strip HTML from snippet
                const temp = document.createElement('div');
                temp.innerHTML = snippet;
                const text = temp.textContent || temp.innerText;

                // Get current selection range
                const range = quill.getSelection(true);
                // Insert blockquote with citation
                quill.insertText(range.index, '\n');
                quill.formatLine(range.index + 1, 1, 'blockquote', true);
                quill.insertText(range.index + 1, title, {
                    'bold': true
                });
                quill.insertText(range.index + 1 + title.length, '\n' + text + '\n\n');
                quill.setSelection(range.index + title.length + text.length + 4);
            }

            function escapeHtml(text) {
                return text
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

            function shareNote(platform) {
                const title = document.getElementById('title').value || 'My Note';
                // Get plain text content from Quill
                const content = quill.getText();
                const text = `${title}\n\n${content}`.substring(0, 2000); // Limit length

                let url = '';
                switch (platform) {
                    case 'email':
                        url = `mailto:?subject=${encodeURIComponent(title)}&body=${encodeURIComponent(text)}`;
                        break;
                    case 'whatsapp':
                        url = `https://wa.me/?text=${encodeURIComponent(text)}`;
                        break;
                    case 'twitter':
                        url = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text.substring(0, 280))}`;
                        break;
                }

                if (url) window.open(url, '_blank');
            }

            function copyToClipboard() {
                const content = quill.getText();
                navigator.clipboard.writeText(content).then(() => {
                    alert('Note content copied to clipboard!');
                }).catch(() => {
                    alert('Failed to copy to clipboard');
                });
            }
        </script>
    @endpush
@endsection
