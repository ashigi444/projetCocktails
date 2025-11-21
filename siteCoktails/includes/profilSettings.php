<h2>Profil</h2>
<?php
include_once 'utils/utils.php';
$username= isset($user) && isset($user['username']) ? $user['username'] : null;
?>

<?php if(isset($username) && checkAccountAlreadyExists($username)) {
    $filename_user= isset($username) ? 'dataUsers/user'.$username.'.php' : "";
    require $filename_user;
    if(isset($infos_user) && is_array($infos_user)) { ?>
        <fieldset>
            <legend>Informations Personnelles</legend>
            <form class="form-settings" method="POST" action="index.php?page=profilSettings">
                <label for="username">Identifiant&nbsp;:
                    <input type="text" name="username" id="username"
                           placeholder="<?php echo $infos_user['username']; ?>"
                           value="<?php echo $infos_user['username']; ?>"
                    />
                </label>
                <button type="submit" class="buttonUpdate" id="updateUsername" name="action" value="updateProfil">Modifier</button>
            </form>

            <form class="form-settings" method="POST" action="index.php?page=profilSettings">
                <label for="oldPassword">Mot de passe&nbsp;:
                    <input type="password" name="oldPassword" id="oldPassword" placeholder="Ancien mot de passe..."
                        <?php if(isset($class_password)) { ?>
                            class="<?php echo $class_password; ?>"
                        <?php }
                        if(isset($old_password)) { ?>
                            value="<?php echo $old_password; ?>"
                        <?php } ?>
                    />
                </label>
                <?php if(isset($is_valid_old_password) && $is_valid_old_password){ ?>
                    <label for="newPassword">Nouveau mot de passe&nbsp;:
                        <input type="password" name="newPassword" id="newPassword" placeholder="Nouveau mot de passe..."/>
                    </label>
                    <button type="submit" class="buttonUpdate" id="annulUpdate" name="action" value="annulUpdate">Annuler</button>
                    <button type="submit" class="buttonUpdate" id="updatePassword" name="action" value="updateProfil">Confirmer</button>

                <?php }else { ?>
                    <button type="submit" class="buttonUpdate" id="wantUpdatePassword" name="action" value="wantUpdatePassword">Modifier</button>
                <?php } ?>
            </form>

            <form class="form-settings" method="POST" action="index.php?page=profilSettings">
                <label for="lastname">Nom&nbsp;:
                    <input type="text" name="lastname" id="lastname"
                        <?php if(isset($infos_user['lastname'])){ ?>
                           placeholder="<?php echo $infos_user['lastname']; ?>"
                           value="<?php echo $infos_user['lastname']; ?>"
                        <?php } ?>
                    />
                </label>
                <button type="submit" class="buttonUpdate" id="updateLastname" name="action" value="updateProfil">Modifier</button>
            </form>

            <form class="form-settings" method="POST" action="index.php?page=profilSettings">
                <label for="firstname">Pr&eacute;nom&nbsp;:
                    <input type="text" name="firstname" id="firstname"
                        <?php if(isset($infos_user['firstname'])){ ?>
                           placeholder="<?php echo $infos_user['firstname']; ?>"
                           value="<?php echo $infos_user['firstname']; ?>"
                        <?php } ?>
                    />
                </label>
                <button type="submit" class="buttonUpdate" id="updateFirstname" name="action" value="updateProfil">Modifier</button>
            </form>

            <form class="form-settings" method="POST" action="index.php?page=profilSettings">
                <label for="birthdate">Date de naissance&nbsp;:
                    <input type="date" name="birthdate" id="birthdate"
                        <?php if(isset($infos_user['birthdate'])){ ?>
                           placeholder="<?php echo $infos_user['birthdate']; ?>"
                           value="<?php echo $infos_user['birthdate']; ?>"
                        <?php } ?>
                    />
                </label>
                <button type="submit" class="buttonUpdate" id="updateBirthdate" name="action" value="updateProfil">Modifier</button>
            </form>

            <form class="form-settings" method="POST" action="index.php?page=profilSettings">
                <label>
                    Sexe&nbsp;:
                    <input type="radio" name="sexe" value="female"
                        <?php if(isset($infos_user['sexe']) && $infos_user['sexe']=="female"){ ?>
                            checked="checked"
                        <?php } ?>
                    />Femme
                    <input type="radio" name="sexe" value="male"
                        <?php if(isset($infos_user['sexe']) && $infos_user['sexe']=="male"){ ?>
                            checked="checked"
                        <?php } ?>
                    />Homme
                </label>
                <button type="submit" class="buttonUpdate" id="updateSexe" name="action" value="updateProfil">Modifier</button>
        </form>
        </fieldset>
    <?php }else{ ?>
        <p>Un probl&egrave;me est survenu avec le chargement de vos informations personnelles...</p>
    <?php } ?>
<?php }else { ?>
    <p>Vous n&apos;&ecirc;tes pas connect&eacute;.</p>
<?php } ?>
