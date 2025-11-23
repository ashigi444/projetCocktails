<?php ?>
<main>
    <?php
    // Affichage des messages d'erreurs si il y en a
    if (!empty($messagesErrors)) { ?>
    <div class="message message-errors">
        <?php foreach ($messagesErrors as $mess) { ?>
            <p><?php echo $mess; ?></p>
        <?php }?>
    </div>

    <?php }
    // Affichage des messages classiques si il y en a
    if (!empty($messages)) { ?>
        <div class="message">
            <?php foreach ($messages as $mess) { ?>
                <p><?php echo $mess; ?></p>
            <?php }?>
        </div>
    <?php }

    if(isset($page)){
        $file='includes/'.$page.'.php';
        if(file_exists($file)){
            include $file;
        }else{ ?>
            <h2>ERREUR&nbsp;404&nbsp;:&nbsp;Page inconnue</h2>
            <p>La page demand&eacute;e n&apos;existe pas.</p>
        <?php }
    } else {
        include 'includes/navigation.php';
    } ?>
</main>
