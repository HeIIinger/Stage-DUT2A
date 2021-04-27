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
    <table>
        <tr>
            <th>ppp</th><th>partant</th><th>instant</th><th>pcc</th>
        </tr>
    <?php
        $requete="SELECT * FROM minimesure ORDER BY instant";
        $reponse=$id_connex->query($requete);
        $ligne=$reponse->fetch(PDO::FETCH_ASSOC);

        echo '<tr><td>'.$ligne['ppp'].'</td><td>'.$ligne['partant'].'</td><td>'.$ligne['instant'].'</td><td>'.$ligne['pcc'].'</td></tr>';

        $tableaudate = explode("-", $ligne["instant"]);
        $datecomparante = mktime(0,0,0,$tableaudate[1],$tableaudate[2],$tableaudate[0]);

        // $date = date('Y-n-d',$datecomparee);
        // echo $date;

        while ($ligne=$reponse->fetch(PDO::FETCH_ASSOC)) {
            $tableaudate = explode("-", $ligne["instant"]);
            $datecomparee = mktime(0,0,0,$tableaudate[1],$tableaudate[2],$tableaudate[0]);

            if ($datecomparante+86400 == $datecomparee) {
                echo '<tr><td>'.$ligne['ppp'].'</td><td>'.$ligne['partant'].'</td><td>'.date('Y-m-d',$datecomparee).'</td><td>'.$ligne['pcc'].'</td></tr>';
            }
            else {
                $difference = ($datecomparee-$datecomparante)/86400;
                for ($i=1; $i<$difference; $i++) {
                    echo '<tr><td></td><td></td><td>'.date('Y-m-d',$datecomparante+(86400*$i)).'</td><td></td></tr>';
                }
                echo '<tr><td>'.$ligne['ppp'].'</td><td>'.$ligne['partant'].'</td><td>'.date('Y-m-d',$datecomparee).'</td><td>'.$ligne['pcc'].'</td></tr>';
            }

            $datecomparante = $datecomparee;
        }
    ?>
    </table>
</body>
</html>