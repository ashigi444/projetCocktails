<h2>Accueil</h2>
<?php
if(isset($_SESSION['username']) && isset($user['username'])){ ?>
    <h3>Bienvenue&nbsp;<?php echo $user['username']; ?>&nbsp;!</h3>
<?php } else { ?>
    <h3>Bienvenue&nbsp;!</h3>
<?php } ?>
