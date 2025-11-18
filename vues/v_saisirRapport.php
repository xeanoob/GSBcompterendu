<section class="container mt-4 mb-5">

    <h1 class="mb-4">Saisir un rapport de visite</h1>

    <?php if (!empty($messageSucces)) : ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($messageSucces) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($erreurs)) : ?>
        <div class="alert alert-danger">
            <p><strong>Veuillez corriger les erreurs suivantes :</strong></p>
            <ul>
                <?php foreach ($erreurs as $err) : ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="post" action="index.php?uc=rapports&action=enregistrer" id="formRapport">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="RAP_DATEVISITE" class="form-label">Date de la visite *</label>
                        <input type="date" name="RAP_DATEVISITE" id="RAP_DATEVISITE" class="form-control"
                               value="<?= htmlspecialchars($rapport['RAP_DATEVISITE'] ?? '') ?>"
                               max="<?= date('Y-m-d') ?>" required>
                        <small class="text-muted">La date ne peut pas être dans le futur.</small>
                    </div>

                    <div class="col-md-6">
                        <label for="PRA_NUM" class="form-label">Praticien visité *</label>
                        <select name="PRA_NUM" id="PRA_NUM" class="form-select" required>
                            <option value="">-- Sélectionnez un praticien --</option>
                            <?php foreach ($listePraticiens as $p) : ?>
                                <option value="<?= htmlspecialchars($p['PRA_NUM']) ?>"
                                    <?php if (!empty($rapport['PRA_NUM']) && $rapport['PRA_NUM'] == $p['PRA_NUM']) echo 'selected'; ?>>
                                    <?= htmlspecialchars($p['PRA_NOM'] . ' ' . $p['PRA_PRENOM'] . ' - ' . $p['PRA_VILLE']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="MOT_CODE" class="form-label">Motif de la visite *</label>
                    <select name="MOT_CODE" id="MOT_CODE" class="form-select" required>
                        <option value="">-- Sélectionnez un motif --</option>
                        <?php foreach ($listeMotifs as $m) : ?>
                            <option value="<?= htmlspecialchars($m['MOT_CODE']) ?>"
                                <?php if (!empty($rapport['MOT_CODE']) && $rapport['MOT_CODE'] == $m['MOT_CODE']) echo 'selected'; ?>>
                                <?= htmlspecialchars($m['MOT_LIBELLE']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="RAP_BILAN" class="form-label">Bilan de la visite *</label>
                    <textarea name="RAP_BILAN" id="RAP_BILAN" class="form-control" rows="5"
                              maxlength="255" required><?= htmlspecialchars($rapport['RAP_BILAN'] ?? '') ?></textarea>
                </div>

                <hr class="my-4">

                <h3 class="h5 mb-3">Médicaments présentés lors de la visite</h3>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="MED_DEPOTLEGAL1" class="form-label">Médicament 1</label>
                        <select name="MED_DEPOTLEGAL1" id="MED_DEPOTLEGAL1" class="form-select">
                            <option value="">-- Aucun --</option>
                            <?php foreach ($listeMedicaments as $med) : ?>
                                <option value="<?= htmlspecialchars($med['MED_DEPOTLEGAL']) ?>"
                                    <?php if (!empty($rapport['MED_DEPOTLEGAL1']) && $rapport['MED_DEPOTLEGAL1'] == $med['MED_DEPOTLEGAL']) echo 'selected'; ?>>
                                    <?= htmlspecialchars($med['MED_NOMCOMMERCIAL'] . ' (' . $med['MED_DEPOTLEGAL'] . ')') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="MED_DEPOTLEGAL2" class="form-label">Médicament 2</label>
                        <select name="MED_DEPOTLEGAL2" id="MED_DEPOTLEGAL2" class="form-select">
                            <option value="">-- Aucun --</option>
                            <?php foreach ($listeMedicaments as $med) : ?>
                                <option value="<?= htmlspecialchars($med['MED_DEPOTLEGAL']) ?>"
                                    <?php if (!empty($rapport['MED_DEPOTLEGAL2']) && $rapport['MED_DEPOTLEGAL2'] == $med['MED_DEPOTLEGAL']) echo 'selected'; ?>>
                                    <?= htmlspecialchars($med['MED_NOMCOMMERCIAL'] . ' (' . $med['MED_DEPOTLEGAL'] . ')') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <hr class="my-4">

                <p class="text-muted">Les champs marqués d'un * sont obligatoires.</p>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        Enregistrer le rapport
                    </button>
                    <a href="index.php?uc=rapports&action=liste" class="btn btn-outline-secondary">
                        Annuler
                    </a>
                </div>

            </form>
        </div>
    </div>

</section>

<script>
// Compteur de caractères pour le bilan
document.getElementById('RAP_BILAN').addEventListener('input', function() {
    const compteur = document.getElementById('compteurCaracteres');
    compteur.textContent = this.value.length;

    if (this.value.length > 255) {
        compteur.classList.add('text-danger');
        compteur.classList.remove('text-muted');
    } else {
        compteur.classList.add('text-muted');
        compteur.classList.remove('text-danger');
    }
});

// Initialiser le compteur au chargement
document.addEventListener('DOMContentLoaded', function() {
    const bilan = document.getElementById('RAP_BILAN');
    document.getElementById('compteurCaracteres').textContent = bilan.value.length;
});
</script>