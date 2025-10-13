<h2>Inscription</h2>
<p>Enregistrement dans un fichier&nbsp;&agrave;&nbsp;impl&eacute;menter</p>
<p>Les champs marqu&eacute;s par une ast&eacute;risque&nbsp;(*) sont obligatoires.</p>
<fieldset>
    <legend>Inscription</legend>
    <form method="POST" action="index.php" class="form-signup">
        <label for="lastname">Nom&nbsp;:&nbsp;
            <input id="lastname" type="text" name="lastname"
                   placeholder="Nom" />
        </label>

        <label for="firstname">Pr&eacute;nom&nbsp;:&nbsp;
            <input id="firstname" type="text" name="firstname"
                   placeholder="Pr&eacute;nom" />
        </label>

        <label>
            Vous êtes :
            <input type="radio" name="sexe" value="female"/> Femme
            <input type="radio" name="sexe" value="male"/> Homme
        </label>

        <label for="birthdate">Date de naissance&nbsp;:&nbsp;
            <input id="birthdate" type="date" name="birthdate"/>
        </label>

        <label for="login">Identifiant*&nbsp;:&nbsp;
            <input id="login" type="text" name="login"
                   placeholder="Identifiant" required="required" />
        </label>

        <label for="password">Mot de passe*&nbsp;:&nbsp;
            <input id="password" type="password" name="password"
                   placeholder="Mot de passe" required="required" />
        </label>

        <button type="submit" name="action" value="signup">
            Inscription
        </button>
    </form>
</fieldset>