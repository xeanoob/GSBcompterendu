<?php

require_once 'modele/praticien.modele.inc.php';

if (!isset($_REQUEST['action']) || empty($_REQUEST['action'])) {
    $action = "selection";
} else {
    $action = $_REQUEST['action'];
}

// variables communes
$listePraticiens = getAllPraticiens();
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
                $mode = 'modification';
                $specialitesPraticien = getSpecialitesPraticien($num);
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

        // Bouton "Annuler" → exception 4-b
        if (isset($_POST['btn']) && $_POST['btn'] === 'annuler') {
            $messageSucces = "La saisie a été annulée.";
            $mode = 'aucun';
            $praticien = null;
            include("vues/v_gererPraticien.php");
            break;
        }

        $mode = isset($_POST['mode']) ? $_POST['mode'] : 'creation';

        // Récupération des champs du formulaire avec sanitization
        $num     = isset($_POST['PRA_NUM']) ? (int) $_POST['PRA_NUM'] : 0;
        $prenom  = trim(strip_tags($_POST['PRA_PRENOM'] ?? ''));
        $nom     = trim(strip_tags($_POST['PRA_NOM'] ?? ''));
        $adresse = trim(strip_tags($_POST['PRA_ADRESSE'] ?? ''));
        $cp      = trim(strip_tags($_POST['PRA_CP'] ?? ''));
        $ville   = trim(strip_tags($_POST['PRA_VILLE'] ?? ''));
        $coef    = trim($_POST['PRA_COEFNOTORIETE'] ?? '');
        $type    = trim($_POST['TYP_CODE'] ?? '');

        // Récupération des spécialités sélectionnées (facultatives)
        $specialitesSelectionnees = $_POST['specialites'] ?? [];

        // Contrôle des champs obligatoires (exception 5-a)
        // En mode modification, le numéro doit être fourni. En mode création, il sera auto-généré.
        if ($mode === 'modification' && $num <= 0) {
            $erreurs[] = "Le numéro du praticien est obligatoire et doit être positif.";
        }
        if ($nom === '')    $erreurs[] = "Le nom du praticien est obligatoire.";
        if ($prenom === '') $erreurs[] = "Le prénom du praticien est obligatoire.";
        if ($cp === '')     $erreurs[] = "Le code postal est obligatoire.";
        if ($ville === '')  $erreurs[] = "La ville est obligatoire.";
        if ($type === '')   $erreurs[] = "Le type de praticien est obligatoire.";
        
        // Validations supplémentaires
        if (!preg_match('/^\d{5}$/', $cp)) {
            $erreurs[] = "Le code postal doit contenir exactement 5 chiffres.";
        }
        
        if ($coef !== '' && !is_numeric($coef)) {
            $erreurs[] = "Le coefficient de notoriété doit être une valeur numérique.";
        } elseif ($coef < 0) {
            $erreurs[] = "Le coefficient de notoriété ne peut pas être négatif.";
        }
        
        // Vérification que le type existe
        $typeExiste = false;
        foreach ($listeTypes as $t) {
            if ($t['TYP_CODE'] == $type) {
                $typeExiste = true;
                break;
            }
        }
        if (!$typeExiste && $type !== '') {
            $erreurs[] = "Le type de praticien sélectionné est invalide.";
        }

        // On reconstruit un tableau praticien pour réafficher le formulaire en cas d'erreur
        $praticien = [
            'PRA_NUM'            => $num,
            'PRA_PRENOM'         => $prenom,
            'PRA_NOM'            => $nom,
            'PRA_ADRESSE'        => $adresse,
            'PRA_CP'             => $cp,
            'PRA_VILLE'          => $ville,
            'PRA_COEFNOTORIETE'  => $coef,
            'TYP_CODE'           => $type
        ];

        if (!empty($erreurs)) {
            // On revient à l’étape de saisie avec les messages d’erreurs
            include("vues/v_gererPraticien.php");
            break;
        }

        // Pas d'erreur → enregistrement
        if ($mode === 'creation') {
            // Création : le numéro sera généré automatiquement par AUTO_INCREMENT
            $num = ajouterPraticien($prenom, $nom, $adresse, $cp, $ville, $coef, $type);
            $messageSucces = "Le praticien a été créé avec succès (n°$num).";
            $mode = 'modification';
        } else {
            modifierPraticien($num, $prenom, $nom, $adresse, $cp, $ville, $coef, $type);
            $messageSucces = "Les informations du praticien ont été mises à jour.";
            $mode = 'modification';
        }

        // Gestion des spécialités (pour création ET modification)
        // On supprime d'abord toutes les spécialités existantes
        supprimerToutesSpecialitesPraticien($num);

        // On ajoute les spécialités sélectionnées
        if (!empty($specialitesSelectionnees) && is_array($specialitesSelectionnees)) {
            foreach ($specialitesSelectionnees as $speCode) {
                ajouterSpecialitePraticien($num, $speCode);
            }

            if (count($specialitesSelectionnees) > 0) {
                $messageSucces .= " " . count($specialitesSelectionnees) . " spécialité(s) associée(s).";
            }
        }

        // On recharge la liste (au cas où)
        $listePraticiens = getAllPraticiens();
        $listeTypes = getAllTypesPraticien();
        $listeSpecialites = getAllSpecialites();
        // On récupère les infos à jour depuis la base
        $praticien = getPraticienByNum($num);
        $specialitesPraticien = getSpecialitesPraticien($num);

        include("vues/v_gererPraticien.php");
        break;

    default:
        header('Location: index.php?uc=praticiens&action=selection');
        break;
}
