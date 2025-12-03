<section class="py-5">
<div class="container mt-4 mb-5">

    <h1 class="mb-4"><?= htmlspecialchars($titrePage) ?></h1>

    <?php if (!empty($erreurs)) : ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($erreurs as $err) : ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Formulaire de recherche -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-funnel"></i> Critères de recherche</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?uc=rapports&action=historiqueRegion">
                <div class="row g-3">
                    <!-- Date de début -->
                    <div class="col-md-4">
                        <label for="date_debut" class="form-label">Date de début *</label>
                        <input type="date" 
                               name="date_debut" 
                               id="date_debut" 
                               class="form-control" 
                               value="<?= htmlspecialchars($dateDebut) ?>" 
                               required>
                    </div>

                    <!-- Date de fin -->
                    <div class="col-md-4">
                        <label for="date_fin" class="form-label">Date de fin *</label>
                        <input type="date" 
                               name="date_fin" 
                               id="date_fin" 
                               class="form-control" 
                               value="<?= htmlspecialchars($dateFin) ?>" 
                               required>
                    </div>

                    <!-- Visiteur (optionnel) -->
                    <div class="col-md-4">
                        <label for="visiteur_matricule" class="form-label">Visiteur (optionnel)</label>
                        <select name="visiteur_matricule" id="visiteur_matricule" class="form-select">
                            <option value="">-- Tous les visiteurs --</option>
                            <?php foreach ($listeVisiteurs as $visiteur) : ?>
                                <option value="<?= htmlspecialchars($visiteur['COL_MATRICULE']) ?>"
                                    <?= ($matriculeVisiteurFiltre === $visiteur['COL_MATRICULE']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($visiteur['COL_NOM'] . ' ' . $visiteur['COL_PRENOM']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Rechercher
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Résultats de la recherche -->
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($erreurs) && !empty($rapports)) : ?>
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-list-check"></i> Résultats de la recherche</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>N° Rapport</th>
                                <th>Date visite</th>
                                <th>Visiteur</th>
                                <th>Praticien visité</th>
                                <th>Motif</th>
                                <th>Médicaments présentés</th>
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
                                    <td><?= htmlspecialchars($rap['COL_NOM'] . ' ' . $rap['COL_PRENOM']) ?></td>
                                    <td><?= htmlspecialchars($rap['PRA_NOM'] . ' ' . $rap['PRA_PRENOM']) ?></td>
                                    <td>
                                        <?php 
                                        if (!empty($rap['MOT_LIBELLE'])) {
                                            echo htmlspecialchars($rap['MOT_LIBELLE']);
                                        } elseif (!empty($rap['RAP_MOTIF'])) {
                                            echo htmlspecialchars($rap['RAP_MOTIF']);
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $meds = [];
                                        if (!empty($rap['MED1_NOM'])) $meds[] = $rap['MED1_NOM'];
                                        if (!empty($rap['MED2_NOM'])) $meds[] = $rap['MED2_NOM'];
                                        echo !empty($meds) ? htmlspecialchars(implode(', ', $meds)) : '-';
                                        ?>
                                    </td>
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
                                        <a href="index.php?uc=rapports&action=detailConsultation&mat=<?= urlencode($rap['VIS_MATRICULE']) ?>&num=<?= $rap['RAP_NUM'] ?>"
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
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($erreurs) && empty($rapports)) : ?>
        <div class="alert alert-info">
            <p class="mb-0"><i class="bi bi-info-circle"></i> Aucun rapport de visite trouvé pour cette période.</p>
        </div>
    <?php endif; ?>

    <div class="mt-3">
        <a href="index.php?uc=accueil" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Retour à l'accueil
        </a>
    </div>

</div>
</section>

<script>
// Mettre automatiquement la date de fin à aujourd'hui si elle n'est pas remplie
document.addEventListener('DOMContentLoaded', function() {
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');
    
    if (!dateDebut.value) {
        // Date de début : il y a 1 mois
        const unMoisAvant = new Date();
        unMoisAvant.setMonth(unMoisAvant.getMonth() - 1);
        dateDebut.value = unMoisAvant.toISOString().split('T')[0];
    }
    
    if (!dateFin.value) {
        // Date de fin : aujourd'hui
        const aujourd'hui = new Date();
        dateFin.value = aujourd'hui.toISOString().split('T')[0];
    }
});
</script>
