


<header> <a href="https://www.thecasinos.com/index2.php"><img src="img/logo-dark.png" alt="Logo"></a>
    <div class="search-bar">
        <input id="search-casino" type="text" placeholder="Search...">
        <ul class="list-group" id="autocompleteResults"></ul>
        <button class="search-btn">
            <img src="/img/icons/zoom.png" alt="Search Icon" />
        </button>
    </div>
    <div class="language">
        <div class="lang-selector" id="lang-selector">
            <img src="img/icons/world.png" alt="World Icon" />
            <span>EN</span>
        </div>
        <div class="lang-dropdown" id="lang-dropdown">
            <div class="lang-option">
                <img src="img/icons/world.png" alt="French Icon" />
                <span>FR</span>
            </div>
            <div class="lang-option">
                <img src="img/icons/world.png" alt="Spanish Icon" />
                <span>ES</span>
            </div>
            <div class="lang-option">
                <img src="img/icons/world.png" alt="Brazilian Icon" />
                <span>BR</span>
            </div>
        </div>
    </div>
    <?php
    $current_page = basename($_SERVER['PHP_SELF']); // Obtient le nom de la page en cours

    if ($current_page == "online.php") {
        echo '<a href="https://www.thecasinos.com/index2.php" class="btn-online">ONSITE</a>';
    } else {
        echo '<a href="online.php" class="btn-online">ONLINE</a>';
    }
    ?>

</header>
