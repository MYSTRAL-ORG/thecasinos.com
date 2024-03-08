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
