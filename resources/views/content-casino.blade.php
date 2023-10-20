<div class="content-casino">


    {!!  $casinoDetail->description!!}


    <h3>Most similar casinos online :</h3>
	<?php
		$lines = 2; // Nombre de lignes à afficher par défaut
		$columns = ['Brand', 'Bonus','Casino']; // Les colonnes à afficher
		@include("top10");
	?>

	<?php
		$lines = 3; // Nombre de lignes à afficher par défaut
		$columns = ['Logo', 'Bonus', 'Review', 'Casino']; // Les colonnes à afficher
		@include("top10");
	?>
</div>
