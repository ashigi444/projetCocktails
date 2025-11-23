<?php
require_once "utils/utils.php";

function updateField($typeUpdate, $newValue, $successMsg, $errorMsg) {
    $messages = [];
    $messagesErrors = [];
    $classFields = null;
    $allowsUpdate = true;

    if (!isset($_SESSION['user']['username'])) {
        $messagesErrors[] = "Vous n&apos;&ecirc;tes pas connect&eacute;.";
        $allowsUpdate = false;
    }

    if(isset($newValue) && empty(trim($newValue))) { // le cas rempli d'espaces
        $allowsUpdate=false;
    }

    $username = $_SESSION['user']['username'];
    $infosUser = loadUserInfos($username);
    if (!isset($infosUser) || !is_array($infosUser) || empty($infosUser)) {
        $messagesErrors[] = "Impossible de charger vos informations.";
        $allowsUpdate = false;
    }

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
        }else if($typeUpdate == "updateSexe"){
            if(!checkSexeField($newValue) || checkSexeFile($newValue, $infosUser)){
                // Si le champ est mauvais OU si c'est le meme que l'ancien
                $allowsUpdate = false;
            }
        }else{
            $allowsUpdate = false;
        }
    }

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

    $messages[] = $successMsg;
    return [
        'messages' => $messages,
        'messagesErrors' => $messagesErrors,
        'classFields' => $classFields,
        'allowsUpdate' => $allowsUpdate
    ];
}

function checkUpdateLastname($newLastName){
    return updateField(
        "updateLastname",
        $newLastName,
        "Nom modifi&eacute;.",
        "Impossible de modifier le nom."
    );
}

function checkUpdateFirstname($newFirstName){
    return updateField(
        "updateFirstname",
        $newFirstName,
        "Pr&eacute;nom modifi&eacute;.",
        "Impossible de modifier le pr&eacute;nom."
    );
}

function checkUpdateBirthdate($newBirthdate){
    return updateField(
        "updateBirthdate",
        $newBirthdate,
        "Date de naissance modifi&eacute;e.",
        "Impossible de modifier la date de naissance."
    );
}

function checkUpdateSexe($newGender){
    return updateField(
        "updateSexe",
        $newGender,
        "Sexe modifi&eacute;.",
        "Impossible de modifier le sexe."
    );
}

function applyUpdateFile($fieldKey, $newValue){
    if (!isset($_SESSION['user']['username'])) {
        return;
    }

    $username = $_SESSION['user']['username'];
    $infosUser = loadUserInfos($username);
    if (!isset($infosUser) || !is_array($infosUser) || empty($infosUser)) {
        return;
    }

    if ($fieldKey === 'password') {
        $infosUser['password'] = password_hash($newValue, PASSWORD_DEFAULT);
    } else {
        $infosUser[$fieldKey] = $newValue;
    }

    // On ne touche pas au reste
    saveUserInfos($username, $infosUser);
}

?>
