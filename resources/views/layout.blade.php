<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="google-site-verification" content="cEs10KedajHFcdCI2ltJ2Wo5fqzy5kSrknaMhxYJ0VQ"/>


    <link rel="canonical" href="{{ config('app.url') . parse_url(request()->getRequestUri(), PHP_URL_PATH) }}">


    @yield('meta-tags')


    @vite("resources/js/app.js")
    <title>@yield('page_title')</title>
    <meta name="description" content="@yield('page_description')">
    <meta name="keywords" content="@yield('page_keywords')">
    @yield('context-js')

    <!-- 2024 Google tag (gtag.js) -->
    <link rel="preconnect" href="https://www.googletagmanager.com">

    <script src="https://www.googletagmanager.com/gtag/js?id=G-BZTJV1TP3F" defer></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());

        gtag('config', 'G-BZTJV1TP3F');
    </script>

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

