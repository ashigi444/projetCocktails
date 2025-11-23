<?php
function replaceSearchByEntity($mode, $searchValue){
    if($mode=='text'){
        return safeTextSearch($searchValue);
    }else if($mode=='input'){
        return safeInputSearch($searchValue);
    }
    return $searchValue;
}

function safeTextSearch($searchValue){
    $searchValue = str_replace('<', '&lt;', $searchValue);
    $searchValue = str_replace('>', '&gt;', $searchValue);
    $searchValue = str_replace('&', '&amp;', $searchValue);
    return $searchValue;
}

function safeInputSearch($searchValue){
    $searchValue = str_replace('"', '&quot;', $searchValue);
    $searchValue = str_replace('\'', '&apos;', $searchValue);
    return $searchValue;
}

function makeFilenameCorrected($filenameOriginal) {
    $filename = trim($filenameOriginal);
    // Remplace tout chaque lettre accentuee par la meme lettre sans accent
    /*$filename = preg_replace('/[àÀâÂæÆáÁäÄãÃåÅāĀ]/', 'a',$filename);
    $filename = preg_replace('/[éÉèÈêÊëËęĘėĖēĒ]/', 'e',$filename);
    $filename = preg_replace('/[ÿŸ]/', 'y',$filename);
    $filename = preg_replace('/[ûÛùÙüÜúÚūŪ]/', 'u',$filename);
    $filename = preg_replace('/[îÎïÏìÌíÍįĮīĪ]/', 'i',$filename);
    $filename = preg_replace('/[ôÔœŒöÖòÒóÓõÕōŌ]/', 'o',$filename);
    $filename = preg_replace('/[çÇćĆčČ]/', 'c',$filename);
    $filename = preg_replace('/[ñÑńŃ]/', 'n',$filename);*/
    $filename = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $filename);
    $filename = preg_replace('/ /', '_',$filename);
    $filename = preg_replace('/[^a-zA-Z0-9_]/', '',$filename);

    return $filename;
}

function makeFilenameImage($imageName) {
    $imageName=makeFilenameCorrected(strtolower($imageName)); // Met tout le nom en minusucule
    if (strlen($imageName) > 0) {
        $imageName[0] = strtoupper($imageName[0]); // Met la premiere lettre en majuscule
    }
    return $imageName.'.jpg'; // return le nom de l'image suivi de .jpg
}

function make_filename_user($username){
    return "dataUsers/user".makeFilenameCorrected($username).".php";
}

/**
 *
 *
 * @param $username
 * @return array|null
 */
function loadUserInfos($username){
    $filename=make_filename_user($username);
    if (!file_exists($filename)) {
        return null;
    }
    $infos_user = null;
    require $filename;
    if (!isset($infos_user) || !is_array($infos_user) || empty($infos_user)) {
        return null;
    }
    return $infos_user;
}

/**
 *
 *
 * @param $username
 * @param $infosUser
 * @return void
 */
function saveUserInfos($username, $infosUser) {
    $filename = make_filename_user($username);
    if (!is_dir('dataUsers')) {
        mkdir('dataUsers', 0755, true);
    }
    $users_print = var_export($infosUser, true);
    $users_put = "<?php\n\$infos_user = " . $users_print . ";\n?>";
    file_put_contents($filename, $users_put);
}

/**
 * Verifie si il existe deja un fichier utilisateur/compte pour le pseudo passe en parametre
 *
 * @param string $username le nom d'utilisateur
 * @return bool  true si il existe un compte pour  $username, false sinon
 */
function checkAccountAlreadyExists($username){
    if(!isset($username) || empty(trim($username)))
        return false;
    return file_exists(make_filename_user($username));
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

    $infos_user=loadUserInfos($username);
    if (isset($infos_user) && is_array($infos_user) && !empty($infos_user)) {
        return [
            'username' => checkUsernameFile($username, $infos_user),
            'password' => checkPasswordFile($password, $infos_user)
        ];
    }else{
        if(checkAccountAlreadyExists($username)){
            return "undefined_infos";
        }else {
            return 'undefined_file';
        }
    }
}

/**
 * Verifie si le nom d'utilisateur est correct dans le tableau d'informations sur l'utilisateur
 * qui, generalement à l'appel de la fonction, vient du fichier d'utilisateur
 *
 * @param string $username le nom d'utilisateur a verifier
 * @param array $infosUser le tableau d'informations sur l'utilisateur
 * @return bool true si le nom d'utilisateur correspond dans le tableau, false sinon
 */
function checkUsernameFile($username, $infosUser){
    // On verifie quand même si login existe dans le tableau
    return isset($infosUser['username']) &&
        $infosUser['username'] == $username;
}

/**
 * Verifie si le mot de passe est correct dans le tableau d'informations sur l'utilisateur
 * qui, generalement à l'appel de la fonction, vient du fichier d'utilisateur
 *
 * @param string $password le mot de passe a verifier
 * @param array $infosUser le tableau d'informations sur l'utilisateur
 * @return bool true si le mot de passe correspond dans le tableau, false sinon
 */
function checkPasswordFile($password, $infosUser){
    return isset($infosUser['password']) &&
        password_verify($password, $infosUser['password']);
}


function checkLastnameFile($lastname, $infosUser){
    return isset($infosUser['lastname']) &&
        $lastname == $infosUser['lastname'];
}

function checkFirstnameFile($firstname, $infosUser){
    return isset($infosUser['firstname']) &&
        $firstname == $infosUser['firstname'];
}

function checkBirthdateFile($birthdate, $infosUser){
    return isset($infosUser['birthdate']) &&
        $birthdate == $infosUser['birthdate'];
}

function checkSexeFile($sexe, $infosUser){
    return isset($infosUser['sexe']) &&
        $sexe == $infosUser['sexe'];
}

// Toutes les fonctions de vérification de champ, à modifier avec des regex plus strictes
/**
 * Verifie si le nom d'utilisateur est correct par rapport à une regex
 * le nom d'utilisateur provient generalement d'un champ rempli par l'utilisateur
 * On n'accepte pas le nom d'utilisateur vide
 *
 * @param string $username le nom d'utilisateur a verifier
 * @return bool true si le nom d'utilisateur existe, n'est pas vide et passe la regex, false sinon
 */
function checkUsernameField($username) {
    return isset($username) && !empty(trim($username)) && preg_match("/^[a-zA-Z0-9]+$/", $username);
}

/**
 * Verifie si le mot de passe est correct par rapport à une regex
 * le mot de passe provient generalement d'un champ rempli par l'utilisateur
 * On n'autorise pas le mot de passe vide.
 *
 * @param string $password le mot de passe a verifier
 * @return bool true si le mot de passe existe, n'est pas vide et passe la regex, false sinon
 */
function checkPasswordField($password) {
    return isset($password) && !empty(trim($password)) && preg_match("/^.+$/", $password);
}

/**
 * Verifie si le nom est correct par rapport à une regex
 * le nom provient generalement d'un champ rempli par l'utilisateur
 *
 * @param string $lastname le nom a verifier
 * @return bool true si le nom existe, n'est pas vide et passe la regex, false sinon
 */
function checkLastnameField($lastname){
    return !isset($lastname) || empty(trim($lastname)) || preg_match(
        "/^([a-zA-ZàÀâÂæÆáÁäÄãÃåÅāĀéÉèÈêÊëËęĘėĖēĒÿŸûÛùÙüÜúÚūŪîÎïÏìÌíÍįĮīĪôÔœŒöÖòÒóÓõÕōŌçÇćĆčČñÑńŃ]+(([-']|( )*)[a-zA-ZàÀâÂæÆáÁäÄãÃåÅāĀéÉèÈêÊëËęĘėĖēĒÿŸûÛùÙüÜúÚūŪîÎïÏìÌíÍįĮīĪôÔœŒöÖòÒóÓõÕōŌçÇćĆčČñÑńŃ]+)*)+$/", $lastname);
}

/**
 * Verifie si le prenom est correct par rapport à une regex
 * le prenom provient generalement d'un champ rempli par l'utilisateur
 *
 * @param string $firstname le prenom a verifier
 * @return bool true si le prenom existe, n'est pas vide et passe la regex, false sinon
 */
function checkFirstnameField($firstname){
    return !isset($firstname) || empty(trim($firstname)) || preg_match(
            "/^([a-zA-ZàÀâÂæÆáÁäÄãÃåÅāĀéÉèÈêÊëËęĘėĖēĒÿŸûÛùÙüÜúÚūŪîÎïÏìÌíÍįĮīĪôÔœŒöÖòÒóÓõÕōŌçÇćĆčČñÑńŃ]+(([-']|( )*)[a-zA-ZàÀâÂæÆáÁäÄãÃåÅāĀéÉèÈêÊëËęĘėĖēĒÿŸûÛùÙüÜúÚūŪîÎïÏìÌíÍįĮīĪôÔœŒöÖòÒóÓõÕōŌçÇćĆčČñÑńŃ]+)*)+$/", $firstname);
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
    list($year,$month,$day)=explode('-',$birthdate);

    $date_of_18_years=($year+18)."-".$month."-".$day; // On a 18ans lorsque la date est
                                                      // meme jour, meme mois, annee de naissance+18
    return (
        checkdate($month,$day,$year) &&
        $date_of_18_years<=$today // Si la date des 18ans est inferieure ou egale a celle du jour
                                  // c'est que l'utilisateur a plus que 18ans
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
    return !isset($sexe) || empty(trim($sexe)) || preg_match("/^(male|female)$/", $sexe);
}


// Fonction pour obtenir tous les aliments de la hierarchie (y compris sous-categories)
function getAlimentsHierarchie($nomAliment, $hierarchie)
{
    $liste = array();
    $liste[] = $nomAliment;

    if (isset($hierarchie[$nomAliment]['sous-categorie'])) {
        foreach ($hierarchie[$nomAliment]['sous-categorie'] as $sousCat) {
            $sousListe = getAlimentsHierarchie($sousCat, $hierarchie);
            foreach ($sousListe as $element) {
                $liste[] = $element;
            }
        }
    }
    return $liste;
}
?>
