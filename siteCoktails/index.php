<?php
session_start();
$messages = [];

// pour l'instant, on connecte juste l'utilisateur
// sans verifs via expressions régulières (=regex)
include 'assets/dataUsers.php';
if (isset($_POST['action'])) {
    if($_POST['action'] == 'signup') {
        $login = isset($_POST['login']) ? trim($_POST['login']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';
        $lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
        $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
        $birthdate = isset($_POST['birthdate']) ? trim($_POST['birthdate']) : '';
        $sexe = isset($_POST['sexe']) ? $_POST['sexe'] : '';

        $loginTrouve=false;
        $passwordCorrect=false;
        $keyIdUser='';

        $is_valid=!empty(trim($login)) && !empty(trim($password)); // Verif sans regex pour le moment
        if ($is_valid) {
            foreach ($users as $idUser => $infosUser) {
                if (isset($infosUser['login']) && $infosUser['login'] == $login) {
                    $loginTrouve = true;
                    $keyIdUser = $idUser;
                    break;
                }
            }

            if(!$loginTrouve) {
                $idUser='user'.(count($users)+1);
                $users[$idUser] = [
                    'login'     => $login,
                    'password'  => $password,
                ];

                if(!empty(trim($lastname))) $users[$idUser]['lastname'] = $lastname;
                if(!empty(trim($firstname))) $users[$idUser]['firstname'] = $firstname;
                if(!empty(trim($sexe))) $users[$idUser]['sexe'] = $sexe;
                if(!empty(trim($birthdate))) $users[$idUser]['birthdate'] = $birthdate;

                $users_print=var_export($users, true);
                $users_put="<?php \$users=".$users_print.";";
                file_put_contents('assets/dataUsers.php', $users_put);

                $_SESSION['user'] = ['login' => $login];
                session_regenerate_id(true);
                $messages[] = "Connect&eacute;&nbsp;en tant que&nbsp;" . $_SESSION['user']['login'];
            }else{
                $messages[] = "Impossible de cr&eacute;er le compte&nbsp;!";
                if($loginTrouve){
                    $messages[] = "Login d&eacute;j&agrave;&nbsp;existant&nbsp;!";
                }
                $_GET['page']='signUp';
            }
        } else {
            $messages[] = "Login vide&nbsp;:&nbsp;connexion impossible.";
        }
    }

    if ($_POST['action'] == 'login') {
        $login = isset($_POST['login']) ? trim($_POST['login']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';

        $loginTrouve=false;
        $passwordCorrect=false;
        $keyIdUser='';

        $is_valid=!empty(trim($login)); // Verif sans regex, uniquement sur le login pour le moment
        if ($is_valid) {
            foreach ($users as $idUser => $infosUser) {
                if (isset($infosUser['login']) && $infosUser['login'] == $login) {
                    $loginTrouve = true;
                    $keyIdUser = $idUser;
                    break;
                }
            }

            if($loginTrouve) {
                if (isset($users[$keyIdUser]['password']) && $users[$keyIdUser]['password'] == $_POST['password']) {
                    $passwordCorrect = true;
                }
            }

            if($loginTrouve && $passwordCorrect) {
                $_SESSION['user'] = ['login' => $login];
                session_regenerate_id(true);
                $messages[] = "Connect&eacute;&nbsp;en tant que&nbsp;" . $_SESSION['user']['login'];
            }else{
                $messages[] = "Impossible de se connecter&nbsp;!";
                if(!$loginTrouve){
                    $messages[] = "Login incorrect&nbsp;!";
                    // TODO ajouter un formulaire vers le bouton d'inscription
                }else if(!$passwordCorrect){
                    $messages[] = "Mot de passe incorrect&nbsp;!";
                    $loginForm=$login;
                }
            }
        } else {
            $messages[] = "Login vide&nbsp;:&nbsp;connexion impossible.";
        }
    } elseif ($_POST['action'] == 'logout') {
        unset($_SESSION['user']);
        $messages[] = "Vous&nbsp;&ecirc;tes d&eacute;connect&eacute;.";
    }
}

// selection d'utilisateur si connecté
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

// selection de page
$page = isset($_GET['page']) && !empty(trim($_GET['page'])) ? trim($_GET['page']) : 'accueil';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Gestion de Cocktails</title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
</head>
<body>

<!-- inclusion du header et du nav-->
<?php
include 'includes/header.php';
include 'includes/nav.php';

// inclusion du main
include 'includes/main.php';

// inclusion du pied de page
include 'includes/footer.php';
?>

</body>
</html>
