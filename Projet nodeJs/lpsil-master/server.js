var express = require('express');
var morgan = require('morgan'); // Charge le middleware de logging
var favicon = require('serve-favicon'); // Charge le middleware de favicon
var bodyParser = require('body-parser');
var session =require('express-session');


var logger = require('log4js').getLogger('Server');
var app = express();


app.use(morgan('combined')); // Active le middleware de logging

app.use(express.static(__dirname + '/public'));// Indique que le dossier /public contient des fichiers statiques (middleware chargé de base)
app.use(bodyParser());

logger.info('server start');
app.listen(1313);

app.set('view engine', 'ejs');
app.set('views', __dirname + '/views');


/* On affiche le formulaire d'enregistrement */

var sess ={
    secret:'keyboard cat'

}
app.use(session(sess))

app.get('/', function(req, res){
    res.redirect('/login');
});

app.get('/login', function(req, res){
    res.render('login');

});

app.post('/login', function (req, res) {
    var username = req.body.username;
    var mdp = req.body.password;
    verif(username,mdp,res);


});
app.post('/req_inscription', function (req, res) {
    var email = req.body.email;
    var nom = req.body.nom;
    var prenom = req.body.prenom;
    var sexe = req.body.sexe;
    var taille = req.body.taille;
    var tel = req.body.tel;
    var ville = req.body.ville;
    var site = req.body.website;
    var mdp = req.body.password;
    var dn = req.body.birthdate;
    var photo = req.body.profilepic;
    var couleur = req.body.couleur;



    inscrireNouveau(email,nom,prenom,sexe,taille,tel,ville,site,mdp,dn,photo,couleur,res);


});

app.post('/req_modif', function (req, res) {
    var nom = req.body.nom;
    var prenom = req.body.prenom;
    var taille = req.body.taille;
    var tel = req.body.tel;
    var ville = req.body.ville;
    var site = req.body.website;
    var photo = req.body.profilepic;
    var couleur = req.body.couleur;


    modifierProfil(nom,prenom,taille,tel,ville,site,photo,couleur,res);

});

app.post('/paint', function (req, res) {
    var dest = req.body.destinataire;
    var mot = req.body.mot;
    var picture = req.body.picture;

    envoyerPicture(dest,mot,picture,res);

});


app.get('/inscription', function (req, res) {
    res.render('register');
});
app.get('/main', function (req, res) {

    res.render('main', {
            photo: sess.photo,
            nom: sess.nom,
            prenom: sess.prenom,
            email: sess.email,
            id:  sess.id,
        }
    );

});


/* On affiche le profile  */
app.get('/profile', function (req, res) {
    // TODO
    // On redirige vers la login si l'utilisateur n'a pas été authentifier
    // Afficher le button logout
});

app.get('/modifProfil', function (req, res) {

    res.render('modifProfil');
});
app.get('/paint', function (req, res) {

    res.render('paint');
});


var mysql = require('mysql');


/*var connection = mysql.createConnection({
    host: 'localhost',
    user: 'test',
    password: 'test',
    database: 'pictionnary'
});*/




var pool =  mysql.createPool({
    connectionLimit : 100, //important
    host : 'localhost',
    user : 'test',
    password: 'test',
    database: 'pictionnary'
});

/*pool.getConnection(function(err,connection){
    if (err) {
        connection.release();
        res.json({"code" : 100, "status" : "Erreur de connexion à la DB"});
        return;
    }

    logger.info('connecté en tant que ' + connection.threadId);

    connection.query("select * from user",function(err,rows){
        connection.release();
        if(!err) {
            res.json(rows);
        }
    });

    connection.on('error', function(err) {
        res.json({"code" : 100, "status" : "Erreur de connexion à la DB"});
        return;
    });
});*/

function verif(username,mdp,res){

    pool.getConnection(function(err,connection){

        connection.query("select * from users where email='"+username+"'",function(err,rows){
            if(!err) {
                if (rows.length > 0)
                    logger.info('identifiant valide');

                     connection.query("select * from users where email='" + username + "' AND password='"+mdp+"'", function (err, rows) {

                         if (!err) {
                             if (rows.length > 0){
                                 logger.info('mot de passe valide');
                                 sess.open=true;
                                 sess.id = rows[0].id;
                                 sess.photo=rows[0].profilepic;
                                 sess.nom=rows[0].nom;
                                 sess.prenom=rows[0].prenom;
                                 sess.email=rows[0].email;
                                res.redirect('/main');

                             }
                             else{
                                 logger.info('mot de passe non valide');
                             }
                         }
                     });
                }
                else
                {
                    logger.info('identifiant non valide ');
                }


        });



    });
}
function inscrireNouveau(email,nom,prenom,sexe,taille,tel,ville,site,mdp,dn,photo,couleur,res){


    pool.getConnection(function(err,connection){

        connection.query("INSERT INTO users (email, password, nom, prenom, tel, website, sexe, birthdate, ville, taille, couleur, profilepic) VALUES ('"+email+"','"+mdp+"','"+nom+"','"+prenom+"','"+tel+"','"+site+"','"+sexe+"', '"+dn+"','"+ville+"', "+taille+",'"+couleur+"','"+photo+"')",function(err,result){
            if(!err) {

                    logger.info('ca compile');
                    sess.open=true;
                    res.redirect('main');


            }
            else
            {
                logger.info('raté ');
                throw  err;

            }


        });



    });

}

function modifierProfil(nom,prenom,taille,tel,ville,site,photo,couleur,res){


    pool.getConnection(function(err,connection){

        connection.query("UPDATE users SET nom ='"+nom+"', prenom='"+prenom+"', tel='"+tel+"' , website='"+site+"', ville='"+ville+"', taille="+taille+", couleur='"+couleur+"', profilepic='"+photo+"' WHERE id='"+sess.id+"' ",function(err,result){
            if(!err) {

                logger.info('ca compile');

                sess.photo=photo;
                sess.nom=nom;
                sess.prenom=prenom;
                res.redirect('main');


            }
            else
            {
                logger.info('raté ');
                throw  err;

            }


        });



    });

}

function envoyerPicture(dest,mot,picture,res){


    pool.getConnection(function(err,connection){

        connection.query("INSERT INTO paint (mailUn, mailDeux, mot, picture) VALUES ('"+dest+"','"+sess.email+"','"+mot+"','"+picture+"')",function(err,result){
            if(!err) {

                logger.info('ca compile');
                res.redirect('main');


            }
            else
            {
                logger.info('raté ');
                throw  err;

            }


        });



    });

}
