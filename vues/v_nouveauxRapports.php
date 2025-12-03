<section class="py-5" style="background-color: #f6f9fe;">
<div class="container mt-4 mb-5">

    <h1 class="mb-4 mt-4"><?= htmlspecialchars($titrePage) ?></h1>

    <?php if (empty($rapports)) : ?>
        <div class="alert alert-info border-0 shadow-sm">
            <i class="bi bi-info-circle me-2"></i>
            <span>Aucun nouveau rapport validé à consulter pour le moment.</span>
        </div>
    <?php else : ?>
        <div class="card shadow-sm border-0">
            <div class="card-header bg-info text-white">
                <h2 class="h5 mb-0 fw-semibold">
                    <i class="bi bi-list-ul me-2"></i>Liste des rapports
                </h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
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
                                    <td class="fw-bold text-primary"><?= htmlspecialchars($rap['RAP_NUM']) ?></td>
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
                                         
                                            echo '-';
                                        } else {
                                            echo htmlspecialchars(implode(', ', $meds));
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="index.php?uc=rapports&action=consulter_detail&mat=<?= $rap['VIS_MATRICULE'] ?>&num=<?= $rap['RAP_NUM'] ?>"
                                           class="btn btn-sm btn-secondary text-white fw-semibold">
                                            <i class="bi bi-eye me-1"></i>Voir détail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php endif; ?>
    </div>

</section>
