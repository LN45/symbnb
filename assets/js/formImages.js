//récupérer un attribut html du formulaire des images de l'annonce pour afficher les champs lorsqu'on clique sur ajouter une image
$('#add-image').click(function () {
    // Je récupère le n° des futurs champs que je vais créer
    const index = +$('#widgets-counter').val(); // on récupère la valeur contenue dans le widgets-counter pour avoir le n° d'index. on met le + devant pour transformer le résultat chaine de caractère en un nombre

    // je récupère le prototype des entrées . Tmpl représente le template de mon prototype c'est à dire un formulaire de mon image
    const tmpl = $('#commande_images').data('prototype').replace(/_name_/g, index);

    //j'injecte ce code au sein de la div (append permet d'ajouter)
    $('#commande_images').append(tmpl);

    $('#widgets-counter').val(index +1);

    //je gère le bouton supprimer
    handleDeleteButtons();
});

function handleDeleteButtons() {
    // permet de récupérer tous les bouton qui ont un data action delete
    $('button[data-action="delete"]').click(function () {
        //dataset représente tous les attributs data et on ajoute target pour accéder au data-target
        const target = this.dataset.target;
        $(target).remove();
    });
}

//fonction mise en place pour permettre de mettre à jour le compteur url des images à l'édition
function updateCounter() {
    const count = +$('#commande_images div.form-group').length;

    $('#widgets-counter').val(count);
}

updateCounter();
//je gère le bouton supprimer également au chargement de page qui sert pour la partie édition d'une annonce
handleDeleteButtons();