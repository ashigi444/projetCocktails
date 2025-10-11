<h2>Profil</h2>
<?php if (isset($_SESSION['user'])){ ?>
    <p>Connect&eacute;&nbsp;:&nbsp;<strong><?php echo $_SESSION['user']['login']; ?></strong></p>
    <p>(form de modif de profil&nbsp;&agrave;&nbsp;impl&eacute;menter)</p>
<?php }else { ?>
    <p>Vous n&apos;&ecirc;tes pas connect&eacute;.</p>
<?php } ?>
