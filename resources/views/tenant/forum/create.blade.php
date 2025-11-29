@extends('tenant.layouts.app')

@section('title', 'Start Discussion')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Start a New Discussion</h5>
                        <a href="{{ route('tenant.forum.index') }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('tenant.forum.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" required
                                    placeholder="What's on your mind?">
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="context_type" class="form-label">Context</label>
                                    <select class="form-select" id="context_type" name="context_type">
                                        <option value="general">General Discussion</option>
                                        <option value="class">Specific Class</option>
                                        <option value="subject">Specific Subject</option>
                                    </select>
                                </div>
                                <div class="col-md-6" id="context_id_wrapper" style="display: none;">
                                    <label for="context_id" class="form-label">Select Specific</label>
                                    <select class="form-select" id="context_id" name="context_id">
                                        <!-- Populated via JS -->
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="content" class="form-label mb-0">Content</label>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="aiAssistBtn">
                                        <i class="bi bi-stars"></i> AI Assist
                                    </button>
                                </div>
                                <textarea class="form-control" id="content" name="content" rows="6" required></textarea>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Post Discussion</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Assist Modal -->
    <div class="modal fade" id="aiAssistModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-stars text-primary"></i> AI Assistant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="aiPrompt" class="form-label">What should I write about?</label>
                        <textarea class="form-control" id="aiPrompt" rows="3"
                            placeholder="E.g., Write a question about the laws of motion..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Model</label>
                        <select class="form-select" id="aiModel">
                            <option value="gemini-pro-3">Gemini 3 Pro (Preview)</option>
                            <option value="gpt-4">GPT-4</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="generateAiContent">Generate</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Context switching logic
                const contextType = document.getElementById('context_type');
                const contextIdWrapper = document.getElementById('context_id_wrapper');
                const contextId = document.getElementById('context_id');

                const classes = @json($classes);
                const subjects = @json($subjects);

                contextType.addEventListener('change', function() {
                    contextId.innerHTML = '';
                    if (this.value === 'general') {
                        contextIdWrapper.style.display = 'none';
                    } else {
                        contextIdWrapper.style.display = 'block';
                        let options = this.value === 'class' ? classes : subjects;
                        options.forEach(opt => {
                            let option = document.createElement('option');
                            option.value = opt.id;
                            option.text = opt.name;
                            contextId.appendChild(option);
                        });
                    }
                });

                // AI Assist Logic
                const aiAssistBtn = document.getElementById('aiAssistBtn');
                const aiModal = new bootstrap.Modal(document.getElementById('aiAssistModal'));
                const generateBtn = document.getElementById('generateAiContent');
                const contentArea = document.getElementById('content');

                aiAssistBtn.addEventListener('click', () => aiModal.show());

                generateBtn.addEventListener('click', function() {
                    const prompt = document.getElementById('aiPrompt').value;
                    const model = document.getElementById('aiModel').value;

                    if (!prompt) return;

                    this.disabled = true;
                    this.innerHTML =
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generating...';

                    fetch('{{ route('tenant.forum.ai-assist') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                prompt,
                                model
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            contentArea.value = data.response;
                            aiModal.hide();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to generate content');
                        })
                        .finally(() => {
                            this.disabled = false;
                            this.innerHTML = 'Generate';
                        });
                });
            });
        </script>
    @endpush
@endsection
