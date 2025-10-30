<!-- Core jQuery library -->
<script src="{{ asset('backend/libs/jquery/jquery.min.js') }}"></script>

<!-- Bootstrap bundle for JavaScript components like modals, tooltips, etc. -->
<script src="{{ asset('backend/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- MetisMenu for creating a collapsible menu in the sidebar -->
<script src="{{ asset('backend/libs/metismenu/metisMenu.min.js') }}"></script>

<!-- SimpleBar for custom scrollbars -->
<script src="{{ asset('backend/libs/simplebar/simplebar.min.js') }}"></script>

<!-- Waves effect for button clicks and other elements -->
<script src="{{ asset('backend/libs/node-waves/waves.min.js') }}"></script>

<!-- Feather Icons for lightweight SVG icons -->
<script src="{{ asset('backend/libs/feather-icons/feather.min.js') }}"></script>

<!-- Pace.js for automatic page load progress bar -->
<script src="{{ asset('backend/libs/pace-js/pace.min.js') }}"></script>

<!-- Toastr JS -->
<script src="{{ asset('backend/libs/toastr/toastr.min.js') }}"></script>

@stack('js')

<!-- Custom JavaScript for additional application-specific functionality and enhancements -->
<script src="{{ asset('backend/js/custom.js') }}"></script>
