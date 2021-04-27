<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            text-align: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }
        table, td, th {
            border: #999 solid 2px;
            border-collapse: collapse;
        }
    </style>
</head>
<body>
    <?php
        $timestamp1 = microtime();

        // Connexion à la BDD //

        try {
            $id_connex = new PDO ('mysql:host=localhost;dbname=stage','root','',array (PDO::MYSQL_ATTR_INIT_COMMAND =>"SET NAMES utf8"));
        }
        catch (Exception $e) {
            die('Erreur : '.$e->getMessage());
        }

        // Déclaration de variables pour les essais (seront sûrement remplacées par des champs de formulaire si j'ai bien compris) //

        $extractxt = "";
        $retourligne="\n";
        $table = "mesure_e";
        $nom = "mesure_e_test";
        $date1 = "2020-03-27";
        $date2 = "2020-03-28";

        // Première requête : sert à connaître toutes les colonnes de la table //

        $requete="SHOW COLUMNS FROM ".$table;
        $reponse=$id_connex->query($requete);
        $ligne=$reponse->fetch(PDO::FETCH_ASSOC);

        $extractxt .= "--".$retourligne."-- Structure de la table ".$nom.$retourligne."--".$retourligne.$retourligne;

        // On met toutes les données dans des variables pour plus de lisibilité //

        $field = $ligne['Field'];
        $type = $ligne['Type'];
        $null = $ligne['Null'];
        $key = $ligne['Key'];
        $default = $ligne['Default'];
        $extra = $ligne['Extra'];

        // On enregistre tous les field dans un tableau afin de pouvoir les réutiliser pour l'insertion de données //
        
        $structureTable[0] = $field;

        // Conditionnement des variables pour les commandes SQL //

        if ($type != 'text') {
            if ($null == 'YES') {
                $default = 'DEFAULT';
                $null = 'NULL';
            }
            elseif ($null == 'NO') {
                $null = 'NOT NULL';
            }
        }
        else {
            $extractxt .= '`'.$field.'` '.$type.' '.$default.' '.$extra;
        }
        if ($key == 'PRI') { // On vérifie si la ligne contient une clé primaire et si c'est le cas on l'enregistre pour plus tard
            $primary = TRUE;
            $primarykey = $field;
        }

        // On crée la table et on la remplit avec la ligne reçue de la première requête //

        $extractxt .= 'CREATE TABLE IF NOT EXISTS '.$nom.' ('.$retourligne.'`'.
            $field.'` '.$type.' '.$default.' '.$null.' '.$extra;

        $i = 1; // Variable utilisée pour le tableau des field

        while ($ligne=$reponse->fetch(PDO::FETCH_ASSOC)) {
            // On reproduit tout le processus
            $field = $ligne['Field'];
            $type = $ligne['Type'];
            $null = $ligne['Null'];
            $key = $ligne['Key'];
            $default = $ligne['Default'];
            $extra = $ligne['Extra'];
            $structureTable[$i] = $field;
            if ($type != 'text') {
                if ($null == 'YES') {
                    $default = 'DEFAULT';
                    $null = 'NULL';
                }
                elseif ($null == 'NO') {
                    $null = 'NOT NULL';
                }
                $extractxt .= ','.$retourligne.$field.' '.$type.' '.$default.' '.$null.' '.$extra;
            }
            else {
                $extractxt .= ','.$retourligne.$field.' '.$type.' '.$default.' '.$extra;
            }
            if ($key == 'PRI') {
                $primary = TRUE;
                $primarykey = $field;
            }
            $i++;
        }
        if ($primary == TRUE) {
            $extractxt .= ','.$retourligne.'PRIMARY KEY (`'.$primarykey.'`)';
        }
        $extractxt .= $retourligne.') ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1185772 ;';
    
        $extractxt .= $retourligne.$retourligne."--".$retourligne."-- Contenu de la table `".$nom."`".$retourligne."--".$retourligne.$retourligne;

        $extractxt .= 'INSERT INTO '.$nom.' (';

        $structureTxt = "";
        foreach ($structureTable as $value) {
            $structureTxt .= $value.', ';
        }
        $structureTxt = rtrim($structureTxt, ', ');
        $extractxt .= $structureTxt;
        $extractxt .= ') VALUES ';

        $requete="SELECT * FROM ".$table." WHERE mes_dates BETWEEN '".$date1."' AND '".$date2."'";
        $reponse=$id_connex->query($requete);
        $ligne=$reponse->fetch(PDO::FETCH_ASSOC);

        foreach ($ligne as $cles => $valeur) {
            if ($valeur==NULL) {
                $ligne[$cles] = "NULL";
            }
        }

        $extractxt .= "(".$ligne['mes_id'].", '".$ligne['mes_datec']."', '".$ligne['mes_dates']."', ".$ligne['mes_uts'].", ".$ligne['mes_count'].", ".$ligne['mes_status'].", ".$ligne['cap_id'].", ".$ligne['mes_valeur1'].", ".$ligne['mes_valeur2'].", ".$ligne['mes_valeur3'].", ".$ligne['mes_valeur4'].", ".$ligne['mes_valeur5'].", ".$ligne['mes_valeur6'].", ".$ligne['mes_valeur7'].", ".$ligne['mes_valeur8'].", ".$ligne['mes_valeur9'].", ".$ligne['mes_valeur10'].", ".$ligne['mes_valeur11'].", ".$ligne['mes_valeur12'].", ".$ligne['mes_valeur13'].", ".$ligne['mes_valeur14'].", ".$ligne['mes_valeur15'].", ".$ligne['mes_valeur16'].", ".$ligne['mes_valeur17'].", ".$ligne['mes_valeur18'].", ".$ligne['mes_valeur19'].", ".$ligne['mes_valeur20'].", '".$ligne['mes_msg']."')";

        while ($ligne=$reponse->fetch(PDO::FETCH_ASSOC)) {
            foreach ($ligne as $cles => $valeur) {
                if ($valeur==NULL) {
                    $ligne[$cles] = "NULL";
                }
            }

            $extractxt .= ", (".$ligne['mes_id'].", '".$ligne['mes_datec']."', '".$ligne['mes_dates']."', ".$ligne['mes_uts'].", ".$ligne['mes_count'].", ".$ligne['mes_status'].", ".$ligne['cap_id'].", ".$ligne['mes_valeur1'].", ".$ligne['mes_valeur2'].", ".$ligne['mes_valeur3'].", ".$ligne['mes_valeur4'].", ".$ligne['mes_valeur5'].", ".$ligne['mes_valeur6'].", ".$ligne['mes_valeur7'].", ".$ligne['mes_valeur8'].", ".$ligne['mes_valeur9'].", ".$ligne['mes_valeur10'].", ".$ligne['mes_valeur11'].", ".$ligne['mes_valeur12'].", ".$ligne['mes_valeur13'].", ".$ligne['mes_valeur14'].", ".$ligne['mes_valeur15'].", ".$ligne['mes_valeur16'].", ".$ligne['mes_valeur17'].", ".$ligne['mes_valeur18'].", ".$ligne['mes_valeur19'].", ".$ligne['mes_valeur20'].", '".$ligne['mes_msg']."')";
        }

        $extractxt .= ';';

        // Cette chaîne de caractère contient tout le code SQL
        // echo $extractxt;

        // Ecriture dans un fichier texte sur le serveur
        $fp = fopen("pw".$nom.".sql",'w');
        $data = fwrite ($fp, $extractxt);
        fclose ($fp);

        $timestamp2 = microtime();

        $executionTime = $timestamp2-$timestamp1;
    ?>

    <a href="<?php echo 'pw'.$nom.'.sql'; ?>" download="<?php echo $nom.'.sql'; ?>"><button>Télécharger le fichier</button></a>

    <h5><i>Exécution en <?php echo abs($executionTime); ?> s.</i></h5>

</body>
</html>