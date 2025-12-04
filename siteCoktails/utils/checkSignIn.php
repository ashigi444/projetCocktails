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
    // tableau pour stocker les messages classiques
    $messages = [];

    // tableau pour stocker les messages d'erreurs
    $messagesErrors = [];

    // booleen global pour savoir si la connexion est correcte
    $allCorrect=true;

    // tableau pour preremplir les champs des formulaires si besoin
    $valueFields=['signinForm'=>[], 'signupForm'=>[]];

    // tableau pour appliquer des classes CSS aux champs (erreurs)
    $classFields=['signinForm'=>[], 'signupForm'=>[]];

    // page par defaut, ou page specifique si definie dans l'URL
    $page=isset($_GET['page']) ? $_GET['page'] : 'navigation';

    // verification du champ username
    $validUsername = checkUsernameField($username);

    // verification du champ password
    $validPassword = checkPasswordField($password);

    // booleen qui dit si les deux champs sont valides
    $validFields = $validUsername && $validPassword;

    if ($validFields) { // Si les champs sont corrects
        // verification dans les fichiers si la combinaison identifiant / mot de passe existe
        $validConnection = checkConnection($username, $password);

        if (!is_array($validConnection)) {
            // Si le retour n'est pas un tableau, alors la connexion a echoue (aucun fichier correspondant par ex.)

            // Redirection vers le formulaire d'inscription
            $valueFields['signupForm']['username'] = $username;
            $page = "signUp";

            if ($validConnection == 'undefined_file') { // Si le fichier de compte n'existe pas
                // ajout d'un message specifique pour dire que l'identifiant n'est pas trouve
                $messagesErrors[] = "
                    Identifiant inexistant, veuillez cre&eacute;er un compte
                    s&apos;il-vous-pla&icirc;t.
                ";
                $allCorrect = false;

            } else if ($validConnection == 'undefined_infos') { // Si le tableau infosUser n'existe pas
                // erreurs liees a une mauvaise lecture ou structure du fichier utilisateur
                $messagesErrors[] = "Erreur lors de la r&eacute;cup&eacute;ration de vos informations...";
                $messagesErrors[] = "Veuillez recr&eacute;er un compte s&apos;il-vous-pla&icirc;t.";
                $allCorrect = false;

            } else {
                // cas generique si on ne sait pas precisement ce qui a echoue
                $messagesErrors[] = "Erreur lors du traitement de vos informations...";
                $messagesErrors[] = "Veuillez r&eacute;essayer s&apos;il-vous-pla&icirc;t.";
                $allCorrect = false;
            }
        } else {

            // Si on obtient un tableau, alors il contient les resultats de la verification username/password
            if (isset($validConnection['username']) && isset($validConnection['password'])) {

                // verification du username (correspondance fichier)
                if (!$validConnection['username']) { // Si username_form != username_file
                    $classFields['signinForm']['username'] = "error"; // applique une classe error
                    $messagesErrors[] = "Identifiant incorrect"; // message d'erreur
                    $allCorrect = false;
                } else {
                    // si bon identifiant, on preremplit le champ username
                    $valueFields['signinForm']['username'] = $username;
                }

                // verification du mot de passe
                if (!$validConnection['password']) { // Si password_form != password_file
                    $classFields['signinForm']['password'] = "error"; // applique une classe error
                    $messagesErrors[] = "Mot de passe incorrect"; // message d'erreur
                    $allCorrect = false;
                } // Mot de passe non recopie dans le formulaire

            } else {
                // si les index username/password ne sont pas presents -> probleme de fichier utilisateur

                // Redirection vers le formulaire d'inscription
                $valueFields['signupForm']['username'] = $username;
                $page = "signUp";

                // messages d'erreurs liés aux informations non recuperables
                $messagesErrors[] = "Erreur lors de la r&eacute;cup&eacute;ration de vos informations...";
                $messagesErrors[] = "Veuillez recr&eacute;er un compte s&apos;il-vous-pla&icirc;t.";
                $allCorrect = false;
            }
        }
    } else {

        // Cas ou au moins un des champs est invalide

        if (!$validUsername) {
            $classFields['signinForm']['username'] = "error"; // classe error sur le champ username
            $messagesErrors[] = "Identifiant invalide."; // message associe
            $allCorrect = false;
        }

        if (!$validPassword) {
            $classFields['signinForm']['password'] = "error"; // classe error sur le champ password
            $messagesErrors[] = "Mot de passe invalide."; // message associe
            $allCorrect = false;

            if ($validUsername) // Recuperation du username pour le champ du formulaire
                $valueFields['signinForm']['username'] = $username; // on garde la saisie correcte
        }
    }

    // Retour de toutes les informations necessaires pour la suite du traitement dans index.php
    return [
        'messages' => $messages, // messages generaux
        'messagesErrors' => $messagesErrors, // messages d'erreurs
        'correctConnection' => $allCorrect, // booleen final de validation
        'valueFields' => $valueFields, // valeurs a preremplir dans les formulaires
        'classFields' => $classFields, // classes CSS a appliquer
        'page' => $page // page vers laquelle on renvoie
    ];
}
?>
