@push('css')
    <!-- Link to Choices.js documentation for reference: https://choices-js.github.io/Choices/ -->
    <!-- Include Choices.js CSS for enhanced select boxes -->
    <link href="{{ asset('backend/libs/choices.js/public/assets/styles/choices.min.css') }}" rel="stylesheet"
        type="text/css" />
@endpush

@push('js')
    <!-- Include Choices.js JavaScript for enhanced select box functionality -->
    <script src="{{ asset('backend/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>

    <!-- Initialize the Choices.js -->
    <script>
        function initializeChoices(selectors, customOptions = {}) {
            const ChoicesDefaultOptions = {
                removeItemButton: true,
                placeholder: true,
                placeholderValue: null,
                allowHTML: true
            };
            const ChoicesfinalOptions = {
                ...ChoicesDefaultOptions,
                ...customOptions
            };

            // If selectors is a string, split it into an array; if it's already an array, use it directly
            const ChoicesSelectorArray = typeof selectors === 'string' ? selectors.split(',').map(s => s.trim()) : Array
                .isArray(
                    selectors) ? selectors : [];

            // Initialize Choices for each selector
            ChoicesSelectorArray.forEach(selector => {
                const ChoicesElement = document.querySelector(selector);
                if (!ChoicesElement) {
                    console.warn(`Element with selector ${selector} not found.`);
                    return;
                }

                // Initialize Choices
                new Choices(ChoicesElement, ChoicesfinalOptions);
            });
        }
    </script>
@endpush
