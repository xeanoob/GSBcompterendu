<section class="container mt-4 mb-5">

    <h1 class="mb-4">Résultats de la recherche</h1>

    <?php
    // Récupérer les critères de recherche depuis la session
    $criteres = $_SESSION['criteres_recherche'] ?? null;
    ?>

    <?php if ($criteres) : ?>
        <div class="card mb-4">
            <div class="card-body bg-light">
                <h6 class="mb-2">Critères de recherche :</h6>
                <p class="mb-1">
                    <strong>Période :</strong>
                    <?php
                    $dateDebut = new DateTime($criteres['date_debut']);
                    $dateFin = new DateTime($criteres['date_fin']);
                    echo $dateDebut->format('d/m/Y') . ' au ' . $dateFin->format('d/m/Y');
                    ?>
                </p>
                <?php if (!empty($criteres['praticien_num'])) : ?>
                    <p class="mb-0">
                        <strong>Praticien :</strong> Filtré sur le praticien n°<?= htmlspecialchars($criteres['praticien_num']) ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="mb-3 d-flex justify-content-between align-items-center">
        <p class="text-muted mb-0">
            <strong><?= count($rapports) ?></strong> rapport<?= count($rapports) > 1 ? 's' : '' ?> trouvé<?= count($rapports) > 1 ? 's' : '' ?>
        </p>
        <a href="index.php?uc=rapports&action=consulter" class="btn btn-outline-secondary btn-sm">
            Nouvelle recherche
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date visite</th>
                            <th>Praticien</th>
                            <th>Motif</th>
                            <th>Médicaments présentés</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rapports as $rap) : ?>
                            <tr>
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
                                <td>
                                    <a href="index.php?uc=rapports&action=detailPraticien&pra=<?= $rap['PRA_NUM'] ?>"
                                       class="text-decoration-none"
                                       title="Voir le détail du praticien">
                                        <?= htmlspecialchars($rap['PRA_NOM'] . ' ' . $rap['PRA_PRENOM']) ?>
                                    </a>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($rap['RAP_MOTIF'])) {
                                        echo htmlspecialchars($rap['RAP_MOTIF']);
                                    } elseif (!empty($rap['MOT_LIBELLE'])) {
                                        echo htmlspecialchars($rap['MOT_LIBELLE']);
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    // Afficher les médicaments présentés
                                    if (!empty($rap['medicaments_presentes'])) {
                                        $medicaments = explode(';', $rap['medicaments_presentes']);
                                        echo '<ul class="list-unstyled mb-0">';
                                        foreach ($medicaments as $med) {
                                            if (!empty($med)) {
                                                list($depot, $nom) = explode(':', $med);
                                                echo '<li>';
                                                echo '<a href="index.php?uc=rapports&action=detailMedicament&med=' . urlencode($depot) . '" ';
                                                echo 'class="text-decoration-none" title="Voir le détail du médicament">';
                                                echo htmlspecialchars($nom);
                                                echo '</a>';
                                                echo '</li>';
                                            }
                                        }
                                        echo '</ul>';
                                    } else {
                                        echo '<span class="text-muted">Aucun</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="index.php?uc=rapports&action=detailConsultation&mat=<?= urlencode($rap['VIS_MATRICULE']) ?>&num=<?= $rap['RAP_NUM'] ?>"
                                       class="btn btn-sm btn-outline-primary">
                                        Détail rapport
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="index.php?uc=rapports&action=consulter" class="btn btn-secondary">
            Retour à la recherche
        </a>
    </div>

</section>
