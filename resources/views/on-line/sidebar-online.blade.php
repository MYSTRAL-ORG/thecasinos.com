<div class="col-lg-4 col-sm-12 pt-2">
    <aside class="casino-sidebar ">
        <!--div class="casino-block-green-online">
            <h3>200% UP TO $600</h3>
        </div>
        <div class="casino-button-container">
            <button class="casino-register-button">Register & Play</button>
        </div>
        <div class="casino-block-orange-online">
            <h3>Table Games</h3>
            <h3>Slot Machine</h3>
            <h3>Poker</h3>
            <h3>Sport betting</h3>
        </div>
        <div class="casino-block-purple-online">
            <h3>Mastercard</h3>
            <h3>Amercian Express</h3>
            <h3>Discover</h3>
            <h3>UPI</h3>
            <h3>JCB</h3>
        </div-->
        <div class="casino-block-pros">
            <div class="casino-block-header-pros">Pros</div>
            @foreach (explode('|', $casinoOnLine->point_pour) as $pour)
                <h3>{{$pour}}</h3>
            @endforeach
        </div>
        <div class="casino-block-cons">
            <div class="casino-block-header-cons">Cons</div>
            @foreach (explode('|', $casinoOnLine->point_contre) as $contre)
                <h3>{{$contre}}</h3>
            @endforeach

        </div>

        <div class="casino-block" id="sumup">
            <h3>To sum up</h3>
            <img src="{{env('APP_URL').$casinoOnLine->logo}}" alt="Casino presentation" class="image-casino">
            <p class="pt-2">{!! $casinoOnLine->sumup_description!!}</p>
        </div>

        <div class="casino-block" id="bonus">
            <h3>Bonus</h3>
            <img src="/img/sidebar-games.jpg" alt="Casino Games" class="image-casino">
            <p class="pt-2"> {!! $casinoOnLine->bonus_description!!}</p>
            <div class="casino-button-container-inside">
                <button class="casino-register-button-inside">{{$casinoOnLine->bonus}}</button>
            </div>
        </div>
        <div class="casino-block" id="deposit">
            <h3>Deposit method</h3>
            <img src="/img/sidebar-facts.jpg" alt="Casino Fun facts" class="image-casino">
            <p class="pt-2">{!!$casinoOnLine->deposit_mehods_description!!}</p>
            <div class="casino-data-container">
                @foreach (explode('|', $casinoOnLine->deposit_mehods) as $dm)
                    <div class="casino-data-box">{{$dm}}</div>
                @endforeach
            </div>
        </div>

        <div class="casino-block" id="contact">
            <h3>Contact</h3>
            <img src="/img/sidebar-contact.jpg" alt="Casino contact" class="image-casino">
            <p class="pt-2">{!!$casinoOnLine->contact_information_description !!}</p>
            <div class="casino-data-container">
                @foreach (explode('|', $casinoOnLine->contact_information) as $contact)
                    <div class="casino-data-box">{{$contact}}</div>
                @endforeach
            </div>
        </div>
    </aside>
</div>
