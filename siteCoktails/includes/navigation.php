<?php
require_once('resources/Donnees.inc.php');
require_once('utils/utils.php');

if (isset($_GET['aliment'])) {
  $currentIngredient = $_GET['aliment'];
} else {
  $currentIngredient ='Aliment';
}

if (!array_key_exists($currentIngredient, $Hierarchie)) {
  $currentIngredient = 'Aliment';
}

$ingredientsValides = getIngredientsHierarchy($currentIngredient,$Hierarchie);
?>

<h3>Liste des cocktails</h3>
<div class="liste-recettes">
  <?php
  foreach ($Recettes as $id =>$recette) {
    $afficherRecette = false;

    if ($currentIngredient == 'Aliment') {
      $afficherRecette = true;
    } else {
      foreach ($recette['index'] as $ing) {
        foreach ($ingredientsValides as $validIngredient) {
          if ($ing == $validIngredient) {
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
      $imageName = makeFilenameImage($recette['titre']);
      $cheminImage = 'resources/Photos/'.$imageName;

      if (!file_exists($cheminImage)) {
        $cheminImage = 'resources/Photos/default.jpg';
      }

      // verif pour favori
      $estFavori = isFavorite($id);
      $heartClass = $estFavori ? 'heart-full' : 'heart-empty';
      $heartSymbol = $estFavori ? '&#10084;' : '&#9825;';

      $toggleUrl = 'index.php?action=toggleFavorite&recipeId=' . $id . '&page=navigation';
      if ($currentIngredient !== 'Aliment') {
        $toggleUrl .= '&aliment=' . urlencode($currentIngredient);
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
