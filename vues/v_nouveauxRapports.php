<section class="bg-light py-5">
<div class="container mt-4 mb-5">

    <h1 class="mb-4"><?= htmlspecialchars($titrePage) ?></h1>

    <?php if (empty($rapports)) : ?>
        <div class="alert alert-info">
            <p class="mb-0">Aucun nouveau rapport validé à consulter pour le moment.</p>
        </div>
    <?php else : ?>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Visiteur</th>
                                <th>N°</th>
                                <th>Date</th>
                                <th>Praticien</th>
                                <th>Motif</th>
                                <th>Médicaments</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rapports as $rap) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($rap['COL_NOM'] . ' ' . $rap['COL_PRENOM']) ?></td>
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
                                    <td><?= htmlspecialchars($rap['PRA_NUM'] . ' - ' . $rap['PRA_NOM'] . ' ' . $rap['PRA_PRENOM']) ?></td>
                                    <td><?= htmlspecialchars($rap['MOT_LIBELLE'] ?? '-') ?></td>
                                    <td>
                                        <?php
                                        $meds = [];
                                        if (!empty($rap['MED1_NOM'])) $meds[] = $rap['MED_DEPOTLEGAL1'] . ': ' . $rap['MED1_NOM']; // Note: MED_DEPOTLEGAL1 needs to be fetched in query?
                                        if (!empty($rap['MED2_NOM'])) $meds[] = $rap['MED_DEPOTLEGAL2'] . ': ' . $rap['MED2_NOM'];
                                        
                                        if (empty($meds)) {
                                            // Check if we have the codes available in the $rap array. 
                                            // The query in rapport.modele.inc.php selects med1.MED_NOMCOMMERCIAL but NOT the codes explicitly in the main SELECT list, 
                                            // BUT it selects r.* which includes MED_DEPOTLEGAL1 and MED_DEPOTLEGAL2.
                                            // Let's verify the query in rapport.modele.inc.php first.
                                            echo '-';
                                        } else {
                                            echo htmlspecialchars(implode(', ', $meds));
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="index.php?uc=rapports&action=consulter_detail&mat=<?= $rap['VIS_MATRICULE'] ?>&num=<?= $rap['RAP_NUM'] ?>"
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
