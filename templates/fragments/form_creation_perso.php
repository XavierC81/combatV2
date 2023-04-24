<?php
/*
Template de fragment : Mise en forme du morceau de formulaire à rajouter pour la création de personnage
Paramètre : néant
*/
?>
<p class="points">Points restant : <span id="point-carac">6</span></p>
<div class="stats">
    <label>
        Force :
        <input type="number" min="3" max="10" name="force_base" value="3">
    </label>
    <label>
        Agilité :
        <input type="number" min="3" max="10" name="agilite_base" value="3">
    </label>
    <label>
        Résistance :
        <input type="number" min="3" max="10" name="resistance_base" value="3">
    </label>
    <small id="erreur-form"></small>
</div>
<p class="avatar">Choisir votre avatar :</p>
<select name="portrait">
    <?php
    $imgPath = "img/portraits/";
    $img = scandir($imgPath);
    foreach ($img as $portrait) {
        if ($portrait != "." && $portrait != "..") {
            include "templates/fragments/ligne_option_portrait.php";
        }
    }
    ?>
</select><br>
<img src="img/portraits/portrait1.jpeg" id="portrait" alt=""><br>