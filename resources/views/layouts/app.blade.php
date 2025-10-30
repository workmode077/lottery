<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', config('app.name'))</title>
    <!-- Include meta tags for SEO and social media -->
    @include('layouts.meta')
    <!-- Include CSS stylesheets -->
    @include('layouts.css')
</head>

<body class="{{ Nav::isRoute('home', 'isHome') }}">
    <!-- Include header -->
    @include('layouts.header')

    <main>
        <!-- Main content area -->
        @yield('content')

        <!-- Include footer -->
        @include('layouts.footer')
    </main>

    <!-- Include JavaScript files -->
    @include('layouts.js')
</body>

</html>
