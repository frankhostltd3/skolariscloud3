@once
    @push('styles')
        <style>
            .ck.ck-editor {
                width: 100%;
            }

            .ck.ck-editor__editable_inline {
                min-height: 180px;
                padding: 1rem;
                background-color: #fff;
                border-radius: 0.375rem;
            }

            .ck.ck-content {
                font-family: 'Quicksand', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                font-size: 0.95rem;
                line-height: 1.55;
            }

            .ck.ck-toolbar {
                border-radius: 0.375rem 0.375rem 0 0;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
        <script>
            window.skolarisEditors = window.skolarisEditors || {};

            function getEditorId(element, fallbackIndex) {
                if (element.id) {
                    return element.id;
                }
                if (element.name) {
                    element.id = `${element.name}-${fallbackIndex}`;
                    return element.id;
                }
                element.id = `wysiwyg-${Date.now()}-${fallbackIndex}`;
                return element.id;
            }

            function applyEditorHeight(editor, height) {
                editor.editing.view.change(writer => {
                    writer.setStyle('min-height', `${height}px`, editor.editing.view.document.getRoot());
                });
            }

            window.initWysiwygEditors = function(selector = '.wysiwyg-editor') {
                const fields = document.querySelectorAll(selector);
                fields.forEach((field, index) => {
                    if (field.dataset.editorInitialized === 'true') {
                        return;
                    }

                    field.dataset.editorInitialized = 'true';
                    const editorId = getEditorId(field, index);
                    const placeholder = field.dataset.placeholder || field.getAttribute('placeholder') ||
                        'Start typing...';
                    const height = parseInt(field.dataset.editorHeight || (field.classList.contains(
                        'wysiwyg-large') ? 320 : 200), 10);

                    ClassicEditor.create(field, {
                            placeholder,
                            toolbar: [
                                'heading',
                                '|',
                                'bold',
                                'italic',
                                'underline',
                                'strikethrough',
                                'link',
                                'bulletedList',
                                'numberedList',
                                'blockQuote',
                                'insertTable',
                                '|',
                                'undo',
                                'redo'
                            ],
                            heading: {
                                options: [{
                                        model: 'paragraph',
                                        title: 'Paragraph',
                                        class: 'ck-heading_paragraph'
                                    },
                                    {
                                        model: 'heading2',
                                        view: 'h2',
                                        title: 'Heading 2',
                                        class: 'ck-heading_heading2'
                                    },
                                    {
                                        model: 'heading3',
                                        view: 'h3',
                                        title: 'Heading 3',
                                        class: 'ck-heading_heading3'
                                    }
                                ]
                            },
                            removePlugins: ['ImageUpload', 'MediaEmbed', 'CKBox', 'CKFinder', 'EasyImage']
                        })
                        .then(editor => {
                            window.skolarisEditors[editorId] = editor;
                            applyEditorHeight(editor, height);
                            editor.model.document.on('change:data', () => editor.updateSourceElement());
                        })
                        .catch(error => console.error('CKEditor initialization error:', error));
                });
            };

            window.getWysiwygContent = function(elementId) {
                if (window.skolarisEditors[elementId]) {
                    return window.skolarisEditors[elementId].getData();
                }
                const el = document.getElementById(elementId);
                return el ? el.value : '';
            };

            window.setWysiwygContent = function(elementId, value) {
                if (window.skolarisEditors[elementId]) {
                    window.skolarisEditors[elementId].setData(value || '');
                    return;
                }
                const el = document.getElementById(elementId);
                if (el) {
                    el.value = value || '';
                }
            };

            document.addEventListener('DOMContentLoaded', () => window.initWysiwygEditors());
        </script>
    @endpush
@endonce
