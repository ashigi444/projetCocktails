<nav>
    <!-- boutons de navigation entre pages -->
    <ul> <?php
        $links['Accueil']='index.php';
        $links['Navigation']='index.php?page=navigation';
        $links['Favoris']='index.php?page=favoriteRecipes';

        $actualLink=isset($page)?'index.php?page='.$page:'index.php';
        foreach($links as $nameLink=>$link){
            if($link!=$actualLink){ ?>
                <li><a href="<?php echo $link ?>">
                    <?php echo $nameLink ?>
                    </a>
                </li>
            <?php }
        } ?>
    </ul>


    <!-- formulaire de recherche -->
    <form method="get" action="index.php">
        <input type="text" name="search" placeholder="&quot;Jus de fruits&quot;"
               value="<?php echo isset($_GET['search']) ? $_GET['search'] : '' ; ?>"
        />
        <button type="submit" name="page" value="search">Rechercher</button>
    </form>

    <!-- zone de connexion -->
    <div>
        <?php if (isset($user) && !empty($user)) { ?>
            <p><a href="index.php?page=profilSettings">Profil&nbsp;:</a>
                <strong><?php echo $user['login'] ; ?></strong></p>
        <?php }


        $redirectionForm='#';
        if(isset($page))
            if($page=="signUp")
                $redirectionForm='index.php';
        ?>
        <form method="post" action="<?php echo $redirectionForm; ?>">                                                                                 <!-- AVANT -> action="index.php<?php // if(isset($page)) echo "?page=".$page; ?>"> -->
            <?php if (isset($user) && !empty($user)) { ?>
                <input type="hidden" name="action" value="logout" />
                <button type="submit">Se d&eacute;connecter</button>
            <?php } else { ?>
                <label for="login" id="login">
                    <input type="text" name="login" placeholder="Identifiant" required="required" />
                </label>
                <label for="password" id="password">
                    <input type="password" name="password" placeholder="Mot de passe" required="required" />
                </label>
                <button type="submit" name="action" value="login">Connexion</button>
            <?php } ?>
        </form>

        <?php if (!isset($user) || empty($user)){ ?>
            <a href="index.php?page=signUp">S&apos;inscrire</a>
        <?php } ?>
    </div>
</nav>
