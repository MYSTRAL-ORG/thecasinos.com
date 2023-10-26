<aside class="casino-sidebar">

    @if($casino->always_open)
        <div class="casino-block-green">
            <h3>Open 24/7</h3>
        </div>
    @endif

	<div class="casino-block-orange">

		<h3>Table Games</h3>
		<h3>Gaming Machine</h3>
		<h3>Poker Tables</h3>
	</div>
	<div class="casino-block-purple">
        @if($casino->self_parking)
		    <h3>Self parking</h3>
        @endif
        @if($casino->valet)
		    <h3>Valet</h3>
        @endif
        @if($casino->restaurants)
                <h3><i class="fa-solid fa-utensils"></i>Restaurants</h3>
        @endif

        @if($casino->hotels)
            <h3>Hotels</h3>
        @endif
        @if($casino->shops)
            <h3>Shops</h3>
        @endif
        @if($casino->spas)
            <h3>Spas</h3>
        @endif

	</div>
	<div class="casino-block" id="sumup">
		<h3>To sum up</h3>
		<img src="/img/casino/{{$casino->img_url}}" alt="Casino presentation" class="image-casino">
		<p>{!!  $casinoDetail->sumup!!}</p>
	</div>

	<div class="casino-block" id="games">
		<h3>Games</h3>
		<img src="/img/sidebar-games.jpg" alt="Casino Games" class="image-casino">
		<p>{!!  $casinoDetail->games!!}</p>
	</div>

	<div class="casino-block" id="funfacts">
		<h3>Fun Facts</h3>
		<img src="/img/sidebar-facts.jpg" alt="Casino Fun facts" class="image-casino">
		<p>{!!  $casinoDetail->fun_facts!!}</p>
	</div>

	<div class="casino-block" id="contact">
		<h3>Contact</h3>
		<img src="/img/sidebar-contact.jpg" alt="Casino contact" class="image-casino">
		<p>{{  $casino->telephone   }} <br> {{  $casino->adresse   }} <br> {{  $casino->email   }} <br> {{  $casino->website   }}</p>
	</div>
</aside>

