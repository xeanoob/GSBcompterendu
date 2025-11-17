<section class="container mt-4 mb-5">

    <h1 class="mb-4">Détail du rapport de visite n°<?= htmlspecialchars($rapport['RAP_NUM']) ?></h1>

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0">Informations générales</h2>
            <?php
            $etatLibelle = $rapport['ETAT_LIBELLE'] ?? 'Non défini';
            $badgeClass = 'bg-secondary';

            if (strpos(strtolower($etatLibelle), 'cours') !== false) {
                $badgeClass = 'bg-warning text-dark';
            } elseif (strpos(strtolower($etatLibelle), 'valid') !== false || strpos(strtolower($etatLibelle), 'définitive') !== false) {
                $badgeClass = 'bg-success';
            } elseif (strpos(strtolower($etatLibelle), 'consult') !== false) {
                $badgeClass = 'bg-info';
            }
            ?>
            <span class="badge <?= $badgeClass ?>">
                <?= htmlspecialchars($etatLibelle) ?>
            </span>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Numéro du rapport :</strong><br>
                    <?= htmlspecialchars($rapport['RAP_NUM']) ?>
                </div>
                <div class="col-md-6">
                    <strong>Date de la visite :</strong><br>
                    <?php
                    if (!empty($rapport['RAP_DATEVISITE'])) {
                        $date = new DateTime($rapport['RAP_DATEVISITE']);
                        echo $date->format('d/m/Y');
                    } else {
                        echo 'Non renseignée';
                    }
                    ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Praticien visité :</strong><br>
                    <?= htmlspecialchars($rapport['PRA_NOM'] . ' ' . $rapport['PRA_PRENOM']) ?>
                </div>
                <div class="col-md-6">
                    <strong>Motif de la visite :</strong><br>
                    <?= htmlspecialchars($rapport['MOT_LIBELLE'] ?? 'Non renseigné') ?>
                </div>
            </div>

            <div class="mb-3">
                <strong>Bilan de la visite :</strong><br>
                <div class="p-3 bg-light rounded">
                    <?= nl2br(htmlspecialchars($rapport['RAP_BILAN'] ?? 'Non renseigné')) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Médicaments présentés -->
    <?php if (!empty($rapport['MED_DEPOTLEGAL1']) || !empty($rapport['MED_DEPOTLEGAL2'])) : ?>
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="h5 mb-0">Médicaments présentés</h2>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <?php if (!empty($rapport['MED_DEPOTLEGAL1'])) : ?>
                        <li class="mb-2">
                            <span class="badge bg-primary">1</span>
                            <?= htmlspecialchars($rapport['MED_DEPOTLEGAL1']) ?>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($rapport['MED_DEPOTLEGAL2'])) : ?>
                        <li>
                            <span class="badge bg-primary">2</span>
                            <?= htmlspecialchars($rapport['MED_DEPOTLEGAL2']) ?>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <!-- Échantillons offerts -->
    <?php if (!empty($echantillons)) : ?>
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="h5 mb-0">Échantillons offerts</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Médicament</th>
                                <th>Dépôt légal</th>
                                <th class="text-end">Quantité</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($echantillons as $ech) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($ech['MED_NOMCOMMERCIAL']) ?></td>
                                    <td><?= htmlspecialchars($ech['MED_DEPOTLEGAL']) ?></td>
                                    <td class="text-end">
                                        <span class="badge bg-secondary">
                                            <?= htmlspecialchars($ech['OFF_QTE']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="alert alert-info">
            <p class="mb-0">Aucun échantillon n'a été offert lors de cette visite.</p>
        </div>
    <?php endif; ?>

    <!-- Boutons d'action -->
    <div class="mt-4">
        <a href="index.php?uc=rapports&action=liste" class="btn btn-outline-secondary">
            Retour à la liste
        </a>
    </div>

</section>
