<section class="py-5">
<div class="container mt-4 mb-5">

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

                <input type="hidden" name="RAP_NUM" value="<?= htmlspecialchars($rapport['RAP_NUM'] ?? 0) ?>">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="RAP_DATEVISITE" class="form-label">Date de la visite *</label>
                        <input type="date" name="RAP_DATEVISITE" id="RAP_DATEVISITE" class="form-control"
                               value="<?= htmlspecialchars($rapport['RAP_DATEVISITE'] ?? '') ?>"
                               max="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
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

                    <div class="col-md-6">
                        <label for="PRA_NUM_REMPLACANT" class="form-label">Praticien de remplacement</label>
                        <select name="PRA_NUM_REMPLACANT" id="PRA_NUM_REMPLACANT" class="form-select">
                            <option value="">-- Aucun --</option>
                            <?php foreach ($listePraticiens as $p) : ?>
                                <option value="<?= htmlspecialchars($p['PRA_NUM']) ?>"
                                    <?php if (!empty($rapport['PRA_NUM_REMPLACANT']) && $rapport['PRA_NUM_REMPLACANT'] == $p['PRA_NUM']) echo 'selected'; ?>>
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

                <div class="mb-3" id="motifAutreContainer" style="display: none;">
                    <label for="RAP_MOTIF" class="form-label">Veuillez préciser le motif *</label>
                    <input type="text" name="RAP_MOTIF" id="RAP_MOTIF" class="form-control" 
                           maxlength="50" placeholder="Précisez le motif de la visite"
                           value="<?= htmlspecialchars($rapport['RAP_MOTIF'] ?? '') ?>">
                    <small class="text-muted">Maximum 50 caractères</small>
                </div>

                <div class="mb-3">
                    <label for="RAP_BILAN" class="form-label">Bilan de la visite *</label>
                    <textarea name="RAP_BILAN" id="RAP_BILAN" class="form-control" rows="5"
                              maxlength="255" required><?= htmlspecialchars($rapport['RAP_BILAN'] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="PRA_COEFCONFIANCE" class="form-label">Coefficient de confiance</label>
                    <input type="number" name="PRA_COEFCONFIANCE" id="PRA_COEFCONFIANCE" 
                           class="form-control" 
                           min="0" max="1000" step="0.01"
                           placeholder="Coefficient"
                           value="<?= htmlspecialchars($rapport['PRA_COEFCONFIANCE'] ?? '') ?>">
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="MED_DEPOTLEGAL1" class="form-label">Médicament présenté 1</label>
                        <select name="MED_DEPOTLEGAL1" id="MED_DEPOTLEGAL1" class="form-select">
                            <option value="">-- Aucun --</option>
                            <?php foreach ($listeMedicaments as $med) : ?>
                                <option value="<?= htmlspecialchars($med['MED_DEPOTLEGAL']) ?>"
                                    <?php if (!empty($rapport['MED_DEPOTLEGAL1']) && $rapport['MED_DEPOTLEGAL1'] == $med['MED_DEPOTLEGAL']) echo 'selected'; ?>>
                                    <?= htmlspecialchars($med['MED_NOMCOMMERCIAL']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="MED_DEPOTLEGAL2" class="form-label">Médicament présenté 2</label>
                        <select name="MED_DEPOTLEGAL2" id="MED_DEPOTLEGAL2" class="form-select">
                            <option value="">-- Aucun --</option>
                            <?php foreach ($listeMedicaments as $med) : ?>
                                <option value="<?= htmlspecialchars($med['MED_DEPOTLEGAL']) ?>"
                                    <?php if (!empty($rapport['MED_DEPOTLEGAL2']) && $rapport['MED_DEPOTLEGAL2'] == $med['MED_DEPOTLEGAL']) echo 'selected'; ?>>
                                    <?= htmlspecialchars($med['MED_NOMCOMMERCIAL']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>


                <hr class="my-4">

                <h3 class="h5 mb-3">Échantillons offerts lors de la visite</h3>
                <p class="text-muted small">Vous pouvez ajouter jusqu'à 10 échantillons différents.</p>

                <div id="echantillonsContainer">
                    <?php if (!empty($echantillons)) : ?>
                        <?php foreach ($echantillons as $index => $ech) : ?>
                            <div class="row mb-2 echantillon-row">
                                <div class="col-md-6">
                                    <select name="echantillon_medicament[]" class="form-select echantillon-select">
                                        <option value="">-- Sélectionnez un médicament --</option>
                                        <?php foreach ($listeMedicaments as $med) : ?>
                                            <option value="<?= htmlspecialchars($med['MED_DEPOTLEGAL']) ?>"
                                                <?php if ($ech['MED_DEPOTLEGAL'] == $med['MED_DEPOTLEGAL']) echo 'selected'; ?>>
                                                <?= htmlspecialchars($med['MED_NOMCOMMERCIAL'] . ' (' . $med['MED_DEPOTLEGAL'] . ')') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" name="echantillon_quantite[]" 
                                           class="form-control echantillon-qte" 
                                           placeholder="Quantité"
                                           min="1" max="1000"
                                           value="<?= htmlspecialchars($ech['OFF_QTE'] ?? '') ?>">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger btn-remove-echantillon w-100">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <button type="button" id="btnAjouterEchantillon" class="btn btn-sm btn-outline-info mb-3">
                    <i class="bi bi-plus-circle"></i> Ajouter un échantillon
                </button>
                <div id="maxEchantillonsWarning" class="alert alert-warning d-none">
                    Vous avez atteint le nombre maximum d'échantillons (10).
                </div>

                <hr class="my-4">

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="saisie_definitive" id="saisie_definitive" 
                               class="form-check-input" value="1"
                               <?php if (!empty($rapport['saisie_definitive'])) echo 'checked'; ?>>
                        <label for="saisie_definitive" class="form-check-label">
                            <strong>Saisie définitive</strong>
                            <small class="d-block text-muted">
                                Cochez cette case pour valider définitivement le rapport. 
                                Si vous ne cochez pas, le rapport sera enregistré en cours de saisie.
                            </small>
                        </label>
                    </div>
                </div>

                <p class="text-muted">Les champs marqués d'un * sont obligatoires.</p>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success text-white">
                        <i class="bi bi-save"></i> Enregistrer le rapport
                    </button>
                    <a href="index.php?uc=accueil" class="btn btn-danger text-white">
                        <i class="bi bi-x-circle"></i> Annuler
                    </a>
                </div>

            </form>
        </div>
    </div>
    </div>

</section>

<script>
const medicamentOptions = `<?php foreach ($listeMedicaments as $med) : ?>
    <option value="<?= htmlspecialchars($med['MED_DEPOTLEGAL']) ?>">
        <?= htmlspecialchars($med['MED_NOMCOMMERCIAL'] . ' (' . $med['MED_DEPOTLEGAL'] . ')') ?>
    </option>
<?php endforeach; ?>`;

const MAX_ECHANTILLONS = 10;

function creerLigneEchantillon() {
    const div = document.createElement('div');
    div.className = 'row mb-2 echantillon-row';
    div.innerHTML = `
        <div class="col-md-6">
            <select name="echantillon_medicament[]" class="form-select echantillon-select">
                <option value="">-- Sélectionnez un médicament --</option>
                ${medicamentOptions}
            </select>
        </div>
        <div class="col-md-4">
            <input type="number" name="echantillon_quantite[]" 
                   class="form-control echantillon-qte" 
                   placeholder="Quantité"
                   min="1" max="1000">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-remove-echantillon w-100">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    return div;
}

// Mettre à jour l'état du bouton d'ajout
function updateBoutonAjout() {
    const nbEchantillons = document.querySelectorAll('.echantillon-row').length;
    const btnAjouter = document.getElementById('btnAjouterEchantillon');
    const warning = document.getElementById('maxEchantillonsWarning');
    
    if (nbEchantillons >= MAX_ECHANTILLONS) {
        btnAjouter.disabled = true;
        warning.classList.remove('d-none');
    } else {
        btnAjouter.disabled = false;
        warning.classList.add('d-none');
    }
}

// Ajouter un échantillon
document.getElementById('btnAjouterEchantillon').addEventListener('click', function() {
    const container = document.getElementById('echantillonsContainer');
    const nbEchantillons = document.querySelectorAll('.echantillon-row').length;
    
    if (nbEchantillons < MAX_ECHANTILLONS) {
        const nouvelleLigne = creerLigneEchantillon();
        container.appendChild(nouvelleLigne);
        updateBoutonAjout();
    }
});

// Supprimer un échantillon (délégation d'événement)
document.getElementById('echantillonsContainer').addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-remove-echantillon') || 
        e.target.closest('.btn-remove-echantillon')) {
        const btn = e.target.closest('.btn-remove-echantillon');
        const row = btn.closest('.echantillon-row');
        row.remove();
        updateBoutonAjout();
    }
});

// Validation du formulaire
document.getElementById('formRapport').addEventListener('submit', function(e) {
    const echantillonRows = document.querySelectorAll('.echantillon-row');
    let erreurs = [];
    
    // Vérifier que chaque échantillon a un médicament ET une quantité
    echantillonRows.forEach((row, index) => {
        const select = row.querySelector('.echantillon-select');
        const qte = row.querySelector('.echantillon-qte');
        
        const medValue = select.value.trim();
        const qteValue = qte.value.trim();
        
        if (medValue && !qteValue) {
            erreurs.push(`Échantillon ${index + 1}: Veuillez saisir une quantité.`);
        }
        
        if (qteValue && !medValue) {
            erreurs.push(`Échantillon ${index + 1}: Veuillez sélectionner un médicament.`);
        }
        
        if (qteValue && (parseInt(qteValue) < 1 || parseInt(qteValue) > 1000)) {
            erreurs.push(`Échantillon ${index + 1}: La quantité doit être entre 1 et 1000.`);
        }
    });
    
    // Vérifier les doublons de médicaments
    const medicaments = [];
    echantillonRows.forEach(row => {
        const medValue = row.querySelector('.echantillon-select').value;
        if (medValue) {
            if (medicaments.includes(medValue)) {
                erreurs.push(`Le médicament ${medValue} est présent plusieurs fois dans les échantillons.`);
            } else {
                medicaments.push(medValue);
            }
        }
    });

    // Vérifier si aucun échantillon n'est sélectionné
    if (medicaments.length === 0) {
        if (!confirm("Voulez-vous vraiment enregistrer ce rapport sans échantillon ?")) {
            e.preventDefault();
            return false;
        }
    }

    // Vérifier si aucun médicament n'est sélectionné (ni présenté, ni échantillon)
    const med1 = document.getElementById('MED_DEPOTLEGAL1').value;
    const med2 = document.getElementById('MED_DEPOTLEGAL2').value;
    
    if (!med1 && !med2 && medicaments.length === 0) {
        if (!confirm("Voulez-vous vraiment enregistrer ce rapport sans médicament ?")) {
            e.preventDefault();
            return false;
        }
    }
    
    if (erreurs.length > 0) {
        e.preventDefault();
        alert('Erreurs de validation:\n\n' + erreurs.join('\n'));
        return false;
    }
});

// Compteur de caractères pour le bilan
document.getElementById('RAP_BILAN').addEventListener('input', function() {
    const maxLength = 255;
    const currentLength = this.value.length;
    
    // Créer ou mettre à jour le compteur
    let compteur = document.getElementById('compteurCaracteres');
    if (!compteur) {
        compteur = document.createElement('small');
        compteur.id = 'compteurCaracteres';
        compteur.className = 'text-muted';
        this.parentNode.appendChild(compteur);
    }
    
    compteur.textContent = currentLength + ' / ' + maxLength + ' caractères';
    
    if (currentLength > maxLength) {
        compteur.classList.add('text-danger');
        compteur.classList.remove('text-muted');
    } else {
        compteur.classList.add('text-muted');
        compteur.classList.remove('text-danger');
    }
});

// Initialiser l'état du bouton au chargement
document.addEventListener('DOMContentLoaded', function() {
    updateBoutonAjout();
    toggleMotifAutre(); // Initialiser l'affichage du champ motif personnalisé
});

// Gérer l'affichage du champ motif personnalisé
document.getElementById('MOT_CODE').addEventListener('change', toggleMotifAutre);

function toggleMotifAutre() {
    const motifSelect = document.getElementById('MOT_CODE');
    const motifAutreContainer = document.getElementById('motifAutreContainer');
    const motifAutreInput = document.getElementById('RAP_MOTIF');
    
    // Afficher le champ si "Autre" (valeur 5) est sélectionné
    if (motifSelect.value === '5') {
        motifAutreContainer.style.display = 'block';
        motifAutreInput.required = true;
    } else {
        motifAutreContainer.style.display = 'none';
        motifAutreInput.required = false;
        motifAutreInput.value = ''; // Réinitialiser la valeur si on change de motif
    }
}
</script>