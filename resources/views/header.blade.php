<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a  class="navbar-brand col-3 ms-2 lg" href="https://www.thecasinos.com/index2.php" alt="TheCasinos.com">
            <img id="logo" src="/img/logo-dark.png" alt="Logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse  me-auto mb-2 mb-lg-0 container-fluid  " id="navbarSupportedContent">
            <div class="container-fluid   navbar-nav me-auto mb-2 mb-lg-0 d-flex justify-content-between" >

                <div class="search-bar " >
                    <input id="search-casino" class=" search-bar "  placeholder="Search..."/>
                    <ul id="search-casino-list" class="search-casino-list d-none"></ul>
                    <button class="btn search-btn">
                        <img src="/img/icons/zoom.png" alt="Search Icon" />
                    </button>
                </div>

                <div class="language">
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
                </div>

                <div class="pe-4" role="search" >
                    @if (Route::currentRouteNamed('online'))
                        <a href="/index2.php"  class="btn-online form-control">ONLINE</a>
                    @else
                        <a href="/online.php" class="btn-online form-control">ONLINE</a>
                    @endif
                </div>
            </div>

        </div>
    </div>
</nav>












