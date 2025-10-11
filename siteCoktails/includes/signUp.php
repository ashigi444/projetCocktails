<h2>Inscription</h2>
<p>Enregistrement dans un fichier&nbsp;&agrave;&nbsp;impl&eacute;menter</p>
<fieldset>
    <legend>Inscription</legend>
    <?php
    $redirectionForm='#';
    if(isset($page))
        if($page=="signUp")
            $redirectionForm='index.php';
    ?>
    <form method="POST" action="<?php echo $redirectionForm; ?>" class="form-signup">
        <label for="login" id="login">Identifiant&nbsp;:&nbsp;
            <input type="text" name="login"
                   placeholder="Identifiant" required="required" />
        </label>
        <label for="password" id="password">Mot de passe&nbsp;:&nbsp;
            <input type="password" name="password"
                   placeholder="Mot de passe" required="required" />
        </label>
        <button type="submit" name="action" value="login"> <!-- name et value temporaires -->
            Inscription
        </button>
    </form>
</fieldset>