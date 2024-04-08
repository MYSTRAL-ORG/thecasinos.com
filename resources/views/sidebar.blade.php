<aside class="casino-sidebar">

    @if($casino->always_open)
        <div class="casino-block-green">
            <h3>Open 24/7</h3>
        </div>
    @endif

    <div class="casino-block-orange">@if($casino->cat_tablegames)
            <h3><i class="fa-solid fa-dice"></i>Table Games</h3>
        @endif
        @if($casino->cat_poker)
            <h3><i class="fa-solid fa-coins"></i>Poker Tables</h3>
        @endif
        @if($casino->cat_slotmachines)
            <h3><i class="fa-solid fa-dollar-sign"></i>Gaming Machine</h3>
        @endif
        @if($casino->cat_sportsbook)
            <h3><i class="fa-solid fa-basketball"></i>Sports Book</h3>
        @endif
        @if($casino->cat_horseracing)
            <h3><i class="fa-solid fa-horse"></i>Horse Racing</h3>
        @endif
        @if($casino->cat_simulcasting)
            <h3><i class="fa-solid fa-screencast"></i>Simulcast</h3>
        @endif
        @if($casino->cat_offtrack)
            <h3><i class="fa-solid fa-shuffle"></i>Off Track</h3>
        @endif
        @if($casino->cat_greyhounds)
            <h3><i class="fa-solid fa-dog"></i>Greyhounds</h3>
        @endif
        @if($casino->cat_bingo)
            <h3><i class="fa-solid fa-table-list"></i>Bingo</h3>
        @endif
        @if($casino->cat_slotmachines)
            <h3><i class="fa-solid fa-circle-dollar-to-slot"></i>Slot Machines</h3>
        @endif</div>


    <div class="casino-block-purple">
        @if($casino->self_parking)
            <h3><i class="fa-solid fa-square-parking"></i>Self parking</h3>
        @endif
        @if($casino->valet)
            <h3><i class="fa-solid fa-car"></i>Valet</h3>
        @endif
        @if($casino->restaurants)
            <h3><i class=" fa-solid fa-utensils"></i>Restaurants</h3>
        @endif

        @if($casino->hotels)
            <h3><i class="fa-solid fa-hotel"></i>Hotels</h3>
        @endif
        @if($casino->shops)
            <h3><i class="fa-solid fa-shop"></i>Shops</h3>
        @endif
        @if($casino->spas)
            <h3><i class="fa-solid fa-spa"></i>Spas</h3>
        @endif</div>
    <div class="casino-block" id="sumup">
        <h3>To sum up</h3>

        <!--  <img loading="lazy"
             src="{{ env('APP_URL') . '/img/casino/mobile/' . $casino->img_url }}"

             srcset="{{ env('APP_URL') . '/img/casino/mobile/' . $casino->img_url }} 480w,
             {{ env('APP_URL') . '/img/casino/tablet/' . $casino->img_url }} 768w,
             {{ env('APP_URL') . '/img/casino/desktop/' . $casino->img_url }} 1024w"
             sizes="(max-width: 480px) 480px, (max-width: 768px) 768px, 1024px" alt="Casino presentation" class=" image-casino ">
-->
        <picture>
            <source media="(min-width: 768px)"
                    srcset="{{ env('APP_URL') . '/img/casino/desktop/' . $casino->img_url }}">
            <source media="(max-width: 767px)"
                    srcset="{{ env('APP_URL') . '/img/casino/mobile/' . $casino->img_url }}" width="300"
                    height="200">
            <img loading="lazy" class="rounded-3 img-fluid image-casino center-image"
                 src="{{ env('APP_URL') . '/img/casino/desktop/' . $casino->img_url }}"
                 alt="{{ $casino->name }}">
        </picture>

        <p class="pt-3">{!!  $casinoDetail->sumup!!}</p>
    </div>

    <div class="casino-block" id="games">
        <h3>Games</h3>
        <img loading="lazy" src="/img/sidebar-games.webp" alt="Casino Games" class="image-casino">
        <p class="pt-3">{!!  $casinoDetail->games!!}</p>
    </div>

    <div class="casino-block" id="funfacts">
        <h3>Fun Facts</h3>
        <img loading="lazy" src="/img/sidebar-facts.webp" alt="Casino Fun facts" class=" image-casino">
        <p class="pt-3">{!!  $casinoDetail->fun_facts!!}</p>
    </div>

    <div class="casino-block" id="contact">
        <h3>Contact</h3>
        <img loading="lazy" src="/img/sidebar-contact.webp" alt="Casino contact" class=" image-casino">
        <br> <br>
        <p>
            @if($casino->telephone)
                <i class="fa-solid fa-phone"></i> &nbsp; <a
                    href="tel:{{  $casino->telephone   }}">{{  $casino->telephone   }}</a>
                <br>
            @endif
            @if($casino->email)
                <i class="fa-solid fa-envelope"></i> &nbsp; <a
                    href="mailto:{{  $casino->email   }}">{{  $casino->email   }}</a>
                <br>
            @endif
            @if($casino->adresse)
                <i class="fa-solid fa-map-marker"></i> &nbsp;{{  $casino->adresse   }}
                <br>
            @endif
            @if($casino->website)
                <i class="fa-solid fa-globe"></i> &nbsp;<a href="{{  $casino->website   }}" target="_blank">Casino
                    Website</a>
                <br>
            @endif
        </p>
    </div>
</aside>

