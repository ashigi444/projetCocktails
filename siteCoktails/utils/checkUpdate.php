<?php
require_once "utils/utils.php";

function updateField($typeUpdate, $newValue, $successMsg, $errorMsg) {
    $messages = [];
    $messages_errors = [];
    $class_fields = null;
    $allows_update = true;

    if (!isset($_SESSION['user']['username'])) {
        $messages_errors[] = "Vous n'êtes pas connecté.";
        $allows_update = false;
    }

    $username = $_SESSION['user']['username'];
    $infosUser = loadUserInfos($username);
    if (!isset($infosUser) || !is_array($infosUser) || empty($infosUser)) {
        $messages_errors[] = "Impossible de charger vos informations.";
        $allows_update = false;
    }

    if($allows_update){ // Si jusqu'ici c'est correct, on teste la validite du champ
        if($typeUpdate == "updateLastname"){
            if(!checkLastnameField($newValue) || checkLastnameFile($newValue, $infosUser)){
                // Si le champ est mauvais OU si c'est le meme que l'ancien
                $allows_update = false;
            }
        }else if($typeUpdate == "updateFirstname"){
            if(!checkFirstnameField($newValue) || checkFirstnameFile($newValue, $infosUser)){
                // Si le champ est mauvais OU si c'est le meme que l'ancien
                $allows_update = false;
            }
        }else if ($typeUpdate == "updateBirthdate"){
            if(!checkBirthdateField($newValue) || checkBirthdateFile($newValue, $infosUser)){
                // Si le champ est mauvais OU si c'est le meme que l'ancien
                $allows_update = false;
            }
        }else if($typeUpdate == "updateSexe"){
            if(!checkSexeField($newValue) || checkSexeFile($newValue, $infosUser)){
                // Si le champ est mauvais OU si c'est le meme que l'ancien
                $allows_update = false;
            }
        }else{
            $allows_update = false;
        }
    }

    if(!$allows_update){ // Si la modification n'est pas autorisee
        $messages_errors[] = $errorMsg;
        $class_fields="error";
        return [
            'messages' => $messages,
            'messages_errors' => $messages_errors,
            'class_fields' => $class_fields,
            'allows_update' => $allows_update
        ];
    }

    $messages[] = $successMsg;
    return [
        'messages' => $messages,
        'messages_errors' => $messages_errors,
        'class_fields' => $class_fields,
        'allows_update' => $allows_update
    ];
}

function checkUpdateLastname($new_lastname){
    return updateField(
        "updateLastname",
        $new_lastname,
        "Nom modifi&eacute;.",
        "Impossible de modifier le nom."
    );
}

function checkUpdateFirstname($new_firstname){
    return updateField(
        "updateFirstname",
        $new_firstname,
        "Pr&eacute;nom modifi&eacute;.",
        "Impossible de modifier le pr&eacute;nom."
    );
}

function checkUpdateBirthdate($new_birthdate){
    return updateField(
        "updateBirthdate",
        $new_birthdate,
        "Date de naissance modifi&eacute;e.",
        "Impossible de modifier la date de naissance."
    );
}

function checkUpdateSexe($new_sexe){
    return updateField(
        "updateSexe",
        $new_sexe,
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
