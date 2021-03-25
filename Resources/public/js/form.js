$(document).ready(function () {
    
    let type = {buy: true, rent: true};
    let oldType = {buy: true, rent: false};

    function prepareTableForElement()
    {
        if (type.rent) {
            $("#base_order_orderElements thead tr td:eq(1)").after('<td>Date d\'enlèvement</td><td>Date de retour</td>');
            $("#offerElementsContainer tr td:eq(1)").after('<td></td><td></td>');
        }
    }


    // On récupère la balise <div> en question qui contient l'attribut « data-prototype » qui nous intéresse.
    let $container = $('#base_order_orderElements');

    // On ajoute un lien pour ajouter une nouvelle catégorie
    let $addLink = $('<a href="#" id="add_element" class="btn btn-success" style="margin-bottom: 10px; margin-left: 10px"><i class="fa fa-plus"></i> Ajouter un élement</a>');
    let $addRentLink = $('<a href="#" id="add_element" class="btn btn-success" style="margin-bottom: 10px; margin-left: 10px"><i class="fa fa-plus"></i> Ajouter un élement louable</a>');
    $("#orderElement-wrapper").append($addLink);
    //$("#orderElement-wrapper").append($addRentLink);

    // On ajoute un nouveau champ à chaque clic sur le lien d'ajout.
    $addLink.click(function (e) {
        addElement($container);
        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        return false;
    });

    $addRentLink.click(function (e) {
        addRentableElement($container);
        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        return false;
    });

    // On définit un compteur unique pour nommer les champs qu'on va ajouter dynamiquement
    let index = $('#offerElementsContainer tr').length;

    // On ajoute un premier champ automatiquement s'il n'en existe pas déjà un (cas d'une nouvelle annonce par exemple).
    if (index == 0) {
        addElement($container);
    } else {
        // Pour chaque catégorie déjà existante, on ajoute un lien de suppression
        $('#offerElementsContainer').children('tr').each(function () {
            addDeleteLink($(this));
        });
    }

    //prepareTableForElement();


    // La fonction qui ajoute un formulaire Categorie
    function addElement($container)
    {
        console.log($container);
        // Dans le contenu de l'attribut « data-prototype », on remplace :
        // - le texte "__name__label__" qu'il contient par le label du champ
        // - le texte "__name__" qu'il contient par le numéro du champ
        let $prototype = $($container.attr('data-prototype'));


        // On ajoute au prototype un lien pour pouvoir supprimer la catégorie
        addDeleteLink($prototype);

        // On ajoute le prototype modifié à la fin de la balise <div>
        $("#offerElementsContainer").append($prototype);

        //On convertit le select en select2
        //console.log()
        $("#dywee_orderbundle_baseorder_orderElements_" + index + "_product").select2();

        // Enfin, on incrémente le compteur pour que le prochain ajout se fasse avec un autre numéro
        index++;
    }

    function addRentableElement()
    {
        let $prototype = $($container.attr('data-prototype'));


        // On ajoute au prototype un lien pour pouvoir supprimer la catégorie
        addDeleteLink($prototype);

        // On ajoute le prototype modifié à la fin de la balise <div>
        $("#offerElementsContainer").append($prototype);

        //On convertit le select en select2
        //console.log()
        $("#dywee_orderbundle_baseorder_orderElements_" + index + "_product").select2();

        // Enfin, on incrémente le compteur pour que le prochain ajout se fasse avec un autre numéro
        index++;
    }

    // La fonction qui ajoute un lien de suppression d'une catégorie
    function addDeleteLink($prototype)
    {
        // Création du lien
        $deleteLink = $('<a href="#" class="btn btn-danger">Supprimer</a>');

        $actionCell = $('<td>');

        $actionCell.append($deleteLink);

        // Ajout du lien
        $prototype.append($actionCell);

        // Ajout du listener sur le clic du lien
        $deleteLink.click(function (e) {
            $prototype.remove();
            e.preventDefault(); // évite qu'un # apparaisse dans l'URL
            return false;
        });
    }

    $("#dywee_orderbundle_baseorder_billingAddress").select2();
    $("#dywee_orderbundle_baseorder_shippingAddress").select2();
});