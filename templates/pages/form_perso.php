<?php
/*
Template de page : Mise en forme du formulaire de connexion ou création de perso
Paramètres :
        $action : connecter ou creer
        $titre : Connecter ou Créer
*/
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "templates/fragments/head.php" ?>
    <title><?= $titre ?> personnage</title>
</head>

<body>
    <h1><?= $titre ?> personnage</h1>
    <div class="flex justify-center">
        <form class="form" action="recap_perso.php?action=<?= $_GET["action"] ?>" method="post" id="form-perso">
            <div class="pseudo">
                <label>
                    Pseudo :
                    <input type="text" name="pseudo">
                </label>
                <label>
                    Mot de passe :
                    <input type="password" name="password">
                </label>
            </div>
            <?php
            if ($_GET["action"] == "creer") {
                include "templates/fragments/form_creation_perso.php";
            }
            ?>
            <input type="submit">
        </form>
    </div>
    <?php include "templates/fragments/footer.php" ?>
</body>

</html>