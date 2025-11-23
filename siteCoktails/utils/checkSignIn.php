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
    $messages_errors = [];
    $all_correct=true;
    $value_fields=['signinForm'=>[], 'signupForm'=>[]];
    $class_fields=['signinForm'=>[], 'signupForm'=>[]];
    $page=isset($_GET['page']) ? $_GET['page'] : 'accueil';

    $valid_username = checkUsernameField($username);
    $valid_password = checkPasswordField($password);
    $valid_fields = $valid_username && $valid_password;

    if ($valid_fields) { // If fields are valid
        $valid_connection = checkConnection($username, $password);
        if (!is_array($valid_connection)) {
            // Redirecting to inscription form...
            $value_fields['signupForm']['username'] = $username;
            // $password_form_signup=$validity['field']['password'] ? $password  null // A voir si on autorise la recopie du mot de passe dans les champs
            $page = "signUp";
            if ($valid_connection == 'undefined_file') { // If the account file doesn't exists
                $messages_errors[] = "
                    Identifiant inexistant, veuillez cr&eacute;er un compte
                    s&apos;il-vous-pla&icirc;t.
                ";
                $all_correct = false;
            } else if ($valid_connection == 'undefined_infos') { // If the table infosUser doesn't exists
                $messages_errors[] = "Erreur lors de la r&eacute;cup&eacute;ration de vos informations...";
                $messages_errors[] = "Veuillez recr&eacute;er un compte s&apos;il-vous-pla&icirc;t.";
                $all_correct = false;
            } else {
                $messages_errors[] = "Erreur lors du traitement de vos informations...";
                $messages_errors[] = "Veuillez r&eacute;essayer s&apos;il-vous-pla&icirc;t.";
                $all_correct = false;
            }
        } else {
            if (isset($valid_connection['username']) && isset($valid_connection['password'])) {
                if (!$valid_connection['username']) { // If username_form != username_file
                    $class_fields['signinForm']['username'] = "error";
                    $messages_errors[] = "Identifiant incorrect";
                    $all_correct = false;
                } else {
                    $value_fields['signinForm']['username'] = $username;
                }

                if (!$valid_connection['password']) { // If password_form != password_file
                    $class_fields['signinForm']['password'] = "error";
                    $messages_errors[] = "Mot de passe incorrect";
                    $all_correct = false;
                } // password not recopied in form
            } else {
                // Redirecting to inscription form...
                $value_fields['signupForm']['username'] = $username;
                $page = "signUp";

                $messages_errors[] = "Erreur lors de la r&eacute;cup&eacute;ration de vos informations...";
                $messages_errors[] = "Veuillez recr&eacute;er un compte s&apos;il-vous-pla&icirc;t.";
                $all_correct = false;
            }
        }
    }else{
        if (!$valid_username) {
            $class_fields['signinForm']['username'] = "error";
            $messages_errors[] = "Identifiant invalide.";
            $all_correct = false;
        }
        if (!$valid_password) {
            $class_fields['signinForm']['password'] = "error";
            $messages_errors[] = "Mot de passe invalide.";
            $all_correct = false;
            if ($valid_username)// Recuperation du username pour le champ du formulaire
                $value_fields['signinForm']['username'] = $username;
        }
    }

    return [
        'messages' => $messages,
        'messages_errors' => $messages_errors,
        'correct_connection' => $all_correct,
        'value_fields' => $value_fields,
        'class_fields' => $class_fields,
        'page' => $page
    ];
}
?>
