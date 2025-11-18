<?php

require_once 'modele/rapport.modele.inc.php';

// Vérifier que l'utilisateur est connecté
if (empty($_SESSION['login'])) {
    header('Location: index.php?uc=connexion');
    exit;
}

// Récupérer le matricule du visiteur connecté
$matriculeVisiteur = $_SESSION['login'];

if (!isset($_REQUEST['action']) || empty($_REQUEST['action'])) {
    $action = "liste";
} else {
    $action = $_REQUEST['action'];
}

// Variables communes
$erreurs = [];
$messageSucces = "";

switch ($action) {

    // Afficher la liste des rapports du visiteur
    case 'liste':
        $rapports = getRapportsParVisiteur($matriculeVisiteur);
        include("vues/v_listeRapports.php");
        break;

    // Afficher le formulaire de création d'un nouveau rapport
    case 'nouveau':
        $prochainNumero = getProchainNumeroRapport($matriculeVisiteur);
        $listePraticiens = getTousPraticiens();
        $listeMotifs = getTousMotifsVisite();
        $listeMedicaments = getTousMedicaments();

        // Initialiser un rapport vide
        $rapport = [
            'RAP_NUM' => $prochainNumero,
            'RAP_DATEVISITE' => date('Y-m-d'),
            'RAP_BILAN' => '',
            'MOT_CODE' => '',
            'PRA_NUM' => '',
            'MED_DEPOTLEGAL1' => '',
            'MED_DEPOTLEGAL2' => ''
        ];

        $echantillons = []; // Tableau vide pour les échantillons

        include("vues/v_saisirRapport.php");
        break;

    // Enregistrer le rapport de visite
    case 'enregistrer':

        // Récupération des données du formulaire
        $numRapport = isset($_POST['RAP_NUM']) ? (int) $_POST['RAP_NUM'] : 0;
        $dateVisite = trim($_POST['RAP_DATEVISITE'] ?? '');
        $bilan = trim($_POST['RAP_BILAN'] ?? '');
        $motifCode = isset($_POST['MOT_CODE']) ? (int) $_POST['MOT_CODE'] : 0;
        $praticienNum = isset($_POST['PRA_NUM']) ? (int) $_POST['PRA_NUM'] : 0;
        $med1 = !empty($_POST['MED_DEPOTLEGAL1']) ? $_POST['MED_DEPOTLEGAL1'] : null;
        $med2 = !empty($_POST['MED_DEPOTLEGAL2']) ? $_POST['MED_DEPOTLEGAL2'] : null;

        // Récupération des échantillons offerts
        $echantillonsMedicaments = $_POST['echantillon_medicament'] ?? [];
        $echantillonsQuantites = $_POST['echantillon_quantite'] ?? [];

        // Validation des champs obligatoires
        if ($numRapport <= 0) {
            $erreurs[] = "Le numéro du rapport est invalide.";
        }

        if (empty($dateVisite)) {
            $erreurs[] = "La date de visite est obligatoire.";
        } else {
            // Vérifier que la date n'est pas dans le futur
            $dateActuelle = new DateTime();
            $dateVisiteObj = DateTime::createFromFormat('Y-m-d', $dateVisite);

            if (!$dateVisiteObj) {
                $erreurs[] = "Le format de la date est incorrect.";
            } elseif ($dateVisiteObj > $dateActuelle) {
                $erreurs[] = "La date de visite ne peut pas être dans le futur.";
            }
        }

        if (empty($bilan)) {
            $erreurs[] = "Le bilan de la visite est obligatoire.";
        } elseif (strlen($bilan) > 255) {
            $erreurs[] = "Le bilan ne peut pas dépasser 255 caractères.";
        }

        if ($motifCode <= 0) {
            $erreurs[] = "Le motif de visite est obligatoire.";
        }

        if ($praticienNum <= 0) {
            $erreurs[] = "Le praticien est obligatoire.";
        }

        // Validation des échantillons
        $echantillonsValides = [];
        foreach ($echantillonsMedicaments as $index => $medDepot) {
            if (!empty($medDepot)) {
                $qte = isset($echantillonsQuantites[$index]) ? (int) $echantillonsQuantites[$index] : 0;

                if ($qte <= 0) {
                    $erreurs[] = "La quantité de l'échantillon doit être supérieure à 0.";
                } elseif ($qte > 1000) {
                    $erreurs[] = "La quantité de l'échantillon ne peut pas dépasser 1000.";
                } else {
                    $echantillonsValides[] = [
                        'medicament' => $medDepot,
                        'quantite' => $qte
                    ];
                }
            }
        }

        // Si erreurs, réafficher le formulaire avec les données saisies
        if (!empty($erreurs)) {
            $listePraticiens = getTousPraticiens();
            $listeMotifs = getTousMotifsVisite();
            $listeMedicaments = getTousMedicaments();

            $rapport = [
                'RAP_NUM' => $numRapport,
                'RAP_DATEVISITE' => $dateVisite,
                'RAP_BILAN' => $bilan,
                'MOT_CODE' => $motifCode,
                'PRA_NUM' => $praticienNum,
                'MED_DEPOTLEGAL1' => $med1,
                'MED_DEPOTLEGAL2' => $med2
            ];

            $echantillons = $echantillonsValides;

            include("vues/v_saisirRapport.php");
            break;
        }

        // Pas d'erreur : enregistrement
        $success = creerRapportVisite(
            $matriculeVisiteur,
            $numRapport,
            $dateVisite,
            $bilan,
            $motifCode,
            $praticienNum,
            1, // État : En cours de saisie
            $med1,
            $med2
        );

        if ($success) {
            // Enregistrer les échantillons offerts
            foreach ($echantillonsValides as $ech) {
                ajouterEchantillonOffert(
                    $matriculeVisiteur,
                    $numRapport,
                    $ech['medicament'],
                    $ech['quantite']
                );
            }

            $messageSucces = "Le rapport de visite n°$numRapport a été créé avec succès.";

            // Rediriger vers la liste des rapports avec le message
            $_SESSION['message_succes_rapport'] = $messageSucces;
            header('Location: index.php?uc=rapports&action=liste');
            exit;
        } else {
            $erreurs[] = "Une erreur s'est produite lors de l'enregistrement du rapport.";

            $listePraticiens = getTousPraticiens();
            $listeMotifs = getTousMotifsVisite();
            $listeMedicaments = getTousMedicaments();

            $rapport = [
                'RAP_NUM' => $numRapport,
                'RAP_DATEVISITE' => $dateVisite,
                'RAP_BILAN' => $bilan,
                'MOT_CODE' => $motifCode,
                'PRA_NUM' => $praticienNum,
                'MED_DEPOTLEGAL1' => $med1,
                'MED_DEPOTLEGAL2' => $med2
            ];

            $echantillons = $echantillonsValides;

            include("vues/v_saisirRapport.php");
        }
        break;

    // Afficher le détail d'un rapport
    case 'detail':
        if (!empty($_GET['num'])) {
            $numRapport = (int) $_GET['num'];
            $rapport = getRapportVisite($matriculeVisiteur, $numRapport);
            $echantillons = getEchantillonsOfferts($matriculeVisiteur, $numRapport);

            if ($rapport) {
                include("vues/v_detailRapport.php");
            } else {
                $erreurs[] = "Le rapport demandé n'existe pas.";
                $rapports = getRapportsParVisiteur($matriculeVisiteur);
                include("vues/v_listeRapports.php");
            }
        } else {
            header('Location: index.php?uc=rapports&action=liste');
            exit;
        }
        break;

    // Afficher le formulaire de recherche/consultation des rapports
    case 'consulter':
        // Récupérer la liste des praticiens pour le filtre
        $listePraticiens = getTousPraticiens();
        include("vues/v_consulterRapports.php");
        break;

    // Effectuer la recherche et afficher les résultats
    case 'rechercher':
        // Récupération des critères de recherche
        $dateDebut = trim($_POST['date_debut'] ?? '');
        $dateFin = trim($_POST['date_fin'] ?? '');
        $praticienNum = !empty($_POST['praticien_num']) ? (int) $_POST['praticien_num'] : null;

        // Validation des critères
        if (empty($dateDebut)) {
            $erreurs[] = "La date de début est obligatoire.";
        }

        if (empty($dateFin)) {
            $erreurs[] = "La date de fin est obligatoire.";
        }

        // Vérifier que date début <= date fin
        if (!empty($dateDebut) && !empty($dateFin)) {
            $dateDebutObj = DateTime::createFromFormat('Y-m-d', $dateDebut);
            $dateFinObj = DateTime::createFromFormat('Y-m-d', $dateFin);

            if (!$dateDebutObj) {
                $erreurs[] = "Le format de la date de début est incorrect.";
            }

            if (!$dateFinObj) {
                $erreurs[] = "Le format de la date de fin est incorrect.";
            }

            if ($dateDebutObj && $dateFinObj && $dateDebutObj > $dateFinObj) {
                $erreurs[] = "La date de début doit être antérieure ou égale à la date de fin.";
            }
        }

        // Si erreurs, réafficher le formulaire
        if (!empty($erreurs)) {
            $listePraticiens = getTousPraticiens();
            include("vues/v_consulterRapports.php");
            break;
        }

        // Rechercher les rapports
        $rapports = rechercherRapports($dateDebut, $dateFin, $praticienNum);

        // Stocker les critères en session pour les boutons retour
        $_SESSION['criteres_recherche'] = [
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'praticien_num' => $praticienNum
        ];

        if (empty($rapports)) {
            $erreurs[] = "Aucun rapport de visite trouvé pour cette période.";
            $listePraticiens = getTousPraticiens();
            include("vues/v_consulterRapports.php");
        } else {
            include("vues/v_resultatConsultationRapports.php");
        }
        break;

    // Afficher le détail d'un rapport (depuis la consultation)
    case 'detailConsultation':
        if (!empty($_GET['mat']) && !empty($_GET['num'])) {
            $matricule = $_GET['mat'];
            $numRapport = (int) $_GET['num'];
            $rapport = getRapportVisiteComplet($matricule, $numRapport);
            $echantillons = getEchantillonsOfferts($matricule, $numRapport);

            if ($rapport) {
                include("vues/v_detailRapport.php");
            } else {
                $erreurs[] = "Le rapport demandé n'existe pas.";
                // Retour à la recherche
                header('Location: index.php?uc=rapports&action=consulter');
                exit;
            }
        } else {
            header('Location: index.php?uc=rapports&action=consulter');
            exit;
        }
        break;

    // Afficher le détail d'un praticien
    case 'detailPraticien':
        require_once('modele/praticien.modele.inc.php');

        if (!empty($_GET['pra'])) {
            $praticienNum = (int) $_GET['pra'];
            $praticien = getPraticienComplet($praticienNum);

            if ($praticien) {
                include("vues/v_detailPraticien.php");
            } else {
                $erreurs[] = "Le praticien demandé n'existe pas.";
                header('Location: index.php?uc=rapports&action=consulter');
                exit;
            }
        } else {
            header('Location: index.php?uc=rapports&action=consulter');
            exit;
        }
        break;

    // Afficher le détail d'un médicament
    case 'detailMedicament':
        require_once('modele/medicament.modele.inc.php');

        if (!empty($_GET['med'])) {
            $medDepot = $_GET['med'];
            $medicament = getAllInformationMedicamentDepot($medDepot);

            if ($medicament) {
                include("vues/v_detailMedicament.php");
            } else {
                $erreurs[] = "Le médicament demandé n'existe pas.";
                header('Location: index.php?uc=rapports&action=consulter');
                exit;
            }
        } else {
            header('Location: index.php?uc=rapports&action=consulter');
            exit;
        }
        break;

    default:
        header('Location: index.php?uc=rapports&action=liste');
        break;
}
