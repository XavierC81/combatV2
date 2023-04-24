<?php

// Controleur : préparer et afficher le formulaire de connexion ou de création de personnage
// Paramètres :
//      GET id : id du perso donné

// Initialisation
include "library/init.php";

// Connection
$perso = new personnage($_GET["id"]);



// Analyse de la demande


// Intéraction objet / BDD
if ($perso->get("agilite") < 15 && $perso->get("salle") > 0 && $perso->get("salle") < 10 && $perso->get("pv") > 0) {
    $perso->set("agilite", ($perso->get("agilite") + 1));
    $perso->logGainAgi();
}

$perso->update();




// Affichage
echo $perso->get("agilite");
