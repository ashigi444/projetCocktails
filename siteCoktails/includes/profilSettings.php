<h2>Profil</h2>
<?php
require_once 'utils/utils.php';
$username = isset($user) && isset($user['username']) ? $user['username'] : null;
?>

<?php if (isset($username) && checkAccountAlreadyExists($username)) {
    $infos_user = loadUserInfos($username);
    if (isset($infos_user) && is_array($infos_user) && !empty($infos_user)) { ?>
        <fieldset>
            <legend>Compte</legend>
            <form class="form-settings" method="POST" action="#">
                <label for="new_username">Identifiant&nbsp;:
                    <input type="text" name="new_username" id="new_username"
                           placeholder="Identifiant..."
                           value="<?php echo $infos_user['username']; ?>"
                            <?php if (isset($class_fields['username'])) { ?>
                                class="<?php echo $class_fields['username']; ?>"
                            <?php } ?>
                            disabled="disabled"
                    />
                </label>
                <label for="statut">Statut de connexion&nbsp;:
                    <input type="color" id="statut" name="statut"
                       <?php if(isset($statut_connexion)) { ?>
                           value="#00FF00"
                           disabled="disabled"
                       <?php } ?>
                    />
                </label>
            </form>
        </fieldset>

        <fieldset>
            <legend>Informations Personnelles</legend>
            <form class="form-settings" method="POST" action="index.php?page=profilSettings">
                <label for="new_lastname">Nom&nbsp;:
                    <input type="text" name="new_lastname" id="new_lastname"
                            <?php if (isset($infos_user['lastname'])) { ?>
                                placeholder="Nom..."
                                value="<?php echo $infos_user['lastname']; ?>"
                                <?php if (isset($class_fields['lastname'])) { ?>
                                    class="<?php echo $class_fields['lastname']; ?>"
                                <?php } ?>
                            <?php } ?>
                    />
                </label>
                <button type="submit" class="buttonUpdate" id="updateLastname" name="action" value="updateLastname">
                    Modifier
                </button>
            </form>

            <form class="form-settings" method="POST" action="index.php?page=profilSettings">
                <label for="new_firstname">Pr&eacute;nom&nbsp;:
                    <input type="text" name="new_firstname" id="new_firstname"
                            <?php if (isset($infos_user['firstname'])) { ?>
                                placeholder="Pr&eacute;nom..."
                                value="<?php echo $infos_user['firstname']; ?>"
                                <?php if (isset($class_fields['firstname'])) { ?>
                                    class="<?php echo $class_fields['firstname']; ?>"
                                <?php } ?>
                            <?php } ?>
                    />
                </label>
                <button type="submit" class="buttonUpdate" id="updateFirstname" name="action" value="updateFirstname">
                    Modifier
                </button>
            </form>

            <form class="form-settings" method="POST" action="index.php?page=profilSettings">
                <label for="new_birthdate">Date de naissance&nbsp;:
                    <input type="date" name="new_birthdate" id="new_birthdate"
                            <?php if (isset($infos_user['birthdate'])) { ?>
                                value="<?php echo $infos_user['birthdate']; ?>"
                                <?php if (isset($class_fields['birthdate'])) { ?>
                                    class="<?php echo $class_fields['birthdate']; ?>"
                                <?php } ?>
                            <?php } ?>
                    />
                </label>
                <button type="submit" class="buttonUpdate" id="updateBirthdate" name="action" value="updateBirthdate">
                    Modifier
                </button>
            </form>

            <form class="form-settings" method="POST" action="index.php?page=profilSettings">
                <label>
                    Sexe&nbsp;:
                    <input type="radio" name="new_sexe" value="female"
                            <?php if (isset($infos_user['sexe']) && $infos_user['sexe']=="female") { ?>
                                checked="checked"
                                <?php if (isset($class_fields['sexe'])) { ?>
                                    class="<?php echo $class_fields['sexe']; ?>"
                                <?php } ?>
                            <?php } ?>
                    />Femme
                    <input type="radio" name="new_sexe" value="male"
                            <?php if (isset($infos_user['sexe']) && $infos_user['sexe']=="male") { ?>
                                checked="checked"
                                <?php if (isset($class_fields['sexe'])) { ?>
                                    class="<?php echo $class_fields['sexe']; ?>"
                                <?php } ?>
                            <?php } ?>
                    />Homme
                </label>
                <button type="submit" class="buttonUpdate" id="updateSexe" name="action" value="updateSexe">
                    Modifier
                </button>
            </form>
        </fieldset>

    <?php } else { ?>
        <p>Un probl&egrave;me est survenu avec le chargement de vos informations personnelles...</p>
    <?php } ?>
<?php } else { ?>
    <p>Vous n&apos;&ecirc;tes pas connect&eacute;.</p>
<?php } ?>
