@push('css')
    <!-- DataTables CSS: Basic styling for the DataTables plugin to match the Bootstrap 4 framework -->
    <link href="{{ asset('backend/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />

    <!-- DataTables Responsive CSS: Adds responsive design features to DataTables, making tables adapt to different screen sizes -->
    <link href="{{ asset('backend/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
@endpush

@push('js')
    <!-- DataTables JavaScript: Core DataTables functionality, providing basic features like sorting, searching, and pagination -->
    <script src="{{ asset('backend/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>

    <!-- DataTables Bootstrap JavaScript: Integrates DataTables with Bootstrap 4, ensuring consistent styling -->
    <script src="{{ asset('backend/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <!-- DataTables Buttons JavaScript: Adds functionality for exporting table data in various formats (Excel, PDF, etc.) -->
    <script src="{{ asset('backend/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>

    <!-- DataTables Responsive JavaScript: Provides responsive capabilities to DataTables, enabling tables to adapt to different screen sizes -->
    <script src="{{ asset('backend/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>

    <!-- DataTables Responsive Bootstrap JavaScript: Integrates responsive DataTables with Bootstrap 4 for consistent styling -->
    <script src="{{ asset('backend/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

    <!-- DataTables Initialization JavaScript: Custom script to initialize DataTables with specific settings for your application -->
    <script src="{{ asset('backend/js/pages/datatables.init.js') }}"></script>

    <!-- DOMPurify JavaScript: Library to sanitize HTML and prevent XSS attacks -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/2.3.4/purify.min.js"></script>

    <script>
        // Initializes a DataTable with the specified settings.
        function initializeDataTable({
            columns,
            options = {},
            ajaxOptions = {},
            id = "#dataTable"
        }) {
            return $(id).DataTable({
                processing: true,
                serverSide: true,
                paging: true,
                bDestroy: true,
                lengthMenu: [
                    [50, 100, 150, -1],
                    [50, 100, 150, "All"]
                ],
                ajax: {
                    url: ajaxOptions.url || "",
                    type: ajaxOptions.type || "GET",
                    data: d => ({
                        ...d,
                        ...(typeof ajaxOptions.data === "function" ? ajaxOptions.data() : ajaxOptions
                            .data || {})
                    })
                },
                columns,
                order: [],
                drawCallback: () => $('[data-bs-toggle="tooltip"]').tooltip(),
                ...options
            });
        }

        // Render a field with sanitized plain text
        function formatData(data, maxLength = 30) {
            const sanitizedText = $.sanitize(data); // Sanitize the input data
            const isLong = sanitizedText.length > maxLength;
            const displayText = isLong ? sanitizedText.slice(0, maxLength) + '...' : sanitizedText;
            const titleAttribute = isLong ? ` title="${sanitizedText}"` : '';

            return `<span${titleAttribute}>${displayText}</span>`;
        }

        // jQuery plugin for sanitizing input data
        (function($) {
            $.sanitize = function(input) {
                if (!input) return '';

                // Decode HTML entities
                const decodedInput = $('<div/>').html(input).text();

                // Remove script tags explicitly
                const sanitizedInput = decodedInput.replace(/<script.*?>.*?<\/script>/gi, '');

                // Optionally, allow safe tags like <p>, <b>, <i>, etc., or remove all tags
                return sanitizedInput
                    .replace(/<\/?[^>]+(>|$)/g, "") // Remove all tags
                    .replace(/&nbsp;/g, ' ') // Replace &nbsp; with space
                    .replace(/\s+/g, ' ') // Replace multiple spaces with single space
                    .trim();
            };
        })(jQuery);
    </script>
@endpush
