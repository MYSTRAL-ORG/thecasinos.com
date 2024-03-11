

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="max-age=3600, must-revalidate">

    <link rel="shortcut icon" href="#" />

    <link rel="canonical" href="{{ config('app.url') . parse_url(request()->getRequestUri(), PHP_URL_PATH) }}">


    <script  src="/js/bootstrap.bundle.js" defer async></script>

    <link   rel="preload" href="/css/bootstrap.min.css" as="style" media="all" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="/css/bootstrap.min.css"></noscript>


    <link   rel="stylesheet" href="/css/casinos.css" media="all">


    <link   rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" as="style" media="all" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"></noscript>



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

