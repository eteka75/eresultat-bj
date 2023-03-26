
<?php
# Inclure le fichier des fonctions
function ConnectBDD($type="mysql"){
	 $host = "localhost";$db = "Db__01"; $user = "user"; $password = "@@@@@@";
	 try {
                $dsn = "pgsql:host=$host;port=5432;dbname=$db;";
                $conn = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

	} catch (PDOException $e) {
        	echo "Connection failed: " . $e->getMessage();
		die($e->getMessage());
	}
    	return $conn;
}

//if(file_exists($fonctions)){include_once($fonctions);}
# Traitement des requêtes
$semestre="";
$matricule="";
if(isset($_GET['requete'])){
    $msg=[];
    $validation=0;
    if(isset($_GET['requete'],$_GET['matricule'],$_GET['annee_academique'],$_GET['annee_d_etude'],$_GET['entite'],$_GET['semestre'],$_GET['date_naissance'])){
       // echo "Bienvenue";
        $mat=trim($_GET['matricule']);
        $date_nais=trim($_GET['date_naissance']);
        $annee_etude=trim($_GET['annee_d_etude']);
        $semestre=trim($_GET['semestre']);
        $etab=trim($_GET['entite']);
        $acad=trim($_GET['annee_academique']);
        if(isset($_GET['requete'])){
            $tacad=explode("-",$acad);
            if(count($tacad)==2){
                $acad=$tacad[1];
            }
        }
        $c=ConnectBDD();
        if($c){
            
            #Traitement de la recherche de l'étudiant dans le système         
           // echo $date_nais;
            try {
            //$c->beginTransaction();              //and annee_etude LIKE CONCAT('%', :annee)  
            //where matricule=:mat and annee_academique=:acad and  	etablissement=:etab and date_naissance=:dn 
            /*$req="SELECT
            a.matricule,
            a.nom_de_famille nom,
            a.prenom ,
            a.annee_etude annee,
            a.statut,
            a.etablissement etab,
            b.semestre,
            b.moyenne,
            b.nbre_ue_validee,
            b.decision_jury,
            c.code_ue,
            c.libelle_ue,
            c.credit_ue,
        FROM
        okapicollege.okapi_dump_2021 a
        INNER JOIN okapicollege.flash_verdict_2021 b 
            ON a.matricule = b.matricule
        INNER JOIN  okapicollege.flash_resultats_2021 c 
            ON b.matricule = c.matricule
        ORDER BY a.matricule;"
        $sth1 = $c->prepare($req);
        $sth1->execute();
        $data = $sth1->fetchAll(PDO::FETCH_OBJ);
        var_dump($data);
        exit('YYYYYYYYYYYYY');*/
            $sth = $c->prepare("SELECT * FROM okapicollege.okapi_dump_2021 where matricule=:mat  and annee_academique=:acad and etablissement=:etab  and date_naissance=:dn and annee_etude LIKE '%$annee_etude' limit 3");
            $sth->bindParam(':mat', $mat, PDO::PARAM_STR);
            $sth->bindParam(':acad', $acad, PDO::PARAM_STR);
            $sth->bindParam(':etab', $etab, PDO::PARAM_STR);
            //$sth->bindParam(':annee', $annee_etude, PDO::PARAM_STR);
            $sth->bindParam(':dn', date("Y-m-d", strtotime($date_nais)), PDO::PARAM_STR);
            $sth->execute();
    
            $sth->setFetchMode(PDO::FETCH_OBJ);           
            $data = $sth->fetchAll(PDO::FETCH_OBJ);
            $etudiant=$etu = count($data)?$data[0]:NULL;
            if($etu && $etu->date_validation1!="" && $etu->date_validation2!=""){
                $validation=1;
            }
            //var_dump($etudiant);
            //exit('hjdhjhjd');
            } catch(PDOException $e){
                //$c->rollBack();
              echo "Erreur : " . $e->getMessage();
              exit('.');
            }
             #Traitement du verdict final de la Flash
             if($etudiant && $etab=="FLASH" ){
             try {
                 
                //$c->beginTransaction(); 
                //if($semestre==1 OR $semestre==2){
                    // and annee_etude LIKE CONCAT('%', :annee)
                    $sth = $c->prepare("SELECT * FROM okapicollege.flash_verdict_2021 WHERE semestre=:sem and matricule=:mat and annee_academique=:acad and etablissement=:etab  and annee_etude LIKE '%$annee_etude'");
                    $sth->bindParam(':sem', $semestre, PDO::PARAM_STR);
                    $sth->bindParam(':mat', $mat, PDO::PARAM_STR);
                    $sth->bindParam(':acad', $acad, PDO::PARAM_STR);
                    $sth->bindParam(':etab', $etab, PDO::PARAM_STR);
                   /* $sth->bindParam(':annee', $annee_etude, PDO::PARAM_STR);*/
                    $sth->execute();

                    $data = $sth->fetchAll(PDO::FETCH_OBJ);
                    $R_final = count($data)?$data[0]:NULL;
                    //var_dump($R_final);
                  
               // }
                    
            } catch(PDOException $e){
                $c->rollBack();
              echo "Erreur : " . $e->getMessage();
              exit('.');
            }
            }else{
                $msg[] =['type' =>"info",'msg'=>"Votre établissement n'est pas encore pris en compte par la plateforme."];
            }
            # Traitement des UE et ECU de l'année
            if($etudiant){
                //and annee_etude LIKE CONCAT('%', :annee)
                $sth = $c->prepare("SELECT * FROM okapicollege.flash_resultats_2021 WHERE semestre=:sem and matricule=:mat and annee_academique=:acad and etablissement=:etab and annee_etude LIKE '%$annee_etude'  ORDER BY code_ue DESC, note_ecu DESC");
                    $sth->bindParam(':mat', $mat, PDO::PARAM_STR);
                    $sth->bindParam(':acad', $acad, PDO::PARAM_STR);                    
                    $sth->bindParam(':etab', $etab, PDO::PARAM_STR);
                    //$sth->bindParam(':annee', $annee_etude, PDO::PARAM_STR);
                    $sth->bindParam(':sem', $semestre, PDO::PARAM_STR);
                    $sth->execute();
                    $R_Credits = $sth->fetchAll(PDO::FETCH_OBJ);
                    //var_dump($R_Credits);
                    //exit('BON222222222222................');
            }else{
                $msg[] =['type' =>"info",'msg'=>"Veuillez renseigner des données correctes."];
            }
        }
    }else{
        echo "Paramètres non valides";
        exit('.');
    }
}else{
    $etudiant="";
    //exit('');
   // echo "Vous n'êtes pas autorisé à accédé à cette page";
  //  exit('.');
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>

    <meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="../favicon.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Wilfried ETEKA">    
    <meta name="theme-color" content="#343a40">
    <meta name="theme-color" media="(prefers-color-scheme: light)" content="white">
    <meta name="theme-color" media="(prefers-color-scheme: dark)" content="#343a40">
    <title>UP-eResultat
        <?php echo (isset($etudiant))? "de ".($n=$etudiant->nom_de_famille." ". $etudiant->prenom."-". $etudiant->matricule.""):''?>
    </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;400&display=swap" rel="stylesheet">  
      
    <link href="./../assets/css/app.css" rel="stylesheet">
    <link href="./../assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="./../assets/plugins/datepicker/jquery.datepicker2.css" rel="stylesheet">
    <style>
    .bg-overlay-primary {
        background: rgba(255, 255, 255, 0.6);
    }

    .bg-white-5 {
        background: rgba(255, 255, 255, 0.8)
    }

    @media(max-width:800px) {
        .display-3 {
            font-size: 2.6em !important;
        }

        .display-4 {
            font-size: 3.2em !important;
        }
        moyenne-ue{
        position: relative;
        margin-top:-30px;
        }
    }
    .ln-min{line-height:14px;}
    .moyenne-ue{
        position: relative;
        margin-top:-40px;
        padding-left: 15px;
    }

    @media print {
        /*@page {
            size: port
        }*/
        @page {
            size: A4 portrait;;
        }
        .p-0-print{
            padding:0px;
        }   
        .card, .card.bg-dar,.bg-gray,.bg-light {background:#ffffff !important;}     
        .intro h3{
            font-size:14px;
        }
            #intro{
           padding:0px;
           text-align:left;
        }
        .badge,
        body,
        .text-muted,
        .text-danger,
        .text-white,.note, .text-warning {
            color: black !important;
        }

        .badge {
            background: none;
            border: none;
            padding: 0px;
        }

        .navbar-toggler,.d-none-print {
            display: none;
        }

        .table {
            border: 1px solid #eeeeee;
            width: 100% !important;
            border-radius: 20px;
            overflow: hidden;
        }
        .py-6,.py-4,.pb-4, .py-4{padding:10px auto !important;}
        .pt-3{padding-top:10px !important;}
        .table tr td,.table tr th{padding-top:5px;padding-bottom:5px;}
        .notebadge{
            height:15px;
            width:15px;
            line-height:15px;
            padding:4px;
        }
        .table tr th,
        .table tr td,
        .border {
            //border: 0px solid #eeeeee;
        }
        .table tr td.print-bb-0{
           border-bottom: 0px solid #ffffff !important;
        }
        .d-none-print,
        .noPrint {
            display: none;
        }

        .print-w2,
        .print-w1,.table {
            background: #fff;
            //width: 100%;
            display: block;
        }

        /*.print-w2 {
            width: 20%;
        }

        .print-w2 {
            width: 80%;
        }*/
    }

    body {
        overflow-x: hidden;
    }

    .form-control {
        border: 1px solid #EEEEEE;
        border-radius: 3px;
    }

    #btntop {
        height: 30px;
        width: 30px;
        line-height: 30px;
        text-align: center;
        background: white;
        color: #aaaaaa;
        position: fixed;
        border: 1px solid #aaaaaa;
        border-radius: 50%;
        bottom: 3%;
        right: 3%;
        z-index: 1000;
    }

    #main-h {
        min-height: 500px;
    }
    .main-chart * {
        border-left: 0px solid #f5f6f8;
        padding: 2px 20px
    }

    
</style>
</head>

<body data-bs-spy="scroll" data-bs-target="#menu">
    <div id="btntop" class="d-none-print">
        <a href="#menu"> <i class="far  fa-arrow-up" aria-hidden="true"></i></a>
    </div>


    <div id="menu">
  <nav id="enav" class="navbar navbar-sm navbar-expand-lg navbar-transparant bg-dark navbar-dark navbar-absolute_ w-100">
   
      <div class="container">
      <a class="navbar-brand " href="../"><img  height="30px" src="../../assets/images/logo-up-eresultat-mini.png" alt="UP-eResultat"></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav ml-auto">
          
          <li class="nav-item">
            <a class="nav-link navlink" href="../"><i class="far fa-home"></i><span
                                    class="d-sm-none">Accueil</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link navlink" href="../#eresultat-ccm">Comment ça marche ?</a>
          </li>
          <li class="nav-item">
            <a class="nav-link navlink" href="../#entites">Les entités éligibles</a>
          </li>
          <li class="nav-item">
            <a class="nav-link navlink" href="../#avis">Avis des étudiants</a>
          </li>
          <li class="nav-item">
            <a class="nav-link navlink" href="../#contact">Nous contacter</a>
          </li>
          
          
         
          <li class="nav-item dropdown">
            <a class="nav-link  dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Autres services
            </a>
            <div class="dropdown-menu">
              <a class="dropdown-item" target="_blank" href="http://www.univ-parakou.bj/">Site officielle de l'UP</a>
              <a class="dropdown-item" target="_blank" href="http://https://inscriptions.univ-parakou.bj/okapiCollege/etudiant/">Plateforme d'inscription</a>
              
            </div>
          </li>
          <li class="nav-item ">
            <a class="nav-link px-1 span-line"  title="Rejoignez nous sur Facebook" target="_blank" href="https://www.facebook.com/UPBENIN"><i class="fab text-white  fa-facebook-f"></i>  <span class="d-sm-none text-white">Facebook</span></a>
          </li>
          <li class="nav-item "  title="Rejoignez nous sur LinkedIn">
            <a class="nav-link px-1 span-line" target="_blank" href="https://www.linkedin.com/company/universite-de-parakou-benin"><i class="fab text-white  fa-linkedin"></i> <span class="d-sm-none text-white">LinkedIn</span>  </a>
          </li>
          <li class="nav-item " title="Rejoignez nous sur Youtube">
            <a class="nav-link px-1 span-line" target="_blank" href="https://www.youtube.com/c/UNIVERSITEDEPARAKOUWEBTV"><i class="fab text-white  fa-youtube"></i>   <span class="d-sm-none text-white">YouTube</span></a>
          </li>
          
        </ul>
        </div>
    </div>
  </nav>
  </div>
    <div id="intro" class="intro  py-4 bg-gray p-0-print  text-white">
        <div class="intro-content">
            <div class="container">
                <div class="row">
                    <div class=" text-sm-12 col-md-9 text-center  text-md-left text-lg-left text-xl-left">
                        <h1 class="h3 font-weight-lighter">Mon résultat</h1>
                        <?php
                            if(isset($etudiant) && $etudiant){
                           
                       ?>
                        <div class=" tex-sm ">
                            <span class="p- text-md badge badge-pill badge-warning">
                                <i class="far fa-user  p-1 rounded-circle "></i>
                                <?php
                            if(isset($etudiant) && $etudiant){
                             $n=$etudiant->nom_de_famille." ". $etudiant->prenom." (". $etudiant->matricule.")";
                             echo ($n);
                            }
                       ?>
                            </span>
                        </div>
                        <?php
                            }
                             ?>
                    </div>
                    <div class="col-xs-12 text-sm-12 col-md-3 py-3 text-center  text-md-right text-lg-right text-xl-right">
                        <a class="btn bg-light pull-right_ font-weight-regular d-none-print" href="#"
                            onclick="window.print();"><i class="far fa-print"></i> <span class="d-noned-sm-inline d-md-inline d-xl-inline">Imprimer</span> </a>
                    </div>
                </div>


            </div>
        </div>
    </div>
    </div>

    <main id="main-h" class=" main-h bg-light p-0-print pb-5 pt-3">
        <div class="container">
        
            <?php
                    
                    if(isset($etudiant) && $etudiant){
                ?>
            <div class="row">
                <div class="col-md-4 col-xl-4 mt-2 print-w1 print-table">
                    <div class="card">
                        <div class="card-body">
                            <div class="float-end mt-2" style="position: relative;">
                                <div id="total-revenue-chart " style="min-height: 40px;">
                                    <div id="apexchartsp1eqti69j"
                                        class="apexcharts-canvas apexchartsp1eqti69j  apexcharts-theme-light"
                                        style="width: 70px; height: 40px;"><svg id="SvgjsSvg1263" width="70" height="40"
                                            xmlns="http://www.w3.org/2000/svg" version="1.1"
                                            xmlns:xlink="http://www.w3.org/1999/xlink"
                                            xmlns:svgjs="http://svgjs.com/svgjs" class="apexcharts-svg"
                                            xmlns:data="ApexChartsNS" transform="translate(0, 0)"
                                            style="background: transparent none repeat scroll 0% 0%;">
                                            <g id="SvgjsG1265" class="apexcharts-inner apexcharts-graphical"
                                                transform="translate(0, 0)">
                                                <defs id="SvgjsDefs1264">
                                                    <linearGradient id="SvgjsLinearGradient1269" x1="0" y1="0" x2="0"
                                                        y2="1">
                                                        <stop id="SvgjsStop1270" stop-opacity="0.4"
                                                            stop-color="rgba(216,227,240,0.4)" offset="0"></stop>
                                                        <stop id="SvgjsStop1271" stop-opacity="0.5"
                                                            stop-color="rgba(190,209,230,0.5)" offset="1"></stop>
                                                        <stop id="SvgjsStop1272" stop-opacity="0.5"
                                                            stop-color="rgba(190,209,230,0.5)" offset="1"></stop>
                                                    </linearGradient>
                                                    <clipPath id="gridRectMaskp1eqti69j">
                                                        <rect id="SvgjsRect1275" width="74" height="40" x="-2" y="0"
                                                            rx="0" ry="0" opacity="1" stroke-width="0" stroke="none"
                                                            stroke-dasharray="0" fill="#fff"></rect>
                                                    </clipPath>
                                                    <clipPath id="gridRectMarkerMaskp1eqti69j">
                                                        <rect id="SvgjsRect1276" width="74" height="44" x="-2" y="-2"
                                                            rx="0" ry="0" opacity="1" stroke-width="0" stroke="none"
                                                            stroke-dasharray="0" fill="#fff"></rect>
                                                    </clipPath>
                                                </defs>
                                                <line id="SvgjsLine1274" x1="0" y1="0" x2="0" y2="40"
                                                    stroke-dasharray="3" class="apexcharts-xcrosshairs" x="0" y="0"
                                                    width="1" height="40" fill="url(#SvgjsLinearGradient1269)"
                                                    filter="none" fill-opacity="0.9" stroke-width="0"></line>
                                                <g id="SvgjsG1291" class="apexcharts-xaxis" transform="translate(0, 0)">
                                                    <g id="SvgjsG1292" class="apexcharts-xaxis-texts-g"
                                                        transform="translate(0, 2.75)"></g>
                                                </g>
                                                <g id="SvgjsG1294" class="apexcharts-grid">
                                                    <g id="SvgjsG1295" class="apexcharts-gridlines-horizontal"
                                                        style="display: none;">
                                                        <line id="SvgjsLine1297" x1="0" y1="0" x2="70" y2="0"
                                                            stroke="#e0e0e0" stroke-dasharray="0"
                                                            class="apexcharts-gridline"></line>
                                                        <line id="SvgjsLine1298" x1="0" y1="8" x2="70" y2="8"
                                                            stroke="#e0e0e0" stroke-dasharray="0"
                                                            class="apexcharts-gridline"></line>
                                                        <line id="SvgjsLine1299" x1="0" y1="16" x2="70" y2="16"
                                                            stroke="#e0e0e0" stroke-dasharray="0"
                                                            class="apexcharts-gridline"></line>
                                                        <line id="SvgjsLine1300" x1="0" y1="24" x2="70" y2="24"
                                                            stroke="#e0e0e0" stroke-dasharray="0"
                                                            class="apexcharts-gridline"></line>
                                                        <line id="SvgjsLine1301" x1="0" y1="32" x2="70" y2="32"
                                                            stroke="#e0e0e0" stroke-dasharray="0"
                                                            class="apexcharts-gridline"></line>
                                                        <line id="SvgjsLine1302" x1="0" y1="40" x2="70" y2="40"
                                                            stroke="#e0e0e0" stroke-dasharray="0"
                                                            class="apexcharts-gridline"></line>
                                                    </g>
                                                    <g id="SvgjsG1296" class="apexcharts-gridlines-vertical"
                                                        style="display: none;"></g>
                                                    <line id="SvgjsLine1304" x1="0" y1="40" x2="70" y2="40"
                                                        stroke="transparent" stroke-dasharray="0"></line>
                                                    <line id="SvgjsLine1303" x1="0" y1="1" x2="0" y2="40"
                                                        stroke="transparent" stroke-dasharray="0"></line>
                                                </g>
                                                <g id="SvgjsG1277" class="apexcharts-bar-series apexcharts-plot-series">
                                                    <g id="SvgjsG1278" class="apexcharts-series" rel="1"
                                                        seriesName="seriesx1" data:realIndex="0">
                                                        <path id="SvgjsPath1280"
                                                            d="M 1.5909090909090908 40L 1.5909090909090908 30L 4.7727272727272725 30L 4.7727272727272725 30L 4.7727272727272725 40L 4.7727272727272725 40z"
                                                            fill="rgba(91,115,232,0.85)" fill-opacity="1"
                                                            stroke-opacity="1" stroke-linecap="square" stroke-width="0"
                                                            stroke-dasharray="0" class="apexcharts-bar-area" index="0"
                                                            clip-path="url(#gridRectMaskp1eqti69j)"
                                                            pathTo="M 1.5909090909090908 40L 1.5909090909090908 30L 4.7727272727272725 30L 4.7727272727272725 30L 4.7727272727272725 40L 4.7727272727272725 40z"
                                                            pathFrom="M 1.5909090909090908 40L 1.5909090909090908 40L 4.7727272727272725 40L 4.7727272727272725 40L 4.7727272727272725 40L 1.5909090909090908 40"
                                                            cy="30" cx="7.954545454545454" j="0" val="25" barHeight="10"
                                                            barWidth="3.1818181818181817"></path>
                                                        <path id="SvgjsPath1281"
                                                            d="M 7.954545454545454 40L 7.954545454545454 13.600000000000001L 11.136363636363637 13.600000000000001L 11.136363636363637 13.600000000000001L 11.136363636363637 40L 11.136363636363637 40z"
                                                            fill="rgba(91,115,232,0.85)" fill-opacity="1"
                                                            stroke-opacity="1" stroke-linecap="square" stroke-width="0"
                                                            stroke-dasharray="0" class="apexcharts-bar-area" index="0"
                                                            clip-path="url(#gridRectMaskp1eqti69j)"
                                                            pathTo="M 7.954545454545454 40L 7.954545454545454 13.600000000000001L 11.136363636363637 13.600000000000001L 11.136363636363637 13.600000000000001L 11.136363636363637 40L 11.136363636363637 40z"
                                                            pathFrom="M 7.954545454545454 40L 7.954545454545454 40L 11.136363636363637 40L 11.136363636363637 40L 11.136363636363637 40L 7.954545454545454 40"
                                                            cy="13.600000000000001" cx="14.318181818181817" j="1"
                                                            val="66" barHeight="26.4" barWidth="3.1818181818181817">
                                                        </path>
                                                        <path id="SvgjsPath1282"
                                                            d="M 14.318181818181817 40L 14.318181818181817 23.6L 17.5 23.6L 17.5 23.6L 17.5 40L 17.5 40z"
                                                            fill="rgba(91,115,232,0.85)" fill-opacity="1"
                                                            stroke-opacity="1" stroke-linecap="square" stroke-width="0"
                                                            stroke-dasharray="0" class="apexcharts-bar-area" index="0"
                                                            clip-path="url(#gridRectMaskp1eqti69j)"
                                                            pathTo="M 14.318181818181817 40L 14.318181818181817 23.6L 17.5 23.6L 17.5 23.6L 17.5 40L 17.5 40z"
                                                            pathFrom="M 14.318181818181817 40L 14.318181818181817 40L 17.5 40L 17.5 40L 17.5 40L 14.318181818181817 40"
                                                            cy="23.6" cx="20.68181818181818" j="2" val="41"
                                                            barHeight="16.4" barWidth="3.1818181818181817"></path>
                                                        <path id="SvgjsPath1283"
                                                            d="M 20.68181818181818 40L 20.68181818181818 4.399999999999999L 23.86363636363636 4.399999999999999L 23.86363636363636 4.399999999999999L 23.86363636363636 40L 23.86363636363636 40z"
                                                            fill="rgba(91,115,232,0.85)" fill-opacity="1"
                                                            stroke-opacity="1" stroke-linecap="square" stroke-width="0"
                                                            stroke-dasharray="0" class="apexcharts-bar-area" index="0"
                                                            clip-path="url(#gridRectMaskp1eqti69j)"
                                                            pathTo="M 20.68181818181818 40L 20.68181818181818 4.399999999999999L 23.86363636363636 4.399999999999999L 23.86363636363636 4.399999999999999L 23.86363636363636 40L 23.86363636363636 40z"
                                                            pathFrom="M 20.68181818181818 40L 20.68181818181818 40L 23.86363636363636 40L 23.86363636363636 40L 23.86363636363636 40L 20.68181818181818 40"
                                                            cy="4.399999999999999" cx="27.045454545454543" j="3"
                                                            val="89" barHeight="35.6" barWidth="3.1818181818181817">
                                                        </path>
                                                        <path id="SvgjsPath1284"
                                                            d="M 27.045454545454543 40L 27.045454545454543 14.8L 30.227272727272727 14.8L 30.227272727272727 14.8L 30.227272727272727 40L 30.227272727272727 40z"
                                                            fill="rgba(91,115,232,0.85)" fill-opacity="1"
                                                            stroke-opacity="1" stroke-linecap="square" stroke-width="0"
                                                            stroke-dasharray="0" class="apexcharts-bar-area" index="0"
                                                            clip-path="url(#gridRectMaskp1eqti69j)"
                                                            pathTo="M 27.045454545454543 40L 27.045454545454543 14.8L 30.227272727272727 14.8L 30.227272727272727 14.8L 30.227272727272727 40L 30.227272727272727 40z"
                                                            pathFrom="M 27.045454545454543 40L 27.045454545454543 40L 30.227272727272727 40L 30.227272727272727 40L 30.227272727272727 40L 27.045454545454543 40"
                                                            cy="14.8" cx="33.40909090909091" j="4" val="63"
                                                            barHeight="25.2" barWidth="3.1818181818181817"></path>
                                                        <path id="SvgjsPath1285"
                                                            d="M 33.40909090909091 40L 33.40909090909091 30L 36.590909090909086 30L 36.590909090909086 30L 36.590909090909086 40L 36.590909090909086 40z"
                                                            fill="rgba(91,115,232,0.85)" fill-opacity="1"
                                                            stroke-opacity="1" stroke-linecap="square" stroke-width="0"
                                                            stroke-dasharray="0" class="apexcharts-bar-area" index="0"
                                                            clip-path="url(#gridRectMaskp1eqti69j)"
                                                            pathTo="M 33.40909090909091 40L 33.40909090909091 30L 36.590909090909086 30L 36.590909090909086 30L 36.590909090909086 40L 36.590909090909086 40z"
                                                            pathFrom="M 33.40909090909091 40L 33.40909090909091 40L 36.590909090909086 40L 36.590909090909086 40L 36.590909090909086 40L 33.40909090909091 40"
                                                            cy="30" cx="39.772727272727266" j="5" val="25"
                                                            barHeight="10" barWidth="3.1818181818181817"></path>
                                                        <path id="SvgjsPath1286"
                                                            d="M 39.772727272727266 40L 39.772727272727266 22.4L 42.954545454545446 22.4L 42.954545454545446 22.4L 42.954545454545446 40L 42.954545454545446 40z"
                                                            fill="rgba(91,115,232,0.85)" fill-opacity="1"
                                                            stroke-opacity="1" stroke-linecap="square" stroke-width="0"
                                                            stroke-dasharray="0" class="apexcharts-bar-area" index="0"
                                                            clip-path="url(#gridRectMaskp1eqti69j)"
                                                            pathTo="M 39.772727272727266 40L 39.772727272727266 22.4L 42.954545454545446 22.4L 42.954545454545446 22.4L 42.954545454545446 40L 42.954545454545446 40z"
                                                            pathFrom="M 39.772727272727266 40L 39.772727272727266 40L 42.954545454545446 40L 42.954545454545446 40L 42.954545454545446 40L 39.772727272727266 40"
                                                            cy="22.4" cx="46.136363636363626" j="6" val="44"
                                                            barHeight="17.6" barWidth="3.1818181818181817"></path>
                                                        <path id="SvgjsPath1287"
                                                            d="M 46.136363636363626 40L 46.136363636363626 32L 49.318181818181806 32L 49.318181818181806 32L 49.318181818181806 40L 49.318181818181806 40z"
                                                            fill="rgba(91,115,232,0.85)" fill-opacity="1"
                                                            stroke-opacity="1" stroke-linecap="square" stroke-width="0"
                                                            stroke-dasharray="0" class="apexcharts-bar-area" index="0"
                                                            clip-path="url(#gridRectMaskp1eqti69j)"
                                                            pathTo="M 46.136363636363626 40L 46.136363636363626 32L 49.318181818181806 32L 49.318181818181806 32L 49.318181818181806 40L 49.318181818181806 40z"
                                                            pathFrom="M 46.136363636363626 40L 46.136363636363626 40L 49.318181818181806 40L 49.318181818181806 40L 49.318181818181806 40L 46.136363636363626 40"
                                                            cy="32" cx="52.499999999999986" j="7" val="20" barHeight="8"
                                                            barWidth="3.1818181818181817"></path>
                                                        <path id="SvgjsPath1288"
                                                            d="M 52.499999999999986 40L 52.499999999999986 25.6L 55.681818181818166 25.6L 55.681818181818166 25.6L 55.681818181818166 40L 55.681818181818166 40z"
                                                            fill="rgba(91,115,232,0.85)" fill-opacity="1"
                                                            stroke-opacity="1" stroke-linecap="square" stroke-width="0"
                                                            stroke-dasharray="0" class="apexcharts-bar-area" index="0"
                                                            clip-path="url(#gridRectMaskp1eqti69j)"
                                                            pathTo="M 52.499999999999986 40L 52.499999999999986 25.6L 55.681818181818166 25.6L 55.681818181818166 25.6L 55.681818181818166 40L 55.681818181818166 40z"
                                                            pathFrom="M 52.499999999999986 40L 52.499999999999986 40L 55.681818181818166 40L 55.681818181818166 40L 55.681818181818166 40L 52.499999999999986 40"
                                                            cy="25.6" cx="58.863636363636346" j="8" val="36"
                                                            barHeight="14.4" barWidth="3.1818181818181817"></path>
                                                        <path id="SvgjsPath1289"
                                                            d="M 58.863636363636346 40L 58.863636363636346 24L 62.045454545454525 24L 62.045454545454525 24L 62.045454545454525 40L 62.045454545454525 40z"
                                                            fill="rgba(91,115,232,0.85)" fill-opacity="1"
                                                            stroke-opacity="1" stroke-linecap="square" stroke-width="0"
                                                            stroke-dasharray="0" class="apexcharts-bar-area" index="0"
                                                            clip-path="url(#gridRectMaskp1eqti69j)"
                                                            pathTo="M 58.863636363636346 40L 58.863636363636346 24L 62.045454545454525 24L 62.045454545454525 24L 62.045454545454525 40L 62.045454545454525 40z"
                                                            pathFrom="M 58.863636363636346 40L 58.863636363636346 40L 62.045454545454525 40L 62.045454545454525 40L 62.045454545454525 40L 58.863636363636346 40"
                                                            cy="24" cx="65.2272727272727" j="9" val="40" barHeight="16"
                                                            barWidth="3.1818181818181817"></path>
                                                        <path id="SvgjsPath1290"
                                                            d="M 65.2272727272727 40L 65.2272727272727 18.4L 68.40909090909089 18.4L 68.40909090909089 18.4L 68.40909090909089 40L 68.40909090909089 40z"
                                                            fill="rgba(91,115,232,0.85)" fill-opacity="1"
                                                            stroke-opacity="1" stroke-linecap="square" stroke-width="0"
                                                            stroke-dasharray="0" class="apexcharts-bar-area" index="0"
                                                            clip-path="url(#gridRectMaskp1eqti69j)"
                                                            pathTo="M 65.2272727272727 40L 65.2272727272727 18.4L 68.40909090909089 18.4L 68.40909090909089 18.4L 68.40909090909089 40L 68.40909090909089 40z"
                                                            pathFrom="M 65.2272727272727 40L 65.2272727272727 40L 68.40909090909089 40L 68.40909090909089 40L 68.40909090909089 40L 65.2272727272727 40"
                                                            cy="18.4" cx="71.59090909090907" j="10" val="54"
                                                            barHeight="21.6" barWidth="3.1818181818181817"></path>
                                                    </g>
                                                    <g id="SvgjsG1279" class="apexcharts-datalabels" data:realIndex="0">
                                                    </g>
                                                </g>
                                                <line id="SvgjsLine1305" x1="0" y1="0" x2="70" y2="0" stroke="#b6b6b6"
                                                    stroke-dasharray="0" stroke-width="1"
                                                    class="apexcharts-ycrosshairs"></line>
                                                <line id="SvgjsLine1306" x1="0" y1="0" x2="70" y2="0"
                                                    stroke-dasharray="0" stroke-width="0"
                                                    class="apexcharts-ycrosshairs-hidden"></line>
                                                <g id="SvgjsG1307" class="apexcharts-yaxis-annotations"></g>
                                                <g id="SvgjsG1308" class="apexcharts-xaxis-annotations"></g>
                                                <g id="SvgjsG1309" class="apexcharts-point-annotations"></g>
                                            </g>
                                            <rect id="SvgjsRect1273" width="0" height="0" x="0" y="0" rx="0" ry="0"
                                                opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0"
                                                fill="#fefefe"></rect>
                                            <g id="SvgjsG1293" class="apexcharts-yaxis" rel="0"
                                                transform="translate(-18, 0)"></g>
                                            <g id="SvgjsG1266" class="apexcharts-annotations"></g>
                                        </svg>
                                        <div class="apexcharts-legend" style="max-height: 20px;"></div>
                                        <div class="apexcharts-tooltip apexcharts-theme-light">
                                            <div class="apexcharts-tooltip-series-group" style="order: 1;"><span
                                                    class="apexcharts-tooltip-marker"
                                                    style="background-color: rgb(0, 143, 251);"></span>
                                                <div class="apexcharts-tooltip-text"
                                                    style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                                    <div class="apexcharts-tooltip-y-group"><span
                                                            class="apexcharts-tooltip-text-label"></span><span
                                                            class="apexcharts-tooltip-text-value"></span></div>
                                                    <div class="apexcharts-tooltip-z-group"><span
                                                            class="apexcharts-tooltip-text-z-label"></span><span
                                                            class="apexcharts-tooltip-text-z-value"></span></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="apexcharts-yaxistooltip apexcharts-yaxistooltip-0 apexcharts-yaxistooltip-left apexcharts-theme-light">
                                            <div class="apexcharts-yaxistooltip-text"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="resize-triggers p-3">

                                </div>
                            </div>
                            <div>
                                <h4 class="mb-1 mt-1">Semestre 
                                    <?php
                                    
                                    if(isset($R_final) && $R_final->semestre!=''){
                                       echo  $R_final->semestre;
                                    }else{
                                       // echo '-';
                                       echo $semestre;
                                    }
                                    ?></span></h4>
                                <p class="text-muted mb-0  p-0"><b>Matricule: </b>
                                    <?php echo isset($etudiant,$etudiant->matricule)? ($etudiant->matricule):$matricule ?> </p>
                                <p class="text-muted mb-0  p-0"><b>Nom : </b>
                                    <?php echo isset($etudiant,$etudiant->nom_de_famille)? ($etudiant->nom_de_famille):'-' ?>
                                </p>
                                <p class="text-muted mb-0  p-0"><b>Prénom(s) : </b>
                                    <?php echo isset($etudiant,$etudiant->prenom)? ($etudiant->prenom):'-' ?>
                                </p>
                                <p class="text-muted mb-0  p-0"><b>Année Académique: </b>
                                    <?php echo isset($etudiant,$etudiant->annee_academique)? (intval($etudiant->annee_academique)-1)."-".$etudiant->annee_academique:'' ?>
                                </p>
                                <p class="text-muted mb-0  p-0"><b>Faculté : </b>
                                    <?php echo isset($etudiant,$etudiant->etablissement)? $etudiant->etablissement:'-' ?>
                                </p>
                                <p class="text-muted mb-0 p-0"><b>Année d'étude : </b>
                                    <?php echo isset($etudiant,$etudiant->annee_etude)? ($etudiant->annee_etude):'-' ?>
                                </p>
                                <?php
                               
                                if(isset($etudiant,$etudiant->date_validation1,$etudiant->date_validation2)){
                                ?>
                                <p class="text-muted mb-0 p-0"><b>Validation : </b>
                                    <?php
                                     echo ($etudiant->date_validation1!="" && $etudiant->date_validation2!="")?"<span class='badge badge-primary px-2'>Validée le ".date('d/m/Y', strtotime($etudiant->date_validation2))."</span>":"<span class='badge badge-secondary  px-2'>Non validée</span>";
                                     /*if ($etudiant->date_validation1!="" && $etudiant->date_validation2!="")
                                     { 
                                         echo "Inscription validé le : ".date('d m Y', strtotime($etudiant->date_validation2));
                                     }else{
                                         echo 'Inscription non validé';
                                         } 
                                         */
                                    ?>
                                </p>
                                <?php
                                }
                                ?>

                            </div>
                             <?php 
                             
                             if(isset($R_final) && count($R_final)  && $R_final->moyenne){
                             ?>
                            <div class="text-muted mt-0 mb-0">
                                
                                <b>Moyenne du semestre <?php echo $semestre?> :</b>
                                <?php echo  ($R_final->moyenne=='-1')?'Défaillant':$R_final->moyenne;?>
                            </div>
                             <?php 
                                }
                             ?>
                           



                        </div>
                    </div>
                    <?php
                   
                    if(isset($R_final,$R_final->semestre) && ($R_final->semestre=='2' || $R_final->semestre=='4' || $R_final->semestre=='6') ){
                    ?>
                    <div class="card my-3">
                        <div class="card-body">
                            <h4 class="h6">Résultat annuel</h4>
                            <?php
                               // print_r($R_final);
                            ?>
                            <?php
                            if(isset($R_final) && count($R_final) > 0){
                                $t=isset($R_final->nbre_ue_validee) && intval($R_final->nbre_ue_validee)>1?"s":'';
                             ?>
                            <hr>
                            <div class="text-muted">
                                <b>Total UE Validé :</b>
                                <?php 
                                   // print_r($R_final);
                                ?>
                                <?php echo  (isset($R_final,$R_final->nbre_ue_validee) && count($R_final)  && $R_final->nbre_ue_validee!='')? $R_final->nbre_ue_validee :"-";?>
                            </div>
                            <?php
                             }
                             ?>
                            <div class="text-muted mt-0 mb-0">
                                <b>Moyenne :</b>
                                <?php echo  (isset($R_final,$R_final->moyenne_annuelle) && count($R_final)  && $R_final->moyenne_annuelle!='')?($R_final->moyenne_annuelle=='-1')?'Défaillant':$R_final->moyenne_annuelle:"-"?>
                            </div>
                            
                            <p class="text-muted mb-0">
                                <b>Décision du Jury : </b>
                            <?php
                             if(isset($R_final,$R_final->decision_jury) && count($R_final) && ($R_final->decision_jury=="REFUSE(E)" OR strtolower($R_final->decision_jury)=="refusé(e)" OR strtolower($R_final->decision_jury)=="refuse(e)" OR strtolower($R_final->decision_jury)=="refuse" OR strtolower($R_final->decision_jury)=="refusé")){
                            ?>
                                <span class="badge badge-danger font-size-12 py-1 px-2"> <i
                                        class="mdi mdi-arrow-up-bold me-1"></i> <?php echo $R_final->decision_jury; ?>
                                </span>
                                <?php
                            }else{
                            ?>
                                <span class="badge badge-success py-1 px-2"> <i class="mdi mdi-arrow-up-bold me-1"></i>
                                    <?php echo $R_final->decision_jury; ?>
                                </span>
                                <?php
                             }
                            ?>
                            </p>

                            
                        </div>
                    </div>
                    <?php
                   }
                    ?>
                   <div class="py-3 d-none-print">
                       <a class="btn btn-default bg-white shadow d-xs-block d-sm-inline d-md-inline  d-none-print " href="../"> <i class="far fa-arrow-left" aria-hidden="true"></i> Retour à l'accueil</a>
                   </div>
                </div> <!-- end col-->
                
                <div class="col-md-8 col-xl-8 mt-2 print-w2">
                <?php
                if(isset($validation) && $validation==0){
                ?>
                <div class="alert alert-warning shadow d-flex align-items-center p-5" role="alert">
                <div class="pull-left">
                  
                </div>   
                <div>
                        <b class="h4"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi mt-n1 mr-2 bi-exclamation-triangle-fillflex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </svg>Inscription non validée</b>
                        <p class="lead">Pour accéder à votre résultat, vous devez valider votre inscription. <i> Veuillez vous rapprocher de la scolarité centrale pour plus d'informations.</i></p>
                        <i>Merci !</i>
                        <div class="py-3">
                            <a href="../">Réessayer </a>
                        </div>
                    </div>
                </div>
            <?php
                    }else{
                ?>
                    <div class="mt-1 text-left card p-2 pt-3 bg-dark shadow_ text-white">
                        <ul class="list-inline main-chart row px-3 text-center text-sm mb-0">
                            <li class="list-inline-item col-sm chart-border-left  me-0 border-0">
                                <h3 class="text-white  h6"><span data-plugin="counterup">Statut: </span><span
                                        class="text-warning d-inline-block font-size-15 ms-3"><?php echo isset($etudiant,$etudiant->statut)?$etudiant->statut:'-'?></span>
                                </h3>
                            </li>
                            <?php if(isset($R_final) && $R_final && $R_final->nbre_ue_valide){ ?>
                            <li class="list-inline-item col-sm chart-border-left me-0">
                                <h3 class="h6 text-white text-uppercases"><span data-plugin="counterup">UE Validées
                                        :</span><span
                                        class="text-warning d-inline-block font-size-15 mx-2"><?php echo  $R_final->nbre_ue_valide;?></span>
                                </h3>
                            </li>
                            <?php }
                            if(isset($R_final) && $R_final && $R_final->moyenne && 0=="1"){
                            ?>
                            <li class="list-inline-item col-sm chart-border-left me-0">
                                <h3 class="h6"><span class="text-white">Moyenne :</span><span
                                        class="text-warning d-inline-block font-size-15 mx-2"><?php echo  $R_final->moyenne;?>
                                    </span>
                                </h3>
                            </li>
                            <?php 
                            }
                            ?>

                        </ul>
                    </div>
                    <div class=" mt-1  ">
                        <table class="table border print-table rounded-lg table-responsive  mt-1 w-100 table-borderless mt-2">
                            <thead>
                                <tr class="border-bottom  text-uppercase">
                                    <th class="d-none d-sm-block"></th>
                                    <th> UE</th>
                                    <th >Crédit</th>
                                    <th>ECU</th>
                                    <th  class="text-center">Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                               
                                $tout=count($R_Credits);
                                if(isset($R_Credits) && (count($R_Credits))){
                                    $tue=[];
                                    $t=0;
                                    $tue[$credit->code_ue]=0;
                                    foreach($R_Credits as $credit){
                                        $tue[$t++]=$credit->code_ue;
                                        $note[$credit->code_ue][$t]+=$credit->note_ecu;
                                        //$tue[$credit->code_ue]=$tue[$credit->code_ue]+$credit->note_ecu;
                                        $nb=(array_count_values($tue));
                                        //print_r($nb);
                                       // exit("");
                                       $a=intval($t-2);
                                       $b=intval($t-1);
                                       $border=$icone="";
                                       if($tue[$a]!=$tue[$b]) {
                                        $border="border-top";
                                        $icone=' <i class="far fa-2x fa-fw fa-graduation-cap d-none-print m-1 text-secondary"></i>';
                                       }
                            ?>
                             <?php
                                                if($tue[$a]!=$tue[$b] && isset($note[$credit->code_ue]) && ($t!="1")) {
                                                    // echo "======a=".$a."====b=".$b."=====";
                                                   // echo $tue[$a]."!=".$tue[$a-1];
                                                   //print_r($note);
                                                  
                                                      $prevUE=$tue[$a];
                                                      $total =array_sum($note[$prevUE]);
                                                      $nb_note =count($note[$prevUE]);
                                                      $moy=$total/$nb_note;   
                                                      $ctclass=($nb_note>1)?'moyenne-ue':'mb-2 pl-3';
                                                      $bclass=(intval($moy)>=10)?'badge-success':'badge-danger';
                                        
                                    ?>
                                     <tr class="bg-white ">
                                            <td class="p-0 m-0  d-none d-sm-block">&nbsp;</td>
                                            <td colspan="4" class="text-uppercase mt-n3 p-0 m-0">
                                            <div class="<?php echo $ctclass; ?>">
                                                <b >Moyenne UE : </b> <span class="badge text-md p-1 <?php echo $bclass; ?> note"><?php
                                            echo $moy;?></span>
                                            </div>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                ?>
                                <tr class="bg-white <?php echo $border;?>">
                                    <td class="d-none d-sm-block">
                                        <div class="media d-none d-sm-block align-items-center">
                                        <?php echo $icone;?>
                                            <div class="media-body ml-2">
                                                <span
                                                    class="h6 mb-0"><?php // (isset($credit->etablissement))?$credit->etablissement:'-' ?></span>
                                            </div>
                                        </div>
                                       
                                    </td>
                                    <td class="align-middle <?php if($tue[$a]!=$tue[$b]) {echo "print-bb-0";}?>" rowspans="<?php if($tue[$a]!=$tue[$b]) { echo (isset($nb[$credit->code_ue]))?$nb[$credit->code_ue]:'0' ;}?>">
                                        <?php 
                                        //echo $a."====".$b;
                                        //    print_r($tue);
                                            //echo "rowspan=".$nb[$credit->code_ue];
                                            //if($tue[$a]!=$tue[$b]) {echo "print-bb-0";}
                                            if($tue[$a]!=$tue[$b]) {
                                        ?>
                                        <b><?php echo (isset($credit->code_ue))?$credit->code_ue:'-' ?></b> 

                                       
                                        <div
                                            class="text-muted text-sm  ln-min"><?php echo (isset($credit->libelle_ue))?($credit->libelle_ue):'-' ?>
                                        </div>
                                        <?php 
                                        }
                                       // echo $credit->note_ecu."".$a."=".$b;
                                       
                                        
                                       
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php                                         
                                        if($tue[$a]!=$tue[$b]) {
                                        ?>
                                        <div class="py-2">
                                        <?php echo (isset($credit->credit_ue))?$credit->credit_ue:'-' ?>
                                        </div>
                                        <?php 
                                       }
                                        ?>
                                    </td>
                                    <td class="align-middle">
                                        <b><?php echo (isset($credit->code_ecu))?$credit->code_ecu:'-' ?></b> 
                                        <div
                                            class="text-muted ln-min  text-sm"><?php echo (isset($credit->libelle_ecu))?($credit->libelle_ecu):'-' ?>
                                    </div>
                                    </td>
                                    <td class="text-center">
                                        <?php $badge= (isset($credit->note_ecu) && intval($credit->note_ecu)>10)? "b-success" : 'b-secondary'; ?>
                                        <div class="py-2">
                                        <div
                                            class="badge text-md p-1 border  note <?php echo $badge;?>">
                                            <span class="mt-n1"><?php echo (isset($credit->note_ecu))?($credit->note_ecu=='-1')?'Défaillant':$credit->note_ecu:'-' ?></span>
                                        </div>
                                    
                                        </div>
                                       </td>
                                </tr>
                                
                                <!--tr class="bg-light">
                                <td colspan="5">
                                </td>
                            </tr-->
                                <?php
                                 
                                } 

                                    if(isset($note[$credit->code_ue]) && ($t==$tout)) {
                                        // echo "======a=".$a."====b=".$b."=====";
                                        // echo $tue[$a]."!=".$tue[$a-1];
                                        //print_r($note);
                                        
                                            $prevUE=$tue[intval($a+1)];
                                            $total =array_sum($note[$prevUE]);
                                            $nb_note =count($note[$prevUE]);
                                            $moy=$total/$nb_note;   
                                            $bclass=(intval($moy)>=10)?'badge-success':'badge-danger';
                                            $ctclass=($nb_note>1)?'moyenne-ue':'mb-2 pl-3';
                                        
                                    ?>
                                     <tr class="bg-white ">
                                            <td class="p-0 m-0  d-none d-sm-block">&nbsp;</td>
                                            <td colspan="4" class="text-uppercase mt-n3 p-0 m-0">
                                            <div class="<?php echo $ctclass; ?>">
                                                <b >Moyenne UE : </b> <span class="badge text-md p-1 <?php echo $bclass; ?> note"><?php
                                            echo $moy;?></span>
                                            </div>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                
                                }else{
                            ?>
                                 <tr class="bg-white border-bottom">
                                    <td class="text-center p-3" colspan="5">
                                        Aucune Unité d'Enseignement(UE) disponible pour le moment
                                    </td>
                                </tr>
                             <?php
                                 
                                }
                            
                            ?>


                            </tbody>
                        </table>

                    </div>
                    <?php
                    }
                ?>
                </div>
            </div>
        </div> <!-- end col-->


        </div>
        </div>
        <?php
                    }else{
                ?>
        <div class="row">
            <div class="col-auto mx-auto col-md-7 col-xl-6 py-3">
                <div>
                    <div class="card bg-white">
                        <div class="card-body text-dark">
                            <h4 class="display-6 text-center">ACCEDER A MON RESULAT</h4>
                            <hr>
                            <form method="GET" action="index.php">
                                <div class="form-group mt-3">
                                    <?php
                                    if(isset($_GET['requete'])){
                                    ?>
                                    <div class="alert alert-danger" role="alert">
                                        <strong>Oups!</strong> <br> Il n'existe aucun résultat correspondant aux renseignements fournis. Si le problème persiste, veuillez <a
                                            class="font-weight-bolds text-primary" href="../#contact">nous
                                            contacter</a>.
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <?php
                                    }
                                    ?>
                                    <div class="row g-2   ">
                                        <label for="marticule" class="col-4  text-md-right text-left">Marticule
                                            :</label>
                                        <div class="col-8">
                                            <input type="text"
                                                value="<?php echo isset($_GET["matricule"])?(($_GET["matricule"])):NULL;?>"
                                                required name="matricule" class="form-control text-md" id="marticule"
                                                placeholder="Entrez votre matricule">
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-2  align-items-center">
                                    <div class="col-4 text-md-right text-left">
                                        <label for="annee">Année académique :</label>
                                    </div>
                                    <div class="col-8">
                                        <select required name="annee_academique" class="form-control  block text-md "
                                            id="annee">
                                            <option
                                                <?php echo (isset($_GET["annee_academique"]) && $_GET["annee_academique"]=="2020-2021") ?"selected='selected'":NULL;?>
                                                value="2020-2021">2020-2021</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="form-group mt-3">
                                    <div class="row g-2 ">
                                        <label for="entite" class="col-4  text-md-right text-left">Entité :</label>
                                        <div class="col-8">
                                            <select name="entite" required class="form-control  block text-md"
                                                id="entite">
                                                <option
                                                    <?php echo (isset($_GET["entite"]) && $_GET["entite"]=="FLASH") ?"selected='selected'":NULL;?>
                                                    value="FLASH">FLASH</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group mt-3">
                                        <div class="row g-2   ">
                                            <label for="annee" class="col-4  text-md-right text-left">Année d'étude
                                                :</label>
                                            <div class="col-8">
                                                <select required name="annee_d_etude"
                                                    class="form-control  block text-md" id="annee">
                                                    <option
                                                        <?php echo (isset($_GET["annee_d_etude"]) && $_GET["annee_d_etude"]=="1") ?"selected='selected'":NULL;?>
                                                        value="1">1ère Année</option>
                                                    <option
                                                        <?php echo (isset($_GET["annee_d_etude"]) && $_GET["annee_d_etude"]=="2") ?"selected='selected'":NULL;?>
                                                        value="2">2ème Année</option>
                                                    <option
                                                        <?php echo (isset($_GET["annee_d_etude"]) && $_GET["annee_d_etude"]=="3") ?"selected='selected'":NULL;?>
                                                        value="3">3ème Année</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mt-3">
                                        <div class="row g-2   ">
                                            <label for="semestre" class="col-4  text-md-right text-left">Résultat de
                                                quel semestre ?</label>
                                            <div class="col-8">
                                                <select required name="semestre" class="form-control  block text-md"
                                                    id="semestre">
                                                    <option
                                                        <?php echo (isset($_GET["semestre"]) && $_GET["semestre"]=="1") ?"selected='selected'":NULL;?>
                                                        value="1">1er Semestre (1ère Année)</option>
                                                    <option
                                                        <?php echo (isset($_GET["semestre"]) && $_GET["semestre"]=="2") ?"selected='selected'":NULL;?>
                                                        value="2">2ème Semestre (1ère Année)</option>

                                                    <option
                                                        <?php echo (isset($_GET["semestre"]) && $_GET["semestre"]=="3") ?"selected='selected'":NULL;?>
                                                        value="3">3ème Semestre (2ème Année)</option>
                                                    <option
                                                        <?php echo (isset($_GET["semestre"]) && $_GET["semestre"]=="4") ?"selected='selected'":NULL;?>
                                                        value="4">4ème Semestre (2ème Année)</option>
                                                    <option
                                                        <?php echo (isset($_GET["semestre"]) && $_GET["semestre"]=="5") ?"selected='selected'":NULL;?>
                                                        value="5">5ème Semestre (3ème Année)</option>
                                                    <option
                                                        <?php echo (isset($_GET["semestre"]) && $_GET["semestre"]=="6") ?"selected='selected'":NULL;?>
                                                        value="6">6ème Semestre (3ème Année)</option>
                                                    <!--option
                                                        <?php echo (isset($_GET["semestre"]) && $_GET["semestre"]=="tout") ?"selected='selected'":NULL;?>
                                                        value="0">Tout</option-->

                                                    <!--option
                                                            <?php echo (isset($_GET["semestre"]) && $_GET["semestre"]=="3") ?"selected='selected'":NULL;?>
                                                            value="3">Toute l'année</option-->
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mt-3">
                                        <div class="row g-2   ">
                                            <label for="date_naissance" class="col-4  text-md-right text-left">Votre
                                                date de naissance ?</label>
                                            <div class="col-8">
                                                <div class="input-group  mb-3">
                                                    <input type="date" id="date_naissance" placeholder="AAAA-MM-DD"
                                                        value="<?php echo isset($_GET["date_naissance"])?(trim($_GET["date_naissance"])):NULL;?>"
                                                        name="date_naissance" required="required" require
                                                        placeholder="AAAA-MM-JJ" class="form-control text-md">
                                                    <div class="input-group-texts">
                                                        <span class=" input-group-text__ fafa-calendar"></span>
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" name="requete"
                                        class="btn btn-success btn-block btn-lg mb-2">Soumettre ma requête </button>
                                    <div class="text-center text-md mt-3">
                                        Avez-vous un problème ? <a href="../#contact">Envoyez-nous un message</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
                    }
                
                ?>
        </div>
    </main>

    <footer role="contentinfo" class="py-3 border-top d-none-print d-print-none pt-5 lh-1 bg-white">
        <div class="container">
            <div class="row">
                <div class="col-md-2">

                </div>
                <div class="col-md-10">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <h4 class="h6">Information</h4>
                            <address>
                                <ul class="list-unstyled">
                                    <li>
                                        <a href="http://www.univ-parakou.bj/">Université de Parakou (UP)</a><br>
                                        <i class="far fa fa-envelope-open"></i> <a href="mailto:contact.up@gouv.bj">
                                            contact.up@gouv.bj</a><br>
                                        <i class="far fa fa-envelope"></i> BP: 123 Parakou<br>
                                        <i class="far fa fa-map"></i> Quartier Arafath, Parakou<br>
                                    </li>
                                </ul>
                            </address>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <h4 class="h6">Plus sur l'UP</h4>
                            <ul class="list-unstyled">
                                <li><a target="_blank" href="http://www.univ-parakou.bj/">Le site Web Officiel</a>
                                </li>
                                <li><a target="_blank" href="http://rps.univ-parakou.bj">Le Répertoire des
                                        publications Scientifiques</a></li>
                                <li><a target="_blank" href="http://www.univ-parakou.bj/activites">Actualités</a>
                                </li>
                                <li><a target="_blank" href="http://upblog.univ-parakou.bj">Le Blog</a></li>
                                <li><a target="_blank" href="http://www.univ-parakou.bj/faq">FAQs</a></li>
                            </ul>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <h4 class="h6">UP sur les Réseaux sociaux</h4>
                            <ul class="list-unstyled">
                                <li><a target="_blank" href="https://www.facebook.com/UPBENIN">Facebook</a></li>
                                <li><a target="_blank"
                                        href="https://www.linkedin.com/company/universite-de-parakou-benin">LinkedIn</a>
                                </li>
                                <li><a target="_blank"
                                        href="https://www.youtube.com/c/UNIVERSITEDEPARAKOUWEBTV">Youtube</a></li>
                                <li><a target="_blank" href="http://www.univ-parakou.bj/rss">Flus RSS</a></li>
                            </ul>
                        </div>
                        <div class="col-md-3 col-sm-6">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-12 text-center text-sm">
                    <p class="mb-0"> <span> <span class="text-md mb-4">UP <span
                                    class="text-warning">e</span>Resulat.</span> &copy; 2021 - <a href="#">Université de
                                Parakou</a>.</p>
                </div>
            </div>
        </div>
    </footer>
    <!--[if IE 9]>
<link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/ie.css">
<![endif]-->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!--[if IE 9]>
<link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/ie.css">
<![endif]-->

    <script src="./../assets/plugins/jquery/jquery-1.12.4.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/fr.js"></script>
    <script>
    $("#date_naissance").flatpickr({
        maxDate: 'today',
        'locale': "fr"
    });
    $(function() {

        $('#btntop').click(function() {
            var body = $("html, body");
            body.stop().animate({
                scrollTop: 0
            }, 500, 'swing', function() {});
        });


        $('#btnrequete').click(function(e) {
            e.preventDefault();
            var mat = $('#matricule').val();

            /*if(mat!=nuull){
              $.ajax({
                url:"traitement-eresultat.php";
                type:"GET";
                data:$('#form').serialize() + "&q=resultat&type=all";
                url:"traitement-eresultat.php";
              })
            }*/
        });


    })
    </script>
    <script src="./../assets/js/app.js"></script>
</body>

<!-- Mirrored from robust.bootlab.io/demo-landing-2.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 03 Jul 2021 10:13:43 GMT -->

</html>
