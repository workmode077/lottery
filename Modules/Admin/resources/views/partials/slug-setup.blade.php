@push('js')
    <script>
        $(document).ready(function() {
            const $slugInput = $('.slug-output');
            const debounceDelay = 300;
            let debounceTimer;

            $(document).on('input', '.source-input', function() {
                updateSlug($(this).val(), 'source');
            });

            $(document).on('blur', '.slug-output', function() {
                updateSlug($(this).val(), 'slug');
            });

            function updateSlug(value, field) {
                const slug = convertToSlug(value);
                checkSlugUniqueness(slug, field);
            }

            const convertToSlug = (text) => {
                if (!text) return '';

                return text
                    .toLowerCase()
                    .normalize('NFKD') // Normalize Unicode (e.g., "café" → "cafe")
                    .replace(/[\u0300-\u036f]/g, '') // Remove diacritics
                    .replace(/@/g, 'at') // Replace "@" with "at"
                    .replace(/[^a-zA-Z0-9\s-]/g, '') // Remove all other special characters
                    .trim()
                    .replace(/\s+/g, '-') // Replace spaces with hyphens
                    .replace(/-+/g, '-'); // Remove duplicate hyphens
            };

            function checkSlugUniqueness(slug, field) {
                clearTimeout(debounceTimer);
                $(':submit:visible').prop('disabled', true);

                debounceTimer = setTimeout(async () => {
                    try {
                        const {
                            id,
                            model,
                            column = 'slug'
                        } = $slugInput.data();
                        const response = await $.get(`/${prefix}/admin-settings/check-slug`, {
                            id,
                            model,
                            column,
                            slug
                        });

                        if (response.exists) {
                            field === 'source' ? $slugInput.val(response.uniqueSlug) : showError(
                                'Slug already exists.');
                        } else {
                            $slugInput.val(slug);
                            showError('');
                        }
                    } catch {
                        showError('Error checking slug uniqueness.');
                    } finally {
                        $(':submit:visible').prop('disabled', false);
                    }
                }, debounceDelay);
            }

            const showError = message => $('.slug-output').siblings('.error-block').text(message);
        });
    </script>
@endpush
