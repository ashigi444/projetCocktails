<?php
require_once('resources/Donnees.inc.php');
require_once('utils/utils.php');

if (isset($_GET['aliment'])) {
  $alimentCourant = $_GET['aliment'];
} else {
  $alimentCourant ='Aliment';
}

if (!array_key_exists($alimentCourant, $Hierarchie)) {
  $alimentCourant = 'Aliment';
}

$ingredientsValides = getAlimentsHierarchie($alimentCourant,$Hierarchie);
?>

<h3>Liste des cocktails</h3>
<div class="liste-recettes">
  <?php
  foreach ($Recettes as $id =>$recette) {
    $afficherRecette = false;

    if ($alimentCourant == 'Aliment') {
      $afficherRecette = true;
    } else {
      foreach ($recette['index'] as $ing) {
        foreach ($ingredientsValides as $valide) {
          if ($ing == $valide) {
            $afficherRecette = true;
            break;
          }
        }
        if ($afficherRecette) {
          break;
        }
      }
    }

    if ($afficherRecette) {
      $nomImage = makeFilenameImage($recette['titre']);
      $cheminImage = 'resources/Photos/'.$nomImage;

      if (!file_exists($cheminImage)) {
        $cheminImage = 'resources/Photos/default.jpg';
      }

      // verif pour favori
      $estFavori = isFavorite($id);
      $heartClass = $estFavori ? 'heart-full' : 'heart-empty';
      $heartSymbol = $estFavori ? '&#10084;' : '&#9825;';

      $toggleUrl = 'index.php?action=toggleFavorite&recipeId=' . $id . '&page=navigation';
      if ($alimentCourant !== 'Aliment') {
        $toggleUrl .= '&aliment=' . urlencode($alimentCourant);
      }
      ?>
      <div class="cocktail-card">
        <div class="card-header">
          <span class="cocktail-title"><?php echo $recette['titre']; ?></span>
          <a href="<?php echo $toggleUrl; ?>" class="favorite-btn <?php echo $heartClass; ?>" title="<?php echo $estFavori ? 'Retirer des favoris' : 'Ajouter aux favoris'; ?>">
            <?php echo $heartSymbol; ?>
          </a>
        </div>
        <div class="card-image">
          <img src="<?php echo $cheminImage; ?>" alt="<?php echo $recette['titre']; ?>">
        </div>
        <ul class="ingredients-list">
          <?php
          foreach($recette['index'] as $ing) {
            echo "<li>" . $ing . "</li>";
          }
          ?>
        </ul>
      </div>
      <?php
    }
  }
  ?>
</div>
