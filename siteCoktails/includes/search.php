<h2>Recherche</h2>
<?php
$search = isset($_GET['search']) ? $_GET['search'] : '';
if ($search !== '') { ?>
    <p>Votre requ&ecirc;te&nbsp;:&nbsp;&quot;<strong><?php echo $search; ?></strong>&quot;</p>
    <p>Traitement de la requ&ecirc;te&nbsp;&agrave;&nbsp;impl&eacute;menter</p>
<?php } else { ?>
    <p>Saisissez une requ&ecirc;te dans la barre de recherche en haut.</p>
<?php } ?>
