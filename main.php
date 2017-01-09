<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 04/12/2016
 * Time: 18:46
 */

    if (isset($_GET["erreur"])) {
        echo "<div><span>".$_GET["erreur"]."</span></div>";
    }

    include("header.php");
    print "<a href='paint.php'>Dessiner</a>";

    if (isset($_SESSION["email"])) {

        // Connect to server and select database.
        $dbh = new PDO('mysql:host=localhost;dbname=pictionnary', 'test', 'test');

        $email = $_SESSION["email"];

        $sql = $dbh->prepare("SELECT * FROM DRAWINGS WHERE EMAIL = '" . $email . "'");
        $sql->execute();

        print "<div><table style=\"float:left\">";
        print "<th>Vos dessins</th>";

        $i = 0;

        foreach ($sql as $row) {
            if ($i == 0) {
                print "<tr>";
            }

            print "<td>";
            print "<img src=\"" . $row["dessin"] . "\" style=\"width:100px; height:100px;\" alt='Dessin' />";
            print "</td>";

            if ($i == 2) {
                print "</tr>";
                $i = 0;
            } else {
                $i++;
            }
        }

        print "</table></div>";

        $sql = $dbh->prepare("SELECT * FROM DRAWINGS WHERE DEST = '" . $email . "'");
        $sql->execute();

        print "<div><table style=\"float:left\">";
        print "<th>Vos demandes</th>";

        $i = 0;

        foreach ($sql as $row) {
            if ($i == 0) {
                print "<tr>";
            }

            print "<td>";
            print "<a href=\"guess.php?id=" . $row["id"] . "\"><img src=\"" . $row["dessin"] . "\" style=\"width:100px; height:100px;\" alt='Dessin' /></a>";
            print "</td>";

            if ($i == 2) {
                echo "</tr>";
                $i = 0;
            } else {
                $i++;
            }
        }
        echo "</table></div>";
    }
?>