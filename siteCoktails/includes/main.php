<main>
    <?php if (!empty($messages)) { ?>
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