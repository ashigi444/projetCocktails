<?php
/**
 * Verifie si il existe deja un fichier utilisateur/compte pour le pseudo passe en parametre
 *
 * @param string $username le nom d'utilisateur
 * @return bool  true si il existe un compte pour  $username, false sinon
 */
function checkAccountAlreadyExists($username){
    if(!isset($username) || empty(trim($username)))
        return false;
    $filename = 'dataUsers/user'.$username.'.php';
    return file_exists($filename);
}

/**
 * Verifie si la connection au compte de nom d'utilisateur est possible via le password
 *
 * @param string $username le nom d'utilisateur a verifier
 * @param string $password le mot de passe a verifier
 * @return array|string  soit la chaine qui decrit le probleme survenue
 *                        , soit le tableau qui contient les booleens de validation
 */
function checkConnection($username, $password) {
    // Verifie la validite des parametres au cas ou
    if(!isset($username) || empty(trim($username)) || !isset($password) || empty($password))
        return "undefined_infos";

    $filename = 'dataUsers/user'.$username.'.php';
    if (file_exists($filename)) { // Si le fichier existe
        require $filename;
        if (isset($infos_user)) {
            $validityConnection = [
                'username' => checkUsernameFile($username, $infos_user),
                'password' => checkPasswordFile($password, $infos_user)
            ];
        }else{
            $validityConnection = 'undefined_infos';
        }
    }else{
        $validityConnection = 'undefined_file';
    }
    return $validityConnection;
}

/**
 * Verifie si le nom d'utilisateur est correct dans le tableau d'informations sur l'utilisateur
 * qui, generalement à l'appel de la fonction, vient du fichier d'utilisateur
 *
 * @param string $username le nom d'utilisateur a verifier
 * @param string $infos_user le tableau d'informations sur l'utilisateur
 * @return bool true si le nom d'utilisateur correspond dans le tableau, false sinon
 */
function checkUsernameFile($username, $infos_user){
    // On verifie quand même si login existent vraiment dans le tableau
    if(isset($infos_user['username'])) {
        // si le login est le bon alors true
        if ($infos_user['username'] == $username) {
            return true;
        }
    }
    return false;
}

/**
 * Verifie si le mot de passe est correct dans le tableau d'informations sur l'utilisateur
 * qui, generalement à l'appel de la fonction, vient du fichier d'utilisateur
 *
 * @param string $password le mot de passe a verifier
 * @param array $infos_user le tableau d'informations sur l'utilisateur
 * @return bool true si le mot de passe correspond dans le tableau, false sinon
 */
function checkPasswordFile($password, $infos_user){
    // On verifie quand même si login existent vraiment dans le tableau
    if(isset($infos_user['password'])) {
        // si le login est le bon alors true
        if (password_verify($password, $infos_user['password']) || $infos_user['password'] == $password) {
            return true;
        }
    }
    return false;
}

// Toutes les fonctions de vérification de champ, à modifier avec des regex plus strictes
/**
 * Verifie si le nom d'utilisateur est correct par rapport à une regex
 * le nom d'utilisateur provient generalement d'un champ rempli par l'utilisateur
 *
 * @param string $username le nom d'utilisateur a verifier
 * @return bool true si le nom d'utilisateur existe, n'est pas vide et passe la regex, false sinon
 */
function checkUsernameField($username) {
    return isset($username) && !empty(trim($username)) && preg_match("/.*/", $username);
}

/**
 * Verifie si le mot de passe est correct par rapport à une regex
 * le mot de passe provient generalement d'un champ rempli par l'utilisateur
 *
 * @param string $password le mot de passe a verifier
 * @return bool true si le mot de passe existe, n'est pas vide et passe la regex, false sinon
 */
function checkPasswordField($password) {
    return isset($password) && !empty(trim($password)) && preg_match("/.*/", $password);
}

/**
 * Verifie si le nom est correct par rapport à une regex
 * le nom provient generalement d'un champ rempli par l'utilisateur
 *
 * @param string $lastname le nom a verifier
 * @return bool true si le nom existe, n'est pas vide et passe la regex, false sinon
 */
function checkLastNameField($lastname){
    return isset($lastname) && preg_match("/.*/", $lastname);
}

/**
 * Verifie si le prenom est correct par rapport à une regex
 * le prenom provient generalement d'un champ rempli par l'utilisateur
 *
 * @param string $firstname le prenom a verifier
 * @return bool true si le prenom existe, n'est pas vide et passe la regex, false sinon
 */
function checkFirstNameField($firstname){
    return isset($firstname) && preg_match("/.*/", $firstname);
}

/**
 * Verifie si la date de naissance est correct par rapport à une regex
 * la date de naissance provient generalement d'un champ rempli par l'utilisateur
 *
 * @param string $birthdate la date de naissance a verifier
 * @return bool true si la date de naissance existe, n'est pas vide et passe la regex, false sinon
 */
function checkBirthdateField($birthdate){
    // Vide autorisé car champ optionnel
    if(!isset($birthdate) || empty(trim($birthdate)))
        return true;

    $today=date("Y-m-d");
    list($year_today, $month_today, $day_today)=explode("-", $today);
    list($year,$month,$day)=explode('-',$birthdate);
    echo $year.'-'.$month.'-'.$day; // DEBUG
    echo $year_today.'-'.$month_today.'-'.$day_today; // DEBUG

    return (
        checkdate($month,$day,$year) &&
        preg_match("/.*/", $year) &&
        preg_match("/.*/", $month) &&
        preg_match("/.*/", $day) &&
        $birthdate<=$today
    );
}

/**
 * Verifie si le sexe est correct par rapport à une regex
 * le sexe provient generalement d'un champ rempli par l'utilisateur
 *
 * @param string $sexe le sexe a verifier
 * @return bool true si le sexe existe, n'est pas vide et passe la regex, false sinon
 */
function checkSexeField($sexe){
    // Vide autorisé car champ optionnel
    return isset($sexe) && (empty(trim($sexe)) || preg_match("/^(male|female)$/", $sexe));
}

/**
 * Verifie la validite du mot de passe par rapport au compte de username
 * Appelee lorsque l'utilisateur veut modifier son mot de pase,
 * pour verifier si "l'ancien" qu'il met correspond au mot de passe du compte
 *
 * @param string $username  le nom de l'utilisateur connecte
 * @param string $password  le mot de passe a verifier
 * @return bool true si le mdp correspond a celui du compte, false sinon
 */
function checkRequestUpdatePassword($username, $password){
    if(!isset($password) || empty($password)
       || !checkPasswordField($password)){
        return false;
    }
    $valid_connection=checkConnection($username, $password);
    if(is_array($valid_connection) && $valid_connection['username'] && $valid_connection['password']) {
        return true;
    }
    return false;
}
?>
