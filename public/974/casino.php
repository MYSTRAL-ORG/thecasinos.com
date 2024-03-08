<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casino Title - TheCasinos.com</title>
    <?php include("link-rel.php") ?>

</head>
<body>
    <?php include("header.php") ?>
		<div class="background-section">
			<div class="header-container">
				<div class="poker-chip">
					<div class="inner-border"></div>
					<div class="inner-circle"></div>
					<span class="chip-letter">$</span>
				</div>
				<h1>Casino Bellagio</h1>
			</div>
			<div class="overlay">
				<div class="light light1"></div>
				<div class="light light2"></div>
				<div class="light light3"></div>
				<div class="light light4"></div>
				<div class="light light5"></div>
				<div class="light light6"></div>
				<div class="light light7"></div>
				<div class="light light8"></div>
				<div class="light light9"></div>
			</div>
		<?php include("breadcrumb.php") ?>
		</div>
	
	<div class="feuille-container">
		<section class="feuille">
			<h2>A night at Bellagio</h2>
			<img src="img/casinos/c2.jpeg" alt="Description de l'image" class="image-casino">
			<?php include("menu-casino.php") ?>
			<?php include("content-casino.php") ?>
			<?php include("sidebar.php") ?>
		</section>
	</div>
	
	<!--div id="googleMap"></div-->
	<?php include("apple-map.php") ?>
	
	<?php include("gallery.php") ?>
	
	<?php include("footer.php") ?>

	<?php include("script.php") ?>

</body>
</html>
