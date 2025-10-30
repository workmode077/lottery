<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @hasSection('title')
            @yield('title') | @settings('website-name')
        @else
            @settings('website-name')
        @endif
    </title>
    <!-- Include meta tags for SEO and other settings -->
    @include('admin::layouts.meta')
    <!-- Include CSS files -->
    @include('admin::layouts.css')

</head>

<body>

    <script>
        (() => {
            ["data-bs-theme", "data-topbar", "data-sidebar"].forEach(attr =>
                document.body.setAttribute(attr, localStorage.getItem("theme") || "light")
            );
        })();
    </script>

    <div id="layout-wrapper">
        <!-- Include the header section -->
        @include('admin::layouts.header')
        <!-- Include the left sidebar navigation -->
        @include('admin::layouts.left-sidebar')
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <!-- Page title and breadcrumb navigation -->
                    @include('admin::layouts.page-title-and-breadcrumb')
                    <!-- Yield the main content of the page -->
                    @yield('content')
                </div>
            </div>
            <!-- Include the footer section -->
            @include('admin::layouts.footer')
        </div>
    </div>
    <!-- Include the right sidebar -->
    @include('admin::layouts.right-sidebar')
    <!-- Overlay for the right sidebar -->
    <div class="rightbar-overlay"></div>

    <!-- Include JS files -->
    @include('admin::layouts.js')

</body>

</html>
