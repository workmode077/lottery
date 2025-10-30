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
    @include('admin::auth.layouts.meta')
    <!-- Include CSS files -->
    @include('admin::auth.layouts.css')

</head>

<body>

    <div class="auth-page">
        <div class="container-fluid p-0">
            <div class="row g-0">
                <div class="col-xxl-3 col-lg-4 col-md-5">
                    <div class="auth-full-page-content d-flex p-sm-5 p-4">
                        <div class="w-100">
                            <div class="d-flex flex-column h-100">
                                <div class="mb-4 mb-md-5 text-center">
                                    <a href="{{ route('admin.show') }}" class="d-block auth-logo">
                                        <img src="@settings('website-logo')" alt="@settings('website-name')"
                                            height="28"> <span class="logo-txt">@settings('website-name')</span>
                                    </a>
                                </div>
                                <div class="auth-content my-auto">
                                    <!-- Yield the main content of the page -->
                                    @yield('content')
                                </div>
                                <div class="mt-4 mt-md-5 text-center">
                                    <p class="mb-0">
                                        <script>
                                            document.write(new Date().getFullYear())
                                        </script> © @settings('website-name').
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-9 col-lg-8 col-md-7">
                    <div class="auth-bg pt-md-5 p-4 d-flex"
                        style="background-image: url(@settings('website-auth-background'));">
                        <div class="bg-overlay bg-primary"></div>
                        <ul class="bg-bubbles">
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include JS files -->
    @include('admin::auth.layouts.js')

</body>

</html>
