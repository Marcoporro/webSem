<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 04/12/2016
 * Time: 23:05
 */

    // on démarre la session, si l'utilisateur n'est pas connecté alors on redirige vers la page main.php.
    session_start();
    if(!isset($_SESSION['prenom'])) {
        header("Location: main.php");
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset=utf-8 />
        <title>Pictionnary</title>
        <link rel="stylesheet" media="screen" href="css/styles.css" >
        <?php

        // Ici, récupérer la liste des commandes dans la table DRAWINGS avec l'identifiant $_GET['id']
        try {
            // Connect to server and select database.
            $dbh = new PDO('mysql:host=localhost;dbname=pictionnary', 'test', 'test');

            // En SQL: sélectionner tous les tuples de la table DRAWINGS tels que l'id est égal à $id.
            $req = "SELECT * FROM DRAWINGS WHERE id = '" . $_GET["id"] . "'";
            $sql = $dbh->prepare($req);
            $sql->execute();
            if (($sql->rowCount()) >= 1) {
                foreach ($sql as $row) {
                    $commandes =  $row["commandes"];
                    $mot	  = $row['mot'];
                }
            }
        }catch (PDOException $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
            $dbh = null;
            die();
        }
        ?>

        <script>
            function verifMot(){
                motEntre = document.getElementById('mot');
                if(motEntre.value == "<?php echo $mot; ?>"){
                    alert("Bravo vous avez trouvé le mot");
                }
            }

            // la taille et la couleur du pinceau
            var size, color;
            // la dernière position du stylo
            var x0, y0;
            // le tableau de commandes de dessin à envoyer au serveur lors de la validation du dessin
            var drawingCommands = <?php echo $commandes;?>;

            console.log(drawingCommands);
            window.onload = function() {
                var canvas = document.getElementById('myCanvas');
                canvas.width = 400;
                canvas.height= 400;
                var context = canvas.getContext('2d');
                var old_x0, old_y0;
                var start = function(c) {
                    console.log("Commande start !");
                    context.beginPath();
                    context.arc(c.x, c.y, c.size, 0, 2 * Math.PI, false);
                    context.fillStyle = c.color;
                    context.fill();
                    old_x0 = c.x;
                    old_y0 = c.y;
                }

                var draw = function(c) {
                    console.log("Commande draw !");
                    context.beginPath();
                    context.arc(c.x, c.y, c.size, 0, 2 * Math.PI, false);
                    context.fillStyle = c.color;
                    context.fill();

                    context.beginPath();
                    //dessiner ligne
                    context.moveTo(c.x, c.y);
                    context.lineTo(old_x0, old_y0);
                    context.lineWidth = c.size * 2;
                    context.strokeStyle = c.color;

                    context.stroke();
                    old_x0 = c.x;
                    old_y0 = c.y;
                }

                var clear = function() {
                    context.clearRect(0, 0, 400, 400);
                }

                // étudiez ce bout de code
                var i = 0;
                var iterate = function() {
                    if(i>=drawingCommands.length)
                        return;
                    var c = drawingCommands[i];
                    switch(c.command) {

                        case "start":
                            start(c);
                            break;
                        case "draw":
                            draw(c);
                            break;
                        case "clear":
                            clear();
                            break;
                        default:
                            console.error("Cette commande n'existe pas "+ c.command);
                    }
                    i++;
                    setTimeout(iterate,30);
                };
                iterate();
            };

            var last = -1;
            function change_dest(id){
                document.getElementById('id_dest').setAttribute('value',id);
                var td_new  = document.getElementById(id);
                var td_last = document.getElementById(last);
                if(last == -1){
                    td_new.style.color  = "#4BB5C1";
                }else {
                    td_new.style.color  = "#4BB5C1";
                    td_last.style.color = "#333";
                }
                last = id;
            }

        </script>
    </head>
    <body>
        <div style="float:left; margin-right:10px;">
            <canvas style="border: thick black solid;"  id="myCanvas"></canvas>
        </div>
        <form name="min_tools" id="min_tools" action="req_paint_min.php" method="post">
        Mot a deviner
        <input type="text" id="mot" name="mot" placeholder="Mot" pattern="[A-Za-z0-9]*" onkeyup="verifMot();">
        <!-- Récupération liste utilisateurs -->
        <div style="clear:both;" class="fb-login-button" data-max-rows="1" data-show-faces="true"></div>
    </body>
</html>