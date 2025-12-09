<?php
// Affichage du message de succès en session si présent
if (isset($_SESSION['message_succes_stats'])) {
    $messageSucces = $_SESSION['message_succes_stats'];
    unset($_SESSION['message_succes_stats']);
}
?>

<section class="bg-light py-4">
    <div class="container">
        <div class="structure-hero pt-3">
            <h1 class="titre text-center"><?php echo htmlspecialchars($titrePage); ?></h1>
            <p class="text text-center mb-4">Consultez les statistiques des médicaments offerts lors des visites de votre secteur.</p>
        </div>

        <!-- Message de succès -->
        <?php if (!empty($messageSucces)) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($messageSucces); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        <?php endif; ?>

        <!-- Affichage des erreurs -->
        <?php if (!empty($erreurs)) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Erreur(s) :</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach ($erreurs as $erreur) : ?>
                        <li><?php echo htmlspecialchars($erreur); ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        <?php endif; ?>

        <!-- Formulaire de filtres -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white text-dark border-bottom">
                <h5 class="mb-0">Critères de recherche</h5>
            </div>
            <div class="card-body">
                <form method="post" action="index.php?uc=rapports&action=statistiques">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="date_debut" class="form-label">Date de début <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date_debut" name="date_debut" 
                                   value="<?php echo htmlspecialchars($dateDebut ?? ''); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="date_fin" class="form-label">Date de fin <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date_fin" name="date_fin" 
                                   value="<?php echo htmlspecialchars($dateFin ?? ''); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="medicament" class="form-label">Médicament (optionnel)</label>
                            <select class="form-select" id="medicament" name="medicament">
                                <option value="">-- Tous les médicaments --</option>
                                <?php foreach ($listeMedicaments as $med) : ?>
                                    <option value="<?php echo htmlspecialchars($med['MED_DEPOTLEGAL']); ?>"
                                            <?php echo ($medDepotLegal == $med['MED_DEPOTLEGAL']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($med['MED_NOMCOMMERCIAL']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-info text-white">
                                <i class="bi bi-search me-2"></i>Afficher les statistiques
                            </button>
                            <a href="index.php?uc=rapports&action=statistiques" class="btn btn-secondary ms-2">
                                <i class="bi bi-arrow-counterclockwise me-2"></i>Réinitialiser
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Résultats des statistiques -->
        <?php if ($rechercheEffectuee) : ?>

            <!-- Tableau des statistiques des médicaments -->
            <div class="card shadow-sm">
                <div class="card-header bg-white text-dark border-bottom">
                    <h5 class="mb-0"><i class="bi bi-capsule me-2"></i>Statistiques des médicaments offerts</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($statistiques)) : ?>
                        <p class="text-muted mb-3">
                            Période du <?php echo date('d/m/Y', strtotime($dateDebut)); ?> 
                            au <?php echo date('d/m/Y', strtotime($dateFin)); ?>
                            <?php if ($medDepotLegal) : ?>
                                - Filtré sur : <strong><?php echo htmlspecialchars($statistiques[0]['MED_NOMCOMMERCIAL'] ?? ''); ?></strong>
                            <?php endif; ?>
                        </p>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead>
                                    <tr style="font-weight: normal;">
                                        <td>#</td>
                                        <td>Médicament</td>
                                        <td>Famille</td>
                                        <td class="text-center">Quantité totale</td>
                                        <td class="text-center">Nb de visites</td>
                                        <td class="text-center">Moyenne/visite</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $totalQuantite = 0;
                                    $totalVisites = 0;
                                    $rang = 1;
                                    foreach ($statistiques as $stat) : 
                                        $totalQuantite += $stat['TOTAL_QUANTITE'];
                                        $totalVisites += $stat['NB_VISITES'];
                                        $moyenne = round($stat['TOTAL_QUANTITE'] / $stat['NB_VISITES'], 1);
                                    ?>
                                        <tr>
                                            <td><?php echo $rang++; ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($stat['MED_NOMCOMMERCIAL']); ?>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($stat['MED_DEPOTLEGAL']); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($stat['FAM_LIBELLE']); ?></td>
                                            <td class="text-center"><?php echo $stat['TOTAL_QUANTITE']; ?></td>
                                            <td class="text-center"><?php echo $stat['NB_VISITES']; ?></td>
                                            <td class="text-center"><?php echo $moyenne; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr style="font-weight: normal;">
                                        <td colspan="3" class="text-end">TOTAUX :</td>
                                        <td class="text-center"><?php echo $totalQuantite; ?></td>
                                        <td class="text-center"><?php echo $totalVisites; ?></td>
                                        <td class="text-center">
                                            <?php if ($totalVisites > 0) : ?>
                                                <?php echo round($totalQuantite / $totalVisites, 1); ?>
                                            <?php else : ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php else : ?>
                        <div class="alert alert-light border text-center mb-0">

                            Aucun médicament n'a été offert lors des visites de votre secteur pour cette période.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        <?php else : ?>
            <!-- Message d'information avant la recherche -->
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <h4 class="mt-3">Visualisez les statistiques de votre secteur</h4>
                    <p class="text-muted">
                        Sélectionnez une période de dates ci-dessus pour afficher les statistiques des médicaments offerts aux praticiens lors des visites.
                        <br>Vous pouvez également filtrer sur un médicament spécifique.
                    </p>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>
