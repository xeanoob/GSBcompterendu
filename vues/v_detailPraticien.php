<section class="container mt-4 mb-5">

    <h1 class="mb-4">Détail du praticien</h1>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h2 class="h5 mb-0">Informations générales</h2>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Numéro :</strong><br>
                    <?= htmlspecialchars($praticien['PRA_NUM']) ?>
                </div>
                <div class="col-md-6">
                    <strong>Type de praticien :</strong><br>
                    <?php
                    if (!empty($praticien['TYP_LIBELLE'])) {
                        echo htmlspecialchars($praticien['TYP_LIBELLE']);
                        if (!empty($praticien['TYP_LIEU'])) {
                            echo ' - ' . htmlspecialchars($praticien['TYP_LIEU']);
                        }
                    } else {
                        echo 'Non renseigné';
                    }
                    ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Nom :</strong><br>
                    <?= htmlspecialchars($praticien['PRA_NOM']) ?>
                </div>
                <div class="col-md-6">
                    <strong>Prénom :</strong><br>
                    <?= htmlspecialchars($praticien['PRA_PRENOM']) ?>
                </div>
            </div>

            <div class="mb-3">
                <strong>Adresse :</strong><br>
                <?= htmlspecialchars($praticien['PRA_ADRESSE'] ?? 'Non renseignée') ?>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Code postal :</strong><br>
                    <?= htmlspecialchars($praticien['PRA_CP'] ?? 'Non renseigné') ?>
                </div>
                <div class="col-md-6">
                    <strong>Ville :</strong><br>
                    <?= htmlspecialchars($praticien['PRA_VILLE'] ?? 'Non renseignée') ?>
                </div>
            </div>

            <div class="mb-0">
                <strong>Coefficient de notoriété :</strong><br>
                <?php
                if (isset($praticien['PRA_COEFNOTORIETE'])) {
                    echo '<span class="badge bg-info">' . htmlspecialchars($praticien['PRA_COEFNOTORIETE']) . '</span>';
                } else {
                    echo 'Non renseigné';
                }
                ?>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body bg-light">
            <h6 class="mb-2">Coordonnées complètes :</h6>
            <address class="mb-0">
                <strong><?= htmlspecialchars($praticien['PRA_PRENOM'] . ' ' . $praticien['PRA_NOM']) ?></strong><br>
                <?php if (!empty($praticien['PRA_ADRESSE'])): ?>
                    <?= htmlspecialchars($praticien['PRA_ADRESSE']) ?><br>
                <?php endif; ?>
                <?php if (!empty($praticien['PRA_CP']) || !empty($praticien['PRA_VILLE'])): ?>
                    <?= htmlspecialchars($praticien['PRA_CP']) ?>     <?= htmlspecialchars($praticien['PRA_VILLE']) ?>
                <?php endif; ?>
            </address>
        </div>
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