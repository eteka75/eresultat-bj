<?php
    $error_message=Array();
    if(isset($_POST['sendmessage'],$_POST['nom'],$_POST['tel'],$_POST['email'],$_POST['message']) && !(isset($_COOKIE['sm']))) {
    //$sendMessage=0;
  
      // EDIT THE 2 LINES BELOW AS REQUIRED
      //$email_to = "eresultat@univ-parakou.bj";
      $email_to = "etekawilfried@gmail.com,souroueteka@ymail.com";
      $email_subject = "Le sujet de votre email  - UP eResuultat";       
  
      $nom = $_POST['nom']; // required
      $email = $_POST['email']; // required
      $telephone = $_POST['tel']; // not required
      $message = $_POST['message']; // required
  
      // $error_message = "";
      $email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
  
      if(!preg_match($email_exp,$email)) {
        $error_message[] =  'L\'adresse e-mail que vous avez entrée ne semble pas être valide.<br />';
      }
    
        // Prend les caractères alphanumériques + le point et le tiret 6
        $string_exp = "/^[A-Za-z0-9 .'-]+$/";
    
      if(!preg_match($string_exp,$nom)) {
        $error_message[]= 'Le nom que vous avez entré ne semble pas être valide.<br />';
      }
    
      
      if(strlen($nom) < 3) {
        $error_message[]= "Le nom saisi n'est pas correcte.<br />";
      }
      if(strlen($message) < 10 || strlen($message) > 1500) {
        $error_message[]= 'Le commentaire que vous avez entré ne semble pas être valide (moins de 10 caractères).<br />';
      }
    
      
      $email_message = "Détail du message <hr>.\n\n";
      $email_message .= "Nom et prénoms: ".$nom."\n";
      $email_message .= "Email: ".$email."\n";
      $email_message .= "Commentaire: ".$message."\n";
  
      // create email headers
      $headers = 'From: '.$email."\r\n".
      'Reply-To: '.$email."\r\n" .
      'X-Mailer: PHP/' . phpversion();
      if(count($error_message) <= 0){
        
      mail($email_to, $email_subject, $email_message, $headers);
      $sendMessage=1;
      setcookie("sm", "1");
      
     // header('location: index.php?sendMessage=Yes');
      //exit('');
      }
      
      }
      //exit("hjhjhj".$sendMessage);
  ?>
<!DOCTYPE html>
<html lang="fr">

  <head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">  
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="Wilfried ETEKA">
  <meta name="theme-color" content="#343a40">
  <meta name="theme-color" media="(prefers-color-scheme: light)" content="white">
  <meta name="theme-color" media="(prefers-color-scheme: dark)" content="#343a40">
  <title>eResultat - Consultez vos résultats à Université de Parakou</title>

  <link href="./../assets/css/app.css" rel="stylesheet">
  <link href="./../assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <link href="./../assets/plugins/datepicker/jquery.datepicker2.css" rel="stylesheet">
  <style>
    .bg-overlay-primary{
      background:rgba(255,255,255,0.6);
    }
    .bg-white-5{background:rgba(255,255,255,0.8)}
    @media(max-width:880px){
      .display-3{
        font-size:2.6em !important;
      }
      .display-4{
        font-size:3.2em !important;
      }
    }

    body{
      overflow-x:hidden;
    }
    .form-control{
      border:1px solid #EEEEEE;border-radius:3px;
    }
    #btntop{
      height:30px;
      width:30px;
      line-height:30px;
      text-align:center;
      background:white;
      color:#aaaaaa;
      position:fixed; 
      border:1px solid #aaaaaa;
      border-radius:50%;
      bottom:3%;
      right: 3%;
      z-index: 1000;
    }
@media (max-width:991.98px){.navbar-transparant{background:#343a40 !important}}
  </style>
</head>
<body data-bs-spy="scroll" data-bs-target="#menu">
    <div id="btntop">
      <a href="#enav"> <i class="far  fa-arrow-up" aria-hidden="true"></i></a>
    </div>
    
  <div id="menu">
  <nav id="enav" class="navbar navbar-sm navbar-expand-lg navbar-transparant navbar-dark navbar-absolute w-100">
   
      <div class="container">
      <a class="navbar-brand " href="./"><img height="30px" clas src="../../assets/images/logo-up-eresultat-mini.png" alt="UP-eResultat"></a>
      
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav ml-auto">
          
          <li class="nav-item">
            <a class="nav-link navlink" href="#eresultat-ccm">Comment ça marche ?</a>
          </li>
          <li class="nav-item">
            <a class="nav-link navlink" href="#entites">Les entités éligibles</a>
          </li>
          <li class="nav-item">
            <a class="nav-link navlink" href="#avis">Avis des étudiants</a>
          </li>
          <li class="nav-item">
            <a class="nav-link navlink" href="#contact">Nous contacter</a>
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

  <div class="intro py-8 bg-primary position-relative text-white">
    <div class="bg-overlay-dark text-white">
      <img src="./../assets/images/image-etudiants-Parakou.jpg" class="img-fluid img-cover text-white" alt="Résultat des étdudiants de l'Université de Parakou" />
    </div>
    <div class="intro-content mt-4">
      <div class="container">
        <div class="row">
          <div class="col-sm-12 col-md-5 col-lg-7 col-xl-6 align-self-center text-md-left text-center">
            <h1 class="display-3 mb-3 ">Vos résultats,</h1>
            <h1 class="display-3 fw-light mt-n4 text-warnings text-xs-center text--md-left ">en quelques <span class="text-warning">Cliques...</span></h1>
            
            <p class="lead mb-4 text-md">Cette plateforme vous permet d'accéder à vos résultats semestriels et annuels de l'Université de Parakou. 
                Il vous suffit de renseigner quelques informations liés à votre inscription pour le découvrir.</p>
          </div><!-- /.col-md-6 -->
          <div class="col-md-7 col-lg-5 col-xl-5 col-sm-12 ml-auto">
            <div class="card bg-white">
              <div class="card-body text-dark">
                  <h4 class="display-6 text-center">ACCEDER A MON RESULTAT</h4>
                  <hr>
                  
                  <form method="GET" action="./resultat/">
                    <div class="form-group mt-3">
                        <div class="row g-2   ">
                            <label for="marticule" class="col-4  text-md-right text-left">Marticule :</label>
                            <div class="col-8">
                                <input type="text"  required name="matricule" class="form-control text-md" id="marticule" placeholder="Entrez votre matricule">
                            </div>
                        </div>
                    </div>
                  <div class="row g-2  align-items-center">
                    <div class="col-4 text-md-right text-left">
                        <label for="annee">Année académique :</label>
                    </div>
                    <div class="col-8">
                        <select required name="annee_academique" class="form-control  block text-md " id="annee">
                            <option value="2020-2021">2020-2021</option>
                        </select>
                    </div>
                    
                  </div>                    
                    <div class="form-group mt-3">
                        <div class="row g-2 ">
                            <label for="entite" class="col-4  text-md-right text-left">Entité :</label>
                        <div class="col-8">
                            <select name="entite" required class="form-control  block text-md" id="entite">
                                <option value="FLASH">FLASH</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <div class="row g-2   ">
                            <label for="annee" class="col-4  text-md-right text-left">Année d'étude :</label>
                        <div class="col-8">
                            <select required name="annee_d_etude" class="form-control  block text-md" id="annee">
                                <option value="1">1ère Année</option>
                                <option value="2">2ème Année</option>
                                <option value="3">3ème Année</option>
                            </select>
                        </div>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <div class="row g-2   ">
                            <label for="semestre" class="col-4  text-md-right text-left">Résultat de quel semestre ?</label>
                            <div class="col-8">
                                <select required name="semestre" class="form-control  block text-md" id="semestre">
                                  <option value="1">1er Semestre (1ère Année)</option>
                                  <option value="2">2ème Semestre (1ère Année)</option>                      
                                  <option value="3">3ème Semestre (2ème Année)</option>
                                  <option value="4">4ème Semestre (2ème Année)</option>
                                  <option value="5">5ème Semestre (3ème Année)</option>
                                  <option value="6">6ème Semestre (3ème Année)</option>
                                  <!--option value="0">Tout</option-->

                                </select>
                            </div>
                        </div>
                    </div>                   
                    <div class="form-group mt-3">
                        <div class="row g-2   ">
                            <label for="date_naissance" class="col-4  text-md-right text-left">Votre date de naissance ?</label>
                            <div class="col-8">                               
                                <div class="input-group  mb-3" >
                                  <input  type="text" class="form-control"placeholder="AAAA-MM-DD" required="required" require id="date_naissance" name="date_naissance" class="form-control text-md">
                                  <div class="input-group-texts">
                                      <span class=" input-group-text__ fafa-calendar"></span>
                                  </div>
                              </div>
              

                  </div>
                        </div>
                    </div>                   
                    
                    <button type="submit" name="requete" class="btn btn-success btn-block btn-lg mb-2">Soumettre ma requête </button>
                    <div class="text-center text-md mt-3">
                        Avez-vous un problème ? <a class="navlink" href="#contact">Envoyez-nous un message</a>
                    </div>
                    </div>
                </form>
                
              </div>
            </div>
          </div><!-- /.col-md-6 -->
        </div>
      </div>
    </div>
  </div>

  <main class="main" id="eresultat-ccm" role="main">
    <div class="bg-white  pt-5  ">
      <div class="container">
        
        <div class="row">
          <div class="col-md-12 text-center py-5">
            <h1 class="display-4">Comment ça marche ?</h1>
          </div>
        </div>
        <div class="row mt-2">
          <div class="col-md-6">
            <div class="media">
              <div class="icon mr-3 bg-success">
                <i class="far fa-check"></i>
              </div>
              <div class="media-body">
                <h3 class="h4">Publication de vos résultats</h3>
                <p class="text-dark text-left">
                  Avant d'accéder à votre résultat, il faudrait que votre entité ai publié les résultats de votre formation sur la plateforme.
                </p>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="media">
              <div class="icon mr-3 bg-warning">
                <i class="far fa-list light"></i>
              </div>
              <div class="media-body">
                <h3 class="h4">Renseigner mes informations</h3>
                <p class="text-dark text-left">
                  Vous n'avez pas besoins d'avoir un compte. <br> Renseignez juste quelques informations relatives à votre formation.
                  
                </p>
              </div>
            </div>
          </div>
          <div class="col-md-6 mt-4 ">
            <div class="media">
              <div class="icon mr-3 ">
                <i class="far fa-box"></i>
              </div>
              <div class="media-body">
                <h3 class="h4">Découvrez vos résultats</h3>
                <p class="text-dark text-left">
                  Visualisez vos résulats semestriels et annuel avec les UE et ECU validés au cours de l'année academique choisie.
                  
                  
                </p>
              </div>
            </div>
          </div>
          <div class="col-md-6 mt-4">
            <div class="media">
              <div class="icon mr-3 bg-danger">
                <i class="far fa-comments"></i>
              </div>
              <div class="media-body">
                <h3 class="h4">Aide et support</h3>
                <p class="text-dark text-left">
                  Vous pouvez à tout moment nous contacter ou faire une demande de réclammation au niveau de votre entité.
                  
                </p>
              </div>
            </div>
          </div>
        </div>
        
      </div>
    </div>
    <div class="row">
          <div class="col-md-12 text-center pt-7 mb-4">
          <a class="navlink d-sm-none" href="#entites"><h1 class="h3"> <i class="far  fa-arrow-down" aria-hidden="true"></i></h1></a>
          </div>
        </div>

    <div class="bg-light py-3" id="entites">
      <div class="container mt-6">
          <div class="row">
            <div class="col-md-12 mx-auto">
              <h1 class="h1 text-center ">Les entités de formation éligibles</h1>
              <p class="lead text-center mb-4">
                Seuls les étudiants des entités de formations et de Recherche suivantes peuvent actuellement consulter leurs résultats via la plateforme.

              </p>

              <div class="row justify-content-center mt-5 mb-4">
                <div class="col-auto">
                  <nav class="nav btn-group">
                    <a href="#facultes" class="btn btn-outline-primary active" data-toggle="tab">FACULTES</a>
                    <a href="#ecoles" class="btn btn-outline-primary" data-toggle="tab">ECOLES</a>
                  </nav>
                </div>
              </div>

              <div class="tab-content">
                <div class="tab-pane fade mt-5 show active" id="facultes">
                  <div class="row  equal-height-cardss">
                    <div class="col-md-6">
                      <div class="card text-left">
                        <div class="card-body d-flexsflex-column">
                          <div class="row">                           
                            <div class="col-md-5">                           
                            <img src="./../assets/images/entites/FLASH.jpg"  class="embed-responsive p-3my-5" alt="FLASH Parakou">
                            </div>
                            <div class="col-md-7 py-4">
                            <h5 class="text-muted m-0">FLASH</h5>
                            <h6>Facultés des Lettres Arts et Sciences Humaines</h6>
                            <ul class="list-unstyled">
                              <li class="mb-2 text-success">
                              <i class="fa fa-calendar-check" title="Dernière mise à jour"></i> 15 Juillet 2021
                              </li>
                              <li class="mb-2 h6 text-md text-muted"  title="Nombre de consultations">
                                1222 Consultations
                              </li>
                            </ul>
                          </div>
                          </div>
                        </div>
                      </div><!-- /.card -->
                    </div>
                    <div class="col-md-6">
                      <!--div class="card text-left">
                        <div class="card-body d-flexsflex-column">
                          <div class="row">                           
                            <div class="col-md-5">                           
                            <img src="./../assets/images/entites/FDSP.png"  class="embed-responsive p-2" alt="FDSP Parakou">
                            </div>
                            <div class="col-md-7 py-4">
                            <h5 class="text-muted m-0">FDSP</h5>
                            <h6>Facultés de Droit et de Science Politique</h6>
                            <ul class="list-unstyled">
                              <li class="mb-2 text-success">
                              <i class="fa fa-calendar-check" title="Dernière mise à jour"></i> 6 Juillet 2021
                              </li>
                              <li class="mb-2 h6 text-md text-muted"  title="Nombre de consultations">
                                454 Consultations
                              </li>
                            </ul>
                          </div>
                          </div>
                        </div>
                      </div--><!-- /.card -->
                    </div>
                  </div><!-- /.row -->
                </div><!-- /.tab-pane -->
                <div class="tab-pane fade" id="ecoles">
                  <div class="row py-4 equal-height-cardss">
                   
                    <div class="col-md-12">
                      <div class="card py-5 text-center">
                        <div class="card-body d-flex flex-column">
                         <h2 class="h1">Aucune écoles pour le moment</h2>
                        </div>
                      </div><!-- /.card -->
                    </div>
                    
                  </div><!-- /.row -->
                </div><!-- /.tab-pane -->
              </div>
            </div>
          </div><!-- /.row -->
        </div>
    
    
        <div class="row">
          <div class="col-md-12 text-center pt-6 pb-O">
          <a class="navlink d-sm-none" href="#avis"><h1 class="h3"> <i class="far  fa-arrow-down" aria-hidden="true"></i></h1></a>
          </div>
        </div>
       </div>
    
    

    <div class="py-8 bg-danger text-white" id="avis">
      <div class="container">
        <div class="text-center mb-5">
          <h3 class="display-4">Avis de nos étudiants</h3>
        </div>
      </div>
      <div class="p-4 d-none" data-flickity='{ "prevNextButtons": false, "wrapAround": true,"autoPlay":8400 }'>
        <div class="carousel-cell">
          <div class="container">
            <div class="row">
              <div class="col-md-10 mx-auto">
                <div class="media">
                  <img src="./../assets/images/entites/FDSP.png" alt="Avatar" class="img-fluid rounded-circle mr-4" style="max-width:128px;" />
                  <div class="media-body">
                    <blockquote class="h3 font-weight-normal">
                      “Lorem ipsum dolor sit amet, consectetur adipisicing elit. Officia iure quibusdam vero quia opti.”
                    </blockquote>
                    <span>- Jane Roe</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="carousel-cell">
          <div class="container">
            <div class="row">
              <div class="col-md-10 mx-auto">
                <div class="media">
                  <img src="./../assets/images/entites/FDSP.png" alt="Avatar" class="img-fluid rounded-circle mr-4" style="max-width:128px;" />
                  <div class="media-body">
                    <blockquote class="h3 font-weight-normal">
                      “Lorem ipsum dolor sit amet,consectetur adipisicing elit. Officia iure quibusda ! ”
                    </blockquote>
                    <span>John Roe</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="carousel-cell">
          <div class="container">
            <div class="row">
              <div class="col-md-10 mx-auto">
                <div class="media">
                  <img src="./../assets/images/entites/FDSP.png" alt="Avatar" class="img-fluid rounded-circle mr-4" style="max-width:128px;" />
                  <div class="media-body">
                    <blockquote class="h3 font-weight-normal">
                      “  “Lorem ipsum dolor sit amet,consectetur adipisicing elit. Officia iure quibusda ! Quick & helpful support! ”
                    </blockquote>
                    <span>Jane Roe</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  
    <div class="bg-light" id="contact">
      <div class="container-fluid p-0">
        <div class="row no-gutters vh-80">
          <div class="col-12 col-sm-6 order-sm-2 bg-white d-flex justify-content-center align-items-center">
            <img src="./../assets/images/image-etudiants-Parakou.jpg" class="img-fluid img-cover" alt="UP eResultat">
          </div>
          <div class="col-12 col-sm-6 bg- order-sm-2 bg-light d-flex position-relative justify-content-center align-items-center">
            <div class="p-3 p-md-4 p-lg-8 w-100">
           
           
                <p>
                  <?php
                    if(isset($error_message) && count($error_message) > 0){
                  ?>
                   <h2 class="h4">Message envoyé</h2>
               
                  <div class="alert alert-danger">
                    <b>Oups !</b>
                    <div class="text-md">
                      Veuillez corriger les erreurs suivantes: 
                    </div>
                    <ul>
                      
                    <?php

                    foreach ($error_message as $key => $value) {
                     echo "<li><b>$value</b></li>";
                    }
                     ?>
                      </ul>
                  </div>
                  <?php
                    }
                  ?>
                </p>
                <?php
                  if(isset($_COOKIE['sm']) && $_COOKIE['sm']=="1"){
                    //exit('jkk');
                  ?>
                  <div class="py-6">
                  <div class="alert alert-success d-flex p-4 align-items-center" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"></use></svg>
                    <div>
                     <h4>Bravo !</h4>
                     <p>Votre message a été envoyé avec succès. Notre équipe technique va vous contacter par email, après aanalyse de votre message</p>
                     <div>Merci :) </div>
                    </div>
                  </div>
                  </div>
                
                <?php
                  }else{
                  ?>
                   <h2 class="h3">Laissez-nous un message</h2>
                <p class="lead text-md">
                 Si vous rencontrez des difficultés sur la plateforme, veuillez le faire savoir à l'équipe technique et en étant claire et précis. Merci.
                </p>
                <form method="POST" action="index.php#contact">
                <div class="form-group">
                  <label for="nom">Nom et prénoms : *</label>
                  <input type="text" class="form-control" value="<?php if (
isset($_POST['nom'])) echo htmlspecialchars($_POST['nom']);?>" required id="nom" aria-describedby="emailHelp" name="nom" placeholder="John MOLENE">
                  </div>
                  <div class="rows">
                    <div class="col-md-6s">
                      <div class="form-group">
                        <label for="email">Adresse Email : *</label>
                        <input type="email" name="email"  required value="<?php if (
isset($_POST['email'])) echo htmlspecialchars($_POST['email']);?>" class="form-control" id="email" aria-describedby="emailHelp" placeholder="nomprenom@gmail.com">
                      </div>
                    </div>
                    <div class="col-md-6s">
                      <div class="form-group">
                        <label for="tel">Téléphone :</label>
                        <input type="tel"  name="tel" value="<?php if (
isset($_POST['tel'])) echo htmlspecialchars($_POST['tel']);?>" class="form-control" id="tel" aria-describedby="emailHelp" placeholder="+229 96553344">
                      </div>
                    </div>
                  </div>
               
                
                <div class="form-group">
                  <label for="message">Message : *</label>
                  
                  <textarea name="message" id="message" class="form-control" rows="3" required="required"><?php if (
isset($_POST['message'])) echo htmlspecialchars($_POST['message']);?> </textarea>
                  
                  <small id="emailHelp" class="form-text text-muted">Nous ne partagerons votre adresse email, ni votre contact avec personne.</small>
                </div>
                <div class="form-group  text-left">
                <button type="submit" name="sendmessage" class="btn btn-primary px-3">Envoyer </button>
                </div>
                                               

              



                            
              </form>
              <?php
                  }
                  ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <footer role="contentinfo" class="py-3 mt-5 lh-1 bg-white">
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
                  <i class="far fa fa-envelope-open"></i> <a href="mailto:contact.up@gouv.bj"> contact.up@gouv.bj</a><br>
                   <i class="far fa fa-envelope"></i> BP: 123 Parakou<br>
                  <i class="far fa fa-map"></i> Quartier Arafath, Parakou<br>
    							</li>
    						</ul>
    					</address>
            </div>
            <div class="col-md-3 col-sm-6">
              <h4 class="h6">Plus sur l'UP</h4>
              <ul class="list-unstyled">
                <li><a target="_blank" href="http://www.univ-parakou.bj/">Le site Web Officiel</a></li>
                <li><a target="_blank" href="http://rps.univ-parakou.bj">Le Répertoire des publications Scientifiques</a></li>
                <li><a target="_blank" href="http://www.univ-parakou.bj/activites">Actualités</a></li>
                <li><a target="_blank" href="http://upblog.univ-parakou.bj">Le Blog</a></li>
                <li><a target="_blank" href="http://www.univ-parakou.bj/faq">FAQs</a></li>
              </ul>
            </div>
            <div class="col-md-3 col-sm-6">
              <h4 class="h6">Retrouvez-nous sur les Réseaux sociaux</h4>
              <ul class="list-unstyled">
                <li><a  target="_blank" href="https://www.facebook.com/UPBENIN">Facebook</a></li>
                <li><a  target="_blank" href="https://www.linkedin.com/company/universite-de-parakou-benin">LinkedIn</a></li>
                <li><a  target="_blank" href="https://www.youtube.com/c/UNIVERSITEDEPARAKOUWEBTV">Youtube</a></li>
                <li><a  target="_blank" href="http://www.univ-parakou.bj/rss">Flus RSS</a></li>
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
          <p class="mb-0"> <span> <span class="text-sm mb-4">UP <span class="text-warning">e</span>Resulat.</span> &copy; 2021 - <a href="#">Université de Parakou</a>.</p>
        </div>
      </div>
    </div>
  </footer>
  <!--[if IE 9]>
<link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/ie.css">
<![endif]-->

  <script src="./../assets/plugins/jquery/jquery-1.12.4.js" ></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/fr.js"></script>
<script >
  $("#date_naissance").flatpickr({ maxDate: 'today','locale':"fr"});
$(function(){
      
      $('#btntop').click(function(){
        var body = $("html, body");
          body.stop().animate({scrollTop:0}, 500, 'swing', function() {        
          });
      });
      $('a.navlink').click(function(e){
        e.preventDefault();
        var lid=$(this).attr('href');
            $('html, body').animate({
            scrollTop: $(lid).offset().top
          }, 1000);
      });

      $('#btnrequete').click(function(e){
        e.preventDefault();
        var mat=$('#matricule').val();
        
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
