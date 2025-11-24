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
                <button type="submit" class="button-update" id="updateLastname" name="action" value="updateLastname">
                    Modifier
                </button>
                <button type="submit" class="button-update" id="resetLastname" name="action" value="resetLastname">
                    Reinitialiser
                </button>
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
                <button type="submit" class="button-update" id="updateFirstname" name="action" value="updateFirstname">
                    Modifier
                </button>
                <button type="submit" class="button-update" id="resetFirstname" name="action" value="resetFirstname">
                    Reinitialiser
                </button>
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
                <button type="submit" class="button-update" id="updateBirthdate" name="action" value="updateBirthdate">
                    Modifier
                </button>
                <button type="submit" class="button-update" id="resetBirthdate" name="action" value="resetBirthdate">
                    Reinitialiser
                </button>
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
                <button type="submit" class="button-update" id="updateGender" name="action" value="updateGender">
                    Modifier
                </button>
                <button type="submit" class="button-update" id="resetGender" name="action" value="resetGender">
                    Reinitialiser
                </button>
            </form>
        </fieldset>

    <?php } else { ?>
        <p>Un probl&egrave;me est survenu avec le chargement de vos informations personnelles...</p>
    <?php } ?>
<?php } else { ?>
    <p>Vous n&apos;&ecirc;tes pas connect&eacute;.</p>
<?php } ?>
