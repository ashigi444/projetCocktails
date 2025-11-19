<?php
include_once('resources/Donnees.inc.php');
function getAlimentsHierarchie($nomAliment, $hierarchie) {
  $liste = array();
  $liste[] = $nomAliment;

  if (isset($hierarchie[$nomAliment]['sous-categorie'])) {
    foreach ($hierarchie[$nomAliment]['sous-categorie'] as $sousCat) {
      $sousListe = getAlimentsHierarchie($sousCat,$hierarchie);
      foreach ($sousListe as $element) {
        $liste[] = $element;
      }
    }
  }
  return $liste;
}
function getNomFichierImage($titre) {
  $titre = strtolower($titre);
  $titre = preg_replace('/[àáâãä]/', 'a',$titre);
  $titre = preg_replace('/[éèêë]/', 'e',$titre);
  $titre = preg_replace('/[ìíîï]/', 'i',$titre);
  $titre = preg_replace('/[òóôõö]/', 'o',$titre);
  $titre = preg_replace('/[ùúûü]/', 'u',$titre);
  $titre = preg_replace('/[ç]/', 'c',$titre);
  $titre = preg_replace('/[ñ]/', 'n',$titre);
  $titre = preg_replace('/ /', '_',$titre);
  $titre = preg_replace('/[^a-z_]/', '',$titre);

  if (strlen($titre) > 0) {
    $premiereLettre = $titre[0];
    $titre[0] = strtoupper($premiereLettre);
  }
  return $titre.'.jpg';
}

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
      $nomImage = getNomFichierImage($recette['titre']);
      $cheminImage = 'resources/Photos/'.$nomImage;

      if (!file_exists($cheminImage)) {
        $cheminImage = 'resources/Photos/default.jpg';
      }
      ?>
      <div class="cocktail-card">
        <div class="card-header">
          <span class="cocktail-title"><?php echo $recette['titre']; ?></span>
        </div>
        <div class="card-image">
          <img src="<?php echo $cheminImage; ?>"alt="<?php echo $recette['titre']; ?>">
        </div>
        <ul class="ingredients-list">
          <?php
          foreach($recette['index'] as $ing) {
            echo "<li>$ing</li>";
          }
          ?>
        </ul>
      </div>
      <?php
    }
  }
  ?>
</div>
