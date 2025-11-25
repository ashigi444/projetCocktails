<?php
session_start();

// cookies qui restent 24h (pour le moment)
if(!isset($_COOKIE['usernameUser']))
    setcookie("usernameUser", "", time()+3600*24);
if(!isset($_COOKIE['lastnameUser']))
    setcookie("lastnameUser", "", time()+3600*24);
if(!isset($_COOKIE['firstnameUser']))
    setcookie("firstnameUser", "", time()+3600*24);
if(!isset($_COOKIE['birthdateUser']))
    setcookie("birthdateUser", "", time()+3600*24);
if(!isset($_COOKIE['genderUser']))
    setcookie("genderUser", "", time()+3600*24);
if(!isset($_COOKIE['favoriteRecipes']))
    setcookie("favoriteRecipes", "", time()+3600*24);

require_once "utils/utils.php";
require_once "utils/utilsFavorites.php";

// creation des tableaux de messages pour l'utilisateur
$messages=[];
$messagesErrors=[];

// selection de la recherche (si elle existe)
$search = isset($_GET['search']) ? $_GET['search'] : '';

// selection de la page (si elle existe)
// Par defaut, la page est 'navigation' avec Aliment comme aliment courant (cf. sujet)
$page = (isset($_GET['page']) && !empty(trim($_GET['page']))) ? trim($_GET['page']) : 'navigation';

// selection de l'action (POST ou GET pour les favoris)
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : null);

// verification du statut de connexion
$connectionStatus=(isset($_SESSION['user']) && !empty($_SESSION['user'])) ? true : false;

// traitement des actions et formulaires
if (isset($action)) { // Si une action a ete demandee, on cherche de quelle action il s'agit
    if($action == "toggleFavorite" && isset($_GET['recipeId'])){ // Gestion des favoris
        // gestion de favoris
        $recipeId = intval($_GET['recipeId']);
        $username = $connectionStatus ? $_SESSION['user']['username'] : null;
        toggleFavorite($recipeId, $username);
        // Redirection pour eviter la resoumission
        $redirectPage = isset($_GET['page']) ? $_GET['page'] : 'navigation';

    }else if($action == "logout"){ // Si c'est une deconnexion, on deconnecte
        if($connectionStatus) {
            unset($_SESSION['user']);
            $messages[] = "Vous&nbsp;&ecirc;tes d&eacute;connect&eacute;.";
        }

    }else if(strstr($action, "update") && $connectionStatus){ // Si c'est une mise a jour du profil,
        require "utils/checkUpdate.php";

        $classFields = [];
        $allowsUpdate=false;
        $result = [];

        if($action=="updateLastname"){
            $newLastname=isset($_POST['newLastname']) ? $_POST['newLastname'] : null;
            if(isset($newLastname)){
                $result=checkUpdateLastname($newLastname);
                $allowsUpdate=$result['allowsUpdate'];
                $messages=$result['messages'];
                $messagesErrors=$result['messagesErrors'];
                if($allowsUpdate){
                    applyUpdateFile('lastname', $newLastname);
                    $_SESSION['user']['lastname'] = $newLastname;
                    setcookie("lastnameUser", $newLastname, time()+3600*24);
                }else{
                    $classFields['lastname']=$result['classFields'];
                }
            }
        }else if($action=="updateFirstname"){
            $newFirstname=isset($_POST['newFirstname']) && !empty(trim($_POST['newFirstname'])) ? $_POST['newFirstname'] : null;

            $result=checkUpdateFirstname($newFirstname);
            $allowsUpdate=$result['allowsUpdate'];
            $messages=$result['messages'];
            $messagesErrors=$result['messagesErrors'];
            if($allowsUpdate){
                applyUpdateFile('firstname', $newFirstname);
                $_SESSION['user']['firstname']=$newFirstname;
                setcookie("firstnameUser", $newFirstname, time()+3600*24);
            }else{
                $classFields['firstname']=$result['classFields'];
            }
        }else if($action=="updateBirthdate"){
            $newBirthdate=isset($_POST['newBirthdate']) && !empty(trim($_POST['newBirthdate'])) ? $_POST['newBirthdate'] : null;

            $result=checkUpdateBirthdate($newBirthdate);
            $allowsUpdate=$result['allowsUpdate'];
            $messages=$result['messages'];
            $messagesErrors=$result['messagesErrors'];
            if($allowsUpdate){
                applyUpdateFile('birthdate', $newBirthdate);
                $_SESSION['user']['birthdate']=$newBirthdate;
                setcookie('birthdateUser', $newBirthdate, time()+3600*24);
            }else{
                $classFields['birthdate']=$result['classFields'];
            }
        }else if($action=="updateGender"){
            $newGender=isset($_POST['newGender']) && !empty(trim($_POST['newGender'])) ? $_POST['newGender'] : null;

            $result=checkUpdateGender($newGender);
            $allowsUpdate=$result['allowsUpdate'];
            $messages=$result['messages'];
            $messagesErrors=$result['messagesErrors'];
            if($allowsUpdate){
                applyUpdateFile('gender', $newGender);
                $_SESSION['user']['gender']=$newGender;
                setcookie("genderUser", $newGender, time()+3600*24);
            }else{
                $classFields['gender']=$result['classFields'];
            }
        }else{
            $messagesErrors[] = "Une erreur est survenue.";
        }

    }else if(strstr($action, "reset") && $connectionStatus){
        require "utils/checkUpdate.php";
        $infosUser=loadUserInfos($_SESSION['user']['username']);
        if(isset($infosUser) && !empty($infosUser)) {
            if ($action == "resetLastname" && !checkLastnameFile("", $infosUser)) {
                $messages[]=resetLastname();
                unset($_SESSION['user']['lastname']);
                setcookie("lastnameUser", "", time() + 3600 * 24);
            } else if ($action == "resetFirstname" && !checkFirstnameFile("", $infosUser)) {
                $messages[]=resetFirstname();
                unset($_SESSION['user']['firstname']);
                setcookie("firstnameUser", "", time() + 3600 * 24);
            } else if ($action == "resetBirthdate" && !checkBirthdateFile("", $infosUser)) {
                $messages[]=resetBirthdate();
                unset($_SESSION['user']['birthdate']);
                setcookie("birthdateUser", "", time() + 3600 * 24);
            } else if ($action == "resetGender" && !checkGenderFile("", $infosUser)) {
                $messages[]=resetGender();
                unset($_SESSION['user']['gender']);
                setcookie("genderUser", "", time() + 3600 * 24);
            }
        }
    }else if($action == "signup" || $action == "signin"){ // Si c'est une connexion ou inscription
        if(!$connectionStatus) {
            // On recupere les variables necessaires au traitement
            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';
            $lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
            $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
            $birthdate = isset($_POST['birthdate']) ? trim($_POST['birthdate']) : '';
            $gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';

            // Et on creer celle qui permettront de validier ou non l'action,
            //       et de reafficher les champs necessaires en cas d'erreur
            $allCorrect = true;
            $valueFields = ['signinForm' => [], 'signupForm' => []];
            $classFields = ['signinForm' => [], 'signupForm' => []];

            if ($action == "signup") { // Si l'action est une inscription, on traite avec la fonction dediee
                require "utils/checkSignUp.php";
                $resultat = checkSignUp($username, $password, $lastname, $firstname, $birthdate, $gender);

                // Puis on recupere les resultats
                $messages = isset($resultat['messages']) ? $resultat['messages'] : [];
                $messagesErrors = isset($resultat['messagesErrors']) ? $resultat['messagesErrors'] : [];
                $allCorrect = isset($resultat['correctSignup']) ? $resultat['correctSignup'] : false;
                $valueFields = isset($resultat['valueFields']) ? $resultat['valueFields'] : [];
                $classFields = isset($resultat['classFields']) ? $resultat['classFields'] : [];
                $page = isset($resultat['page']) ? $resultat['page'] : $page;

            } else { // Si l'action est une connexion, on traite avec la fonction dediee
                require "utils/checkSignIn.php";
                $resultat = checkSignIn($username, $password);

                // Puis on recupere les resultats
                $messages = isset($resultat['messages']) ? $resultat['messages'] : [];
                $messagesErrors = isset($resultat['messagesErrors']) ? $resultat['messagesErrors'] : [];
                $allCorrect = isset($resultat['correctConnection']) ? $resultat['correctConnection'] : false;
                $valueFields = isset($resultat['valueFields']) ? $resultat['valueFields'] : [];
                $classFields = isset($resultat['classFields']) ? $resultat['classFields'] : [];
                $page = isset($resultat['page']) ? $resultat['page'] : $page;
            }

            // Validation de la connexion (commune a signup + signin)
            if (isset($allCorrect) && $allCorrect) { // Si tout est correct, alors on creer la session
                session_regenerate_id(true);

                $infosUser = loadUserInfos($username); // charge les infos de l'utilisateur depuis le fichier

                if(isset($infosUser) && !empty($infosUser)){
                    // recupere toutes les infos de session utilisateur
                    $_SESSION['user']['username'] = (isset($infosUser['username']) && !empty(trim($infosUser['username']))) ? $infosUser['username'] : null;
                    $_SESSION['user']['lastname'] = (isset($infosUser['lastname']) && !empty(trim($infosUser['lastname']))) ? $infosUser['lastname'] : null;
                    $_SESSION['user']['firstname'] = (isset($infosUser['firstname']) && !empty(trim($infosUser['firstname']))) ? $infosUser['firstname'] : null;
                    $_SESSION['user']['birthdate'] = (isset($infosUser['birthdate']) && !empty(trim($infosUser['birthdate']))) ? $infosUser['birthdate'] : null;
                    $_SESSION['user']['gender'] = (isset($infosUser['gender']) && !empty(trim($infosUser['gender']))) ? $infosUser['gender'] : null;
                    $_SESSION['favoriteRecipes'] = loadFavoritesFromFile($username); // charge les favoris depuis le fichier utilisateur

                    // recopie les infos de session dans les cookies
                    setcookie("usernameUser", (isset($_SESSION['user']['username']) ? $_SESSION['user']['username'] : "") , time()+3600*24);
                    setcookie("lastnameUser", (isset($_SESSION['user']['lastname']) ? $_SESSION['user']['lastname'] : "") , time()+3600*24);
                    setcookie("firstnameUser", (isset($_SESSION['user']['firstname']) ? $_SESSION['user']['firstname'] : "") , time()+3600*24);
                    setcookie("birthdateUser", (isset($_SESSION['user']['birthdate']) ? $_SESSION['user']['birthdate'] : "") , time()+3600*24);
                    setcookie("genderUser", (isset($_SESSION['user']['gender']) ? $_SESSION['user']['gender'] : "") , time()+3600*24);
                    setcookie("favoriteRecipes", (isset($_SESSION['favoriteRecipes']) ? implode(",", $_SESSION['favoriteRecipes']) : "") , time()+3600*24);

                    $messages[] = "Connect&eacute;&nbsp;en tant que&nbsp;" . $_SESSION['user']['username'];

                }else{
                    $messagesErrors[] = "Une erreur est survenue.";
                }
            }
        }
    }else{ // Sinon (au cas ou l'action n'a pas ete trouvee)
        $messagesErrors[] = "Une erreur est survenue. Cette action est impossible actuellement...";
        $messagesErrors[] = "V&eacute;rifiez votre statut de connexion puis r&eacute;essayez.";
    }
}

// selection d'utilisateur si connecte
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

// suppression des messages d'erreurs avec redirection vers la navigation si l'utilisateur vient de creer son compte.
//   permet d'eviter une erreur ainsi qu'une redirection sur le formulaire d'inscription si actualisation de la page apres creation du compte...
if(isset($user)){
    if(($page=="signUp" || (isset($action) && $action=="signup"))) {
        $messagesErrors = []; // Reset le tableau des erreurs
        $page = 'navigation'; // Redirige l'utilisateur sur la navigation
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Gestion de Cocktails</title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
</head>
<body>
<?php
include 'assets/header.php';
include 'assets/nav.php';
include 'assets/main.php';
include 'assets/footer.php';
?>
</body>
</html>
