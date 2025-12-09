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
        $num = isset($_POST['PRA_NUM']) ? (int) $_POST['PRA_NUM'] : 0;
        $prenom = trim(strip_tags($_POST['PRA_PRENOM'] ?? ''));
        $nom = trim(strip_tags($_POST['PRA_NOM'] ?? ''));
        $adresse = trim(strip_tags($_POST['PRA_ADRESSE'] ?? ''));
        $cp = trim(strip_tags($_POST['PRA_CP'] ?? ''));
        $ville = trim(strip_tags($_POST['PRA_VILLE'] ?? ''));
        $coef = trim($_POST['PRA_COEFNOTORIETE'] ?? '');
        $coefConfiance = trim($_POST['PRA_COEFCONFIANCE'] ?? '');
        $type = trim($_POST['TYP_CODE'] ?? '');

        // Récupération des spécialités sélectionnées (facultatives)
        $specialitesSelectionnees = $_POST['specialites'] ?? [];

        // Contrôle des champs obligatoires (exception 5-a)
        // En mode modification, le numéro doit être fourni. En mode création, il sera auto-généré.
        if ($mode === 'modification' && $num <= 0) {
            $erreurs[] = "Le numéro du praticien est obligatoire et doit être positif.";
        }
        if ($nom === '')
            $erreurs[] = "Le nom du praticien est obligatoire.";
        if ($prenom === '')
            $erreurs[] = "Le prénom du praticien est obligatoire.";
        if ($cp === '')
            $erreurs[] = "Le code postal est obligatoire.";
        if ($ville === '')
            $erreurs[] = "La ville est obligatoire.";
        if ($type === '')
            $erreurs[] = "Le type de praticien est obligatoire.";

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

        if (!empty($erreurs)) {
            // On revient à l’étape de saisie avec les messages d’erreurs
            include("vues/v_gererPraticien.php");
            break;
        }

        // Pas d'erreur → enregistrement
        if ($mode === 'creation') {
            // Création : le numéro sera généré automatiquement par AUTO_INCREMENT
            $num = ajouterPraticien($prenom, $nom, $adresse, $cp, $ville, $coef, $type, $coefConfiance);
            $messageSucces = "Le praticien a été créé avec succès (n°$num).";
            $mode = 'modification';
        } else {
            // En mode modification, vérifier les droits selon l'habilitation
            $habilitation = $_SESSION['habilitation'];
            
            // Délégué (2) : peut modifier uniquement les praticiens de sa région
            if ($habilitation == 2 && $regionUtilisateur) {
                if (!isPraticienDansRegion($num, $regionUtilisateur)) {
                    $erreurs[] = "Vous ne pouvez modifier que les praticiens de votre région.";
                    include("vues/v_gererPraticien.php");
                    break;
                }
            }
            
            // Responsable Secteur (3) : peut modifier uniquement les praticiens de son secteur
            if ($habilitation == 3 && $secteurUtilisateur) {
                if (!isPraticienDansSecteur($num, $secteurUtilisateur)) {
                    $erreurs[] = "Vous ne pouvez modifier que les praticiens de votre secteur.";
                    include("vues/v_gererPraticien.php");
                    break;
                }
            }

            // Vérifier que le nouveau code postal (si modifié) reste dans la zone autorisée
            $praticienActuel = getPraticienByNum($num);
            if ($praticienActuel && $cp !== $praticienActuel['PRA_CP']) {
                $codeDept = (int) substr($cp, 0, 2);
                
                // Pour le délégué : vérifier la région
                if ($habilitation == 2 && $regionUtilisateur) {
                    try {
                        $pdo = connexionPDO();
                        $sqlCheck = 'SELECT REG_CODE FROM departement WHERE NoDEPT = :dept';
                        $stmtCheck = $pdo->prepare($sqlCheck);
                        $stmtCheck->bindValue(':dept', $codeDept, PDO::PARAM_INT);
                        $stmtCheck->execute();
                        $deptRegion = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                        if (!$deptRegion || $deptRegion['REG_CODE'] !== $regionUtilisateur) {
                            $erreurs[] = "Le nouveau code postal doit rester dans votre région.";
                            include("vues/v_gererPraticien.php");
                            break;
                        }
                    } catch (PDOException $e) {
                        $erreurs[] = "Erreur lors de la vérification de la région.";
                        include("vues/v_gererPraticien.php");
                        break;
                    }
                }
                
                // Pour le responsable secteur : vérifier le secteur
                if ($habilitation == 3 && $secteurUtilisateur) {
                    try {
                        $pdo = connexionPDO();
                        $sqlCheck = 'SELECT r.SEC_CODE FROM departement d
                                     INNER JOIN region r ON d.REG_CODE = r.REG_CODE
                                     WHERE d.NoDEPT = :dept';
                        $stmtCheck = $pdo->prepare($sqlCheck);
                        $stmtCheck->bindValue(':dept', $codeDept, PDO::PARAM_INT);
                        $stmtCheck->execute();
                        $deptSecteur = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                        if (!$deptSecteur || $deptSecteur['SEC_CODE'] !== $secteurUtilisateur) {
                            $erreurs[] = "Le nouveau code postal doit rester dans votre secteur.";
                            include("vues/v_gererPraticien.php");
                            break;
                        }
                    } catch (PDOException $e) {
                        $erreurs[] = "Erreur lors de la vérification du secteur.";
                        include("vues/v_gererPraticien.php");
                        break;
                    }
                }
            }

            modifierPraticien($num, $prenom, $nom, $adresse, $cp, $ville, $coef, $type, $coefConfiance);
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
        $listePraticiens = getAllPraticiens($regionUtilisateur, $tri);
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
