<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance | @settings('website-name')</title>
    <!-- App favicon -->
    <link rel="shortcut icon" href="@settings('website-favicon')">
    <!-- Preloader CSS to show a loading animation -->
    <link rel="stylesheet" href="{{ asset('backend/css/preloader.min.css') }}" type="text/css" />
    <!-- Core Bootstrap CSS for layout and grid system -->
    <link href="{{ asset('backend/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons CSS for including icon fonts -->
    <link href="{{ asset('backend/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Main App CSS for overall styling of the application -->
    <link href="{{ asset('backend/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />

</head>

<body>

    <div class="bg-light-subtle min-vh-100 d-flex align-items-center justify-content-center">
        <div class="w-100">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <div class="mb-5">
                                <a href="{{ route('dashboard.index') }}">
                                    <img src="@settings('website-logo')" alt="@settings('website-name')" height="30"
                                        class="me-1">
                                    <span class="logo-txt text-dark font-size-22">@settings('website-name')</span>
                                </a>
                            </div>
                            <div class="maintenance-cog-icon text-primary pt-4">
                                <i class="mdi mdi-cog spin-right display-3"></i>
                                <i class="mdi mdi-cog spin-left display-4 cog-icon"></i>
                            </div>
                            <h3 class="mt-4">Website Under Maintenance</h3>
                            <p>The site is temporarily down for maintenance. Please check back later.</p>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
    </div>

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

</body>

</html>
