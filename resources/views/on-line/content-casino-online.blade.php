<div class="content-casino">
	{{$casinoOnLine->description}}

	<h3>Want to try this casino ?</h3>
	<p>Register on the link below to get the bonus </p>
	<div class="casino-button-container">
		<button class="casino-register-button">200% up to $600</button>
	</div>

	<h3>Want to play others casinos ?</h3>
	<p>Find your perfect match below</p>
	@php
		$lines = 3; // Nombre de lignes à afficher par défaut
		$columns = ['Logo', 'Bonus', 'Review', 'Casino']; // Les colonnes à afficher

	@endphp
    @include("top10");
</div>
