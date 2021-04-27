<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        label {
            display: inline-block;
            width: 150px;
        }
    </style>
</head>
<body>
    <form method="post">
        <fieldset>
        <label for="mail">Email UHA :</label>
            <input type="email" id="mail" name="mail" pattern=".+@uha.fr" value="" required><br>
            <label for="dpt">Département :</label>
            <select id="dpt" name="dpt" value="" required>
                <option>GEA</option>
                <option>GEII</option>
                <option>GLT</option>
                <option>GMP</option>
                <option>FTM</option>
                <option>SGM</option>
                <option>MMI</option>
                <option>IRIMAS</option>
                <option>LPMT</option>
            </select><br>
            <label for="date">Date :</label>
            <input type="date" id="date" name="date" min="<?php echo date("Y-m-d"); ?>" value="<?php echo date("Y-m-d"); ?>" required><br>
            <label for="heure">Heure :</label>
            <select name="heure" id="heure" value="" required>
                <option>9h00</option>
                <option>9h30</option>
                <option>10h00</option>
                <option>10h30</option>
                <option>11h00</option>
                <option>11h30</option>
                <option>14h00</option>
                <option>14h30</option>
                <option>15h00</option>
                <option>15h30</option>
                <option>16h00</option>
                <option>16h30</option>
            </select><br>
            <label for="duree">Durée (en heures) :</label>
            <input type="number" id="duree" name="duree" min="1" value="" required><br>
            <label for="bat">Bâtiment :</label>
            <select name="bat" id="bat" value="" required>
                <option>A</option>
                <option>B</option>
                <option>C</option>
                <option>D</option>
                <option>E</option>
                <option>F</option>
                <option>G</option>
                <option>H</option>
            </select><br>
            <label for="motif">Motif important :</label>
            <input type="textarea" id="motif" name="motif" value="" required><br>
            <input type="submit" value="Envoyer">
        </fieldset>
    </form>

    <?php
        var_dump($_POST);
        // $envoi = mail('direction.iutmulhouse@uha.fr', 'Envoi depuis le formulaire', $_POST['motif'], 'From : '.$_POST['mail']);
    ?>

</body>
</html>