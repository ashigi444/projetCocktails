<?php
?>
<h2>Profil</h2>
<?php
require_once 'utils/utils.php';
$username = isset($user) && isset($user['username']) ? $user['username'] : null;
?>

<?php if (isset($username) && checkAccountAlreadyExists($username)) {
    $infosUser = loadUserInfos($username);
    if (isset($infosUser) && is_array($infosUser) && !empty($infosUser)) { ?>
        <fieldset>
            <legend>Compte</legend>
            <form class="form-settings" method="POST" action="#">
                <label for="username">Identifiant&nbsp;:
                    <input type="text" name="username" id="username"
                           placeholder="Identifiant..."
                           value="<?php echo $infosUser['username']; ?>"
                           disabled="disabled"
                    />
                </label>
                <label for="statut" class="status-label">Statut de connexion&nbsp;:
                    <input type="radio" class="status-radio" id="statut" name="statut"
                            <?php if(isset($connectionStatus)) { ?>
                                checked
                            <?php } ?>
                           disabled="disabled"
                    />
                </label>
            </form>
        </fieldset>

        <fieldset>
            <legend>Informations Personnelles</legend>
            <form class="form-settings" method="POST" action="index.php?page=profilSettings">
                <label for="newLastname">Nom&nbsp;:
                    <input type="text" name="newLastname" id="newLastname"
                            <?php if (isset($infosUser['lastname'])) { ?>
                                placeholder="Nom..."
                                value="<?php echo $infosUser['lastname']; ?>"
                                <?php if (isset($classFields['lastname'])) { ?>
                                    class="<?php echo $classFields['lastname']; ?>"
                                <?php } ?>
                            <?php } ?>
                    />
                </label>
                <input class="button-update" type="submit" name="updateLastname" value="Modifier" />
                <input class="button-update" type="submit" name="resetLastname" value="Reinitialiser" />
            </form>

            <form class="form-settings" method="POST" action="index.php?page=profilSettings">
                <label for="newFirstname">Pr&eacute;nom&nbsp;:
                    <input type="text" name="newFirstname" id="newFirstname"
                            <?php if (isset($infosUser['firstname'])) { ?>
                                placeholder="Pr&eacute;nom..."
                                value="<?php echo $infosUser['firstname']; ?>"
                                <?php if (isset($classFields['firstname'])) { ?>
                                    class="<?php echo $classFields['firstname']; ?>"
                                <?php } ?>
                            <?php } ?>
                    />
                </label>

                <input class="button-update" type="submit" name="updateFirstname" value="Modifier" />
                <input class="button-update" type="submit" name="updateFirstname" value="Reinitialiser" />
            </form>

            <form class="form-settings" method="POST" action="index.php?page=profilSettings">
                <label for="newBirthdate">Date de naissance&nbsp;:
                    <input type="date" name="newBirthdate" id="newBirthdate"
                            <?php if (isset($infosUser['birthdate'])) { ?>
                                value="<?php echo $infosUser['birthdate']; ?>"
                                <?php if (isset($classFields['birthdate'])) { ?>
                                    class="<?php echo $classFields['birthdate']; ?>"
                                <?php } ?>
                            <?php } ?>
                    />
                </label>
                <input class="button-update" type="submit" name="updateBirthdate" value="Modifier" />
                <input class="button-update" type="submit" name="resetBirthdate" value="Reinitialiser" />
            </form>

            <form class="form-settings" method="POST" action="index.php?page=profilSettings">
                <span class="gender-wrapper">Sexe&nbsp;:
                    <label for="female"
                           class="gender-radio <?php echo isset($classFields['gender']) ? $classFields['gender'] : ''; ?>">
                    Femme
                        <input class="input-gender-radio" type="radio" id="female" name="newGender" value="female"
                        <?php if ($infosUser['gender']=="female") echo 'checked'; ?>>
                    </label>

                    <label for="male"
                           class="gender-radio <?php echo isset($classFields['gender']) ? $classFields['gender'] : ''; ?>">
                    Homme
                        <input class="input-gender-radio" type="radio" id="male" name="newGender" value="male"
                        <?php if ($infosUser['gender']=="male") echo 'checked'; ?>>
                    </label>
                </span>
                <input class="button-update" type="submit" name="updateGender" value="Modifier" />
                <input class="button-update" type="submit" name="resetGender" value="Reinitialiser" />
            </form>
        </fieldset>

    <?php } else { ?>
        <p>Un probl&egrave;me est survenu avec le chargement de vos informations personnelles...</p>
    <?php } ?>
<?php } else { ?>
    <p>Vous n&apos;&ecirc;tes pas connect&eacute;.</p>
<?php } ?>
