@push('js')
    <!-- TinyMCE library -->
    <script src="{{ asset('backend/libs/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
    <script>
        function initTinyMCE() {
            const isDarkMode = document.body.getAttribute("data-bs-theme") === "dark";

            tinymce.remove(); // Remove all instances

            document.querySelectorAll('textarea.tinymce').forEach((el) => {
               const isBulletOnly = el.dataset.bulletsOnly === 'true';

                tinymce.init({
                    target: el,
                    license_key: 'gpl',
                    branding: false,
                    promotion: false,
                    height: el.dataset.height || 400,
                    relative_urls: false,
                    remove_script_host: false,
                    skin: isDarkMode ? 'oxide-dark' : 'oxide',
                    content_css: isDarkMode ? 'dark' : 'default',
                    content_style: "body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; }",

                    menubar: isBulletOnly ? false : 'file edit insert format tools table',
                    plugins: isBulletOnly ? 'lists' : 'advlist autolink link lists table code image',
                    toolbar: isBulletOnly ? 'bullist' :
                        'undo redo | styleselect | blocks | formatselect | fontselect fontsizeselect | bold italic underline strikethrough | alignleft aligncenter alignright | forecolor backcolor | bullist numlist | link image table code',

                    font_formats: 'Arial=arial,helvetica,sans-serif; Times New Roman=times new roman,times,serif; Courier New=courier new,courier,monospace; Verdana=verdana,geneva,sans-serif; Georgia=georgia,palatino,serif; Tahoma=tahoma,arial,helvetica,sans-serif',
                    fontsize_formats: '10pt 12pt 14pt 16pt 18pt 24pt 36pt',

                    menu: isBulletOnly ? {} : {
                        file: {
                            title: 'File',
                            items: 'newdocument'
                        },
                        edit: {
                            title: 'Edit',
                            items: 'undo redo | cut copy paste | selectall'
                        },
                        insert: {
                            title: 'Insert',
                            items: 'link image table hr'
                        },
                        format: {
                            title: 'Format',
                            items: 'bold italic underline strikethrough | forecolor backcolor | superscript subscript | blockquote | alignleft aligncenter alignright alignjustify | removeformat'
                        },
                        tools: {
                            title: 'Tools',
                            items: 'code'
                        },
                        table: {
                            title: 'Table',
                            items: 'inserttable deletetable | tableprops | cell cellprops mergecells splitcells | row rowprops insertrowbefore insertrowafter deleterow | column columnprops insertcolumnbefore insertcolumnafter deletecolumn'
                        }
                    },

                    setup: function(editor) {
                        editor.on('input change', function() {
                            let errorMessage = $(editor.getElement()).closest('.form-group')
                                .find('.error-block');
                            if (errorMessage.text().trim()) {
                                errorMessage.text('');
                            }
                        });
                    }
                });
            });
        }

        document.addEventListener('DOMContentLoaded', initTinyMCE);
    </script>
@endpush
