<?php
// header:
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>
<div style="background:#f7f7f7; border-bottom:1px solid #ddd; padding:10px 0;">
  <div style="max-width:900px; margin:0 auto; font-family:sans-serif;">

    <!-- ligne du haut -->
    <div style="display:inline-block; vertical-align:middle;">
      <span style="font-weight:bold; font-size:18px; margin-right:15px;">
        <a href="index.php" style="text-decoration:none; color:#222;">Gestion de cocktails</a>
      </span>

      <a href="index.php?page=navigation" style="margin-right:10px;">Navigation</a>
      <a href="index.php?page=favorites" style="margin-right:20px;">Recettes</a>
    </div>

    <!-- formulaire de recherche -->
    <div style="display:inline-block; vertical-align:middle; margin-right:15px;">
      <form method="get" action="index.php" style="display:inline;">
        <input type="hidden" name="page" value="recherche">
        <input type="text" name="q" placeholder='"Jus de fruits" +Sel -Whisky'
               value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>"
               style="padding:3px; width:180px;">
        <button type="submit" style="padding:3px 6px;">Rechercher</button>
      </form>
    </div>

    <!-- zone de connexion -->
    <div style="display:inline-block; vertical-align:middle; float:right;">
      <?php if ($user): ?>
        <span>Connecté : <strong><?= htmlspecialchars($user['login']) ?></strong></span>
        <a href="index.php?page=profil">Profil</a>
        <form method="post" action="index.php" style="display:inline;">
          <input type="hidden" name="action" value="logout">
          <button type="submit" style="padding:2px 6px;">Se déconnecter</button>
        </form>
      <?php else: ?>
        <form method="post" action="index.php" style="display:inline;">
          <input type="text" name="login" placeholder="Login" style="padding:3px;" required>
          <input type="password" name="password" placeholder="Mot de passe" style="padding:3px;" required>
          <input type="hidden" name="action" value="login">
          <button type="submit" style="padding:3px 6px;">Connexion</button>
          <a href="index.php?page=inscription">S’inscrire</a>
        </form>
      <?php endif; ?>
    </div>

    <div style="clear:both;"></div>
  </div>
</div>
