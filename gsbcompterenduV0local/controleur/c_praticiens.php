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
$praticien = null;
$mode = 'aucun';          // 'aucun' | 'creation' | 'modification'
$erreurs = [];
$messageSucces = "";

switch ($action) {

    // 1. Le délégué demande à gérer les praticiens (scénario nominal)
    case 'selection':
        // Juste la liste déroulante + bouton "Créer"
        include("vues/v_gererPraticien.php");
        break;

    // 2. L’utilisateur choisit un praticien dans la liste
    case 'afficher':
        if (!empty($_POST['praticien'])) {
            $num = (int) $_POST['praticien'];
            $praticien = getPraticienByNum($num);
            if ($praticien) {
                $mode = 'modification';
            } else {
                $erreurs[] = "Le praticien sélectionné n'existe pas.";
            }
        } else {
            $erreurs[] = "Vous devez choisir un praticien dans la liste.";
        }
        include("vues/v_gererPraticien.php");
        break;

    // 2-a. Le délégué demande à créer un nouveau praticien (exception)
    case 'nouveau':
        $mode = 'creation';
        // $praticien reste null → formulaire vierge
        include("vues/v_gererPraticien.php");
        break;

    // 4. Le délégué saisit ou modifie les informations puis clique sur "Valider"
    case 'enregistrer':

        // Bouton "Annuler" → exception 4-b
        if (isset($_POST['btn']) && $_POST['btn'] === 'annuler') {
            $messageSucces = "La saisie a été annulée.";
            $mode = 'aucun';
            $praticien = null;
            include("vues/v_gererPraticien.php");
            break;
        }

        $mode = isset($_POST['mode']) ? $_POST['mode'] : 'creation';

        // Récupération des champs du formulaire
        $num     = isset($_POST['PRA_NUM']) ? (int) $_POST['PRA_NUM'] : 0;
        $prenom  = trim($_POST['PRA_PRENOM'] ?? '');
        $nom     = trim($_POST['PRA_NOM'] ?? '');
        $adresse = trim($_POST['PRA_ADRESSE'] ?? '');
        $cp      = trim($_POST['PRA_CP'] ?? '');
        $ville   = trim($_POST['PRA_VILLE'] ?? '');
        $coef    = trim($_POST['PRA_COEFNOTORIETE'] ?? '');
        $type    = trim($_POST['TYP_CODE'] ?? '');

        // Contrôle des champs obligatoires (exception 5-a)
        if ($num <= 0)      $erreurs[] = "Le numéro du praticien est obligatoire.";
        if ($nom === '')    $erreurs[] = "Le nom du praticien est obligatoire.";
        if ($prenom === '') $erreurs[] = "Le prénom du praticien est obligatoire.";
        if ($cp === '')     $erreurs[] = "Le code postal est obligatoire.";
        if ($ville === '')  $erreurs[] = "La ville est obligatoire.";
        if ($type === '')   $erreurs[] = "Le type de praticien est obligatoire.";

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

        // Pas d’erreur → enregistrement
        if ($mode === 'creation') {
            // On vérifie que le numéro n'existe pas déjà
            $existant = getPraticienByNum($num);
            if ($existant) {
                $erreurs[] = "Un praticien avec ce numéro existe déjà.";
                include("vues/v_gererPraticien.php");
                break;
            }
            ajouterPraticien($num, $prenom, $nom, $adresse, $cp, $ville, $coef, $type);
            $messageSucces = "Le praticien a été créé avec succès.";
            $mode = 'modification';
        } else {
            modifierPraticien($num, $prenom, $nom, $adresse, $cp, $ville, $coef, $type);
            $messageSucces = "Les informations du praticien ont été mises à jour.";
            $mode = 'modification';
        }

        // On recharge la liste (au cas où)
        $listePraticiens = getAllPraticiens();
        $listeTypes = getAllTypesPraticien();
        // On récupère les infos à jour depuis la base
        $praticien = getPraticienByNum($num);

        include("vues/v_gererPraticien.php");
        break;

    default:
        header('Location: index.php?uc=praticiens&action=selection');
        break;
}
