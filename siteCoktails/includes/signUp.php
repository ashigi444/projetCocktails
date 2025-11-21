<h2>Inscription</h2>
<p>Les champs marqu&eacute;s par une ast&eacute;risque&nbsp;(*) sont obligatoires.</p>
<fieldset>
    <legend>Inscription</legend>
    <form method="POST" action="index.php" class="form-signup">

        <label for="lastname">Nom&nbsp;:&nbsp;
            <input id="lastname" type="text" name="lastname"
                   placeholder="Nom"
                    <?php if (isset($value_fields['signupForm']['lastname'])) { ?>
                        value="<?php echo $value_fields['signupForm']['lastname']; ?>"
                    <?php }
                    if (isset($class_fields['signupForm']['lastname'])) { ?>
                        class="<?php echo $class_fields['signupForm']['lastname']; ?>"
                    <?php } ?>
            />
        </label>

        <label for="firstname">Pr&eacute;nom&nbsp;:&nbsp;
            <input id="firstname" type="text" name="firstname"
                   placeholder="Pr&eacute;nom"
                    <?php if (isset($value_fields['signupForm']['firstname'])) { ?>
                        value="<?php echo $value_fields['signupForm']['firstname']; ?>"
                    <?php }
                    if (isset($class_fields['signupForm']['firstname'])) { ?>
                        class="<?php echo $class_fields['signupForm']['firstname']; ?>"
                    <?php } ?>
            />
        </label>

        <?php $sexe_val = isset($value_fields['signupForm']['sexe']) ? $value_fields['signupForm']['sexe'] : ''; ?>
        <label>
            Vous&nbsp;&ecirc;tes :
            <input type="radio" name="sexe" value="female"
                   <?php if ($sexe_val === "female") { ?>checked="checked"<?php } ?>
                    <?php if (isset($class_fields['signupForm']['sexe'])) { ?>
                        class="<?php echo $class_fields['signupForm']['sexe']; ?>"
                    <?php } ?>
            /> Femme
            <input type="radio" name="sexe" value="male"
                   <?php if ($sexe_val === "male") { ?>checked="checked"<?php } ?>
                    <?php if (isset($class_fields['signupForm']['sexe'])) { ?>
                        class="<?php echo $class_fields['signupForm']['sexe']; ?>"
                    <?php } ?>
            /> Homme
        </label>

        <label for="birthdate">Date de naissance&nbsp;:&nbsp;
            <input id="birthdate" type="date" name="birthdate"
                    <?php if (isset($value_fields['signupForm']['birthdate'])) { ?>
                        value="<?php echo $value_fields['signupForm']['birthdate']; ?>"
                    <?php }
                    if (isset($class_fields['signupForm']['birthdate'])) { ?>
                        class="<?php echo $class_fields['signupForm']['birthdate']; ?>"
                    <?php } ?>
            />
        </label>

        <label for="username">Identifiant*&nbsp;:&nbsp;
            <input id="username" type="text" name="username"
                   placeholder="Identifiant" required="required"
                    <?php if (isset($value_fields['signupForm']['username'])) { ?>
                        value="<?php echo $value_fields['signupForm']['username']; ?>"
                    <?php }
                    if (isset($class_fields['signupForm']['username'])) { ?>
                        class="<?php echo $class_fields['signupForm']['username']; ?>"
                    <?php } ?>
            />
        </label>

        <label for="password">Mot de passe*&nbsp;:&nbsp;
            <input id="password" type="password" name="password"
                   placeholder="Mot de passe" required="required"
                    <?php if (isset($class_fields['signupForm']['password'])) { ?>
                        class="<?php echo $class_fields['signupForm']['password']; ?>"
                    <?php } ?>
            />
        </label>

        <button class="buttonSub" type="submit" name="action" value="signup">
            Inscription
        </button>
    </form>
</fieldset>