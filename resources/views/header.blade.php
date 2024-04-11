<nav class="navbar navbar-expand-lg bg-body-tertiary header ">
    <div class="container-fluid   d-flex justify-content-between">
        <a class="navbar-brand  " href="/" alt="TheCasinos.com">
            <img loading="lazy" id="logo" src="/img/logo-dark.webp" height="50px" width="148px" alt="The Casinos">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target=".nav_item"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        @if(Route::currentRouteNamed('index') ||  Route::currentRouteNamed('casino')  || Route::currentRouteNamed('category'))
            <div class=" search-bar collapse navbar-collapse  nav_item ">
                <input id="search-casino" class="align-middle  search-bar  " placeholder="Search..."/>
                <ul id="search-casino-list" class="search-casino-list d-none"></ul>
                <button class="btn search-btn">
                    <img loading="lazy" src="/img/icons/zoom.png" width="20px" height="20px" alt="Search"/>
                </button>
            </div>
        @endif
        <!-- <div class="language collapse navbar-collapse  nav_item ">
            <div class="dropdown">
                <button class="btn form-control  dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="/img/icons/world.png" alt="World Icon"> EN
                </button>
                <ul class="dropdown-menu">
                    <a class="dropdown-item" href="#"><img src="/img/icons/world.png" alt="French Icon"> FR</a>
                    <a class="dropdown-item" href="#"><img src="/img/icons/world.png" alt="Spanish Icon"> ES</a>
                    <a class="dropdown-item" href="#"><img src="/img/icons/world.png" alt="Brazilian Icon"> BR</a>
                </ul>
            </div>
        </div> -->

        <div class=" collapse navbar-collapse nav_item  " role="search">
            @if (Route::currentRouteNamed('online'))
                <a href="{{ route('index')}}" class="btn-online form-control">ONLINE</a>
            @else
                <a href="{{ route('online')}}" class="btn-online form-control">ONLINE</a>
            @endif
        </div>

    </div>
</nav>












