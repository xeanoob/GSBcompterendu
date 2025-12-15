<?php

require_once 'modele/praticien.modele.inc.php';

if (!isset($_REQUEST['action']) || empty($_REQUEST['action'])) {
    $action = "selection";
} else {
    $action = $_REQUEST['action'];
}

// variables communes
// Récupérer la région de l'utilisateur connecté
$regionUtilisateur = $_SESSION['region'] ?? null;
$secteurUtilisateur = $_SESSION['secteur'] ?? null;

// Récupérer le paramètre de tri (par défaut 'nom')
$tri = $_REQUEST['tri'] ?? 'nom';

// Récupérer le filtre (tous ou modifiables)
$filtre = $_REQUEST['filtre'] ?? 'tous';

// Charger les praticiens selon le filtre
if ($filtre === 'moi') {
    // Afficher uniquement les praticiens modifiables
    if ($_SESSION['habilitation'] == 2 && $regionUtilisateur) {
        // Délégué : praticiens de sa région
        $listePraticiens = getAllPraticiens($regionUtilisateur, $tri);
    } elseif ($_SESSION['habilitation'] == 3 && $secteurUtilisateur) {
        // Responsable secteur : praticiens de son secteur
        $listePraticiens = getAllPraticiens(null, $tri, $secteurUtilisateur);
    } else {
        $listePraticiens = getAllPraticiens(null, $tri);
    }
} else {
    // Afficher tous les praticiens
    $listePraticiens = getAllPraticiens(null, $tri);
}
$listeTypes = getAllTypesPraticien();
$listeSpecialites = getAllSpecialites();
$specialitesPraticien = [];
$praticien = null;
$mode = 'aucun';          // 'aucun' | 'creation' | 'modification'
$erreurs = [];
$messageSucces = "";

switch ($action) {

    // Le délégué demande à gérer les praticiens (scénario nominal)
    case 'selection':
        // Vérification des droits d'accès (Délégué, Visiteur ou Responsable)
        if ($_SESSION['habilitation'] != 2 && $_SESSION['habilitation'] != 1 && $_SESSION['habilitation'] != 3) {
            header('Location: index.php?uc=accueil');
            exit;
        }
        // Juste la liste déroulante + bouton "Créer"
        include("vues/v_gererPraticien.php");
        break;

    // L'utilisateur choisit un praticien dans la liste
    case 'afficher':
        // Vérification des droits d'accès (Délégué, Visiteur ou Responsable)
        if ($_SESSION['habilitation'] != 2 && $_SESSION['habilitation'] != 1 && $_SESSION['habilitation'] != 3) {
            header('Location: index.php?uc=accueil');
            exit;
        }
        if (!empty($_POST['praticien'])) {
            $num = (int) $_POST['praticien'];
            $praticien = getPraticienByNum($num);
            if ($praticien) {
                $mode = 'consultation';  // Mode consultation par défaut
                $specialitesPraticien = getSpecialitesPraticien($num);
            } else {
                $erreurs[] = "Le praticien sélectionné n'existe pas.";
            }
        } else {
            $erreurs[] = "Vous devez choisir un praticien dans la liste.";
        }
        include("vues/v_gererPraticien.php");
        break;

    // Le délégué clique sur "Modifier" depuis le mode consultation
    case 'modifier':
        // Vérification des droits d'accès (Délégué ou Responsable)
        if ($_SESSION['habilitation'] != 2 && $_SESSION['habilitation'] != 3) {
            header('Location: index.php?uc=accueil');
            exit;
        }
        if (!empty($_GET['num'])) {
            $num = (int) $_GET['num'];
            $praticien = getPraticienByNum($num);
            if ($praticien) {
                // Vérifier que le praticien appartient à la région de l'utilisateur
                if ($regionUtilisateur && !isPraticienDansRegion($num, $regionUtilisateur)) {
                    $erreurs[] = "Vous ne pouvez modifier que les praticiens de votre région.";
                    $praticien = null;
                } else {
                    $mode = 'modification';
                    $specialitesPraticien = getSpecialitesPraticien($num);
                }
            } else {
                $erreurs[] = "Le praticien sélectionné n'existe pas.";
            }
        } else {
            $erreurs[] = "Aucun praticien spécifié.";
        }
        include("vues/v_gererPraticien.php");
        break;

    // Le délégué demande à créer un nouveau praticien 
    case 'nouveau':
        // Vérification des droits d'accès (Délégué ou Responsable)
        if ($_SESSION['habilitation'] != 2 && $_SESSION['habilitation'] != 3) {
            header('Location: index.php?uc=accueil');
            exit;
        }
        $mode = 'creation';
        // $praticien reste null → formulaire vierge
        include("vues/v_gererPraticien.php");
        break;

    // Le délégué saisit ou modifie les informations puis clique sur "Valider"
    case 'enregistrer':
        // Vérification des droits d'accès (Délégué ou Responsable)
        if ($_SESSION['habilitation'] != 2 && $_SESSION['habilitation'] != 3) {
            header('Location: index.php?uc=accueil');
            exit;
        }

        // Bouton "Annuler" → retour à la sélection
        if (isset($_POST['btn']) && $_POST['btn'] === 'annuler') {
            $messageSucces = "La saisie a été annulée.";
            include("vues/v_gererPraticien.php");
            break;
        }

        $mode = isset($_POST['mode']) ? $_POST['mode'] : 'creation';

        // 1. Récupération des champs du formulaire
        $num = isset($_POST['PRA_NUM']) ? (int) $_POST['PRA_NUM'] : 0;
        $prenom = trim(strip_tags($_POST['PRA_PRENOM'] ?? ''));
        $nom = trim(strip_tags($_POST['PRA_NOM'] ?? ''));
        $adresse = trim(strip_tags($_POST['PRA_ADRESSE'] ?? ''));
        $cp = trim(strip_tags($_POST['PRA_CP'] ?? ''));
        $ville = trim(strip_tags($_POST['PRA_VILLE'] ?? ''));
        $coef = trim($_POST['PRA_COEFNOTORIETE'] ?? '');
        $coefConfiance = trim($_POST['PRA_COEFCONFIANCE'] ?? '');
        $type = trim($_POST['TYP_CODE'] ?? '');
        $specialitesSelectionnees = $_POST['specialites'] ?? [];

        // 2. Validation des champs obligatoires
        if ($mode === 'modification' && $num <= 0) {
            $erreurs[] = "Le numéro du praticien est obligatoire.";
        }
        if ($nom === '')
            $erreurs[] = "Le nom est obligatoire.";
        if ($prenom === '')
            $erreurs[] = "Le prénom est obligatoire.";
        if ($cp === '')
            $erreurs[] = "Le code postal est obligatoire.";
        if ($ville === '')
            $erreurs[] = "La ville est obligatoire.";
        if ($type === '')
            $erreurs[] = "Le type est obligatoire.";

        // Validation du format code postal
        if ($cp !== '' && !preg_match('/^\d{5}$/', $cp)) {
            $erreurs[] = "Le code postal doit contenir 5 chiffres.";
        }

        // Validation coefficient notoriété
        if ($coef !== '' && (!is_numeric($coef) || $coef < 0)) {
            $erreurs[] = "Le coefficient de notoriété doit être un nombre positif.";
        }

        // Vérification du type
        $typeValide = false;
        foreach ($listeTypes as $t) {
            if ($t['TYP_CODE'] == $type) {
                $typeValide = true;
                break;
            }
        }
        if ($type !== '' && !$typeValide) {
            $erreurs[] = "Le type sélectionné est invalide.";
        }

        // 3. Vérification des droits de modification (région/secteur)
        if ($mode === 'modification' && empty($erreurs)) {
            $habilitation = $_SESSION['habilitation'];

            // Vérifier appartenance région (Délégué)
            if ($habilitation == 2 && $regionUtilisateur) {
                if (!isPraticienDansRegion($num, $regionUtilisateur)) {
                    $erreurs[] = "Vous ne pouvez modifier que les praticiens de votre région.";
                }
            }

            // Vérifier appartenance secteur (Responsable)
            if ($habilitation == 3 && $secteurUtilisateur) {
                if (!isPraticienDansSecteur($num, $secteurUtilisateur)) {
                    $erreurs[] = "Vous ne pouvez modifier que les praticiens de votre secteur.";
                }
            }
        }

        // Reconstruire praticien pour réaffichage en cas d'erreur
        $praticien = [
            'PRA_NUM' => $num,
            'PRA_PRENOM' => $prenom,
            'PRA_NOM' => $nom,
            'PRA_ADRESSE' => $adresse,
            'PRA_CP' => $cp,
            'PRA_VILLE' => $ville,
            'PRA_COEFNOTORIETE' => $coef,
            'PRA_COEFCONFIANCE' => $coefConfiance,
            'TYP_CODE' => $type
        ];

        // 4. Si erreurs, réafficher le formulaire
        if (!empty($erreurs)) {
            include("vues/v_gererPraticien.php");
            break;
        }

        // 5. Enregistrement (pas d'erreur)
        if ($mode === 'creation') {
            $num = ajouterPraticien($prenom, $nom, $adresse, $cp, $ville, $coef, $type, $coefConfiance);
            $messageSucces = "Praticien créé avec succès (n°$num).";
            $mode = 'modification';
        } else {
            modifierPraticien($num, $prenom, $nom, $adresse, $cp, $ville, $coef, $type, $coefConfiance);
            $messageSucces = "Praticien modifié avec succès.";
        }

        // 6. Gestion des spécialités
        supprimerToutesSpecialitesPraticien($num);
        if (!empty($specialitesSelectionnees)) {
            foreach ($specialitesSelectionnees as $speCode) {
                ajouterSpecialitePraticien($num, $speCode);
            }
            $messageSucces .= " " . count($specialitesSelectionnees) . " spécialité(s) associée(s).";
        }

        // 7. Recharger les données
        $listePraticiens = getAllPraticiens($regionUtilisateur, $tri);
        $praticien = getPraticienByNum($num);
        $specialitesPraticien = getSpecialitesPraticien($num);

        include("vues/v_gererPraticien.php");
        break;

    default:
        header('Location: index.php?uc=praticiens&action=selection');
        break;
}
