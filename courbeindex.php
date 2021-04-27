<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        table, td, th {
            border: #999 solid 2px;
            border-collapse: collapse;
        }
    </style>
</head>
<body>
    <?php
        try {
            $id_connex = new PDO ('mysql:host=localhost;dbname=stage','root','',array (PDO::MYSQL_ATTR_INIT_COMMAND =>"SET NAMES utf8"));
        }
        catch (Exception $e) {
            die('Erreur : '.$e->getMessage());
        }
    ?>

    <form method="get" action="courbecharge.php">
        <fieldset>
            Choix de la table<br>
            <select name="table">
            <?php
                $requete="SHOW TABLES FROM stage";
                $reponse=$id_connex->query($requete);
                $ligne=$reponse->fetch(PDO::FETCH_ASSOC);
                echo "<option>".$ligne['Tables_in_stage']."</option>";
                while ($ligne=$reponse->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option>".$ligne['Tables_in_stage']."</option>";
                }
            ?>
            </select><br>
            Choix du capteur<br>
            <select name="capteur">
            <?php
                $requete="SELECT DISTINCT cap_id FROM mesure_e";
                $reponse=$id_connex->query($requete);
                $ligne=$reponse->fetch(PDO::FETCH_ASSOC);
                echo "<option>".$ligne['cap_id']."</option>";
                while ($ligne=$reponse->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option>".$ligne['cap_id']."</option>";
                }
            ?>
            </select><br>
            Choix de la date<br>
            <input type="date" name="date" value=""><br>
            Choix de la valeur<br>
            <select name="valeur">
                <option>mes_count</option>
            <?php
                for ($i=1; $i<=20; $i++) {
                    echo "<option>mes_valeur".$i."</option>";
                }
            ?>
            </select><br>
            Méthode de tracé<br>
            <select name="trace">
                <option value="AVG">Moyenne</option>
                <option value="SUM">Somme</option>
                <option value="COUNT">Compte</option>
            </select><br><br>
            <input type="submit" value="Envoyer">
        </fieldset>
    </form>

</body>
</html>