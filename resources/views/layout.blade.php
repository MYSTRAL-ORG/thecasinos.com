

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="max-age=3600, must-revalidate">

    <link rel="shortcut icon" href="#" />

    <link rel="canonical" href="{{ config('app.url') . parse_url(request()->getRequestUri(), PHP_URL_PATH) }}">
    <script src="/js/bootstrap.bundle.js" defer async></script>



        <link rel="stylesheet" href="/css/bootstrap.min.css" media="all">
        <link rel="stylesheet" href="/css/casinos.css" media="all">
        <link    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" >



    @vite("resources/js/app.js")
    <title>@yield('page_title')</title>
    <meta name="description" content="@yield('page_description')">
    <meta name="keywords" content="@yield('page_keywords')">
    <title>@yield('context-js')</title>


    <!-- Google Tag Manager -->
    <script async defer>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-W6V8TZST');</script>
    <!-- End Google Tag Manager -->

    <!-- Google tag (gtag.js) -->
    <script async defer src="https://www.googletagmanager.com/gtag/js?id=G-BZTJV1TP3F"></script>
    <script async defer>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
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

