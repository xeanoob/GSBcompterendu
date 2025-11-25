<section class="bg-light py-5">
<div class="container mt-4 mb-5">

    <h1 class="mb-4">Saisir un rapport de visite</h1>

    <?php
    // Afficher le message de succès s'il y en a un dans la session
    if (isset($_SESSION['message_succes_rapport'])) {
        echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['message_succes_rapport']) . '</div>';
        unset($_SESSION['message_succes_rapport']);
    }
    ?>

    <?php if (!empty($erreurs)) : ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($erreurs as $err) : ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="mb-3">
        <a href="index.php?uc=rapports&action=nouveau" class="btn btn-info text-light">
            + Créer un nouveau rapport de visite
        </a>
    </div>

    <?php if (empty($rapports)) : ?>
        <div class="alert alert-info">
            <p class="mb-0">Vous n'avez pas encore créé de rapport de visite.</p>
        </div>
    <?php else : ?>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Date</th>
                                <th>Praticien</th>
                                <th>Motif</th>
                                <th>État</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rapports as $rap) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($rap['RAP_NUM']) ?></td>
                                    <td>
                                        <?php
                                        if (!empty($rap['RAP_DATEVISITE'])) {
                                            $date = new DateTime($rap['RAP_DATEVISITE']);
                                            echo $date->format('d/m/Y');
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td><?= htmlspecialchars($rap['PRA_NOM'] . ' ' . $rap['PRA_PRENOM']) ?></td>
                                    <td><?= htmlspecialchars($rap['MOT_LIBELLE'] ?? '-') ?></td>
                                    <td>
                                        <?php
                                        $etatLibelle = $rap['ETAT_LIBELLE'] ?? 'Non défini';
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
                                    </td>
                                    <td>
                                        <?php if ($rap['ETAT_CODE'] == 1) : ?>
                                            <a href="index.php?uc=rapports&action=modifier&num=<?= $rap['RAP_NUM'] ?>"
                                               class="btn btn-sm btn-outline-warning me-1">
                                                <i class="bi bi-pencil"></i> Modifier
                                            </a>
                                        <?php endif; ?>
                                        <a href="index.php?uc=rapports&action=detail&num=<?= $rap['RAP_NUM'] ?>"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Voir détail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <p class="text-muted mt-3">
            Total : <?= count($rapports) ?> rapport<?= count($rapports) > 1 ? 's' : '' ?>
        </p>
    <?php endif; ?>
    </div>

</section>
