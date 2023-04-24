<?php
/*
Template de page : Mise en forme de la page pour connecter ou créer un perso
Paramètre : néant
*/
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "templates/fragments/head.php" ?>
    <title>Combat</title>
</head>

<body>
    <main>
        <h1>Combat</h1>
        <div class="flex justify-center">
            <div class="form-choix">
                <a href="selectionner_perso.php?action=connecter" title="" class="btn">Connecter personnage</a>
                <a href="selectionner_perso.php?action=creer" title="" class="btn">Créer personnage</a>
            </div>
        </div>
    </main>
    <?php include "templates/fragments/footer.php" ?>
</body>

</html>