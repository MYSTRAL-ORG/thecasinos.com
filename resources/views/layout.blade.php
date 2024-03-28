<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="google-site-verification" content="cEs10KedajHFcdCI2ltJ2Wo5fqzy5kSrknaMhxYJ0VQ" />


    <link rel="canonical" href="{{ config('app.url') . parse_url(request()->getRequestUri(), PHP_URL_PATH) }}">


    <script  src="{{asset('/js/bootstrap.bundle.js')}}" defer ></script>


    <link rel="preload" href="{{asset('/css/bootstrap.min.css')}}" as="style" onload="this.onload=null;this.rel='stylesheet'" media="all">

    <noscript><link rel="stylesheet" href="/css/bootstrap.min.css"></noscript>


    <link   rel="stylesheet" href="/css/casinos.css" media="all">


    <link   rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" as="style" media="all" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"></noscript>



    @vite("resources/js/app.js")
    <title>@yield('page_title')</title>
    <meta name="description" content="@yield('page_description')">
    <meta name="keywords" content="@yield('page_keywords')">
    @yield('context-js')


    <!-- Google Tag Manager -->
    <script  >(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-W6V8TZST');</script>
    <!-- End Google Tag Manager -->

    <!-- Google tag (gtag.js) -->
    <script   src="https://www.googletagmanager.com/gtag/js?id=G-BZTJV1TP3F"></script>
    <script  >
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

