<?php ?>
<nav>
    <!-- boutons de navigation entre pages -->
    <ul> <?php
        $links['Accueil']=['ref1' => 'index.php', 'ref2' => 'index.php?page=accueil'];
        $links['Navigation']='index.php?page=navigation';
        $links['Favoris']='index.php?page=favoriteRecipes';

        $actualLink=isset($page)?'index.php?page='.$page : 'index.php';
        foreach($links as $nameLink=>$link){
            $isPrintableLink=false;
            if(is_array($link) && count($link)>0){
                if(!in_array($actualLink, $link)){
                    $link=$link['ref1'];
                    $isPrintableLink=true;
                }
            }else{
                if($link!=$actualLink){
                    $isPrintableLink=true;
                }
            }

            if($isPrintableLink){ ?>
                <li><a class="buttonLinkPage" href="<?php echo $link ?>">
                    <?php echo $nameLink ?>
                    </a>
                </li>
            <?php }
        } ?>
    </ul>


    <!-- formulaire de recherche -->
    <?php require_once 'utils/utils.php' ?>
    <form method="get" action="index.php">
        <label for="search">
            <?php
            $search_value = isset($_GET['search']) ? $_GET['search'] : '';
            ?>
            <input id="search" type="text" name="search"
                   placeholder="&quot;Jus de fruits&quot;"
                   value="<?php echo replaceSearchByEntity("input", $search_value); ?>"
            />
        </label>
        <button type="submit" name="page" value="search">Rechercher</button>
    </form>

    <!-- zone de connexion ou profil-->
    <div>
        <?php if ((!isset($user) || empty($user)) // Si aucun utilisateur n'existe
                && (!isset($_GET['page']) || $_GET['page']!='signUp')) // Et que si il y a une page elle est differente de cette d'inscription
        { ?>
            <a class="button-signup" href="index.php?page=signUp">S&apos;inscrire</a>
        <?php } ?>

        <?php if (isset($user) && !empty($user)) { ?>
            <p>
                <a href="index.php?page=profilSettings">Profil&nbsp;:</a>
                <strong><?php echo $user['username'] ; ?></strong>
            </p>
        <?php } ?>
        <?php $redirect_form = (isset($page) && !empty(trim($page))) ? 'index.php?page='.$page : 'index.php'; ?>
        <form class="form-signin" method="post" action="<?php echo $redirect_form ?>">
            <?php if (isset($user) && !empty($user)) { ?>
                <button type="submit" name="action" value="logout">Se d&eacute;connecter</button>
            <?php } else { ?>
                <label for="signin_username">
                    <input
                        id="signin_username" type="text" name="username" placeholder="Identifiant" required="required"
                        <?php
                        // Si le username existe deja et qu'il est valide, on le reecrit
                        if(isset($value_fields['signinForm']['username'])){ ?>
                            value="<?php echo $value_fields['signinForm']['username']; ?>"
                        <?php }
                        // Si il a une class de style, on l'applique
                        if(isset($class_fields['signinForm']['username'])) { ?>
                            class="<?php echo $class_fields['signinForm']['username']; ?>"
                        <?php } ?>
                    />
                </label>
                <label for="signin_password">
                    <input id="signin_password" type="password" name="password" placeholder="Mot de passe" required="required"
                    <?php // Si il a une class de style, on l'applique
                    if(isset($class_fields['signinForm']['password'])) { ?>
                        class="<?php echo $class_fields['signinForm']['password']; ?>"
                    <?php } ?>
                    />
                </label>
                <button class="button-sub" type="submit" name="action" value="signin">Connexion</button>
            <?php } ?>
        </form>
    </div>
</nav>
