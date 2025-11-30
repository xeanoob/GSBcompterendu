<section class="container mt-4 mb-5">

    <h1 class="mb-4">Détail du médicament</h1>

    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h2 class="h5 mb-0">Informations générales</h2>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Dépôt légal :</strong><br>
                    <span class="badge bg-secondary"><?= htmlspecialchars($medicament['depotlegal']) ?></span>
                </div>
                <div class="col-md-6">
                    <strong>Nom commercial :</strong><br>
                    <span class="text-primary fs-5"><?= htmlspecialchars($medicament['nomcom']) ?></span>
                </div>
            </div>

            <div class="mb-3">
                <strong>Famille :</strong><br>
                <?= htmlspecialchars($medicament['famille'] ?? 'Non renseignée') ?>
            </div>

            <div class="mb-3">
                <strong>Prix échantillon :</strong><br>
                <?php
                if (isset($medicament['prixechan'])) {
                    echo '<span class="badge bg-info">' . number_format($medicament['prixechan'], 2, ',', ' ') . ' €</span>';
                } else {
                    echo 'Non renseigné';
                }
                ?>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h2 class="h5 mb-0">Composition</h2>
        </div>
        <div class="card-body">
            <div class="p-3 bg-light rounded">
                <?php
                if (!empty($medicament['compo'])) {
                    echo nl2br(htmlspecialchars($medicament['compo']));
                } else {
                    echo '<span class="text-muted">Non renseignée</span>';
                }
                ?>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h2 class="h5 mb-0">Effets</h2>
        </div>
        <div class="card-body">
            <div class="p-3 bg-light rounded">
                <?php
                if (!empty($medicament['effet'])) {
                    echo nl2br(htmlspecialchars($medicament['effet']));
                } else {
                    echo '<span class="text-muted">Non renseignés</span>';
                }
                ?>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-warning">
            <h2 class="h5 mb-0">Contre-indications</h2>
        </div>
        <div class="card-body">
            <div class="p-3 bg-light rounded border border-warning">
                <?php
                if (!empty($medicament['contreindic'])) {
                    echo nl2br(htmlspecialchars($medicament['contreindic']));
                } else {
                    echo '<span class="text-muted">Non renseignées</span>';
                }
                ?>
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        <strong>Information médicale :</strong> Ce médicament fait partie de la famille
        <strong><?= htmlspecialchars($medicament['famille'] ?? 'non spécifiée') ?></strong>.
        Consultez toujours un professionnel de santé avant toute prescription.
    </div>

    <div class="mt-4">
        <?php
        // Si on vient d'une consultation, retourner aux résultats
        if (isset($_SESSION['criteres_recherche']) && !isset($retour)) {
            // Rediriger vers les résultats de recherche en POST
            echo '<form method="POST" action="index.php?uc=rapports&action=rechercher" class="d-inline">';
            echo '<input type="hidden" name="date_debut" value="' . htmlspecialchars($_SESSION['criteres_recherche']['date_debut']) . '">';
            echo '<input type="hidden" name="date_fin" value="' . htmlspecialchars($_SESSION['criteres_recherche']['date_fin']) . '">';
            if (!empty($_SESSION['criteres_recherche']['praticien_num'])) {
                echo '<input type="hidden" name="praticien_num" value="' . htmlspecialchars($_SESSION['criteres_recherche']['praticien_num']) . '">';
            }
            echo '<button type="submit" class="btn btn-secondary">Retour aux résultats de recherche</button>';
            echo '</form>';

            echo '<a href="index.php?uc=rapports&action=consulter" class="btn btn-outline-secondary ms-2">Retour à la recherche</a>';
        } elseif (isset($retour) && $retour == 'nouveaux') {
            echo '<a href="javascript:history.back()" class="btn btn-outline-secondary">Retour au rapport</a>';
        } else {
            echo '<a href="index.php?uc=rapports&action=consulter" class="btn btn-outline-secondary">Retour à la recherche</a>';
        }
        ?>
    </div>

</section>