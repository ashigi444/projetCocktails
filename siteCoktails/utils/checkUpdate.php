<?php
require_once "utils/utils.php";

/**
 * Gere la verification et la preparation de la mise a jour d'un champ du profil utilisateur.
 * Verifie la connexion, la validite de la nouvelle valeur, et le type de mise a jour demande.
 * Si la mise a jour est autorisee, renvoie un tableau avec messages et indicateur de succes,
 * sinon renvoie un tableau avec messages d'erreurs et la classe CSS a appliquer.
 *
 * @param $typeUpdate le type de champ a modifier (updateLastname, updateFirstname, updateBirthdate, updateGender)
 * @param $newValue la nouvelle valeur proposee pour le champ
 * @param $successMsg le message de succes a afficher si la mise a jour est valide
 * @param $errorMsg le message d'erreur a afficher si la mise a jour est refusee
 * @return array tableau contenant messages, messages d'erreurs, classe CSS et booleen allowsUpdate
 */
function updateField($typeUpdate, $newValue, $successMsg, $errorMsg) {
    // tableau pour messages classiques
    $messages = [];
    // tableau pour messages d'erreurs
    $messagesErrors = [];
    // classe CSS a appliquer au champ (ex: "error")
    $classFields = null;
    // booleen qui controle si on autorise la mise a jour
    $allowsUpdate = true;

    // verification que l'utilisateur est bien connecte
    if (!isset($_SESSION['user']['username'])) {
        $messagesErrors[] = "Vous n&apos;&ecirc;tes pas connect&eacute;.";
        $allowsUpdate = false;
    }

    // verification que la nouvelle valeur n'est pas composee uniquement d'espaces
    if(isset($newValue) && empty(trim($newValue))) { // le cas rempli d'espaces
        $allowsUpdate=false;
    }

    // recuperation du nom d'utilisateur courant
    $username = $_SESSION['user']['username'];
    // chargement des informations utilisateur depuis le fichier
    $infosUser = loadUserInfos($username);
    if (!isset($infosUser) || !is_array($infosUser) || empty($infosUser)) {
        // si les infos utilisateur ne sont pas disponibles, on bloque la mise a jour
        $messagesErrors[] = "Impossible de charger vos informations.";
        $allowsUpdate = false;
    }

    // si tout est encore bon, on verifie la validite du nouveau champ selon le type de mise a jour
    if($allowsUpdate){ // Si jusqu'ici c'est correct, on teste la validite du champ
        if($typeUpdate == "updateLastname"){
            if(!checkLastnameField($newValue) || checkLastnameFile($newValue, $infosUser)){
                // Si le champ est mauvais OU si c'est le meme que l'ancien
                $allowsUpdate = false;
            }
        }else if($typeUpdate == "updateFirstname"){
            if(!checkFirstnameField($newValue) || checkFirstnameFile($newValue, $infosUser)){
                // Si le champ est mauvais OU si c'est le meme que l'ancien
                $allowsUpdate = false;
            }
        }else if ($typeUpdate == "updateBirthdate"){
            if(!checkBirthdateField($newValue) || checkBirthdateFile($newValue, $infosUser)){
                // Si le champ est mauvais OU si c'est le meme que l'ancien
                $allowsUpdate = false;
            }
        }else if($typeUpdate == "updateGender"){
            if(!checkGenderField($newValue) || checkGenderFile($newValue, $infosUser)){
                // Si le champ est mauvais OU si c'est le meme que l'ancien
                $allowsUpdate = false;
            }
        }else{
            // type de mise a jour inconnu, on refuse
            $allowsUpdate = false;
        }
    }

    // si la modification est refusee, on renvoie la structure avec message d'erreur
    if(!$allowsUpdate){ // Si la modification n'est pas autorisee
        $messagesErrors[] = $errorMsg;
        $classFields="error";
        return [
            'messages' => $messages,
            'messagesErrors' => $messagesErrors,
            'classFields' => $classFields,
            'allowsUpdate' => $allowsUpdate
        ];
    }

    // sinon, la modification est valide (cote valeur), on renvoie le message de succes
    $messages[] = $successMsg;
    return [
        'messages' => $messages,
        'messagesErrors' => $messagesErrors,
        'classFields' => $classFields,
        'allowsUpdate' => $allowsUpdate
    ];
}

// Fonction specifique pour verifier/modifier le nom de famille
/**
 * Verifie et prepare la mise a jour du nom de famille de l'utilisateur.
 * Appelle updateField avec le type de mise a jour "updateLastname".
 *
 * @param string $newLastName le nouveau nom de famille propose
 * @return array tableau de resultat retourne par updateField
 */
function checkUpdateLastname($newLastName){
    return updateField(
        "updateLastname",
        $newLastName,
        "Nom modifi&eacute;.",
        "Impossible de modifier le nom."
    );
}

// Fonction specifique pour verifier/modifier le prenom
/**
 * Verifie et prepare la mise a jour du prenom de l'utilisateur.
 * Appelle updateField avec le type de mise a jour "updateFirstname".
 *
 * @param string $newFirstName le nouveau prenom propose
 * @return array tableau de resultat retourne par updateField
 */
function checkUpdateFirstname($newFirstName){
    return updateField(
        "updateFirstname",
        $newFirstName,
        "Pr&eacute;nom modifi&eacute;.",
        "Impossible de modifier le pr&eacute;nom."
    );
}

// Fonction specifique pour verifier/modifier la date de naissance
/**
 * Verifie et prepare la mise a jour de la date de naissance de l'utilisateur.
 * Appelle updateField avec le type de mise a jour "updateBirthdate".
 *
 * @param string $newBirthdate la nouvelle date de naissance au format attendu
 * @return array tableau de resultat retourne par updateField
 */
function checkUpdateBirthdate($newBirthdate){
    return updateField(
        "updateBirthdate",
        $newBirthdate,
        "Date de naissance modifi&eacute;e.",
        "Impossible de modifier la date de naissance."
    );
}

// Fonction specifique pour verifier/modifier le sexe (gender)
/**
 * Verifie et prepare la mise a jour du sexe (gender) de l'utilisateur.
 * Appelle updateField avec le type de mise a jour "updateGender".
 *
 * @param string $newGender le nouveau gender propose (male ou female)
 * @return array tableau de resultat retourne par updateField
 */
function checkUpdateGender($newGender){
    return updateField(
        "updateGender",
        $newGender,
        "Sexe modifi&eacute;.",
        "Impossible de modifier le gender."
    );
}

// Applique la mise a jour directement dans le fichier utilisateur
/**
 * Applique directement une modification dans le fichier utilisateur.
 * Charge les informations de l'utilisateur courant, modifie la cle donnee,
 * puis sauvegarde le tableau complet via saveUserInfos.
 *
 * @param string $fieldKey la cle du champ a modifier dans le tableau infosUser
 * @param mixed $newValue la nouvelle valeur a ecrire dans le fichier
 * @return void dans le cas d'une erreur
 */
function applyUpdateFile($fieldKey, $newValue){
    // si l'utilisateur n'est pas connecte, on ne fait rien
    if (!isset($_SESSION['user']['username'])) {
        return;
    }

    // recuperation username et infos utilisateur
    $username = $_SESSION['user']['username'];
    $infosUser = loadUserInfos($username);
    if (!isset($infosUser) || !is_array($infosUser) || empty($infosUser)) {
        // si on ne peut pas charger les infos, on arrete
        return;
    }

    // mise a jour simple du champ demande dans le tableau utilisateur
    $infosUser[$fieldKey] = $newValue;

    // On ne touche pas au reste
    saveUserInfos($username, $infosUser);
}

// Reinitialise le nom de famille dans le fichier utilisateur
/**
 * Reinitialise le nom de famille de l'utilisateur dans le fichier.
 * Utilise applyUpdateFile pour mettre une chaine vide dans le champ lastname.
 *
 * @return string message de confirmation pour l'utilisateur
 */
function resetLastname(){
    applyUpdateFile('lastname', '');
    return "Nom r&eacute;initialis&eacute;.";
}

// Reinitialise le prenom dans le fichier utilisateur
/**
 * Reinitialise le prenom de l'utilisateur dans le fichier.
 * Utilise applyUpdateFile pour mettre une chaine vide dans le champ firstname.
 *
 * @return string message de confirmation pour l'utilisateur
 */
function resetFirstname(){
    applyUpdateFile('firstname', '');
    return "Pr&eacute;nom r&eacute;initialis&eacute;.";
}

// Reinitialise la date de naissance dans le fichier utilisateur
/**
 * Reinitialise la date de naissance de l'utilisateur dans le fichier.
 * Utilise applyUpdateFile pour mettre une chaine vide dans le champ birthdate.
 *
 * @return string message de confirmation pour l'utilisateur
 */
function resetBirthdate(){
    applyUpdateFile('birthdate', '');
    return "Date de naissance r&eacute;initialis&eacute;.";
}

// Reinitialise le sexe dans le fichier utilisateur
/**
 * Reinitialise le sexe (gender) de l'utilisateur dans le fichier.
 * Utilise applyUpdateFile pour mettre une chaine vide dans le champ gender.
 *
 * @return string message de confirmation pour l'utilisateur
 */
function resetGender(){
    applyUpdateFile('gender', '');
    return "Sexe r&eacute;initialis&eacute;.";
}

?>
