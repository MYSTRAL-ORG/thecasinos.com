<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="max-age=3600, must-revalidate">

    <link rel="shortcut icon" href="#" />


    <script src="/js/bootstrap.bundle.js"></script>

    <link rel="stylesheet" href="/css/bootstrap.min.css">

    <link rel="stylesheet" href="/css/casinos.css">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    @vite("resources/js/app.js")
    <title>@yield('page_title')</title>
    <meta name="description" content="@yield('page_description')">
    <meta name="keywords" content="@yield('page_keywords')">
    <title>@yield('context-js')</title>
</head>
    <body>

    @include('header')
    @yield('page-info')
    @yield('casino')
    @yield('casino-online')
    @yield('map')
    @yield('online')
    @yield('category')
    @include('footer')

    </body>
</html>

