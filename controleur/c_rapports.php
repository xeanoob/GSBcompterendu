<?php

require_once 'modele/rapport.modele.inc.php';

// Vérifier que l'utilisateur est connecté
if (empty($_SESSION['login'])) {
    header('Location: index.php?uc=connexion');
    exit;
}

// Récupérer le matricule du visiteur connecté
$matriculeVisiteur = $_SESSION['matricule'];

if (!isset($_REQUEST['action']) || empty($_REQUEST['action'])) {
    $action = "nouveau";
} else {
    $action = $_REQUEST['action'];
}

// Variables communes
$erreurs = [];
$messageSucces = "";

switch ($action) {


    // Afficher le formulaire de création d'un nouveau rapport
    case 'nouveau':
        // Vérification des droits d'accès (Visiteur ou Délégué uniquement)
        if ($_SESSION['habilitation'] != 1 && $_SESSION['habilitation'] != 2) {
            header('Location: index.php?uc=rapports&action=nouveau');
            exit;
        }

        // Vérifier s'il existe des rapports en cours de saisie
        $rapportsEnCours = getRapportsEnCours($matriculeVisiteur);

        if (!empty($rapportsEnCours)) {
            include("vues/v_rapportsEnCours.php");
        } else {
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
        }
        break;

    // Créer un nouveau rapport (même s'il y a des rapports en cours)
    case 'creerNouveau':
        // Vérification des droits d'accès (Visiteur ou Délégué uniquement)
        if ($_SESSION['habilitation'] != 1 && $_SESSION['habilitation'] != 2) {
            header('Location: index.php?uc=rapports&action=nouveau');
            exit;
        }

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

    // Modifier un rapport existant en cours de saisie
    case 'modifier':
        // Vérification des droits d'accès (Visiteur ou Délégué uniquement)
        if ($_SESSION['habilitation'] != 1 && $_SESSION['habilitation'] != 2) {
            header('Location: index.php?uc=rapports&action=nouveau');
            exit;
        }

        if (!empty($_GET['num'])) {
            $numRapport = (int) $_GET['num'];
            $rapport = getRapportVisite($matriculeVisiteur, $numRapport);

            if ($rapport && $rapport['ETAT_CODE'] == 1) {
                // Le rapport existe et est en cours de saisie
                $listePraticiens = getTousPraticiens();
                $listeMotifs = getTousMotifsVisite();
                $listeMedicaments = getTousMedicaments();
                $echantillons = getEchantillonsOfferts($matriculeVisiteur, $numRapport);

                include("vues/v_saisirRapport.php");
            } else {
                // Le rapport n'existe pas ou est déjà validé
                $erreurs[] = "Ce rapport ne peut pas être modifié.";
                header('Location: index.php?uc=rapports&action=nouveau');
                exit;
            }
        } else {
            header('Location: index.php?uc=rapports&action=nouveau');
            exit;
        }
        break;

    // Enregistrer le rapport de visite
    case 'enregistrer':
        // Vérification des droits d'accès (Visiteur ou Délégué uniquement)
        if ($_SESSION['habilitation'] != 1 && $_SESSION['habilitation'] != 2) {
            header('Location: index.php?uc=rapports&action=nouveau');
            exit;
        }

        // Récupération des données du formulaire
        $numRapport = isset($_POST['RAP_NUM']) ? (int) $_POST['RAP_NUM'] : 0;
        $dateVisite = trim($_POST['RAP_DATEVISITE'] ?? '');
        $bilan = trim($_POST['RAP_BILAN'] ?? '');
        $motifCode = isset($_POST['MOT_CODE']) ? (int) $_POST['MOT_CODE'] : 0;
        $rapMotif = trim($_POST['RAP_MOTIF'] ?? ''); // Motif personnalisé
        $praticienNum = isset($_POST['PRA_NUM']) ? (int) $_POST['PRA_NUM'] : 0;
        $praticienRemplacant = !empty($_POST['PRA_NUM_REMPLACANT']) ? (int) $_POST['PRA_NUM_REMPLACANT'] : null;
        $med1 = !empty($_POST['MED_DEPOTLEGAL1']) ? $_POST['MED_DEPOTLEGAL1'] : null;
        $med2 = !empty($_POST['MED_DEPOTLEGAL2']) ? $_POST['MED_DEPOTLEGAL2'] : null;
        
        // Récupérer le coefficient de confiance du praticien
        $praCoefConfiance = !empty($_POST['PRA_COEFCONFIANCE']) ? (float) $_POST['PRA_COEFCONFIANCE'] : null;

        // Récupérer l'état en fonction de la case "Saisie définitive"
        $saisieDefinitive = isset($_POST['saisie_definitive']) && $_POST['saisie_definitive'] == '1';
        $etatCode = $saisieDefinitive ? 2 : 1; // 2 = Validé, 1 = En cours

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

        // Valider le motif personnalisé si "Autre" est sélectionné
        if ($motifCode == 5) {
            if (empty($rapMotif)) {
                $erreurs[] = "Veuillez préciser le motif de la visite.";
            } elseif (strlen($rapMotif) > 50) {
                $erreurs[] = "Le motif personnalisé ne peut pas dépasser 50 caractères.";
            }
        } else {
            // Si "Autre" n'est pas sélectionné, vider le champ RAP_MOTIF
            $rapMotif = null;
        }

        if ($praticienNum <= 0) {
            $erreurs[] = "Le praticien est obligatoire.";
        }

        // Validation du coefficient de confiance
        if ($praCoefConfiance !== null) {
            if ($praCoefConfiance < 0) {
                $erreurs[] = "Le coefficient de confiance ne peut pas être négatif.";
            } elseif ($praCoefConfiance > 1000) {
                $erreurs[] = "Le coefficient de confiance ne peut pas dépasser 1000.";
            }
        }

        // Validation des échantillons
        $echantillonsValides = [];
        $medicamentsEchantillons = [];

        foreach ($echantillonsMedicaments as $index => $medDepot) {
            if (!empty($medDepot)) {
                $qte = isset($echantillonsQuantites[$index]) ? (int) $echantillonsQuantites[$index] : 0;

                if ($qte <= 0) {
                    $erreurs[] = "La quantité de l'échantillon doit être supérieure à 0.";
                } elseif ($qte > 1000) {
                    $erreurs[] = "La quantité de l'échantillon ne peut pas dépasser 1000.";
                } else {
                    if (in_array($medDepot, $medicamentsEchantillons)) {
                        $erreurs[] = "Le médicament $medDepot est présent plusieurs fois dans les échantillons.";
                    } else {
                        $medicamentsEchantillons[] = $medDepot;
                        $echantillonsValides[] = [
                            'medicament' => $medDepot,
                            'quantite' => $qte
                        ];
                    }
                }
            }
        }

        if (count($echantillonsValides) > 10) {
            $erreurs[] = "Vous ne pouvez pas ajouter plus de 10 échantillons.";
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
                'RAP_MOTIF' => $rapMotif,
                'PRA_NUM' => $praticienNum,
                'PRA_NUM_REMPLACANT' => $praticienRemplacant,
                'MED_DEPOTLEGAL1' => $med1,
                'MED_DEPOTLEGAL2' => $med2,
                'PRA_COEFCONFIANCE' => $praCoefConfiance,
                'saisie_definitive' => $saisieDefinitive
            ];

            $echantillons = $echantillonsValides;

            include("vues/v_saisirRapport.php");
            break;
        }

        // Vérifier si le rapport existe déjà (modification)
        $rapportExistant = getRapportVisite($matriculeVisiteur, $numRapport);

        if ($rapportExistant) {
            // Mise à jour du rapport existant
            $success = mettreAJourRapport(
                $matriculeVisiteur,
                $numRapport,
                $dateVisite,
                $bilan,
                $motifCode,
                $praticienNum,
                $etatCode,
                $med1,
                $med2,
                $rapMotif
            );

            if ($success) {
                // Supprimer les anciens échantillons
                supprimerEchantillonsRapport($matriculeVisiteur, $numRapport);

                // Ajouter les nouveaux échantillons
                foreach ($echantillonsValides as $ech) {
                    ajouterEchantillonOffert(
                        $matriculeVisiteur,
                        $numRapport,
                        $ech['medicament'],
                        $ech['quantite']
                    );
                }

                $messageSucces = "Le rapport de visite n°$numRapport a été modifié avec succès.";
                if ($saisieDefinitive) {
                    $messageSucces .= " Il est maintenant validé.";
                }
            }
        } else {
            // Création d'un nouveau rapport
            $success = creerRapportVisite(
                $matriculeVisiteur,
                $numRapport,
                $dateVisite,
                $bilan,
                $motifCode,
                $praticienNum,
                $etatCode,
                $med1,
                $med2,
                $rapMotif
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
                if ($saisieDefinitive) {
                    $messageSucces .= " Il est validé.";
                } else {
                    $messageSucces .= " Il est en cours de saisie.";
                }
            }
        }

        if ($success) {

            // Mettre à jour le coefficient de confiance du praticien si renseigné
            if ($praCoefConfiance !== null) {
                require_once 'modele/praticien.modele.inc.php';
                $coefUpdateSuccess = mettreAJourCoefConfiance($praticienNum, $praCoefConfiance);
                if (!$coefUpdateSuccess) {
                    // Log l'erreur mais ne bloque pas le processus
                    error_log("Erreur lors de la mise à jour du coefficient de confiance pour le praticien $praticienNum");
                }
            }

            // Rediriger vers la liste des rapports avec le message
            $_SESSION['message_succes_rapport'] = $messageSucces;
            header('Location: index.php?uc=rapports&action=nouveau');
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
                'RAP_MOTIF' => $rapMotif,
                'PRA_NUM' => $praticienNum,
                'PRA_NUM_REMPLACANT' => $praticienRemplacant,
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
                header('Location: index.php?uc=rapports&action=nouveau');
                exit;
            }
        } else {
            header('Location: index.php?uc=rapports&action=nouveau');
            exit;
        }
        break;

    // Afficher le formulaire de recherche/consultation des rapports
    case 'consulter':
        // Vérification des droits d'accès (Tout le monde : 1, 2, 3)
        if ($_SESSION['habilitation'] != 1 && $_SESSION['habilitation'] != 2 && $_SESSION['habilitation'] != 3) {
            header('Location: index.php?uc=accueil');
            exit;
        }
        // Récupérer la liste des praticiens pour le filtre
        $listePraticiens = getTousPraticiens();
        include("vues/v_consulterRapports.php");
        break;

    // Effectuer la recherche et afficher les résultats
    case 'rechercher':
        // Vérification des droits d'accès (Tout le monde : 1, 2, 3)
        if ($_SESSION['habilitation'] != 1 && $_SESSION['habilitation'] != 2 && $_SESSION['habilitation'] != 3) {
            header('Location: index.php?uc=accueil');
            exit;
        }
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
            $retour = $_GET['retour'] ?? null;

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
            $retour = $_GET['retour'] ?? null;

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

    // Afficher les nouveaux rapports de la région/secteur (pour délégués et responsables)
    case 'nouveaux':
        // Vérification des droits d'accès (Délégué ou Responsable uniquement)
        if ($_SESSION['habilitation'] != 2 && $_SESSION['habilitation'] != 3) {
            header('Location: index.php?uc=accueil');
            exit;
        }

        $titrePage = "";
        $rapports = [];

        if ($_SESSION['habilitation'] == 2) {
            // Délégué régional : rapports de sa région
            $region = $_SESSION['region'];
            $titrePage = "Nouveaux rapports de la région " . $region;
            $rapports = getNouveauxRapportsRegion($region);
        } elseif ($_SESSION['habilitation'] == 3) {
            // Responsable secteur : rapports de son secteur
            $secteur = $_SESSION['secteur'];
            $titrePage = "Nouveaux rapports du secteur " . $secteur;
            $rapports = getNouveauxRapportsSecteur($secteur);
        }

        include("vues/v_nouveauxRapports.php");
        break;

    // Consulter le détail d'un nouveau rapport (sans le marquer comme consulté tout de suite)
    case 'consulter_detail':
        // Vérification des droits d'accès (Délégué ou Responsable uniquement)
        if ($_SESSION['habilitation'] != 2 && $_SESSION['habilitation'] != 3) {
            header('Location: index.php?uc=accueil');
            exit;
        }

        if (!empty($_GET['mat']) && !empty($_GET['num'])) {
            $matricule = $_GET['mat'];
            $numRapport = (int) $_GET['num'];

            // Note: On ne marque PAS comme consulté ici, car la demande spécifie
            // que la requête doit être faite au clic sur le bouton retour.

            $rapport = getRapportVisiteComplet($matricule, $numRapport);
            $echantillons = getEchantillonsOfferts($matricule, $numRapport);

            if ($rapport) {
                // Variable pour le bouton retour spécifique
                $retourNouveaux = true;
                include("vues/v_detailRapport.php");
            } else {
                $erreurs[] = "Le rapport demandé n'existe pas.";
                header('Location: index.php?uc=rapports&action=nouveaux');
                exit;
            }
        } else {
            header('Location: index.php?uc=rapports&action=nouveaux');
            exit;
        }
        break;

    // Marquer le rapport comme consulté et retourner à la liste
    case 'marquer_lu_et_retour':
        // Vérification des droits d'accès (Délégué ou Responsable uniquement)
        if ($_SESSION['habilitation'] != 2 && $_SESSION['habilitation'] != 3) {
            header('Location: index.php?uc=accueil');
            exit;
        }

        if (!empty($_GET['mat']) && !empty($_GET['num'])) {
            $matricule = $_GET['mat'];
            $numRapport = (int) $_GET['num'];

            // Marquer comme consulté (ETAT_CODE = 3)
            marquerRapportConsulte($matricule, $numRapport);

            // Retour à la liste des nouveaux rapports
            header('Location: index.php?uc=rapports&action=nouveaux');
            exit;
        } else {
            header('Location: index.php?uc=rapports&action=nouveaux');
            exit;
        }
        break;

    // Afficher l'historique complet des rapports de la région (pour délégués uniquement)
    case 'historiqueRegion':
        // Vérification des droits d'accès (Délégué régional uniquement)
        if ($_SESSION['habilitation'] != 2) {
            header('Location: index.php?uc=accueil');
            exit;
        }

        $region = $_SESSION['region'];
        $titrePage = "Historique complet des rapports de la région " . $region;
        
        // Récupérer la liste des visiteurs de la région pour le filtre
        $listeVisiteurs = getVisiteursRegion($region);

        // Initialiser les variables
        $dateDebut = '';
        $dateFin = '';
        $matriculeVisiteurFiltre = null;
        $rapports = [];

        // Si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dateDebut = trim($_POST['date_debut'] ?? '');
            $dateFin = trim($_POST['date_fin'] ?? '');
            $matriculeVisiteurFiltre = !empty($_POST['visiteur_matricule']) ? $_POST['visiteur_matricule'] : null;

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

            // Si pas d'erreurs, effectuer la recherche
            if (empty($erreurs)) {
                $rapports = consulterHistoriqueRapportsRegion($region, $dateDebut, $dateFin, $matriculeVisiteurFiltre);
            }
        }

        include("vues/v_historiqueRapportsRegion.php");
        break;
}
