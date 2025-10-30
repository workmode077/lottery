@push('css')
    <!-- Flatpickr CSS: Styling for the Flatpickr date picker plugin via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('js')
    <!-- Flatpickr JavaScript: Enables the Flatpickr date picker functionality via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- Initialize the Flatpickr date picker -->
    <script>
        function initializeFlatpickr(selectors, customOptions = {}) {
            const FlatpickrDefaultOptions = {
                defaultDate: new Date(),
            };
            const FlatpickrFinalOptions = {
                ...FlatpickrDefaultOptions,
                ...customOptions
            };

            // If selectors is a string, split it into an array; if it's already an array, use it directly
            const FlatpickrSelectorArray = typeof selectors === 'string' ? selectors.split(',').map(s => s.trim()) : Array
                .isArray(
                    selectors) ? selectors : [];

            // Initialize Flatpickr for each selector
            FlatpickrSelectorArray.forEach(selector => {
                const FlatpickrElement = document.querySelector(selector);
                if (!FlatpickrElement) {
                    console.warn(`Element with selector ${selector} not found.`);
                    return;
                }

                // Initialize Flatpickr date picker
                flatpickr(FlatpickrElement, FlatpickrFinalOptions);
            });
        }
    </script>
@endpush
