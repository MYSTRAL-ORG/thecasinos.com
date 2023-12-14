<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="max-age=3600, must-revalidate">

    <link rel="shortcut icon" href="#" />

    <link rel="canonical" href="{{ config('app.url') . request()->getRequestUri() }}">
    <script src="/js/bootstrap.bundle.js" defer></script>




    <script>
        window.addEventListener('load', function() {
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = '/css/bootstrap.min.css';
            document.head.appendChild(link);
            var link2 = document.createElement('link');
            link2.rel = 'stylesheet';
            link2.href = '/css/casinos.css';
            document.head.appendChild(link2);
            var link3 = document.createElement('link');
            link3.rel = 'stylesheet';
            link3.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css';
            document.head.appendChild(link3);
        });
    </script>
    @vite("resources/js/app.js")
    <title>@yield('page_title')</title>
    <meta name="description" content="@yield('page_description')">
    <meta name="keywords" content="@yield('page_keywords')">
    <title>@yield('context-js')</title>


    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-W6V8TZST');</script>
    <!-- End Google Tag Manager -->


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

