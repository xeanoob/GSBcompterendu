<section class="bg-light py-5">
<div class="container mt-4 mb-5">

    <h1 class="mb-4">Saisir un rapport de visite</h1>

    <?php if (!empty($messageInfo)) : ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($messageInfo) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <h2 class="h5 mb-3">Rapports en cours de saisie</h2>
            <p class="text-muted">Vous avez des rapports non validés. Vous pouvez continuer à modifier un rapport existant ou créer un nouveau rapport.</p>

            <?php if (!empty($rapportsEnCours)) : ?>
                <div class="table-responsive mb-4">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>N° Rapport</th>
                                <th>Date visite</th>
                                <th>Praticien</th>
                                <th>Motif</th>
                                <th>Bilan</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rapportsEnCours as $rap) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($rap['RAP_NUM']) ?></td>
                                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($rap['RAP_DATEVISITE']))) ?></td>
                                    <td><?= htmlspecialchars($rap['PRA_NOM'] . ' ' . $rap['PRA_PRENOM']) ?></td>
                                    <td><?= htmlspecialchars($rap['MOT_LIBELLE']) ?></td>
                                    <td><?= htmlspecialchars(substr($rap['RAP_BILAN'], 0, 50)) ?><?= strlen($rap['RAP_BILAN']) > 50 ? '...' : '' ?></td>
                                    <td>
                                        <a href="index.php?uc=rapports&action=modifier&num=<?= $rap['RAP_NUM'] ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="bi bi-pencil"></i> Modifier
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div class="d-flex gap-2">
                <a href="index.php?uc=rapports&action=creerNouveau" class="btn btn-info text-light">
                    <i class="bi bi-plus-circle"></i> Créer un nouveau rapport
                </a>
                <a href="index.php?uc=rapports&action=liste" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Retour à la liste
                </a>
            </div>
        </div>
    </div>
</div>
</section>
