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
 * @param string $gender le gender coche par l'utilisateur
 * @return array le tableau qui contient tout les resultats,
 *       -  le booleen qui indique la validite de la creation du compte,
 *       -  les messages (classiques ou d'erreurs) a afficher a l'utilisateur,
 *       -  les classes de style et preremplissage de champs en cas d'echec de la creation du compte,
 *       -  etc...
 */
function checkSignUp($username, $password, $lastname,
                     $firstname, $birthdate, $gender){
    // tableau pour stocker les messages classiques
    $messages = [];
    // tableau pour stocker les messages d'erreurs
    $messagesErrors = [];
    // booleen pour indiquer si la creation de compte a reussi
    $allCorrect = false;
    // page de destination par defaut en cas d'echec
    $page = 'signUp';
    // valeurs a preremplir dans les formulaires de connexion et d'inscription
    $valueFields = ['signinForm' => [],'signupForm' => []];
    // classes CSS a appliquer aux champs pour les erreurs eventuelles
    $classFields = ['signinForm' => [], 'signupForm' => []];

    // verification de chaque champ un par un
    $validUsername=checkUsernameField($username); // true si username correct, false sinon
    $validPassword=checkPasswordField($password); // true si password correct, false sinon
    $validLastName=checkLastnameField($lastname); // true si lastname correct, false sinon
    $validFirstName=checkFirstnameField($firstname); // true si firstname correct, false sinon
    $validBirthdate=checkBirthdateField($birthdate); // true si birthdate correct, false sinon
    $validGender=checkGenderField($gender); // true si gender correct, false sinon

    // booleen global qui verifie que tous les champs sont valides
    $validFields = $validUsername && $validPassword &&
        $validLastName && $validFirstName &&
        $validBirthdate && $validGender; // true si tout est correct, false sinon

    if ($validFields) { // Si tout est correct
        // on verifie si un compte existe deja pour ce username
        $validAccountCreation=!checkAccountAlreadyExists($username); // true si aucun compte n'existe pour ce username, false sinon

        if ($validAccountCreation) { // Si aucun compte n'existe
            // Recuperer les favoris de session si existants
            $sessionFavorites = isset($_SESSION['favoriteRecipes']) ? $_SESSION['favoriteRecipes'] : array();

            // creation de la structure de donnees pour le nouveau compte utilisateur
            $newUser = [
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT), // Hashage du mot de passe
                'lastname' => $lastname,
                'firstname' => $firstname,
                'birthdate' => $birthdate,
                'gender' => $gender,
                'favoriteRecipes' => $sessionFavorites, // Transfert des favoris de session
            ];

            // Ecriture via la fonction utilitaire
            saveUserInfos($username, $newUser);

            // On verifie qu'on retrouve bien l'utilisateur
            $verify = checkConnection($username, $password);
            if (is_array($verify)
                && isset($verify['username']) && isset($verify['password'])
                && $verify['username'] && $verify['password']) {

                // si la verification apres creation est bonne, on valide la creation
                $allCorrect=true;
                // apres une connexion valide, on redirige vers la navigation
                $page='navigation'; // apres une connexion valide, on redirige vers la navigation
                $messages[]="Compte cr&eacute;&eacute; avec succ&egrave;s.";
            } else {
                // si la verification echoue, on signale une erreur generique
                $messagesErrors[]=
                    "Une erreur est survenue lors de la cr&eacute;ation du compte."
                    ."<br />Veuillez r&eacute;essayer s&apos;il-vous-pla&icirc;t.";
                $page='signUp';
            }

        } else { // sinon (si il existe deja un compte pour ce pseudo)
            // on indique que l'identifiant est deja pris et on marque le champ en erreur
            $messagesErrors[] = "Cet identifiant existe d&eacute;j&agrave;.";
            $classFields['signupForm']['username'] = 'error';
            $valueFields['signupForm']['username'] = $username;
            $page = 'signUp';
        }
    } else { // Sinon (si au moins 1 champ n'est pas correct)
        // message global d'echec de creation de compte
        $messagesErrors[] = "Impossible de cr&eacute;er le compte&nbsp;!";

        // gestion du champ username
        if (!$validUsername) {
            $classFields['signupForm']['username']="error";
            $messagesErrors[] = "L&apos;identifiant est invalide.";
        }else{
            // si le champ est valide, on conserve la valeur dans le formulaire
            $valueFields['signupForm']['username']=$username;
        }

        // gestion du champ password
        if (!$validPassword) {
            $classFields['signupForm']['password']="error";
            $messagesErrors[]="Le mot de passe est invalide.";
        }

        // gestion du champ firstname
        if (!$validFirstName) {
            $classFields['signupForm']['firstname']="error";
            $messagesErrors[]="Le pr&eacute;nom est invalide.";
        } else {
            $valueFields['signupForm']['firstname']=$firstname;
        }

        // gestion du champ lastname
        if (!$validLastName) {
            $classFields['signupForm']['lastname']="error";
            $messagesErrors[]="Le nom est invalide.";
        } else {
            $valueFields['signupForm']['lastname']=$lastname;
        }

        // gestion du champ birthdate
        if (!$validBirthdate) {
            $classFields['signupForm']['birthdate']="error";
            $messagesErrors[]="La date de naissance est invalide.";
        } else {
            $valueFields['signupForm']['birthdate']=$birthdate;
        }

        // gestion du champ gender
        if (!$validGender) {
            $classFields['signupForm']['gender']="error";
            $messagesErrors[]="Le gender est invalide.";
        } else {
            $valueFields['signupForm']['gender']=$gender;
        }

        // on confirme que la creation n'est pas valide et on reste sur la page d'inscription
        $allCorrect=false;
        $page='signUp';
    }

    // on renvoie tout ce qui est necessaire pour l'affichage et le traitement cote index.php
    return [
        'messages'=> $messages,
        'messagesErrors' => $messagesErrors,
        'correctSignup' => $allCorrect,
        'valueFields' => $valueFields,
        'classFields' => $classFields,
        'page' => $page
    ];
}

?>
