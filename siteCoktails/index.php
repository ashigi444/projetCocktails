<?php
session_start();
if(!isset($_COOKIE['user'])) {
    setcookie("user", "", time() + 3600 * 24); // cookies qui reste 24heures pour le moment
}

require_once "utils/utils.php";
require_once "utils/utilsFavorites.php";

// creation des tableaux de messages pour l'utilisateur
$messages=[];
$messages_errors=[];

// selection de page
$page = (isset($_GET['page']) && !empty(trim($_GET['page']))) ? trim($_GET['page']) : 'accueil';
// selection de l'action (POST ou GET pour les favoris)
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : null);
// verification du statut de connexion
$statut_connexion=(isset($_SESSION['user']) && !empty($_SESSION['user'])) ? true : false;
// traitement des actions et formulaires
if (isset($action)) { // Si une action a ete demandee, on cherche de quelle action il s'agit
    if($action == "toggleFavorite" && isset($_GET['recipeId'])){ // Gestion des favoris
        // gestion de favoris
        $recipeId = intval($_GET['recipeId']);
        $username = $statut_connexion ? $_SESSION['user']['username'] : null;
        toggleFavorite($recipeId, $username);
        // Redirection pour eviter la resoumission
        $redirectPage = isset($_GET['page']) ? $_GET['page'] : 'navigation';

    }else if($action == "logout"){ // Si c'est une deconnexion, on deconnecte
        if($statut_connexion) {
            unset($_SESSION['user']);
            $messages[] = "Vous&nbsp;&ecirc;tes d&eacute;connect&eacute;.";
        }

    }else if(strstr($action, "update") && $statut_connexion){ // Si c'est une mise a jour du profil,
        require "utils/checkUpdate.php";

        $new_lastname=isset($_POST['new_lastname']) ? $_POST['new_lastname'] : null;
        $new_firstname=isset($_POST['new_firstname']) ? $_POST['new_firstname'] : null;
        $new_birthdate=isset($_POST['new_birthdate']) ? $_POST['new_birthdate'] : null;
        $new_sexe=isset($_POST['new_sexe']) ? $_POST['new_sexe'] : null;

        $class_fields = [];
        $allows_update=false;
        $result = [];

        if($action=="updateLastname"){
            $result=checkUpdateLastname($new_lastname);
            $allows_update=$result['allows_update'];
            $messages=$result['messages'];
            $messages_errors=$result['messages_errors'];
            if($allows_update){
                applyUpdateFile('lastname', $new_lastname);
            }else{
                $class_fields['lastname']=$result['class_fields'];
            }
        }else if($action=="updateFirstname"){
            $result=checkUpdateFirstname($new_firstname);
            $allows_update=$result['allows_update'];
            $messages=$result['messages'];
            $messages_errors=$result['messages_errors'];
            if($allows_update){
                applyUpdateFile('firstname', $new_firstname);
            }else{
                $class_fields['firstname']=$result['class_fields'];
            }
        }else if($action=="updateBirthdate"){
            $result=checkUpdateBirthdate($new_birthdate);
            $allows_update=$result['allows_update'];
            $messages=$result['messages'];
            $messages_errors=$result['messages_errors'];
            if($allows_update){
                applyUpdateFile('birthdate', $new_birthdate);
            }else{
                $class_fields['birthdate']=$result['class_fields'];
            }
        }else if($action=="updateSexe"){
            $result=checkUpdateSexe($new_sexe);
            $allows_update=$result['allows_update'];
            $messages=$result['messages'];
            $messages_errors=$result['messages_errors'];
            if($allows_update){
                applyUpdateFile('sexe', $new_sexe);
            }else{
                $class_fields['sexe']=$result['class_fields'];
            }
        }else{
            $messages_errors[] = "Une erreur est survenue.";
        }

    }else if($action == "signup" || $action == "signin"){ // Si c'est une connexion ou inscription
        if(!$statut_connexion) {
            // On recupere les variables necessaires au traitement
            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';
            $lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
            $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
            $birthdate = isset($_POST['birthdate']) ? trim($_POST['birthdate']) : '';
            $sexe = isset($_POST['sexe']) ? trim($_POST['sexe']) : '';

            // Et on creer celle qui permettront de validier ou non l'action,
            //       et de reafficher les champs necessaires en cas d'erreur
            $all_correct = true;
            $value_fields = ['signinForm' => [], 'signupForm' => []];
            $class_fields = ['signinForm' => [], 'signupForm' => []];

            if ($action == "signup") { // Si l'action est une inscription, on traite avec la fonction dediee
                require "utils/checkSignUp.php";
                $resultat = checkSignUp($username, $password, $lastname, $firstname, $birthdate, $sexe);

                // Puis on recupere les resultats
                $messages = isset($resultat['messages']) ? $resultat['messages'] : [];
                $messages_errors = isset($resultat['messages_errors']) ? $resultat['messages_errors'] : [];
                $all_correct = isset($resultat['correct_signup']) ? $resultat['correct_signup'] : false;
                $value_fields = isset($resultat['value_fields']) ? $resultat['value_fields'] : [];
                $class_fields = isset($resultat['class_fields']) ? $resultat['class_fields'] : [];
                $page = isset($resultat['page']) ? $resultat['page'] : $page;

            } else { // Si l'action est une connexion, on traite avec la fonction dediee
                require "utils/checkSignIn.php";
                $resultat = checkSignIn($username, $password);

                // Puis on recupere les resultats
                $messages = isset($resultat['messages']) ? $resultat['messages'] : [];
                $messages_errors = isset($resultat['messages_errors']) ? $resultat['messages_errors'] : [];
                $all_correct = isset($resultat['correct_connection']) ? $resultat['correct_connection'] : false;
                $value_fields = isset($resultat['value_fields']) ? $resultat['value_fields'] : [];
                $class_fields = isset($resultat['class_fields']) ? $resultat['class_fields'] : [];
                $page = isset($resultat['page']) ? $resultat['page'] : $page;
            }

            // Validation de la connexion (commune a signup + signin)
            if (isset($all_correct) && $all_correct) { // Si tout est correct, alors on creer la session
                session_regenerate_id(true);
                // recupere toutes les infos de session utilisateur
                $_SESSION['user']['username'] = !empty($username) ? $username : null;
                $_SESSION['user']['lastname'] = !empty($lastname) ? $lastname : null;
                $_SESSION['user']['firstname'] = !empty($firstname) ? $firstname : null;
                $_SESSION['user']['birthdate'] = !empty($birthdate) ? $birthdate : null;
                $_SESSION['user']['sexe'] = !empty($sexe) ? $sexe : null;
                loadFavoritesFromFile($username); // charge les favoris depuis le fichier utilisateur
                $_COOKIE['user'] = $_SESSION['user']; // recopie les infos de session dans le cookie
                $messages[] = "Connect&eacute;&nbsp;en tant que&nbsp;" . $_SESSION['user']['username'];
            }
        }
    }else{ // Sinon (au cas ou l'action n'a pas ete trouvee)
        $messages_errors[] = "Une erreur est survenue. Cette action est impossible actuellement...";
        $messages_errors[] = "V&eacute;rifiez votre statut de connexion puis r&eacute;essayez.";
    }
}

// selection d'utilisateur si connecte
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

// suppression des messages d'erreurs avec redirection vers l'accueil si l'utilisateur vient de creer son compte.
//  -> permet d'eviter une erreur ainsi qu'une redirection sur le formulaire d'inscription si actualisation de la page apres creation du compte...
if(isset($user)){
    if(($page=="signUp" || (isset($action) && $action=="signup"))) {
        $messages_errors = []; // Reset le tableau des erreurs
        $page = 'accueil'; // Redirige l'utilisateur sur la page d'accueil
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
