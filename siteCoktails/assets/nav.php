<?php ?>
<nav>
    <!-- boutons de navigation entre pages -->
    <ul> <?php
        $links['Navigation']=['ref1' => 'index.php', 'ref2' => 'index.php?page=navigation'];
        $links['Recettes &#10084;']='index.php?page=favoriteRecipes';

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
        <label for="searchValue">
            <input id="searchValue" type="text" name="searchValue"
                   placeholder="&quot;Jus de fruits&quot;"
                    <?php if(isset($searchValue)) {?>
                        value="<?php echo replaceSearchByEntity("input", $searchValue); ?>"
                    <?php } ?>
            />
        </label>
        <input class="button-sub" type="submit" name="search" value="Rechercher" />
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
                <a class="buttonLinkPage" href="index.php?page=profilSettings">Profil&nbsp;:</a>
                <strong><?php echo $user['username'] ; ?></strong>
            </p>
        <?php } ?>
        <?php $redirectForm = (isset($page) && !empty(trim($page))) ? 'index.php?page='.$page : 'index.php'; ?>
        <form class="form-signin" method="post" action="<?php echo $redirectForm ?>">
            <?php if (isset($user) && !empty($user)) { ?>
                <!-- bouton de déconnexion -->
                <input class="button-sub" type="submit" name="logout" value="Se&nbsp;d&eacute;connecter" />
            <?php } else { ?>
                <label for="signin_username">
                    <input
                            id="signin_username" type="text" name="username" placeholder="Identifiant" required="required"
                            <?php
                            // Si le username existe deja et qu'il est valide, on le reecrit
                            if(isset($valueFields['signinForm']['username'])){ ?>
                                value="<?php echo $valueFields['signinForm']['username']; ?>"
                            <?php }
                            // Si il a une class de style, on l'applique
                            if(isset($classFields['signinForm']['username'])) { ?>
                                class="<?php echo $classFields['signinForm']['username']; ?>"
                            <?php } ?>
                    />
                </label>
                <label for="signin_password">
                    <input id="signin_password" type="password" name="password" placeholder="Mot de passe" required="required"
                            <?php // Si il a une class de style, on l'applique
                            if(isset($classFields['signinForm']['password'])) { ?>
                                class="<?php echo $classFields['signinForm']['password']; ?>"
                            <?php } ?>
                    />
                </label>
                <!-- bouton de connexion -->
                <input class="button-sub" type="submit" name="signin" value="Se&nbsp;connecter" />
            <?php } ?>
        </form>
    </div>
</nav>
