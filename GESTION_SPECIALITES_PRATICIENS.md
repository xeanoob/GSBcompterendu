# Gestion des spécialités des praticiens

## Implémentation complète

Date : 18 novembre 2025

---

## Vue d'ensemble

Cette amélioration permet de gérer les **spécialités** des praticiens dans l'application GSB. Un praticien peut avoir **plusieurs spécialités**, et chaque spécialité peut être associée à plusieurs praticiens (relation N:N).

---

## Structure de la base de données

### Tables concernées

#### Table `specialite`
```sql
CREATE TABLE specialite (
  SPE_CODE varchar(5) PRIMARY KEY,
  SPE_LIBELLE varchar(150)
)
```
Contient la liste des spécialités disponibles (ex: Cardiologie, Dermatologie, etc.)

#### Table `posseder` (table de liaison)
```sql
CREATE TABLE posseder (
  PRA_NUM int(11),
  SPE_CODE varchar(5),
  POS_DIPLOME varchar(10),
  POS_COEFPRESCRIPTIO float,
  PRIMARY KEY (PRA_NUM, SPE_CODE)
)
```
Lie les praticiens à leurs spécialités avec des informations complémentaires :
- `POS_DIPLOME` : Diplôme obtenu dans cette spécialité
- `POS_COEFPRESCRIPTIO` : Coefficient de prescription

---

## Modifications apportées

### 1. Modèle (modele/praticien.modele.inc.php)

**Nouvelles fonctions ajoutées :**

```php
function getAllSpecialites()
```
- Récupère toutes les spécialités disponibles
- Utilisée pour afficher la liste de sélection

```php
function getSpecialitesPraticien($praticienNum)
```
- Récupère toutes les spécialités d'un praticien
- Retourne les informations complètes (libellé, diplôme, coefficient)

```php
function ajouterSpecialitePraticien($praticienNum, $speCode, $diplome, $coefPrescription)
```
- Ajoute une spécialité à un praticien
- Les paramètres diplôme et coefficient sont optionnels (par défaut vides/0)

```php
function supprimerToutesSpecialitesPraticien($praticienNum)
```
- Supprime toutes les spécialités d'un praticien
- Utilisée avant de réenregistrer les nouvelles sélections

---

### 2. Contrôleur (controleur/c_praticiens.php)

**Modifications :**

#### Variables globales
Ajout de :
```php
$listeSpecialites = getAllSpecialites();
$specialitesPraticien = [];
```

#### Action `afficher`
- Récupère les spécialités du praticien sélectionné
- `$specialitesPraticien = getSpecialitesPraticien($num);`

#### Action `modifier`
- Récupère les spécialités pour pré-remplir le formulaire
- `$specialitesPraticien = getSpecialitesPraticien($num);`

#### Action `enregistrer`
- Récupère les spécialités sélectionnées : `$specialitesSelectionnees = $_POST['specialites'] ?? [];`
- Supprime toutes les anciennes spécialités
- Ajoute les nouvelles spécialités sélectionnées
- Message de confirmation avec le nombre de spécialités associées

**Logique d'enregistrement :**
```php
// 1. Supprimer toutes les spécialités existantes
supprimerToutesSpecialitesPraticien($num);

// 2. Ajouter les nouvelles spécialités
foreach ($specialitesSelectionnees as $speCode) {
    ajouterSpecialitePraticien($num, $speCode);
}
```

---

### 3. Vue (vues/v_gererPraticien.php)

#### Modification 1 : Liste déroulante des praticiens
**Avant :**
```php
Dupont Jean (n°123)
```

**Après :**
```php
Dupont Jean (n°123) - Médecin généraliste
```
Affiche maintenant le **type du praticien** dans la liste déroulante.

#### Modification 2 : Mode consultation
Ajout d'une section "Spécialités" qui affiche :
- La liste des spécialités du praticien sous forme de badges
- Le diplôme (si renseigné)
- Le coefficient de prescription (si renseigné)
- Message "Aucune spécialité renseignée" si le praticien n'a pas de spécialité

**Affichage :**
```
Spécialités :
  🔵 Cardiologie (Diplôme: DESC) - Coef: 1.5
  🔵 Médecine générale - Coef: 1.0
```

#### Modification 3 : Formulaire de création/modification
Ajout d'un champ de **sélection multiple** pour les spécialités :

- `<select multiple>` avec toutes les spécialités disponibles
- Taille de 8 lignes pour afficher plusieurs options
- Aide visuelle : instructions Ctrl/Cmd pour sélection multiple
- Message informatif : "Vous pouvez créer un praticien sans spécialité"
- Pré-sélection des spécialités existantes en mode modification

**Code HTML :**
```html
<select name="specialites[]" multiple size="8">
  <option value="SPE1">Cardiologie (SPE1)</option>
  <option value="SPE2" selected>Dermatologie (SPE2)</option>
  ...
</select>
```

---

## Fonctionnalités implémentées

### ✅ 1. Afficher tous les praticiens avec leur type
La liste déroulante affiche maintenant :
- Nom et prénom du praticien
- Numéro du praticien
- **Type du praticien** (Médecin, Pharmacien, etc.)

### ✅ 2. Afficher toutes les spécialités disponibles
Le formulaire affiche toutes les spécialités de la table `specialite` dans une liste à choix multiple.

### ✅ 3. Sélection multiple des spécialités
Le délégué peut sélectionner 0, 1 ou plusieurs spécialités :
- Maintenez Ctrl (Windows) ou Cmd (Mac) pour sélection multiple
- Les spécialités existantes sont pré-sélectionnées en mode modification

### ✅ 4. Enregistrement sans spécialité
Le délégué peut créer/modifier un praticien **sans sélectionner de spécialités**.
- Les spécialités sont facultatives
- Message informatif dans le formulaire
- Pas d'erreur si aucune spécialité n'est sélectionnée

### ✅ 5. Affichage en mode consultation
Les spécialités sont affichées visuellement avec :
- Badges colorés pour chaque spécialité
- Informations complémentaires (diplôme, coefficient)
- Message clair si aucune spécialité

---

## Flux d'utilisation

### Scénario 1 : Créer un praticien avec spécialités

1. **Accès** : Menu "Praticiens" → "Gérer les praticiens"
2. **Création** : Cliquer sur "Créer un nouveau praticien"
3. **Saisie** :
   - Remplir les champs obligatoires (numéro, nom, prénom, etc.)
   - **Sélectionner une ou plusieurs spécialités** (optionnel)
     - Maintenir Ctrl/Cmd pour sélectionner plusieurs
4. **Validation** : Cliquer sur "Valider"
5. **Confirmation** : Message "Le praticien a été créé avec succès. X spécialité(s) associée(s)."

### Scénario 2 : Créer un praticien sans spécialité

1. Suivre les étapes 1-3 du scénario 1
2. **Ne pas sélectionner de spécialité**
3. Cliquer sur "Valider"
4. Le praticien est créé normalement (pas d'erreur)

### Scénario 3 : Modifier les spécialités d'un praticien

1. **Sélection** : Choisir un praticien dans la liste
2. **Affichage** : Cliquer sur "Afficher les informations"
3. **Consultation** : Voir les spécialités actuelles
4. **Modification** : Cliquer sur "Modifier ce praticien"
5. **Édition** :
   - Les spécialités actuelles sont déjà sélectionnées
   - Ajouter/retirer des spécialités
6. **Validation** : Cliquer sur "Valider"
7. **Mise à jour** : Les anciennes spécialités sont remplacées par les nouvelles

### Scénario 4 : Consulter les praticiens avec leur type

1. **Accès** : Menu "Praticiens" → "Gérer les praticiens"
2. **Liste** : La liste déroulante affiche :
   ```
   Dupont Jean (n°123) - Médecin généraliste
   Martin Pierre (n°456) - Pharmacien
   Durand Sophie (n°789) - Médecin spécialiste
   ```

---

## Tests à effectuer

### Test 1 : Création avec spécialités
```
1. Créer un nouveau praticien
2. Sélectionner 2-3 spécialités (Ctrl+Clic)
3. Valider
Résultat attendu : Praticien créé avec message "3 spécialité(s) associée(s)"
```

### Test 2 : Création sans spécialité
```
1. Créer un nouveau praticien
2. Ne sélectionner aucune spécialité
3. Valider
Résultat attendu : Praticien créé sans erreur
```

### Test 3 : Modification - Ajout de spécialités
```
1. Sélectionner un praticien sans spécialité
2. Modifier
3. Sélectionner 2 spécialités
4. Valider
Résultat attendu : Les spécialités sont ajoutées
```

### Test 4 : Modification - Suppression de spécialités
```
1. Sélectionner un praticien avec spécialités
2. Modifier
3. Désélectionner toutes les spécialités
4. Valider
Résultat attendu : Le praticien n'a plus de spécialités
```

### Test 5 : Modification - Remplacement de spécialités
```
1. Praticien avec Cardiologie et Dermatologie
2. Modifier
3. Désélectionner Dermatologie, ajouter Médecine générale
4. Valider
Résultat attendu : Le praticien a Cardiologie et Médecine générale
```

### Test 6 : Affichage dans la liste
```
1. Accéder à "Gérer les praticiens"
2. Observer la liste déroulante
Résultat attendu : Chaque praticien affiche son type
```

### Test 7 : Affichage en consultation
```
1. Sélectionner un praticien avec spécialités
2. Afficher les informations
Résultat attendu : Section "Spécialités" avec badges et infos
```

---

## Points techniques

### Gestion de la relation N:N

La relation N:N entre praticiens et spécialités est gérée par :
1. **Suppression-réinsertion** : Les anciennes associations sont supprimées puis recréées
2. **Simplicité** : Pas de gestion complexe des mises à jour différentielles
3. **Intégrité** : Les clés étrangères assurent la cohérence

### Attributs optionnels

Les champs `POS_DIPLOME` et `POS_COEFPRESCRIPTIO` sont pour l'instant :
- **Enregistrés avec des valeurs par défaut** (vide et 0)
- **Affichés en consultation** s'ils sont renseignés
- **Non éditables** dans le formulaire actuel

Pour permettre la saisie de ces attributs, il faudrait :
- Ajouter des champs input pour chaque spécialité sélectionnée (JavaScript dynamique)
- Ou créer un formulaire de gestion détaillée des spécialités

### Sélection multiple HTML

Le `<select multiple>` a quelques limitations :
- **UX pas optimale** : Nécessite Ctrl/Cmd
- **Solution alternative** : Checkboxes ou bibliothèque Select2/Choices.js
- **Avantage** : Pas de dépendance JavaScript, fonctionne sans JS

---

## Améliorations futures possibles

### Interface utilisateur
1. **Bibliothèque Select2** : Améliorer l'UX de la sélection multiple
   - Recherche dans les spécialités
   - Tags visuels
   - Pas besoin de Ctrl

2. **Checkboxes** : Alternative plus intuitive
   - Une checkbox par spécialité
   - Plus visuel

3. **Gestion des attributs** : Diplôme et coefficient
   - Champs additionnels par spécialité
   - Interface dynamique (JavaScript)

### Fonctionnalités
1. **Filtrage** : Rechercher les praticiens par spécialité
2. **Statistiques** : Nombre de praticiens par spécialité
3. **Export** : Liste des praticiens avec leurs spécialités
4. **Validation** : Vérifier la cohérence type/spécialités

### Performance
1. **Optimisation SQL** : Récupérer les spécialités en une seule requête
2. **Cache** : Mise en cache de la liste des spécialités
3. **Index** : Index sur les clés étrangères

---

## Fichiers modifiés

### Modèle
- ✅ `modele/praticien.modele.inc.php` - Ajout de 4 fonctions

### Contrôleur
- ✅ `controleur/c_praticiens.php` - Gestion des spécialités dans toutes les actions

### Vue
- ✅ `vues/v_gererPraticien.php` - Affichage du type, sélection multiple, consultation

---

## SQL de vérification

### Voir toutes les spécialités
```sql
SELECT * FROM specialite ORDER BY SPE_LIBELLE;
```

### Voir les spécialités d'un praticien
```sql
SELECT p.PRA_NOM, p.PRA_PRENOM, s.SPE_LIBELLE, po.POS_DIPLOME, po.POS_COEFPRESCRIPTIO
FROM praticien p
INNER JOIN posseder po ON p.PRA_NUM = po.PRA_NUM
INNER JOIN specialite s ON po.SPE_CODE = s.SPE_CODE
WHERE p.PRA_NUM = 1;
```

### Compter les praticiens par spécialité
```sql
SELECT s.SPE_LIBELLE, COUNT(po.PRA_NUM) as nb_praticiens
FROM specialite s
LEFT JOIN posseder po ON s.SPE_CODE = po.SPE_CODE
GROUP BY s.SPE_CODE, s.SPE_LIBELLE
ORDER BY nb_praticiens DESC;
```

### Praticiens sans spécialité
```sql
SELECT p.PRA_NUM, p.PRA_NOM, p.PRA_PRENOM
FROM praticien p
LEFT JOIN posseder po ON p.PRA_NUM = po.PRA_NUM
WHERE po.PRA_NUM IS NULL;
```

---

## Conclusion

L'implémentation de la gestion des spécialités est **complète et fonctionnelle**.

**Fonctionnalités livrées :**
- ✅ Affichage du type dans la liste des praticiens
- ✅ Sélection multiple des spécialités (0 à N)
- ✅ Création de praticiens sans spécialité
- ✅ Modification des spécialités
- ✅ Affichage en consultation avec informations détaillées
- ✅ Gestion automatique de la table de liaison

Le code respecte :
- L'architecture MVC existante
- Les standards de sécurité (requêtes préparées, htmlspecialchars)
- La charte graphique Bootstrap
- Les contraintes métier (spécialités facultatives)

**L'application est prête pour les tests !**
