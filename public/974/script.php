<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
	$(document).ready(function() {
		$(".see-more").click(function() {
			var block = $(this).closest(".block");

			// Si le bloc actuel est déjà étendu, fermez-le
			if (block.hasClass("expanded")) {
				block.removeClass("expanded");
				$(this).text("See more");
				// Montrez tous les blocs, y compris celui qui était caché précédemment
				$(".block").show();
				return; // Terminez l'exécution du gestionnaire d'événements
			}

			// Fermer tous les blocs étendus
			$(".block.expanded").removeClass("expanded").find(".see-more").text("See more");

			// Montrez tous les blocs (pour vous assurer que le bloc précédemment caché est à nouveau visible)
			$(".block").show();

			// Étendez le bloc actuellement cliqué
			block.addClass("expanded");
			$(this).text("See less");

			if (block.is(":last-child")) {
				// Si le bloc est le dernier bloc, cachez le premier bloc
				$(".block").first().hide();
			} else {
				// Sinon, cachez le dernier bloc
				$(".block").last().hide();
			}
		});
	});
</script>

<script src="js/menu.js"></script>

<!-- Google Maps API -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=VOTRE_CLÉ_D'API&callback=initMap"></script>

<!--script>
$(document).ready(function(){
    $(".load-more-btn").click(function(){
        if($(this).text() == "Load More") {
            // Afficher les lignes cachées
            $(".hidden-row").show();
            // Changer le texte du bouton
            $(this).text("Load Less");
        } else {
            // Masquer les lignes
            $(".hidden-row").hide();
            // Changer le texte du bouton
            $(this).text("Load More");
        }
    });
});
</script-->

<script>
document.addEventListener('DOMContentLoaded', function() {

    // Sélectionner tous les boutons "Load More"
    let loadMoreButtons = document.querySelectorAll('.load-more-btn');

    loadMoreButtons.forEach(button => {
        button.addEventListener('click', function() {
            let table = this.closest('table');
            table.querySelectorAll('.hidden-row').forEach(row => {
                row.style.display = 'table-row';
            });
            table.querySelector('.load-more-btn').style.display = 'none';
            table.querySelector('.load-less-btn').style.display = 'block';
        });
    });

    // Sélectionner tous les boutons "Load Less"
    let loadLessButtons = document.querySelectorAll('.load-less-btn');

    loadLessButtons.forEach(button => {
        button.addEventListener('click', function() {
            let table = this.closest('table');
            table.querySelectorAll('.hidden-row').forEach(row => {
                row.style.display = 'none';
            });
            table.querySelector('.load-more-btn').style.display = 'block';
            table.querySelector('.load-less-btn').style.display = 'none';
        });
    });

});

</script>

<script>
	document.addEventListener("DOMContentLoaded", function() {
  const rightButton = document.querySelector('.review-right-button');
  const casinoImage = document.querySelector('.review-casino-image');

  const rightButtonWidth = rightButton.offsetWidth;
  casinoImage.style.width = `${rightButtonWidth}px`;
});

</script>
