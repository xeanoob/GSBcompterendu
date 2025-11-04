<section class="container mt-4 mb-5">

    <h1 class="mb-4">Gérer les praticiens</h1>

    <?php if (!empty($messageSucces)) : ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($messageSucces) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($erreurs)) : ?>
        <div class="alert alert-danger">
            <p>Veuillez corriger les erreurs suivantes :</p>
            <ul>
                <?php foreach ($erreurs as $err) : ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- 1 : Sélection d’un praticien dans une liste déroulante -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="post" action="index.php?uc=praticiens&action=afficher">
                <label class="form-label" for="listePraticiens">Praticiens disponibles :</label>
                <select name="praticien" id="listePraticiens" class="form-select">
                    <option value="">- Choisissez un praticien -</option>
                    <?php foreach ($listePraticiens as $p) : ?>
                        <option value="<?= htmlspecialchars($p['PRA_NUM']) ?>"
                            <?php if (!empty($praticien) && $praticien['PRA_NUM'] == $p['PRA_NUM']) echo 'selected'; ?>>
                            <?= htmlspecialchars($p['PRA_NOM'] . ' ' . $p['PRA_PRENOM'] . ' (n°' . $p['PRA_NUM'] . ')') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Afficher les informations</button>
                    <a href="index.php?uc=praticiens&action=nouveau" class="btn btn-secondary ms-2">
                        Créer un nouveau praticien
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- 3-4 : Formulaire de saisie / modification -->
    <?php if ($mode === 'creation' || $mode === 'modification') : ?>
        <div class="card">
            <div class="card-body">
                <h2 class="h4 mb-3">
                    <?= ($mode === 'creation') ? 'Création d\'un praticien' : 'Modification du praticien' ?>
                </h2>

                <form method="post" action="index.php?uc=praticiens&action=enregistrer">
                    <input type="hidden" name="mode" value="<?= htmlspecialchars($mode) ?>">

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="PRA_NUM" class="form-label">Numéro *</label>
                            <input type="number" name="PRA_NUM" id="PRA_NUM" class="form-control"
                                   value="<?= htmlspecialchars($praticien['PRA_NUM'] ?? '') ?>"
                                   <?= ($mode === 'modification') ? 'readonly' : '' ?>>
                        </div>
                        <div class="col-md-4">
                            <label for="PRA_NOM" class="form-label">Nom *</label>
                            <input type="text" name="PRA_NOM" id="PRA_NOM" class="form-control"
                                   value="<?= htmlspecialchars($praticien['PRA_NOM'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="PRA_PRENOM" class="form-label">Prénom *</label>
                            <input type="text" name="PRA_PRENOM" id="PRA_PRENOM" class="form-control"
                                   value="<?= htmlspecialchars($praticien['PRA_PRENOM'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="PRA_ADRESSE" class="form-label">Adresse</label>
                        <input type="text" name="PRA_ADRESSE" id="PRA_ADRESSE" class="form-control"
                               value="<?= htmlspecialchars($praticien['PRA_ADRESSE'] ?? '') ?>">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="PRA_CP" class="form-label">Code postal *</label>
                            <input type="text" name="PRA_CP" id="PRA_CP" class="form-control"
                                   value="<?= htmlspecialchars($praticien['PRA_CP'] ?? '') ?>">
                        </div>
                        <div class="col-md-5">
                            <label for="PRA_VILLE" class="form-label">Ville *</label>
                            <input type="text" name="PRA_VILLE" id="PRA_VILLE" class="form-control"
                                   value="<?= htmlspecialchars($praticien['PRA_VILLE'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="PRA_COEFNOTORIETE" class="form-label">Coef. notoriété</label>
                            <input type="number" step="0.01" name="PRA_COEFNOTORIETE" id="PRA_COEFNOTORIETE" class="form-control"
                                   value="<?= htmlspecialchars($praticien['PRA_COEFNOTORIETE'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="TYP_CODE" class="form-label">Type de praticien *</label>
                        <select name="TYP_CODE" id="TYP_CODE" class="form-select">
                            <option value="">- Choisissez un type -</option>
                            <?php foreach ($listeTypes as $t) : ?>
                                <option value="<?= htmlspecialchars($t['TYP_CODE']) ?>"
                                    <?php
                                    if (!empty($praticien) && $praticien['TYP_CODE'] == $t['TYP_CODE']) {
                                        echo 'selected';
                                    }
                                    ?>>
                                    <?= htmlspecialchars($t['TYP_LIBELLE'] . ' (' . $t['TYP_CODE'] . ')') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <p class="text-muted">Les champs marqués d’un * sont obligatoires.</p>

                    <button type="submit" name="btn" value="valider" class="btn btn-success">
                        Valider
                    </button>
                    <button type="submit" name="btn" value="annuler" class="btn btn-outline-secondary ms-2">
                        Annuler
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>

</section>
