<!-- App favicon -->
<link rel="shortcut icon" href="@settings('website-favicon')">

@stack('css')

<!-- Preloader CSS to show a loading animation -->
<link rel="stylesheet" href="{{ asset('backend/css/preloader.min.css') }}" type="text/css" />

<!-- Core Bootstrap CSS for layout and grid system -->
<link href="{{ asset('backend/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />

<!-- Icons CSS for including icon fonts -->
<link href="{{ asset('backend/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

<!-- Main App CSS for overall styling of the application -->
<link href="{{ asset('backend/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />

<!-- Toastr CSS -->
<link rel="stylesheet" href="{{ asset('backend/libs/toastr/toastr.min.css') }}">

<!-- Custom CSS for additional styling and overrides specific to the application -->
<link href="{{ asset('backend/css/custom.css') }}" rel="stylesheet" type="text/css" />


<script src="https://unpkg.com/feather-icons"></script>
<script>
    feather.replace();
</script>

