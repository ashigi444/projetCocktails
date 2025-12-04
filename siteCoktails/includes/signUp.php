<?php
?>
<h2>Inscription</h2>
<p>Les champs marqu&eacute;s par une ast&eacute;risque&nbsp;(*) sont obligatoires.</p>
<fieldset>
    <legend>Inscription</legend>
    <!-- Formulaire d'inscription de l'utilisateur -->
    <form method="POST" action="index.php" class="form-signup">
        <!-- Section du Nom -->
        <label for="lastname">Nom&nbsp;:&nbsp;
            <input id="lastname" type="text" name="lastname"
                   placeholder="Nom"
                    <?php if (isset($valueFields['signupForm']['lastname'])) { ?>
                        value="<?php echo $valueFields['signupForm']['lastname']; ?>"
                    <?php }
                    if (isset($classFields['signupForm']['lastname'])) { ?>
                        class="<?php echo $classFields['signupForm']['lastname']; ?>"
                    <?php } ?>
            />
        </label>
        <!-- Section du Prenom -->
        <label for="firstname">Pr&eacute;nom&nbsp;:&nbsp;
            <input id="firstname" type="text" name="firstname"
                   placeholder="Pr&eacute;nom"
                    <?php if (isset($valueFields['signupForm']['firstname'])) { ?>
                        value="<?php echo $valueFields['signupForm']['firstname']; ?>"
                    <?php }
                    if (isset($classFields['signupForm']['firstname'])) { ?>
                        class="<?php echo $classFields['signupForm']['firstname']; ?>"
                    <?php } ?>
            />
        </label>

        <!-- Section de recuperation de la valeur et de la classe d'erreur associees au champ "gender" -->
        <?php
        $genderValue = isset($valueFields['signupForm']['gender']) ? $valueFields['signupForm']['gender'] : null;
        $genderErrorClass = isset($classFields['signupForm']['gender']) ? $classFields['signupForm']['gender'] : null;
        ?>

        <!--Section ou l'on choisit le sexe-->
        <span class="gender-wrapper">
            Vous&nbsp;êtes&nbsp;:

            <label for="signupFemale"
                   class="gender-radio <?php
                   if(isset($classFields['signupForm']['gender']) && $classFields['signupForm']['gender'] == "error") {
                       echo $classFields['signupForm']['gender'];
                   } ?>">
                Femme
                <input class="input-gender-radio" type="radio" id="signupFemale" name="gender" value="female"
                    <?php if ($genderValue === "female") { ?>
                        checked="checked"
                    <?php } ?>
                />
            </label>

            <label for="signupMale"
                   class="gender-radio <?php
                   if(isset($classFields['signupForm']['gender']) && $classFields['signupForm']['gender'] == "error") {
                       echo $classFields['signupForm']['gender'];
                   } ?>">
               Homme
                <input class="input-gender-radio" type="radio" id="signupMale" name="gender" value="male"
                    <?php if ($genderValue === "male") { ?>
                        checked="checked"
                    <?php } ?>
                />
            </label>
        </span>
        <!-- Section de la date de naissance -->
        <label for="birthdate">Date de naissance&nbsp;:&nbsp;
            <input id="birthdate" type="date" name="birthdate"
                    <?php if (isset($valueFields['signupForm']['birthdate'])) { ?>
                        value="<?php echo $valueFields['signupForm']['birthdate']; ?>"
                    <?php }
                    if (isset($classFields['signupForm']['birthdate'])) { ?>
                        class="<?php echo $classFields['signupForm']['birthdate']; ?>"
                    <?php } ?>
            />
        </label>

        <!-- Section de l'identifiant -->
        <label for="username">Identifiant*&nbsp;:&nbsp;
            <input id="username" type="text" name="username"
                   placeholder="Identifiant" required="required"
                    <?php if (isset($valueFields['signupForm']['username'])) { ?>
                        value="<?php echo $valueFields['signupForm']['username']; ?>"
                    <?php }
                    if (isset($classFields['signupForm']['username'])) { ?>
                        class="<?php echo $classFields['signupForm']['username']; ?>"
                    <?php } ?>
            />
        </label>

        <!-- Section du mot de passe -->
        <label for="password">Mot de passe*&nbsp;:&nbsp;
            <input id="password" type="password" name="password"
                   placeholder="Mot de passe" required="required"
                    <?php if (isset($classFields['signupForm']['password'])) { ?>
                        class="<?php echo $classFields['signupForm']['password']; ?>"
                    <?php } ?>
            />
        </label>

        <!-- Bouton d'envoi du formulaire, et donc de l'inscription de l'utilisateur -->
        <input class="button-sub" type="submit" name="signup" value="Inscription" />
    </form>
</fieldset>
