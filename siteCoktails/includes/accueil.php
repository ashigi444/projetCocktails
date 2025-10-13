<h2>Accueil</h2>
<?php
if(isset($user) && isset($user['login'])){ ?>
    <h3>Bienvenue&nbsp;<?php echo $user['login']; ?>&nbsp;!</h3>
<?php } else { ?>
    <h3>Bienvenue&nbsp;!</h3>
<?php } ?>
