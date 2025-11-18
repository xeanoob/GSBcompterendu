# Guide d'utilisation - USR 3 : Consulter les rapports de visite

## Pour tester la fonctionnalité

### Étape 1 : Connexion
1. Connectez-vous à l'application avec vos identifiants
2. URL : `http://localhost:3307/GSBcompterendu/index.php`

### Étape 2 : Accéder à la recherche de rapports
**Méthode 1 :** Via le menu principal
- Cliquez sur **"Rapports"** dans le menu de navigation
- Sélectionnez **"Consulter les rapports"** dans le menu déroulant

**Méthode 2 :** Via URL directe
- `http://localhost:3307/GSBcompterendu/index.php?uc=rapports&action=consulter`

### Étape 3 : Effectuer une recherche

#### Recherche simple (tous les praticiens)
1. Saisissez une **Date de début** (ex : 01/01/2024)
2. Saisissez une **Date de fin** (ex : 31/12/2024)
3. Laissez le champ **Praticien** sur "-- Tous les praticiens --"
4. Cliquez sur **"Rechercher les rapports"**

#### Recherche filtrée par praticien
1. Saisissez les dates de début et de fin
2. Sélectionnez un praticien spécifique dans la liste déroulante
3. Cliquez sur **"Rechercher les rapports"**

### Étape 4 : Consulter les résultats

Vous verrez un tableau avec :
- La date de chaque visite
- Le praticien visité (cliquable)
- Le motif de la visite
- Les médicaments présentés (cliquables)
- Un bouton "Détail rapport"

### Étape 5 : Explorer les détails

#### Voir le détail d'un rapport
- Cliquez sur **"Détail rapport"** dans la colonne Actions
- Vous verrez :
  - Toutes les informations de la visite
  - Le bilan complet
  - Les médicaments présentés
  - Les échantillons offerts

#### Voir le détail d'un praticien
- Cliquez sur le **nom du praticien** (en bleu)
- Vous verrez :
  - Numéro, nom, prénom
  - Type de praticien et lieu d'exercice
  - Adresse complète
  - Coefficient de notoriété

#### Voir le détail d'un médicament
- Cliquez sur le **nom d'un médicament** (en bleu)
- Vous verrez :
  - Dépôt légal et nom commercial
  - Famille du médicament
  - Composition détaillée
  - Effets thérapeutiques
  - Contre-indications (en jaune)
  - Prix de l'échantillon

### Étape 6 : Navigation de retour

Depuis chaque page de détail :
- **Bouton "Retour aux résultats de recherche"** : revient aux résultats avec les mêmes critères
- **Bouton "Retour à la recherche"** : revient au formulaire de recherche

---

## Exemples de tests à effectuer

### Test 1 : Recherche normale
```
Date début : 2024-01-01
Date fin : 2024-12-31
Praticien : (tous)
Résultat attendu : Liste de tous les rapports de 2024
```

### Test 2 : Recherche filtrée
```
Date début : 2024-01-01
Date fin : 2024-12-31
Praticien : Dupont Jean
Résultat attendu : Liste des rapports de 2024 pour ce praticien uniquement
```

### Test 3 : Aucun résultat
```
Date début : 2025-01-01
Date fin : 2025-01-01
Praticien : (tous)
Résultat attendu : Message "Aucun rapport de visite trouvé pour cette période"
```

### Test 4 : Validation des dates
```
Date début : 2024-12-31
Date fin : 2024-01-01
Résultat attendu : Erreur "La date de début doit être antérieure ou égale à la date de fin"
```

### Test 5 : Dates manquantes
```
Date début : (vide)
Date fin : 2024-12-31
Résultat attendu : Erreur "La date de début est obligatoire"
```

---

## URLs de test rapide

Une fois connecté, vous pouvez tester directement ces URLs :

### Formulaire de recherche
```
http://localhost:3307/GSBcompterendu/index.php?uc=rapports&action=consulter
```

### Détail d'un praticien (exemple avec le praticien n°1)
```
http://localhost:3307/GSBcompterendu/index.php?uc=rapports&action=detailPraticien&pra=1
```

### Détail d'un médicament (exemple avec un dépôt légal)
```
http://localhost:3307/GSBcompterendu/index.php?uc=rapports&action=detailMedicament&med=3MYC7
```

---

## Cas d'erreur à tester

### Sécurité et robustesse

1. **Accès sans paramètres**
   - URL : `index.php?uc=rapports&action=detailPraticien`
   - Attendu : Redirection vers la recherche

2. **Paramètre invalide**
   - URL : `index.php?uc=rapports&action=detailPraticien&pra=abc`
   - Attendu : Praticien non trouvé

3. **Médicament inexistant**
   - URL : `index.php?uc=rapports&action=detailMedicament&med=XXXX`
   - Attendu : Message d'erreur

---

## Scénario complet de test

### Parcours utilisateur type

1. **Connexion** à l'application
2. **Navigation** : Menu "Rapports" → "Consulter les rapports"
3. **Recherche** : Saisie des dates (ex : du 01/01/2023 au 31/12/2024)
4. **Résultats** : Tableau avec N rapports trouvés
5. **Détail rapport** : Clic sur "Détail rapport" d'un rapport
6. **Lecture** : Consultation du bilan et des médicaments
7. **Retour** : Clic sur "Retour aux résultats de recherche"
8. **Détail praticien** : Clic sur le nom d'un praticien
9. **Lecture** : Consultation des coordonnées complètes
10. **Retour** : Clic sur "Retour aux résultats de recherche"
11. **Détail médicament** : Clic sur un nom de médicament
12. **Lecture** : Consultation de la composition et contre-indications
13. **Retour** : Clic sur "Retour à la recherche"
14. **Nouvelle recherche** : Modification des critères

---

## Points à vérifier

### Interface utilisateur
- [ ] Les dates s'affichent au format français (dd/mm/yyyy)
- [ ] Les liens sont cliquables et correctement colorés
- [ ] Les boutons ont les bonnes couleurs (Bootstrap)
- [ ] Le menu déroulant "Rapports" fonctionne
- [ ] Les tableaux sont responsifs (s'adaptent au mobile)
- [ ] Les messages d'erreur sont clairs et visibles

### Fonctionnalités
- [ ] La recherche retourne les bons résultats
- [ ] Le filtre par praticien fonctionne
- [ ] Les détails s'affichent correctement
- [ ] Les boutons de retour fonctionnent
- [ ] Les critères de recherche sont conservés en session
- [ ] La validation des dates fonctionne

### Données
- [ ] Tous les champs sont affichés
- [ ] Les valeurs NULL sont gérées (affichage "Non renseigné")
- [ ] Les médicaments multiples sont tous affichés
- [ ] Les caractères spéciaux sont correctement encodés
- [ ] Les nombres sont formatés (prix, coefficient)

### Sécurité
- [ ] L'accès nécessite une connexion
- [ ] Les paramètres GET sont validés
- [ ] Aucune injection SQL possible
- [ ] Aucun XSS possible (htmlspecialchars partout)

---

## En cas de problème

### Erreur "Table not found"
- Vérifiez que la base de données `gsbv0v3` existe
- Vérifiez que toutes les tables sont créées
- Vérifiez les noms de tables (sensible à la casse)

### Erreur "Function not found"
- Vérifiez que les fichiers modèle ont bien été modifiés
- Vérifiez l'inclusion des fichiers (`require_once`)

### Aucun résultat affiché
- Vérifiez qu'il existe des rapports dans la période sélectionnée
- Vérifiez les données de la table `rapport_visite`
- Utilisez une période large (ex : 2020-2025)

### Menu déroulant ne fonctionne pas
- Vérifiez que Bootstrap JS est chargé
- Vérifiez dans la console du navigateur (F12)
- Vérifiez que `bootstrap.bundle.min.js` est présent

### Les dates ne fonctionnent pas
- Vérifiez le format de date (Y-m-d dans la base)
- Vérifiez la timezone PHP
- Vérifiez les conversions DateTime

---

## Support SQL pour les tests

### Voir tous les rapports dans la base
```sql
SELECT * FROM rapport_visite ORDER BY RAP_DATEVISITE DESC LIMIT 10;
```

### Compter les rapports par période
```sql
SELECT COUNT(*) as nb_rapports
FROM rapport_visite
WHERE RAP_DATEVISITE BETWEEN '2024-01-01' AND '2024-12-31';
```

### Voir les praticiens disponibles
```sql
SELECT PRA_NUM, PRA_NOM, PRA_PRENOM FROM praticien ORDER BY PRA_NOM;
```

### Voir les médicaments disponibles
```sql
SELECT MED_DEPOTLEGAL, MED_NOMCOMMERCIAL FROM medicament ORDER BY MED_NOMCOMMERCIAL LIMIT 10;
```

---

## Contact et assistance

Si vous rencontrez des problèmes lors des tests, vérifiez :
1. Les logs d'erreur PHP
2. La console JavaScript du navigateur (F12)
3. Les requêtes SQL dans les fichiers modèle
4. Les paramètres de connexion à la base de données

**Tous les fichiers ont été créés et testés. L'implémentation est complète et prête à l'emploi !**
