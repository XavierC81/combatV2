let intervalAgi;
window.addEventListener("DOMContentLoaded", function () {
    // Poser une écoute sur le bouton avancer
    if (document.querySelector("#avancer") != null) {
        document.querySelector("#avancer").addEventListener("click", avancer);
    }
    // Poser une écoute sur le bouton reculer
    if (document.querySelector("#reculer") != null) {
        document.querySelector("#reculer").addEventListener("click", reculer);
    }
    if (document.querySelector("#recommencer") != null) {
        document.querySelector("#recommencer").addEventListener("click", recommencer);
    }
    // Poser une écoute sur le formulaire de création
    if (document.querySelector("#form-perso") != null) {
        document.querySelector("#form-perso").addEventListener("change", caracForm);
    }
    if (document.querySelector("#res") != null) {
        document.querySelector("#res").addEventListener("click", for_res);
    }
    if (document.querySelector("#res") != null) {
        document.querySelector("#for").addEventListener("click", res_for);
    }
    // Poser une écoute sur la soumission du formulaire
    if (document.querySelector("#point-carac") != null) {
        document.querySelector("#form-perso").addEventListener("submit", function (e) {
            e.preventDefault();
            verifierForm();
        });
    }
    intervalAgi = setInterval(gainAgi, 3000);
    setInterval(miseAJour, 500);

    let scrollDiv = document.querySelector('#ecran-log');

    scrollDiv.scrollTop = scrollDiv.scrollHeight; // réglez le défilement en bas de la div

    scrollDiv.addEventListener('DOMNodeInserted', () => { // écoutez les changements du contenu de la div
        scrollDiv.scrollTop = scrollDiv.scrollHeight; // réglez à nouveau le défilement en bas de la div
    });
})

function recommencer() {
    // Rôle : Permet de recommencer une partie en cas de mort ou de fin de partie
    // Retour : néant
    // Paramètre : néant
    let id = this.dataset.id;
    let url = `recommencer.php?id=${id}`;
    clearInterval(intervalAgi);
    intervalAgi = setInterval(gainAgi, 3000);
    $.ajax(url, {
        type: "GET",
        success: finaliseRecommencer,
        error: function () {
            console.error("Erreur de communication")
        },
    });
    miseAJour();
}

function finaliseRecommencer(data) {
    // Rôle : Traiter les données envoyée par la requête HTTP
    // Retour néant
    // Paramètre :
    //      data : donnée reçu du serveur
    $().html(data);
}

function for_res() {
    // Rôle : Tranforme un point de force en resistance contre 3 agilité
    // Retour : néant
    // Paramètre : néant
    let id = this.dataset.id;
    let url = `for_res.php?action=res&id=${id}`;
    clearInterval(intervalAgi);
    intervalAgi = setInterval(gainAgi, 3000);
    $.ajax(url, {
        type: "GET",
        success: finaliseStat,
        error: function () {
            console.error("Erreur de communication")
        },
    });
}
function res_for() {
    // Rôle : Tranforme un point de resistance en force contre 3 agilité
    // Retour : néant
    // Paramètre : néant
    let id = this.dataset.id;
    let url = `for_res.php?action=for&id=${id}`;
    clearInterval(intervalAgi);
    intervalAgi = setInterval(gainAgi, 3000);
    $.ajax(url, {
        type: "GET",
        success: finaliseStat,
        error: function () {
            console.error("Erreur de communication")
        },
    });
}


function gainAgi() {
    // Rôle : Rajoute un point d'agilité toutes les trois secondes
    // Retour : néant
    // Paramètre : néant
    let id = document.querySelector("#avancer").dataset.id;
    let url = `gagner_agilite.php?id=${id}`;
    $.ajax(url, {
        type: "GET",
        success: finaliseGainAgi,
        error: function () {
            console.error("Erreur de communication")
        },
    });
}

function finaliseGainAgi(data) {
    // Rôle : Traiter les données envoyée par la requête HTTP
    // Retour néant
    // Paramètre :
    //      data : donnée reçu du serveur
    $("#agi-perso").html(data);
}



function combat(id) {
    // Rôle : lance l'ajax sur le controleur de combat
    // Retour : néant
    // Paramètre : 
    //      id : id donné
    let url = `combattre.php?idadversaire=${id}`;
    $.ajax(url, {
        type: "GET",
        success: finaliseCombat,
        error: function () {
            console.error("Erreur de communication")
        },
    });
}

function finaliseCombat(data) {
    // Rôle : Traiter les données envoyée par la requête HTTP
    // Retour néant
    // Paramètre :
    //      data : donnée reçu du serveur (page à insérer)
    $("#ecran_interface").html(data);
    clearInterval(intervalAgi);
    intervalAgi = setInterval(gainAgi, 3000);
}


function verifierForm() {
    // Rôle : Vérifie que les champs du form soient correctement rempli
    // Retour : néant
    // Paramètre : néant
    let form = document.querySelector("#form-perso");
    let force = parseFloat(form.force_base.value);
    let agilite = parseFloat(form.agilite_base.value);
    let resistance = parseFloat(form.resistance_base.value);
    if (force >= 3 && force <= 10 && agilite >= 3 && agilite <= 10 && resistance >= 3 && resistance <= 10 && (force + agilite + resistance) <= 15 && (force + agilite + resistance) >= 9) {
        form.submit()
    } else {
        document.querySelector("#erreur-form").textContent = "Les valeurs des caractéristiques de sont pas correct";
    }
}


function caracForm() {
    // Rôle : vérifier les points de caractéristiques donné
    // Retour : néant
    // Paramètre : néant
    let form = document.querySelector("#form-perso");
    let pointsRestant = document.querySelector("#point-carac");
    let points = 15;
    points = 15 - (parseFloat(form.force_base.value) + parseFloat(form.agilite_base.value) + parseFloat(form.resistance_base.value));
    pointsRestant.textContent = points;
    document.querySelector("#portrait").src = form.portrait.value;
}

function miseAJour() {
    // Rôle mettre à jour l'écran
    // Retour : néant
    // Paramètre : néant
    miseAJourJoueur();
    miseAJourStat();
    miseAJourLog();
    verifMort();
}

function verifMort() {
    // Rôle : vérifier si le perso est à 0 pv
    // Retour : néant
    // Paramètre : néant
    let id = document.querySelector("#avancer").dataset.id;
    let pv = document.querySelector("#pv").dataset.pv;
    let url = `mourir.php?id=${id}`;
    if (pv <= 0) {
        $.ajax(url, {
            type: "GET",
            success: finaliseMort,
            error: function () {
                console.error("Erreur de communication")
            },
        });
    }
}

function finaliseMort(data) {
    // Rôle : Traiter les données envoyée par la requête HTTP
    // Retour néant
    // Paramètre :
    //      data : donnée reçu du serveur (page à insérer)
    $("#salle").html(data);

}

function miseAJourBouton() {
    // Rôle : Mettre à jour les boutons d'action
    // Retour : néant
    // Paramètre : néant
    let id = document.querySelector("#avancer").dataset.id;
    let url = `mise_a_jour_boutons.php?id=${id}`;
    $.ajax(url, {
        type: "GET",
        success: finaliseBoutons,
        error: function () {
            console.error("Erreur de communication")
        },
    });
}

function miseAJourLog() {
    // Rôle : Mettre à jour les stats du joueur
    // Retour : néant
    // Paramètre : néant
    let id = document.querySelector("#avancer").dataset.id;
    let url = `mise_a_jour_log.php?id=${id}`;
    $.ajax(url, {
        type: "GET",
        success: finaliseLog,
        error: function () {
            console.error("Erreur de communication")
        },
    });
}

function miseAJourStat() {
    // Rôle : Mettre à jour les stats du joueur
    // Retour : néant
    // Paramètre : néant
    let id = document.querySelector("#avancer").dataset.id;
    let url = `mise_a_jour_stat.php?id=${id}`;
    $.ajax(url, {
        type: "GET",
        success: finaliseStat,
        error: function () {
            console.error("Erreur de communication")
        },
    });
    //$("#ecran-interface").html("chargement");
}

function miseAJourJoueur() {
    // Rôle : Mettre à jour la liste des joueurs
    // Retour : néant
    // Paramètre : néant
    let id = document.querySelector("#avancer").dataset.id;
    let url = `mise_a_jour_joueur.php?id=${id}`;
    $.ajax(url, {
        type: "GET",
        success: finalise_joueur,
        error: function () {
            console.error("Erreur de communication")
        },
    });

}


function reculer() {
    // Rôle : calcule la nouvelle salle à charger
    // Retour : néant
    // Paramètre : 
    //      id : id de l'objet de la classe personnage demandé

    let id = this.dataset.id;
    let url = `mouvement_salle.php?action=reculer&id=${id}`;
    $.ajax(url, {
        type: "GET",
        success: finaliseMouvement,
        error: function () {
            console.error("Erreur de communication")
        },
    });
}

function avancer() {
    // Rôle : calcule la nouvelle salle à charger
    // Retour : néant
    // Paramètre : néant

    let id = this.dataset.id;
    let url = `mouvement_salle.php?action=avancer&id=${id}`;
    $.ajax(url, {
        type: "GET",
        success: finaliseMouvement,
        error: function () {
            console.error("Erreur de communication")
        },
    });

}

function finaliseMouvement(data) {
    // Rôle : Traiter les données envoyée par la requête HTTP
    // Retour néant
    // Paramètre :
    //      data : donnée reçu du serveur (page à insérer)
    clearInterval(intervalAgi);
    intervalAgi = setInterval(gainAgi, 3000);
    $("#salle").html(data);
}

function finaliseLog(data) {
    // Rôle : Traiter les données envoyée par la requête HTTP
    // Retour néant
    // Paramètre :
    //      data : donnée reçu du serveur (page à insérer)

    $("#ecran-log").html(data);

}

function finalise_joueur(data) {
    // Rôle : Traiter les données envoyée par la requête HTTP
    // Retour néant
    // Paramètre :
    //      data : donnée reçu du serveur (page à insérer)

    $("#liste-joueur").html(data);

}

function finaliseStat(data) {
    // Rôle : Traiter les données envoyée par la requête HTTP
    // Retour néant
    // Paramètre :
    //      data : donnée reçu du serveur (page à insérer)

    $("#ecran-interface").html(data);

}
function finaliseBoutons(data) {
    // Rôle : Traiter les données envoyée par la requête HTTP
    // Retour néant
    // Paramètre :
    //      data : donnée reçu du serveur (page à insérer)

    $("#boutons-action").html(data);

}