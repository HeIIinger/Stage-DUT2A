<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }
        table, td, th {
            border: #999 solid 2px;
            border-collapse: collapse;
        }
    </style>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
    <?php
        try {
            $id_connex = new PDO ('mysql:host=localhost;dbname=stage','root','',array (PDO::MYSQL_ATTR_INIT_COMMAND =>"SET NAMES utf8"));
        }
        catch (Exception $e) {
            die('Erreur : '.$e->getMessage());
        }

        $date1 = $_GET['date'];
        $date2 = date('Y-m-d', strtotime($date1.'+ 1 day'));
        $donnees = array("0", "0", "0", "0", "0", "0", "0", "0", "0", "0", "0", "0", "0", "0", "0", "0", "0", "0", "0", "0", "0", "0", "0", "0");

        if ($_GET['trace']=="AVG" || $_GET['trace']=="SUM") {
            $requete="SELECT cap_id, MIN(mes_dates), MAX(mes_dates), AVG(".$_GET['valeur'].") FROM ".$_GET['table']." WHERE mes_dates>='".$date1."' AND mes_dates<'".$date2."' AND cap_id=".$_GET['capteur']." GROUP BY YEAR(mes_dates), MONTH(mes_dates), DAY(mes_dates), HOUR(mes_dates) ORDER BY mes_dates";
            $reponse=$id_connex->query($requete);
            $ligne=$reponse->fetch(PDO::FETCH_ASSOC);
        }
        elseif ($_GET['trace']=="COUNT") {
            $requete="SELECT cap_id, MIN(mes_dates), MAX(mes_dates), COUNT(".$_GET['valeur'].") FROM ".$_GET['table']." WHERE mes_dates>='".$date1."' AND mes_dates<'".$date2."' AND cap_id=".$_GET['capteur']." GROUP BY YEAR(mes_dates), MONTH(mes_dates), DAY(mes_dates), HOUR(mes_dates) ORDER BY mes_dates";
            $reponse=$id_connex->query($requete);
            $ligne=$reponse->fetch(PDO::FETCH_ASSOC);
        }

        // Traitement pour vérifier qu'il ne manque pas une heure //
        $instant = explode(" ", $ligne['MIN(mes_dates)']);
        $tableauheure = explode(":", $instant[1]);

        // Traitement pour arrondir les données //
        if ($_GET['trace']=="AVG" || $_GET['trace']=="SUM") {
            $part = explode(".", $ligne["AVG(".$_GET['valeur'].")"]);
        }
        elseif ($_GET['trace']=="COUNT") {
            $part = explode(".", $ligne["COUNT(".$_GET['valeur'].")"]);
        }

        $max = $part[0];
        $min = $part[0];
        $i=0;

        // Affichage de la première donnée //
        if ($tableauheure[0] == $i) {
            $donnees[$i] = $part[0];
        }
        else {
            while ($tableauheure[0] != $i) {
                $min = "0";
                $i++;
            }
            $donnees[$i] = $part[0];
        }

        $i++;

        if ($_GET['trace']=="AVG" || $_GET['trace']=="COUNT") {
            while ($ligne=$reponse->fetch(PDO::FETCH_ASSOC)) {
                $instant = explode(" ", $ligne['MIN(mes_dates)']);
                $tableauheure = explode(":", $instant[1]);
                $part = explode(".", $ligne[$_GET['trace']."(".$_GET['valeur'].")"]);

                if ($tableauheure[0] == $i) {
                    $donnees[$i] = $part[0];
                }
                else {
                    while ($tableauheure[0] != $i) {
                        $min = "0";
                        $i++;
                    }
                    $donnees[$i] = $part[0];
                }

                if ($part[0]>$max) {
                    $max = $part[0];
                }
                if ($part[0]<$min) {
                    $min = $part[0];
                }
                
                $i++;
            }
        }
        elseif ($_GET['trace']=="SUM") {
            while ($ligne=$reponse->fetch(PDO::FETCH_ASSOC)) {
                $instant = explode(" ", $ligne['MIN(mes_dates)']);
                $tableauheure = explode(":", $instant[1]);
                if ($_GET['trace']=="AVG" || $_GET['trace']=="SUM") {
                    $part = explode(".", $ligne["AVG(".$_GET['valeur'].")"]);
                }
                elseif ($_GET['trace']=="COUNT") {
                    $part = explode(".", $ligne["COUNT(".$_GET['valeur'].")"]);
                }
                $j = $i-1;

                if ($tableauheure[0] == $i) {
                    $donnees[$i] = $donnees[$j]+$part[0];
                }
                else {
                    while ($tableauheure[0] != $i) {
                        $i++;
                    }
                    $donnees[$i] = $donnees[$j]+$part[0];
                }

                $min = $donnees[0];
                $max = $donnees[23];

                $i++;
            }
        }

        $echelle = $max-$min;

        // On calcule une valeur pour arrondir les médianes //
        $arrondiMedianes = round(strlen($max)/2);

        // Génération des valeurs intermédiaires //
        $valEchelle = round($echelle/10, 0);
        $valMedianes[0] = $min;

        for ($i=1; $i<=9; $i++) {
            $j = $i-1;
            $valMedianes[$i] = round($valMedianes[$j]+$valEchelle, -$arrondiMedianes+1);
            $hauteurMedianes[$i] = 508-((($valMedianes[$i]-$min)/$echelle)*500);
        }

        foreach ($donnees as $key => $value) {
            $hauteur[$key] = 508-((($value-$min)/$echelle)*500);
        }

        // Valeur utilisée pour les étiquettes //
        $middle = $min+($echelle/2);

        $svg = ('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 810 550">
        <defs><style>.cls-1,.cls-3,.cls-4,.cls-5,.cls-6,.cls-7{fill:none;}.cls-1,.cls-4,.cls-5,.cls-6,.cls-7{stroke:#000;stroke-miterlimit:10;}.cls-1{stroke-width:4px;}.cls-2{font-size:16px;}.cls-2,.cls-9{font-family:MyriadPro-Regular, Myriad Pro;}.cls-3{stroke:#273676;}.cls-10,.cls-3{stroke-linejoin:round;}.cls-3,.cls-7{stroke-width:5px;}.cls-5{stroke-dasharray:11.9 11.9;}.cls-6{stroke-width:0.5px;}.cls-10,.cls-8{fill:#fff;}.cls-9{font-size:12px;}.cls-10{stroke:#273676;stroke-linecap:round;}.cls-13{fill:#00b0f0;}.cls-23{fill:#c0e0f5;}</style></defs>
        <!-- Rectangles -->
        <g id="Calque_3" data-name="Calque3">
            <rect class="cls-13 rectangle" x="88" y="7" width="15" height="500"/>');
            for ($i=0; $i<=21; $i++) {
                $svg .= '<rect class="cls-'.(($i%2 == 1)?(1):(2)).'3 rectangle" x="'.(103+$i*30).'" y="7" width="30" height="500"/>';
            }
            $svg .= ('<rect class="cls-23 rectangle" x="763" y="7" width="15" height="500"/>
        </g>
        
        <g id="Calque_1" data-name="Calque1">
            <!-- Origines -->
            <polyline class="cls-1" points="78 508 89 508 89 520"/>
            <!-- Points abscisse -->');
            for ($i=0; $i<=23; $i++) {
                $svg .= '<line class="cls-1" x1="'.(89+$i*30).'" y1="520" x2="'.(89+$i*30).'" y2="508"/>';
            }
            $svg .= ('<!-- Axe ordonnée -->
            <text class="cls-2" transform="translate(2 513)">'.$min.'</text>');
            for ($i=1; $i<=9; $i++) {
                $svg .= '<text class="cls-2" transform="translate(2 '.$hauteurMedianes[$i].')">'.$valMedianes[$i].'</text>';
            }
            $svg .= ('<text class="cls-2" transform="translate(2 13)">'.$max.'</text>
            <!-- Axe abscisse -->');
            for ($i=0; $i<=23; $i++) {
                $svg .= '<text class="cls-2 heure" transform="translate('.(81+$i*30).' 537)">'.$i.'</text>';
            }
            $svg .= ('<!-- Lignes verticales -->
            <line class="cls-4" x1="209" y1="502" x2="209" y2="508"/>
            <line class="cls-4" x1="329" y1="7" x2="329" y2="13"/>
            <line class="cls-5" x1="329" y1="8" x2="329" y2="496"/>
            <line class="cls-4" x1="329" y1="502" x2="329" y2="508"/>
            <line class="cls-4" x1="449" y1="7" x2="449" y2="13"/>
            <line class="cls-5" x1="449" y1="8" x2="449" y2="496"/>
            <line class="cls-4" x1="449" y1="502" x2="449" y2="508"/>
            <line class="cls-4" x1="569" y1="7" x2="569" y2="13"/>
            <line class="cls-5" x1="569" y1="8" x2="569" y2="496"/>
            <line class="cls-4" x1="569" y1="502" x2="569" y2="508"/>
            <line class="cls-4" x1="689" y1="7" x2="689" y2="13"/>
            <line class="cls-5" x1="689" y1="8" x2="689" y2="496"/>
            <line class="cls-4" x1="689" y1="502" x2="689" y2="508"/>
            <line class="cls-4" x1="209" y1="7" x2="209" y2="13"/>
            <line class="cls-5" x1="209" y1="8" x2="209" y2="496"/>
            <!-- Lignes horizontales -->
            <line class="cls-6" x1="89" y1="8" x2="779" y2="8"/> <!-- Ligne du max -->');
            for ($i=1; $i<=9; $i++) {
                $svg .= '<line class="cls-6" x1="89" y1="'.$hauteurMedianes[$i].'" x2="779" y2="'.$hauteurMedianes[$i].'"/><line class="cls-7" x1="78" y1="'.$hauteurMedianes[$i].'" x2="89" y2="'.$hauteurMedianes[$i].'"/>';
            }
            $svg .= ('<!-- Abscisse et ordonnée -->
            <polyline class="cls-1" points="78 8 89 8 89 508 779 508 779 523"/>
            <polyline class="cls-1" points="89 8 779 8 779 508"/>
            <!-- Courbe -->
            <polyline class="cls-3" points="');
            for ($i=0; $i<=23; $i++) {
                $svg .= (89+$i*30).' '.$hauteur[$i].' ';
            }
            $svg .= ('"/></g>
        <!-- Étiquettes -->
        <g id="Calque_2" data-name="Calque2">');

        $svg1 = $svg;

        $svg .= ('</g></svg>');

        for ($i=0; $i<=23; $i++) {
            $svg1 .= ('<g id="eti_'.$i.'">
                <rect class="cls-8" x="'.(58+$i*30).'" y="'.(($donnees[$i]>=$middle)?($hauteur[$i]+6):($hauteur[$i]-20)).'" width="60" height="14"/>
                <text class="cls-9" transform="translate('.(64+$i*30).' '.(($donnees[$i]>=$middle)?($hauteur[$i]+17):($hauteur[$i]-9)).')">'.$donnees[$i].'</text>
                <polyline class="cls-10" points="'.(58+$i*30).' '.($hauteur[$i]+6).' '.(83+$i*30).' '.($hauteur[$i]+6).' '.(89+$i*30).' '.$hauteur[$i].' '.(95+$i*30).' '.($hauteur[$i]+6).' '.(119+$i*30).' '.($hauteur[$i]+6).'"/>
                <polyline class="cls-10" points="'.(58+$i*30).' '.($hauteur[$i]-6).' '.(83+$i*30).' '.($hauteur[$i]-6).' '.(89+$i*30).' '.$hauteur[$i].' '.(95+$i*30).' '.($hauteur[$i]-6).' '.(119+$i*30).' '.($hauteur[$i]-6).'"/>
            </g>');
        }

        $svg1 .= '</g></svg>';

        echo $svg1;

        // Script pour les valeurs //

        $values = ("<h3>Échelle :</h3>".$echelle."<br><h3>Valeurs :</h3>");
        $valTxt = "";
        foreach ($donnees as $value) {
            $valTxt .= $value.", ";
        }
        $valTxt = rtrim($valTxt, ", ");
        $values .= $valTxt;

        echo $values;

        // Script pour créer les fichiers svg et *pdf* //
        
        // SVG
        $fp = fopen('courbe.svg','w');
        $data = fwrite ($fp, $svg);
        fclose ($fp);

        // PDF
    ?>

    <br><br>
    <a href="courbe.svg" download=""><button>Télécharger le SVG</button></a>

    <script>
        // Script pour les étiquettes //

        $(document).ready(function() {
            var middle = <?php echo $middle; ?>;

            $("#Calque_2 rect, #Calque_2 text, #Calque_2 polyline").hide();
            $(".rectangle").mouseover(function() {
                var list = $("#Calque_3").find(".rectangle");
                var index = list.index($(this));
                var value = $("#eti_"+index+" text").text();
                if (value>=middle) {
                    $("#eti_"+index+" rect, #eti_"+index+" text, #eti_"+index+" polyline:first").show();
                }
                else {
                    $("#eti_"+index+" rect, #eti_"+index+" text, #eti_"+index+" polyline:last").show();
                }
            });
            $(".rectangle").mouseout(function() {
                var list = $("#Calque_3").find(".rectangle");
                var index = list.index($(this));
                $("#eti_"+index+" rect, #eti_"+index+" text, #eti_"+index+" polyline").hide();
            });
            // Pour les heures
            $(".heure").mouseover(function() {
                var list = $("#Calque_1").find(".heure");
                var index = list.index($(this));
                var value = $("#eti_"+index+" text").text();
                if (value>=middle) {
                    $("#eti_"+index+" rect, #eti_"+index+" text, #eti_"+index+" polyline:first").show();
                }
                else {
                    $("#eti_"+index+" rect, #eti_"+index+" text, #eti_"+index+" polyline:last").show();
                }
            });
            $(".heure").mouseout(function() {
                var list = $("#Calque_1").find(".heure");
                var index = list.index($(this));
                $("#eti_"+index+" rect, #eti_"+index+" text, #eti_"+index+" polyline").hide();
            });
        });
    </script>
</body>
</html>