<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500: Server Error | @settings('website-name')</title>
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

    <div class="my-5 pt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center mb-5">
                        <h1 class="display-1 fw-semibold">5<span class="text-primary mx-2">0</span>0</h1>
                        <h4 class="text-uppercase">Internal Server Error</h4>
                        <div class="mt-5 text-center">
                            <a class="btn btn-primary waves-effect waves-light"
                                href="{{ route('dashboard.index') }}">Back to Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-10 col-xl-8">
                    <div>
                        <img src="{{ asset('backend/images/error-img.png') }}" alt="500: Server Error"
                            class="img-fluid">
                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end content -->

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
