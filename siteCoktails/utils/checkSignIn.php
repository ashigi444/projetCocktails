<?php
require_once "utils/utils.php";

/**
 * Verifie si il est possible de se connecter au compte de $username, avec le nom d'utilisateur
 * et le mot de passe entres par l'utilisateur.
 * On verifie d'abord la validite des saisies dans les champs, puis si tout passe correctement,
 * alors on verifie qu'il existe bien un fichier avec ce $username, si oui alors on verifie que tout correspond,
 * sinon on met les messages d'erreurs necessaires en fonction du cas qui a fait echouer la connexion au compte.
 * Si aucun compte n'existe avec cet identifiant, l'utilisateur est redirige vers la page d'inscription
 *
 * @param string $username l'identifiant saisit par l'utilisateur pour se connecter
 * @param string $password le mot de passe saisit par l'utilisateur pour se connecter
 * @return array le tableau qui contient tout les resultats,
 *        -  le booleen qui indique la validite de la connexion,
 *        -  les messages (classiques ou d'erreurs) a afficher a l'utilisateur,
 *        -  les classes de style et preremplissage de champs en cas d'echec de la connexion,
 *        -  etc...
 */
function checkSignIn($username, $password)
{
    $messages = [];
    $messagesErrors = [];
    $allCorrect=true;
    $valueFields=['signinForm'=>[], 'signupForm'=>[]];
    $classFields=['signinForm'=>[], 'signupForm'=>[]];
    $page=isset($_GET['page']) ? $_GET['page'] : 'accueil';

    $validUsername = checkUsernameField($username);
    $validPassword = checkPasswordField($password);
    $validFields = $validUsername && $validPassword;

    if ($validFields) { // If fields are valid
        $validConnection = checkConnection($username, $password);
        if (!is_array($validConnection)) {
            // Redirecting to inscription form...
            $valueFields['signupForm']['username'] = $username;
            // $passwordFormSignup=$validity['field']['password'] ? $password  null // A voir si on autorise la recopie du mot de passe dans les champs
            $page = "signUp";
            if ($validConnection == 'undefined_file') { // If the account file doesn't exists
                $messagesErrors[] = "
                    Identifiant inexistant, veuillez cr&eacute;er un compte
                    s&apos;il-vous-pla&icirc;t.
                ";
                $allCorrect = false;
            } else if ($validConnection == 'undefined_infos') { // If the table infosUser doesn't exists
                $messagesErrors[] = "Erreur lors de la r&eacute;cup&eacute;ration de vos informations...";
                $messagesErrors[] = "Veuillez recr&eacute;er un compte s&apos;il-vous-pla&icirc;t.";
                $allCorrect = false;
            } else {
                $messagesErrors[] = "Erreur lors du traitement de vos informations...";
                $messagesErrors[] = "Veuillez r&eacute;essayer s&apos;il-vous-pla&icirc;t.";
                $allCorrect = false;
            }
        } else {
            if (isset($validConnection['username']) && isset($validConnection['password'])) {
                if (!$validConnection['username']) { // If username_form != username_file
                    $classFields['signinForm']['username'] = "error";
                    $messagesErrors[] = "Identifiant incorrect";
                    $allCorrect = false;
                } else {
                    $valueFields['signinForm']['username'] = $username;
                }

                if (!$validConnection['password']) { // If password_form != password_file
                    $classFields['signinForm']['password'] = "error";
                    $messagesErrors[] = "Mot de passe incorrect";
                    $allCorrect = false;
                } // password not recopied in form
            } else {
                // Redirecting to inscription form...
                $valueFields['signupForm']['username'] = $username;
                $page = "signUp";

                $messagesErrors[] = "Erreur lors de la r&eacute;cup&eacute;ration de vos informations...";
                $messagesErrors[] = "Veuillez recr&eacute;er un compte s&apos;il-vous-pla&icirc;t.";
                $allCorrect = false;
            }
        }
    }else{
        if (!$validUsername) {
            $classFields['signinForm']['username'] = "error";
            $messagesErrors[] = "Identifiant invalide.";
            $allCorrect = false;
        }
        if (!$validPassword) {
            $classFields['signinForm']['password'] = "error";
            $messagesErrors[] = "Mot de passe invalide.";
            $allCorrect = false;
            if ($validUsername)// Recuperation du username pour le champ du formulaire
                $valueFields['signinForm']['username'] = $username;
        }
    }

    return [
        'messages' => $messages,
        'messages_errors' => $messagesErrors,
        'correct_connection' => $allCorrect,
        'value_fields' => $valueFields,
        'class_fields' => $classFields,
        'page' => $page
    ];
}
?>
