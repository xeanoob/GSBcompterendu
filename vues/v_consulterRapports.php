<section class="py-5" style="background-color: #f6f9fe;">
<div class="container mt-4 mb-5">

    <h1 class="mb-4 mt-4">Consulter les rapports de visite</h1>

    <?php if (!empty($erreurs)) : ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($erreurs as $err) : ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-light text-dark">
            <h2 class="h5 mb-0 fw-semibold">
                <i class="bi bi-funnel me-2"></i>Critères de recherche
            </h2>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?uc=rapports&action=rechercher">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="date_debut" class="form-label fw-semibold">
                            Date de début <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               class="form-control"
                               id="date_debut"
                               name="date_debut"
                               value="<?= htmlspecialchars($_POST['date_debut'] ?? '') ?>"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label for="date_fin" class="form-label fw-semibold">
                            Date de fin <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               class="form-control"
                               id="date_fin"
                               name="date_fin"
                               value="<?= htmlspecialchars($_POST['date_fin'] ?? '') ?>"
                               required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="praticien_num" class="form-label fw-semibold">
                        Praticien (optionnel)
                    </label>
                    <select class="form-select" id="praticien_num" name="praticien_num">
                        <option value=""> Tous les praticiens </option>
                        <?php foreach ($listePraticiens as $prat) : ?>
                            <option value="<?= $prat['PRA_NUM'] ?>"
                                <?= (isset($_POST['praticien_num']) && $_POST['praticien_num'] == $prat['PRA_NUM']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($prat['PRA_NOM'] . ' ' . $prat['PRA_PRENOM']) ?>
                                <?= !empty($prat['PRA_VILLE']) ? '(' . htmlspecialchars($prat['PRA_VILLE']) . ')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-info text-white fw-semibold">
                        <i class="bi bi-search me-1"></i>Rechercher les rapports
                    </button>
                    <a href="index.php?uc=accueil" class="btn btn-danger text-white">
                        <i class="bi bi-x-circle me-1"></i>Annuler
                    </a>
                </div>

                <p class="text-muted mt-3 mb-0">
                    <small><span class="text-danger">*</span> Champs obligatoires</small>
                </p>

            </form>
        </div>
    </div>
    </div>

</section>
