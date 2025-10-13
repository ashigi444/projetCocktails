<nav>
    <!-- boutons de navigation entre pages -->
    <ul> <?php
        $links['Accueil']=['index.php', 'index.php?page=accueil'];
        $links['Navigation']='index.php?page=navigation';
        $links['Favoris']='index.php?page=favoriteRecipes';

        $actualLink=isset($page)?'index.php?page='.$page:'index.php';
        foreach($links as $nameLink=>$link){
            $isPrintableLink=false;
            if(is_array($link) && count($link)>0){
                if(!in_array($actualLink, $link)){
                    $link=$link[0];
                    $isPrintableLink=true;
                }
            }else{
                if($link!=$actualLink){
                    $isPrintableLink=true;
                }
            }

            if($isPrintableLink){ ?>
                <li><a href="<?php echo $link ?>">
                    <?php echo $nameLink ?>
                    </a>
                </li>
            <?php }
        } ?>
    </ul>


    <!-- formulaire de recherche -->
    <form method="get" action="index.php">
        <label for="search">
            <input id="search" type="text" name="search" placeholder="&quot;Jus de fruits&quot;"
                   value="<?php echo isset($_GET['search']) ? $_GET['search'] : '' ; ?>"
            />
        </label>
        <button type="submit" name="page" value="search">Rechercher</button>
    </form>

    <!-- zone de connexion -->
    <div>
        <?php if (isset($user) && !empty($user)) { ?>
            <p>
                <a href="index.php?page=profilSettings">Profil&nbsp;:</a>
                <strong><?php echo $user['login'] ; ?></strong>
            </p>
        <?php } ?>
        <form method="post" action="index.php">
            <?php if (isset($user) && !empty($user)) { ?>
                <button type="submit" name="action" value="logout">Se d&eacute;connecter</button>
            <?php } else { ?>
                <label for="login">
                    <input
                        id="login" type="text" name="login" placeholder="Identifiant" required="required"
                        <?php if(isset($loginForm)) { ?> value="<?php echo $loginForm; ?>" <?php } ?>
                    />
                </label>
                <label for="password">
                    <input id="password" type="password" name="password" placeholder="Mot de passe" required="required" />
                </label>
                <button type="submit" name="action" value="login">Connexion</button>
            <?php } ?>
        </form>

        <?php if (!isset($user) || empty($user)){ ?>
            <a href="index.php?page=signUp">S&apos;inscrire</a>
        <?php } ?>
    </div>
</nav>
