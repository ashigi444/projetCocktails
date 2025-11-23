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
 * @param string $gender le sexe coche par l'utilisateur
 * @return array le tableau qui contient tout les resultats,
 *       -  le booleen qui indique la validite de la creation du compte,
 *       -  les messages (classiques ou d'erreurs) a afficher a l'utilisateur,
 *       -  les classes de style et preremplissage de champs en cas d'echec de la creation du compte,
 *       -  etc...
 */
function checkSignUp($username, $password, $lastname,
                     $firstname, $birthdate, $gender){
    $messages = [];
    $messagesErrors = [];
    $allCorrect = false;
    $page = 'signUp';
    $valueFields = ['signinForm' => [],'signupForm' => []];
    $classFields = ['signinForm' => [], 'signupForm' => []];

    $validUsername=checkUsernameField($username); // true si username correct, false sinon
    $validPassword=checkPasswordField($password); // true si password correct, false sinon
    $validLastName=checkLastnameField($lastname); // true si lastname correct, false sinon
    $validFirstName=checkFirstnameField($firstname); // true si firstname correct, false sinon
    $validBirthdate=checkBirthdateField($birthdate); // true si birthdate correct, false sinon
    $validGender=checkSexeField($gender); // true si sexe correct, false sinon
    $validFields = $validUsername && $validPassword &&
        $validLastName && $validFirstName &&
        $validBirthdate && $validGender; // true si tout est correct, false sinon

    if ($validFields) { // Si tout est correct
        $validAccountCreation=!checkAccountAlreadyExists($username); // true si aucun compte n'existe pour ce username, false sinon
        if ($validAccountCreation) { // Si aucun compte n'existe
            // Recuperer les favoris de session si existants
            $sessionFavorites = isset($_SESSION['favoriteRecipes']) ? $_SESSION['favoriteRecipes'] : array();

            // creation du fichier utilisateur
            $newUser = [
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT), // Hashage du mot de passe
                'lastname' => $lastname,
                'firstname' => $firstname,
                'birthdate' => $birthdate,
                'sexe' => $gender,
                'favoriteRecipes' => $sessionFavorites, // Transfert des favoris de session
            ];

            // Ecriture via la fonction utilitaire
            saveUserInfos($username, $newUser);

            // On verifie qu'on retrouve bien l'utilisateur
            $verify = checkConnection($username, $password);
            if (is_array($verify)
                && isset($verify['username']) && isset($verify['password'])
                && $verify['username'] && $verify['password']) {

                $allCorrect=true;
                $page='navigation'; // apres une connexion valide, on redirige vers la navigation
                $messages[]="Compte cr&eacute;&eacute; avec succ&egrave;s.";
            } else {
                $messagesErrors[]=
                    "Une erreur est survenue lors de la cr&eacute;ation du compte."
                    ."<br />Veuillez r&eacute;essayer s&apos;il-vous-pla&icirc;t.";
                $page='signUp';
            }

        } else { // sinon (si il existe deja un compte pour ce pseudo)
            $messagesErrors[] = "Cet identifiant existe d&eacute;j&agrave;.";
            $classFields['signupForm']['username'] = 'error';
            $valueFields['signupForm']['username'] = $username;
            $page = 'signUp';
        }
    } else { // Sinon (si au moins 1 champ n'est pas correct)
        $messagesErrors[] = "Impossible de cr&eacute;er le compte&nbsp;!";

        if (!$validUsername) {
            $classFields['signupForm']['username']="error";
            $messagesErrors[] = "L&apos;identifiant est invalide.";
        }else{
            $valueFields['signupForm']['username']=$username;
        }

        if (!$validPassword) {
            $classFields['signupForm']['password']="error";
            $messagesErrors[]="Le mot de passe est invalide.";
        }

        if (!$validFirstName) {
            $classFields['signupForm']['firstname']="error";
            $messagesErrors[]="Le pr&eacute;nom est invalide.";
        } else {
            $valueFields['signupForm']['firstname']=$firstname;
        }

        if (!$validLastName) {
            $classFields['signupForm']['lastname']="error";
            $messagesErrors[]="Le nom est invalide.";
        } else {
            $valueFields['signupForm']['lastname']=$lastname;
        }

        if (!$validBirthdate) {
            $classFields['signupForm']['birthdate']="error";
            $messagesErrors[]="La date de naissance est invalide.";
        } else {
            $valueFields['signupForm']['birthdate']=$birthdate;
        }

        if (!$validGender) {
            $classFields['signupForm']['sexe']="error";
            $messagesErrors[]="Le sexe est invalide.";
        } else {
            $valueFields['signupForm']['sexe']=$gender;
        }

        $allCorrect=false;
        $page='signUp';
    }

    return [
        'messages'=> $messages,
        'messages_errors' => $messagesErrors,
        'correct_signup' => $allCorrect,
        'value_fields' => $valueFields,
        'class_fields' => $classFields,
        'page' => $page
    ];
}

?>
