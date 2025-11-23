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
    /*$filename = preg_replace('/[aAaAæÆaAaAaAaAaAeEeEeEeEeEeEeEyYuUuUuUuUuUiIiIiIiIiIiIoOœŒoOoOoOoOoOcCcCcCnNnN]+(([-']|( )*)[a-zA-ZaAaAæÆaAaAaAaAaAeEeEeEeEeEeEeEyYuUuUuUuUuUiIiIiIiIiIiIoOœŒoOoOoOoOoOcCcCcCnNnN]+)*)+$/', 'a',$filename)
    $filename = preg_replace('/[eEeEeEeEeEeEeE]/', 'e',$filename)
    $filename = preg_replace('/[yY]/', 'y',$filename)
    $filename = preg_replace('/[uUuUuUuUuU]/', 'u',$filename)
    $filename = preg_replace('/[iIiIiIiIiIiI]/', 'i',$filename)
    $filename = preg_replace('/[oOœŒoOoOoOoOoO]/', 'o',$filename)
    $filename = preg_replace('/[cCcCcC]/', 'c',$filename)
    $filename = preg_replace('/[nNnN]/', 'n',$filename)*/
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

function makeFilenameUser($username){
    return "dataUsers/user".makeFilenameCorrected($username).".php";
}

/**
 *
 *
 * @param $username
 * @return array|null
 */
function loadUserInfos($username){
    $filename=makeFilenameUser($username);
    if (!file_exists($filename)) {
        return null;
    }
    $infosUser = null;
    require $filename;
    if (!isset($infosUser) || !is_array($infosUser) || empty($infosUser)) {
        return null;
    }
    return $infosUser;
}

/**
 *
 *
 * @param $username
 * @param $infosUser
 * @return void
 */
function saveUserInfos($username, $infosUser) {
    $filename = makeFilenameUser($username);
    if (!is_dir('dataUsers')) {
        mkdir('dataUsers', 0755, true);
    }
    $users_print = var_export($infosUser, true);
    $users_put = "<?php\n\$infosUser = " . $users_print . ";\n?>";
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
    return file_exists(makeFilenameUser($username));
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

    $infosUser=loadUserInfos($username);
    if (isset($infosUser) && is_array($infosUser) && !empty($infosUser)) {
        return [
            'username' => checkUsernameFile($username, $infosUser),
            'password' => checkPasswordFile($password, $infosUser)
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
 * qui, generalement a l'appel de la fonction, vient du fichier d'utilisateur
 *
 * @param string $username le nom d'utilisateur a verifier
 * @param array $infosUser le tableau d'informations sur l'utilisateur
 * @return bool true si le nom d'utilisateur correspond dans le tableau, false sinon
 */
function checkUsernameFile($username, $infosUser){
    // On verifie quand meme si login existe dans le tableau
    return isset($infosUser['username']) &&
        $infosUser['username'] == $username;
}

/**
 * Verifie si le mot de passe est correct dans le tableau d'informations sur l'utilisateur
 * qui, generalement a l'appel de la fonction, vient du fichier d'utilisateur
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

function checkSexeFile($gender, $infosUser){
    return isset($infosUser['sexe']) &&
        $gender == $infosUser['sexe'];
}

// Toutes les fonctions de verification de champ, a modifier avec des regex plus strictes
/**
 * Verifie si le nom d'utilisateur est correct par rapport a une regex
 * le nom d'utilisateur provient normalement d'un champ rempli par l'utilisateur
 * On n'accepte pas le nom d'utilisateur vide
 *
 * @param string $username le nom d'utilisateur a verifier
 * @return bool true si le nom d'utilisateur existe, n'est pas vide et passe la regex, false sinon
 */
function checkUsernameField($username) {
    return isset($username) && !empty(trim($username)) && preg_match("/^[a-zA-Z0-9]+$/", $username);
}

/**
 * Verifie si le mot de passe est correct par rapport a une regex
 * le mot de passe provient normalement d'un champ rempli par l'utilisateur
 * On n'autorise pas le mot de passe vide.
 *
 * @param string $password le mot de passe a verifier
 * @return bool true si le mot de passe existe, n'est pas vide et passe la regex, false sinon
 */
function checkPasswordField($password) {
    return isset($password) && !empty(trim($password)) && preg_match("/^.+$/", $password);
}

/**
 * Verifie si le nom est correct par rapport a une regex qui accepte
 * les noms (et prenom) qui sont composes de lettres minuscules et/ou de lettres MAJUSCULES,
 * les caracteres "-", " " et "'".
 * Les lettres peuvent etre accentuees.
 * Tirets et apostrophes sont forcements encadres par deux lettres,
 * Par contre plusieurs espaces sont possibles entre deux parties de prenom/nom.
 * Le nom provient normalement d'un champ rempli par l'utilisateur
 *
 * @param string $lastname le nom a verifier
 * @return bool true si le nom existe, n'est pas vide et passe la regex, false sinon
 */
function checkLastnameField($lastname){
    return !isset($lastname) || empty(trim($lastname)) || preg_match(
        "/^([a-zA-ZàÀâÂæÆáÁäÄãÃåÅāĀéÉèÈêÊëËęĘėĖēĒÿŸûÛùÙüÜúÚūŪîÎïÏìÌíÍįĮīĪôÔœŒöÖòÒóÓõÕōŌçÇćĆčČñÑńŃ]+(([-']|( )*)[a-zA-ZàÀâÂæÆáÁäÄãÃåÅāĀéÉèÈêÊëËęĘėĖēĒÿŸûÛùÙüÜúÚūŪîÎïÏìÌíÍįĮīĪôÔœŒöÖòÒóÓõÕōŌçÇćĆčČñÑńŃ]+)*)+$/", $lastname);
}

/**
 * Verifie si le prenom est correct par rapport la meme regex que sur le lastname
 * le prenom provient normalement d'un champ rempli par l'utilisateur
 *
 * @param string $firstname le prenom a verifier
 * @return bool true si le prenom existe, n'est pas vide et passe la regex, false sinon
 */
function checkFirstnameField($firstname){
    return checkLastnameField($firstname); // la meme regex et les memes contraintes...
}

/**
 * Verifie si la date de naissance est correct,
 * on verifie si la date de naissance est valide et si aujourd'hui est au moins 18ans apres la naissance
 * la date de naissance provient normalement d'un champ rempli par l'utilisateur
 *
 * @param string $birthdate la date de naissance a verifier
 * @return bool true si la date de naissance existe, n'est pas vide et passe la regex, false sinon
 */
function checkBirthdateField($birthdate){
    // Vide autorise car champ optionnel
    if(!isset($birthdate) || empty(trim($birthdate)))
        return true;

    $today=date("Y-m-d");
    list($year,$month,$day)=explode('-',$birthdate);

    $dateOf18Years=($year+18)."-".$month."-".$day; // On a 18ans lorsque la date est
                                                      // meme jour, meme mois, annee de naissance+18
    return (
        checkdate($month,$day,$year) &&
        $dateOf18Years<=$today // Si la date des 18ans est inferieure ou egale a celle du jour
                                  // c'est que l'utilisateur a plus que 18ans
    );
}

/**
 * Verifie si le sexe est correct par rapport a une regex qui match sur 'male' ou 'female'
 * le sexe provient normalement d'un champ rempli par l'utilisateur
 *
 * @param string $gender le sexe a verifier
 * @return bool true si le sexe existe, n'est pas vide et passe la regex, false sinon
 */
function checkSexeField($gender){
    // Vide autorise car champ optionnel
    return !isset($gender) || empty(trim($gender)) || preg_match("/^(male|female)$/", $gender);
}


// Fonction pour obtenir tous les aliments de la hierarchie (y compris sous-categories)
function getIngredientsHierarchy($ingredientName, $hierarchie)
{
    $liste = array();
    $liste[] = $ingredientName;

    if (isset($hierarchie[$ingredientName]['sous-categorie'])) {
        foreach ($hierarchie[$ingredientName]['sous-categorie'] as $sousCat) {
            $sousListe = getIngredientsHierarchy($sousCat, $hierarchie);
            foreach ($sousListe as $element) {
                $liste[] = $element;
            }
        }
    }
    return $liste;
}
?>
