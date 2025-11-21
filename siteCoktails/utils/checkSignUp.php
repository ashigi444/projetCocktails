<?php
require_once "utils/utils.php";

/**
 * Verifie si il est possible de creer un compte avec toutes les informations entrees par l'utilisateur
 * L'identifiant et le mot de passe sont obligatoires, le reste est facultatif
 * On verifie d'abord la validite des saisies dans les champs, puis si tout passe correctement,
 * alors on verifie qu'il n'existe pas deja un fichier avec ce $username, si tout passe alors on le creer,
 * sinon on met les messages d'erreurs necessaires en fonction du cas qui a fait echouer la creation du compte
 *
 * @param string $username le nom d'utilisateur saisit par l'utilisateur
 * @param string $password le mot de passe saisit par l'utilisateur
 * @param string $lastname le nom saisit par l'utilisateur
 * @param string $firstname le prenom saisit par l'utilisateur
 * @param string $birthdate la date d'anniversaire saisit par l'utilisateur
 * @param string $sexe le sexe coche par l'utilisateur
 * @return array le tableau qui contient tout les resultats,
 *      -> le booleen qui indique la validite de la creation du compte,
 *      -> les messages (classiques ou d'erreurs) a afficher a l'utilisateur,
 *      -> les classes de style et preremplissage de champs en cas d'echec de la creation du compte,
 *      -> etc...
 */
function checkSignUp($username, $password, $lastname,
                     $firstname, $birthdate, $sexe){
    $messages = [];
    $messages_errors = [];
    $all_correct = false;
    $page = 'signUp';
    $value_fields = ['signinForm' => [],'signupForm' => []];
    $class_fields = ['signinForm' => [], 'signupForm' => []];

    $valid_username=checkUsernameField($username); // true si username correct, false sinon
    $valid_password=checkPasswordField($password); // true si password correct, false sinon
    $valid_lastname=checkLastNameField($lastname); // true si lastname correct, false sinon
    $valid_firstname=checkFirstNameField($firstname); // true si firstname correct, false sinon
    $valid_birthdate=checkBirthdateField($birthdate); // true si birthdate correct, false sinon
    $valid_sexe=checkSexeField($sexe); // true si sexe correct, false sinon
    $valid_fields = $valid_username && $valid_password &&
                    $valid_lastname && $valid_firstname &&
                    $valid_birthdate && $valid_sexe; // true si tout est correct, false sinon

    if ($valid_fields) { // Si tout est correct
        $valid_creation_account=!checkAccountAlreadyExists($username); // true si aucun compte n'existe pour ce username, false sinon
        if ($valid_creation_account) { // Si aucun compte n'existe
            // Recuperer les favoris de session si existants
            $sessionFavorites = isset($_SESSION['favoriteRecipes']) ? $_SESSION['favoriteRecipes'] : array();

            // création du fichier utilisateur
            $new_user = [
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT), // Hashage du mot de passe
                'lastname' => $lastname,
                'firstname' => $firstname,
                'birthdate' => $birthdate,
                'sexe' => $sexe,
                'favoriteRecipes' => $sessionFavorites, // Transfert des favoris de session
            ];
            $users_print=var_export($new_user, true);
            $users_put="<?php\n\$infos_user=" . $users_print . ";\n?>";

            if (!is_dir('dataUsers')) {
                mkdir('dataUsers', 0755, true);
            }
            $filename="dataUsers/user" . $username . ".php";
            file_put_contents($filename, $users_put);

            // On vérifie qu'on retrouve bien l'utilisateur
            $verify = checkConnection($username, $password);
            if (is_array($verify)
                && isset($verify['username']) && isset($verify['password'])
                && $verify['username'] && $verify['password']) {

                $all_correct=true;
                $page='accueil'; // après signup OK, tu pourras même laisser index gérer la redirection
                $messages[]="Compte cr&eacute;&eacute; avec succ&egrave;s.";
            } else {
                $messages_errors[]=
                    "Une erreur est survenue lors de la cr&eacute;ation du compte."
                    ."<br />Veuillez r&eacute;essayer s&apos;il-vous-pla&icirc;t.";
                $page='signUp';
            }

        } else { // sinon -> si il existe deja un compte pour ce pseudo
            $messages_errors[] = "Cet identifiant existe d&eacute;j&agrave;.";
            $class_fields['signupForm']['username'] = 'error';
            $value_fields['signupForm']['username'] = $username;
            $page = 'signUp';
        }
    } else { // Sinon -> Si au moins 1 champ n'est pas correct
        $messages_errors[] = "Impossible de cr&eacute;er le compte&nbsp;!";

        if (!$valid_username) {
            $class_fields['signupForm']['username']="error";
            $messages_errors[] = "L&apos;identifiant est invalide.";
        }else{
            $value_fields['signupForm']['username']=$username;
        }

        if (!$valid_password) {
            $class_fields['signupForm']['password']="error";
            $messages_errors[]="Le mot de passe est invalide.";
        }/*else{
            $value_fields['signupForm']['password']=$password; // -> A voir si on autorise la recopie du mot de passe
        }*/

        if (!$valid_firstname) {
            $class_fields['signupForm']['firstname']="error";
            $messages_errors[]="Le pr&eacute;nom est invalide.";
        } else {
            $value_fields['signupForm']['firstname']=$firstname;
        }

        if (!$valid_lastname) {
            $class_fields['signupForm']['lastname']="error";
            $messages_errors[]="Le nom est invalide.";
        } else {
            $value_fields['signupForm']['lastname']=$lastname;
        }

        if (!$valid_birthdate) {
            $class_fields['signupForm']['birthdate']="error";
            $messages_errors[]="La date de naissance est invalide.";
        } else {
            $value_fields['signupForm']['birthdate']=$birthdate;
        }

        if (!$valid_sexe) {
            $class_fields['signupForm']['sexe']="error";
            $messages_errors[]="Le sexe est invalide.";
        } else {
            $value_fields['signupForm']['sexe']=$sexe;
        }

        $all_correct=false;
        $page='signUp';
    }

    return [
        'messages'=> $messages,
        'messages_errors' => $messages_errors,
        'correct_signup' => $all_correct,
        'value_fields' => $value_fields,
        'class_fields' => $class_fields,
        'page' => $page
    ];
}

?>