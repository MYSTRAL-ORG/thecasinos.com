<div class="col-lg-8 col-sm-12 ">
    <div class="content-casino">
        {!! $casinoOnLine->description!!}

        <h3 class="pt-3">Want to try this casino ?</h3>
        <p>Register on the link below to get the bonus </p>
        <div class="casino-button-container">

            <a href="{{$casinoOnLine->register_link}}" target="_blank" class="button review-right-button">Register & Play</a>
        </div>

        <h3>Want to play others casinos ?</h3>
        <p>Find your perfect match below</p>
        @php
            $lines = 3; // Nombre de lignes à afficher par défaut
            $columns = ['Logo', 'Bonus', 'Review', 'Casino']; // Les colonnes à afficher
        @endphp
        @include("top10")
    </div>
</div>
