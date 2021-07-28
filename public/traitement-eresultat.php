<?php
# Inclure le fichier des fonctions
$fonctions='./../../includes/functions.php';
if(file_exists($fonctions)){include_once($fonctions);}
# Traitement des requêtes
if(isset($_GET['requete'])){
    if(isset($_GET['requete'],$_GET['matricule'],$_GET['annee_academique'],$_GET['annee_d_etude'],$_GET['entite'],$_GET['semestre'],$_GET['date_naissance'])){
        echo "Bienvenue";
        $c=ConnectBDD();
        if($c){
            //echo "Connexion réussie";
        }
    }else{
        echo "Paramètres non valides";
        exit('.');
    }
}else{
    echo "Vous n'êtes pas autorisés à accéder à cette page";
    exit('.');
}
