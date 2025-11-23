<?php
?>
<h2>Inscription</h2>
<p>Les champs marqu&eacute;s par une ast&eacute;risque&nbsp;(*) sont obligatoires.</p>
<fieldset>
    <legend>Inscription</legend>
    <form method="POST" action="index.php" class="form-signup">

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

        <?php $genderValue = isset($valueFields['signupForm']['sexe']) ? $valueFields['signupForm']['sexe'] : ''; ?>
        <label>
            Vous&nbsp;&ecirc;tes :
            <input type="radio" name="sexe" value="female"
                   <?php if ($genderValue === "female") { ?>checked="checked"<?php } ?>
                    <?php if (isset($classFields['signupForm']['sexe'])) { ?>
                        class="<?php echo $classFields['signupForm']['sexe']; ?>"
                    <?php } ?>
            /> Femme
            <input type="radio" name="sexe" value="male"
                   <?php if ($genderValue === "male") { ?>checked="checked"<?php } ?>
                    <?php if (isset($classFields['signupForm']['sexe'])) { ?>
                        class="<?php echo $classFields['signupForm']['sexe']; ?>"
                    <?php } ?>
            /> Homme
        </label>

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

        <label for="password">Mot de passe*&nbsp;:&nbsp;
            <input id="password" type="password" name="password"
                   placeholder="Mot de passe" required="required"
                    <?php if (isset($classFields['signupForm']['password'])) { ?>
                        class="<?php echo $classFields['signupForm']['password']; ?>"
                    <?php } ?>
            />
        </label>

        <button class="button-sub" type="submit" name="action" value="signup">
            Inscription
        </button>
    </form>
</fieldset>
