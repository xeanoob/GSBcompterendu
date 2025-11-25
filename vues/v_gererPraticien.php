<section class="bg-light py-5">
<div class="container mt-4 mb-5">

    <h1 class="mb-4">
        <?= ($_SESSION['habilitation'] == 1) ? 'Voir les praticiens' : 'Gérer les praticiens' ?>
    </h1>

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
                        <?php
                        // Trouver le type de ce praticien
                        $typeLibelle = '';
                        foreach ($listeTypes as $t) {
                            if ($t['TYP_CODE'] == $p['TYP_CODE']) {
                                $typeLibelle = $t['TYP_LIBELLE'];
                                break;
                            }
                        }
                        ?>
                        <option value="<?= htmlspecialchars($p['PRA_NUM']) ?>"
                            <?php if (!empty($praticien) && $praticien['PRA_NUM'] == $p['PRA_NUM']) echo 'selected'; ?>>
                            <?= htmlspecialchars($p['PRA_NOM'] . ' ' . $p['PRA_PRENOM']) ?>
                            (n°<?= $p['PRA_NUM'] ?>) - <?= htmlspecialchars($typeLibelle) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div class="mt-3">
                    <button type="submit" class="btn btn-info text-light">Afficher les informations</button>
                    <?php if ($_SESSION['habilitation'] == 2 || $_SESSION['habilitation'] == 3) : ?>
                        <a href="index.php?uc=praticiens&action=nouveau" class="btn btn-info text-light ms-2">
                            + Créer un nouveau praticien
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- 2 : Affichage en mode consultation -->
    <?php if ($mode === 'consultation') : ?>
        <div class="card">
            <div class="card-body">
                <h2 class="h4 mb-3">Informations du praticien</h2>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Numéro :</strong><br>
                        <?= htmlspecialchars($praticien['PRA_NUM']) ?>
                    </div>
                    <div class="col-md-4">
                        <strong>Nom :</strong><br>
                        <?= htmlspecialchars($praticien['PRA_NOM']) ?>
                    </div>
                    <div class="col-md-5">
                        <strong>Prénom :</strong><br>
                        <?= htmlspecialchars($praticien['PRA_PRENOM']) ?>
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Adresse :</strong><br>
                    <?= htmlspecialchars($praticien['PRA_ADRESSE'] ?? 'Non renseignée') ?>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Code postal :</strong><br>
                        <?= htmlspecialchars($praticien['PRA_CP']) ?>
                    </div>
                    <div class="col-md-5">
                        <strong>Ville :</strong><br>
                        <?= htmlspecialchars($praticien['PRA_VILLE']) ?>
                    </div>
                    <div class="col-md-4">
                        <strong>Coef. notoriété :</strong><br>
                        <?= htmlspecialchars($praticien['PRA_COEFNOTORIETE'] ?? 'Non renseigné') ?>
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Type de praticien :</strong><br>
                    <?php
                    $typeLibelle = '';
                    foreach ($listeTypes as $t) {
                        if ($t['TYP_CODE'] == $praticien['TYP_CODE']) {
                            $typeLibelle = $t['TYP_LIBELLE'];
                            break;
                        }
                    }
                    ?>
                    <?= htmlspecialchars($typeLibelle . ' (' . $praticien['TYP_CODE'] . ')') ?>
                </div>

                <div class="mb-3">
                    <strong>Spécialités :</strong><br>
                    <?php if (!empty($specialitesPraticien)) : ?>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <?php foreach ($specialitesPraticien as $spe) : ?>
                                <span class="badge bg-info text-light rounded-pill px-3 py-2">
                                    <?= htmlspecialchars($spe['SPE_LIBELLE']) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <span class="text-muted">Aucune spécialité renseignée</span>
                    <?php endif; ?>
                </div>

                <div class="mt-4">
                    <?php if ($_SESSION['habilitation'] == 2 || $_SESSION['habilitation'] == 3) : ?>
                        <a href="index.php?uc=praticiens&action=modifier&num=<?= $praticien['PRA_NUM'] ?>"
                           class="btn btn-info text-light">
                            Modifier ce praticien
                        </a>
                    <?php endif; ?>
                    <a href="index.php?uc=praticiens&action=selection"
                       class="btn btn-outline-secondary ms-2">
                        Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

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
                        <?php if ($mode === 'modification') : ?>
                            <div class="col-md-3">
                                <label for="PRA_NUM" class="form-label">Numéro *</label>
                                <input type="number" name="PRA_NUM" id="PRA_NUM" class="form-control" min="1"
                                       value="<?= htmlspecialchars($praticien['PRA_NUM'] ?? '') ?>"
                                       readonly>
                            </div>
                        <?php endif; ?>
                        <div class="<?= ($mode === 'modification') ? 'col-md-4' : 'col-md-6' ?>">
                            <label for="PRA_NOM" class="form-label">Nom *</label>
                            <input type="text" name="PRA_NOM" id="PRA_NOM" class="form-control" maxlength="50"
                                   value="<?= htmlspecialchars($praticien['PRA_NOM'] ?? '') ?>">
                        </div>
                        <div class="<?= ($mode === 'modification') ? 'col-md-4' : 'col-md-6' ?>">
                            <label for="PRA_PRENOM" class="form-label">Prénom *</label>
                            <input type="text" name="PRA_PRENOM" id="PRA_PRENOM" class="form-control" maxlength="50"
                                   value="<?= htmlspecialchars($praticien['PRA_PRENOM'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="PRA_ADRESSE" class="form-label">Adresse</label>
                        <input type="text" name="PRA_ADRESSE" id="PRA_ADRESSE" class="form-control" maxlength="100"
                               value="<?= htmlspecialchars($praticien['PRA_ADRESSE'] ?? '') ?>">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="PRA_CP" class="form-label">Code postal *</label>
                            <input type="text" name="PRA_CP" id="PRA_CP" class="form-control" maxlength="5" pattern="[0-9]{5}" title="5 chiffres requis"
                                   value="<?= htmlspecialchars($praticien['PRA_CP'] ?? '') ?>">
                        </div>
                        <div class="col-md-5">
                            <label for="PRA_VILLE" class="form-label">Ville *</label>
                            <input type="text" name="PRA_VILLE" id="PRA_VILLE" class="form-control" maxlength="50"
                                   value="<?= htmlspecialchars($praticien['PRA_VILLE'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="PRA_COEFNOTORIETE" class="form-label">Coef. notoriété</label>
                            <input type="number" step="0.01" min="0" name="PRA_COEFNOTORIETE" id="PRA_COEFNOTORIETE" class="form-control"
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

                    <div class="mb-3">
                        <label class="form-label">Spécialités (optionnel - sélection multiple)</label>
                        <?php
                        // Créer un tableau des codes de spécialités déjà sélectionnées
                        $specialitesSelectionnees = [];
                        if (!empty($specialitesPraticien)) {
                            foreach ($specialitesPraticien as $spe) {
                                $specialitesSelectionnees[] = $spe['SPE_CODE'];
                            }
                        }
                        ?>
                        <div class="border rounded p-3 bg-white" style="max-height: 300px; overflow-y: auto;">
                            <div class="row g-2">
                                <?php foreach ($listeSpecialites as $spe) : ?>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="specialites[]" 
                                                   value="<?= htmlspecialchars($spe['SPE_CODE']) ?>"
                                                   id="spe_<?= htmlspecialchars($spe['SPE_CODE']) ?>"
                                                   <?php if (in_array($spe['SPE_CODE'], $specialitesSelectionnees)) echo 'checked'; ?>>
                                            <label class="form-check-label" for="spe_<?= htmlspecialchars($spe['SPE_CODE']) ?>">
                                                <?= htmlspecialchars($spe['SPE_LIBELLE']) ?>
                                                <small class="text-muted">(<?= htmlspecialchars($spe['SPE_CODE']) ?>)</small>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="form-text">
                            Cochez les spécialités du praticien. Vous pouvez créer un praticien sans spécialité.
                        </div>
                    </div>

                    <p class="text-muted">Les champs marqués d'un * sont obligatoires.</p>

                    <button type="submit" name="btn" value="valider" class="btn btn-info text-light">
                        Valider
                    </button>
                    <button type="submit" name="btn" value="annuler" class="btn btn-outline-secondary ms-2">
                        Annuler
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
    </div>

</section>
