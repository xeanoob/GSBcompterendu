# Fonctionnalité ajoutée : Champ motif personnalisé pour "Autre"

## Résumé des modifications

Lorsque l'utilisateur sélectionne "Autre" dans le champ "Motif de la visite" du formulaire de saisie de rapport, un champ de texte apparaît dynamiquement pour permettre de préciser le motif personnalisé.

## Fichiers modifiés

### 1. Vue - `vues/v_saisirRapport.php`
- **Ajout d'un champ de texte dynamique** (`RAP_MOTIF`) qui s'affiche uniquement quand "Autre" (MOT_CODE = 5) est sélectionné
- **JavaScript ajouté** pour gérer l'affichage/masquage du champ :
  - La fonction `toggleMotifAutre()` affiche le champ si "Autre" est sélectionné
  - Le champ devient obligatoire (`required`) quand visible
  - Le champ est réinitialisé si l'utilisateur change de motif

### 2. Modèle - `modele/rapport.modele.inc.php`
- **Fonction `creerRapportVisite()`** : Ajout du paramètre `$rapMotif` pour stocker le motif personnalisé dans la base de données
- **Fonction `mettreAJourRapport()`** : Ajout du paramètre `$rapMotif` pour mettre à jour le motif personnalisé

### 3. Contrôleur - `controleur/c_rapports.php`
- **Récupération du champ** `RAP_MOTIF` depuis le formulaire POST
- **Validation** :
  - Si "Autre" (motifCode == 5) est sélectionné, le champ `RAP_MOTIF` est obligatoire et limité à 50 caractères
  - Si un autre motif est sélectionné, le champ `RAP_MOTIF` est vidé (null)
- **Transmission** du paramètre `$rapMotif` aux fonctions `creerRapportVisite()` et `mettreAJourRapport()`

### 4. Vue - `vues/v_detailRapport.php`
- **Affichage du motif personnalisé** dans le détail du rapport
- Le motif personnalisé s'affiche en italique et en gris sous le libellé du motif principal

## Base de données

Le champ `RAP_MOTIF` existe déjà dans la table `rapport_visite` :
```sql
RAP_MOTIF varchar(50) DEFAULT NULL
```

## Utilisation

1. L'utilisateur crée ou modifie un rapport de visite
2. Il sélectionne "Autre" dans le menu déroulant "Motif de la visite"
3. Un champ texte apparaît automatiquement pour préciser le motif
4. Le champ est obligatoire et limité à 50 caractères
5. Lors de l'enregistrement, le motif personnalisé est stocké dans la base de données
6. Le motif personnalisé s'affiche dans le détail du rapport

## Validation

- Le champ est obligatoire uniquement si "Autre" est sélectionné
- Maximum 50 caractères
- Si l'utilisateur change de motif après avoir saisi un texte, le champ est réinitialisé
