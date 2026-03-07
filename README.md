# Analyse du sujet – GSB Compte-rendu de visite

## Contexte général

Le projet **GSB (Galaxy Swiss Bourdin)** est une application web PHP destinée à un **laboratoire pharmaceutique**. Elle permet aux collaborateurs (visiteurs médicaux, délégués régionaux, responsables de secteur) de gérer les **comptes-rendus de visites** effectuées auprès de praticiens (médecins, pharmaciens…).

L'application est hébergée localement via **WAMP** et utilise une base de données **MySQL** (`gsbv0v3`).

---

## Architecture technique

| Composant | Détail |
|---|---|
| **Langage** | PHP (procédural) |
| **Architecture** | MVC (Modèle – Vue – Contrôleur) |
| **Base de données** | MySQL via PDO |
| **CSS / UI** | Bootstrap |
| **Serveur** | WAMP (localhost) |
| **Routage** | Paramètre `uc` (use case) dans [index.php](file:///c:/wamp64/www/GSBcompterendu-betta/index.php) |

### Point d'entrée : [index.php](file:///c:/wamp64/www/GSBcompterendu-betta/index.php)

Le fichier [index.php](file:///c:/wamp64/www/GSBcompterendu-betta/index.php) route les requêtes via un `switch` sur le paramètre `uc` :

| `uc` | Contrôleur | Accès |
|---|---|---|
| [connexion](file:///c:/wamp64/www/GSBcompterendu-betta/modele/bd.inc.php#3-21) | [c_connexion.php](file:///c:/wamp64/www/GSBcompterendu-betta/controleur/c_connexion.php) | Public |
| `praticiens` | [c_praticiens.php](file:///c:/wamp64/www/GSBcompterendu-betta/controleur/c_praticiens.php) | Authentifié |
| `rapports` | [c_rapports.php](file:///c:/wamp64/www/GSBcompterendu-betta/controleur/c_rapports.php) | Authentifié |
| `medicaments` | [c_medicaments.php](file:///c:/wamp64/www/GSBcompterendu-betta/controleur/c_medicaments.php) | Authentifié |
| (défaut) | [v_accueil.php](file:///c:/wamp64/www/GSBcompterendu-betta/vues/v_accueil.php) | Public |

---

## Modèle Conceptuel de Données (MCD)

Le MCD comporte **15+ entités** principales :

### Entités clés

| Entité | Rôle | Clé primaire |
|---|---|---|
| **collaborateur** | Visiteurs médicaux, délégués, responsables | `VIS_MATRICULE` |
| **praticien** | Médecins/pharmaciens visités | `PRA_NUM` |
| **rapport_visite** | Comptes-rendus de visite | `RAP_NUM` |
| **medicament** | Médicaments du laboratoire | `MED_DEPOTLEGAL` |
| **login** | Identifiants de connexion | `LOG_ID` |
| **specialite** | Spécialités médicales | `SPE_CODE` |
| **type_praticien** | Types de praticiens (médecin, pharmacien…) | `TYP_CODE` |
| **region** / **secteur** / **departement** | Découpage géographique | `REG_CODE` / `SEC_CODE` / `NoDEPT` |
| **habilitation** | Niveaux de droits | `HAB_ID` |
| **famille** / **presentation** / **dosage** | Classification des médicaments | Clés respectives |

### Relations importantes

- **collaborateur** ↔ **rapport_visite** : un collaborateur *rédige* des rapports
- **rapport_visite** ↔ **praticien** : un rapport *concerne* un praticien
- **rapport_visite** ↔ **medicament** : un rapport peut *présenter* / *offrir* des médicaments
- **praticien** ↔ **specialite** : relation N:N via la table `posseder`
- **praticien** ↔ **type_praticien** : chaque praticien a un type

---

## Modules fonctionnels implémentés

### 1. Connexion / Authentification
- Login via matricule collaborateur
- Mot de passe haché en SHA-512
- Session PHP avec stockage de l'habilitation, de la région et du secteur

### 2. Gestion des praticiens (`uc=praticiens`)

Le module le plus développé, avec un **CRUD complet** :

| Action | Description | Habilitation requise |
|---|---|---|
| `selection` | Afficher la liste déroulante des praticiens | Visiteur (1), Délégué (2), Responsable (3) |
| `afficher` | Consulter les informations d'un praticien | Visiteur (1), Délégué (2), Responsable (3) |
| [modifier](file:///c:/wamp64/www/GSBcompterendu-betta/modele/praticien.modele.inc.php#140-173) | Modifier un praticien | Délégué (2), Responsable (3) |
| `nouveau` | Créer un nouveau praticien | Délégué (2), Responsable (3) |
| `enregistrer` | Valider la création/modification | Délégué (2), Responsable (3) |

**Fonctionnalités spécifiques :**
- Filtrage par **région** (délégué) ou **secteur** (responsable)
- Tri par nom ou numéro
- Gestion des **spécialités** (relation N:N avec checkboxes)
- Validation des champs obligatoires (nom, prénom, CP, ville, type)
- Contrôle d'accès : un délégué ne peut modifier que les praticiens **de sa région**

### 3. Rapports de visite (`uc=rapports`)
- Saisie de rapports de visite
- Consultation des rapports
- Historique par région
- Statistiques de visites

### 4. Médicaments (`uc=medicaments`)
- Consultation des médicaments
- Détails des médicaments

---

## Système de droits (habilitations)

| HAB_ID | Rôle | Droits |
|---|---|---|
| **1** | Visiteur médical | Consulter praticiens, rédiger rapports |
| **2** | Délégué régional | + Créer/modifier praticiens **de sa région** |
| **3** | Responsable de secteur | + Créer/modifier praticiens **de son secteur** |

---

## Fichiers du projet

```
GSBcompterendu-betta/
├── index.php                          ← Point d'entrée (routeur)
├── modele/
│   ├── bd.inc.php                     ← Connexion PDO
│   ├── connexion.modele.inc.php       ← Fonctions authentification
│   ├── praticien.modele.inc.php       ← Fonctions CRUD praticiens
│   └── medicament.modele.inc.php      ← Fonctions médicaments
├── controleur/
│   ├── c_connexion.php                ← Logique connexion
│   ├── c_praticiens.php               ← Logique gestion praticiens
│   ├── c_rapports.php                 ← Logique rapports (30 KB)
│   └── c_medicaments.php              ← Logique médicaments
├── vues/                              ← 19 fichiers PHP (Bootstrap)
├── bdd/
│   └── gsbv0v3.sql                    ← Script SQL complet
├── docs/                             ← Cahier des charges, cas d'utilisation
├── RAPPORT/                           ← Rapports d'implémentation
└── TestsMaquettesFonctionnelles/      ← Maquettes et tests fonctionnels
```

---

## Résumé

Ce projet est une **application de gestion de comptes-rendus de visites médicales** pour le laboratoire GSB, construite en **PHP/MVC avec Bootstrap**. Elle couvre :

1. **L'authentification** avec 3 niveaux d'habilitation
2. **La gestion complète des praticiens** (CRUD + spécialités + contrôle d'accès géographique)
3. **Les rapports de visite** (saisie, consultation, historique, statistiques)
4. **La consultation des médicaments**

Le module « **Gérer praticien** » est au cœur du sujet que vous avez ouvert, avec un système de droits d'accès géographique (région/secteur) et une gestion des spécialités en relation N:N.
