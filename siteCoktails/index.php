<?php
session_start();

// creation des tableaux de messages pour l'utilisateur
$messages=[];
$messages_errors=[];

// selection de page
$page = (isset($_GET['page']) && !empty(trim($_GET['page']))) ? trim($_GET['page']) : 'accueil';
// selection de l'action
$action = isset($_POST['action']) ? $_POST['action'] : null;
// verification du statut de connexion
$statut_connexion=(isset($_SESSION['user']) && !empty($_SESSION['user'])) ? true : false;
// traitement des actions et formulaires
if (isset($action)) { // Si une action a ete demandee, on cherche de quelle action il s'agit
    if($action == "logout" && $statut_connexion){ // Si c'est une deconnexion, on deconnecte
        unset($_SESSION['user']);
        $messages[] = "Vous&nbsp;&ecirc;tes d&eacute;connect&eacute;.";

    }else if($action == "wantUpdatePassword" && $statut_connexion){ // Si c'est une demande de modification
        // du mot de passe, on verifie si l'ancien mot de passe saisit est correct.
        require "utils/utils.php";
        $act_username=isset($_SESSION['user']['username']) ? $_SESSION['user']['username'] : "";
        $old_password=isset($_POST['oldPassword']) ? $_POST['oldPassword'] : "";
        $is_valid_old_password=checkRequestUpdatePassword($act_username,$old_password);
        $class_password = (isset($is_valid_old_password) && !$is_valid_old_password) ? "error" : null;
        if (isset($page) && $page != "profilSettings") $page = "profilSettings";

    }else if($action == 'update' && $statut_connexion){ // Si c'est une mise a jour du profil,
        require "utils/checkUpdate.php";
        // TODO (pas encore implementé)

    }else if(($action == "signup" || $action == "signin") && !$statut_connexion){ // Si c'est une connexion ou inscription
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

        if($action == "signup"){ // Si l'action est une inscription, on traite avec la fonction dediee
            require "utils/checkSignUp.php";
            $resultat = checkSignUp($username, $password, $lastname, $firstname, $birthdate, $sexe);

            // Puis on recupere les resultats
            $messages = isset($resultat['messages']) ? $resultat['messages'] : [];
            $messages_errors = isset($resultat['messages_errors']) ? $resultat['messages_errors'] : [];
            $all_correct = isset($resultat['correct_signup']) ? $resultat['correct_signup'] : false;
            $value_fields = isset($resultat['value_fields']) ? $resultat['value_fields'] : [];
            $class_fields = isset($resultat['class_fields']) ? $resultat['class_fields'] : [];
            $page = isset($resultat['page']) ? $resultat['page'] : $page;

        }else{ // Si l'action est une connexion, on traite avec la fonction dediee
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

        // Validation de la connexion (commune à signup + signin)
        if (isset($all_correct) && $all_correct) { // Si tout est correct, alors on creer la session
            $_SESSION['user'] = !empty($username) ? ['username' => $username] : null;
            session_regenerate_id(true);
            $messages[] = "Connect&eacute;&nbsp;en tant que&nbsp;" . $_SESSION['user']['username'];
        }
    }else{ // Sinon (au cas ou l'action n'a pas ete trouvee)
        $messages_errors[] = "Une erreur est survenue. Cette action est impossible actuellement...";
        $messages_errors[] = "V&eacute;rifiez votre statut de connexion puis r&eacute;essayez.";
    }
}

// selection d'utilisateur si connecté
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

// suppression des messages d'erreurs avec redirection vers l'accueil si l'utilisateur vient de créer son compte.
// -> permet d'éviter une erreur ainsi qu'une redirection sur le formulaire d'inscription si actualisation de la page après création du compte...
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
