<?php

include_once 'bd.inc.php';

/**
 * Récupère le prochain numéro de rapport disponible pour un visiteur
 */
function getProchainNumeroRapport($matricule)
{
    try {
        $pdo = connexionPDO();
        $sql = 'SELECT COALESCE(MAX(RAP_NUM), 0) + 1 AS prochain_num
                FROM rapport_visite
                WHERE VIS_MATRICULE = :matricule';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['prochain_num'];
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        die();
    }
}

/**
 * Récupère la liste de tous les praticiens pour la liste déroulante
 */
function getTousPraticiens()
{
    try {
        $pdo = connexionPDO();
        $sql = 'SELECT PRA_NUM, PRA_NOM, PRA_PRENOM, PRA_VILLE
                FROM praticien
                ORDER BY PRA_NOM, PRA_PRENOM';
        $res = $pdo->query($sql);
        return $res->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        die();
    }
}

/**
 * Récupère la liste des praticiens ayant déjà fait l'objet d'un rapport de visite
 */
function getPraticiensAyantVisite()
{
    try {
        $pdo = connexionPDO();
        $sql = 'SELECT DISTINCT p.PRA_NUM, p.PRA_NOM, p.PRA_PRENOM, p.PRA_VILLE
                FROM praticien p
                INNER JOIN rapport_visite r ON p.PRA_NUM = r.PRA_NUM
                ORDER BY p.PRA_NOM, p.PRA_PRENOM';
        $res = $pdo->query($sql);
        return $res->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        die();
    }
}

/**
 * Récupère la liste de tous les motifs de visite
 */
function getTousMotifsVisite()
{
    try {
        $pdo = connexionPDO();
        $sql = 'SELECT MOT_CODE, MOT_LIBELLE
                FROM motif_visite
                ORDER BY MOT_CODE';
        $res = $pdo->query($sql);
        return $res->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        die();
    }
}

/**
 * Récupère la liste de tous les médicaments
 */
function getTousMedicaments()
{
    try {
        $pdo = connexionPDO();
        $sql = 'SELECT MED_DEPOTLEGAL, MED_NOMCOMMERCIAL
                FROM medicament
                ORDER BY MED_NOMCOMMERCIAL';
        $res = $pdo->query($sql);
        return $res->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        die();
    }
}

/**
 * Récupère la liste de tous les états de rapport
 */
function getTousEtats()
{
    try {
        $pdo = connexionPDO();
        $sql = 'SELECT ETAT_CODE, ETAT_LIBELLE
                FROM etat
                ORDER BY ETAT_CODE';
        $res = $pdo->query($sql);
        return $res->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        die();
    }
}

/**
 * Crée un nouveau rapport de visite
 * Retourne true en cas de succès, false sinon
 */
function creerRapportVisite($matricule, $numRapport, $dateVisite, $bilan, $motifCode, $praticienNum, $etatCode = 1, $med1 = null, $med2 = null, $rapMotif = null)
{
    try {
        $pdo = connexionPDO();

        $sql = 'INSERT INTO rapport_visite
                (VIS_MATRICULE, RAP_NUM, RAP_DATEVISITE, RAP_BILAN, MOT_CODE, PRA_NUM, ETAT_CODE, MED_DEPOTLEGAL1, MED_DEPOTLEGAL2, RAP_MOTIF)
                VALUES (:matricule, :num, :dateVisite, :bilan, :motifCode, :praticienNum, :etatCode, :med1, :med2, :rapMotif)';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->bindValue(':num', $numRapport, PDO::PARAM_INT);
        $stmt->bindValue(':dateVisite', $dateVisite, PDO::PARAM_STR);
        $stmt->bindValue(':bilan', $bilan, PDO::PARAM_STR);
        $stmt->bindValue(':motifCode', $motifCode, PDO::PARAM_INT);
        $stmt->bindValue(':praticienNum', $praticienNum, PDO::PARAM_INT);
        $stmt->bindValue(':etatCode', $etatCode, PDO::PARAM_INT);
        $stmt->bindValue(':med1', $med1, PDO::PARAM_STR);
        $stmt->bindValue(':med2', $med2, PDO::PARAM_STR);
        $stmt->bindValue(':rapMotif', $rapMotif, PDO::PARAM_STR);

        return $stmt->execute();
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        return false;
    }
}


function ajouterEchantillonOffert($matricule, $numRapport, $medDepotLegal, $quantite)
{
    try {
        $pdo = connexionPDO();

        $sql = 'INSERT INTO offrir
                (VIS_MATRICULE, RAP_NUM, MED_DEPOTLEGAL, OFF_QTE)
                VALUES (:matricule, :num, :medDepotLegal, :quantite)';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->bindValue(':num', $numRapport, PDO::PARAM_INT);
        $stmt->bindValue(':medDepotLegal', $medDepotLegal, PDO::PARAM_STR);
        $stmt->bindValue(':quantite', $quantite, PDO::PARAM_INT);

        return $stmt->execute();
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        return false;
    }
}


function getRapportsParVisiteur($matricule)
{
    try {
        $pdo = connexionPDO();
        $sql = 'SELECT r.*, p.PRA_NOM, p.PRA_PRENOM, m.MOT_LIBELLE, e.ETAT_LIBELLE
                FROM rapport_visite r
                INNER JOIN praticien p ON r.PRA_NUM = p.PRA_NUM
                LEFT JOIN motif_visite m ON r.MOT_CODE = m.MOT_CODE
                LEFT JOIN etat e ON r.ETAT_CODE = e.ETAT_CODE
                WHERE r.VIS_MATRICULE = :matricule
                ORDER BY r.RAP_DATEVISITE DESC, r.RAP_NUM DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        die();
    }
}

/**
 * Récupère un rapport de visite spécifique
 */
function getRapportVisite($matricule, $numRapport)
{
    try {
        $pdo = connexionPDO();
        $sql = 'SELECT r.*, p.PRA_NOM, p.PRA_PRENOM, 
                       m.MOT_LIBELLE, e.ETAT_LIBELLE,
                       med1.MED_NOMCOMMERCIAL as MED1_NOM,
                       med2.MED_NOMCOMMERCIAL as MED2_NOM
                FROM rapport_visite r
                INNER JOIN praticien p ON r.PRA_NUM = p.PRA_NUM
                LEFT JOIN motif_visite m ON r.MOT_CODE = m.MOT_CODE
                LEFT JOIN etat e ON r.ETAT_CODE = e.ETAT_CODE
                LEFT JOIN medicament med1 ON r.MED_DEPOTLEGAL1 = med1.MED_DEPOTLEGAL
                LEFT JOIN medicament med2 ON r.MED_DEPOTLEGAL2 = med2.MED_DEPOTLEGAL
                WHERE r.VIS_MATRICULE = :matricule AND r.RAP_NUM = :num';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->bindValue(':num', $numRapport, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        die();
    }
}

/**
 * Récupère les échantillons offerts pour un rapport
 */
function getEchantillonsOfferts($matricule, $numRapport)
{
    try {
        $pdo = connexionPDO();
        $sql = 'SELECT o.*, m.MED_NOMCOMMERCIAL
                FROM offrir o
                INNER JOIN medicament m ON o.MED_DEPOTLEGAL = m.MED_DEPOTLEGAL
                WHERE o.VIS_MATRICULE = :matricule AND o.RAP_NUM = :num';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->bindValue(':num', $numRapport, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        die();
    }
}

/**
 * Récupère les rapports en cours de saisie d'un visiteur (non validés)
 * @param string $matricule Matricule du visiteur
 * @return array Liste des rapports en cours
 */
function getRapportsEnCours($matricule)
{
    try {
        $pdo = connexionPDO();
        $sql = 'SELECT r.*, p.PRA_NOM, p.PRA_PRENOM, 
                       m.MOT_LIBELLE, e.ETAT_LIBELLE
                FROM rapport_visite r
                INNER JOIN praticien p ON r.PRA_NUM = p.PRA_NUM
                LEFT JOIN motif_visite m ON r.MOT_CODE = m.MOT_CODE
                LEFT JOIN etat e ON r.ETAT_CODE = e.ETAT_CODE
                WHERE r.VIS_MATRICULE = :matricule AND r.ETAT_CODE = 1
                ORDER BY r.RAP_DATEVISITE DESC, r.RAP_NUM DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        die();
    }
}

/**
 * Met à jour un rapport de visite existant
 * @return bool true en cas de succès, false sinon
 */
function mettreAJourRapport($matricule, $numRapport, $dateVisite, $bilan, $motifCode, $praticienNum, $etatCode, $med1 = null, $med2 = null, $rapMotif = null)
{
    try {
        $pdo = connexionPDO();

        $sql = 'UPDATE rapport_visite
                SET RAP_DATEVISITE = :dateVisite,
                    RAP_BILAN = :bilan,
                    MOT_CODE = :motifCode,
                    PRA_NUM = :praticienNum,
                    ETAT_CODE = :etatCode,
                    MED_DEPOTLEGAL1 = :med1,
                    MED_DEPOTLEGAL2 = :med2,
                    RAP_MOTIF = :rapMotif
                WHERE VIS_MATRICULE = :matricule AND RAP_NUM = :num';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->bindValue(':num', $numRapport, PDO::PARAM_INT);
        $stmt->bindValue(':dateVisite', $dateVisite, PDO::PARAM_STR);
        $stmt->bindValue(':bilan', $bilan, PDO::PARAM_STR);
        $stmt->bindValue(':motifCode', $motifCode, PDO::PARAM_INT);
        $stmt->bindValue(':praticienNum', $praticienNum, PDO::PARAM_INT);
        $stmt->bindValue(':etatCode', $etatCode, PDO::PARAM_INT);
        $stmt->bindValue(':med1', $med1, PDO::PARAM_STR);
        $stmt->bindValue(':med2', $med2, PDO::PARAM_STR);
        $stmt->bindValue(':rapMotif', $rapMotif, PDO::PARAM_STR);

        return $stmt->execute();
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        return false;
    }
}

/**
 * Supprime tous les échantillons d'un rapport
 * @param string $matricule Matricule du visiteur
 * @param int $numRapport Numéro du rapport
 * @return bool true en cas de succès, false sinon
 */
function supprimerEchantillonsRapport($matricule, $numRapport)
{
    try {
        $pdo = connexionPDO();

        $sql = 'DELETE FROM offrir
                WHERE VIS_MATRICULE = :matricule AND RAP_NUM = :num';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->bindValue(':num', $numRapport, PDO::PARAM_INT);

        return $stmt->execute();
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        return false;
    }
}

/**
 * Recherche les rapports de visite selon des critères
 * @param string $dateDebut Date de début (format Y-m-d)
 * @param string $dateFin Date de fin (format Y-m-d)
 * @param int|null $praticienNum Numéro du praticien (optionnel)
 * @return array Liste des rapports correspondant aux critères
 */
function rechercherRapports($dateDebut, $dateFin, $praticienNum = null)
{
    try {
        $pdo = connexionPDO();

        // Construction de la requête de base
        $sql = 'SELECT r.VIS_MATRICULE, r.RAP_NUM, r.RAP_DATEVISITE, r.RAP_MOTIF, r.RAP_BILAN,
                       p.PRA_NUM, p.PRA_NOM, p.PRA_PRENOM,
                       m.MOT_LIBELLE,
                       GROUP_CONCAT(DISTINCT CONCAT(med.MED_DEPOTLEGAL, ":", med.MED_NOMCOMMERCIAL) SEPARATOR ";") as medicaments_presentes
                FROM rapport_visite r
                INNER JOIN praticien p ON r.PRA_NUM = p.PRA_NUM
                LEFT JOIN motif_visite m ON r.MOT_CODE = m.MOT_CODE
                LEFT JOIN medicament med ON (r.MED_DEPOTLEGAL1 = med.MED_DEPOTLEGAL OR r.MED_DEPOTLEGAL2 = med.MED_DEPOTLEGAL)
                WHERE r.RAP_DATEVISITE BETWEEN :dateDebut AND :dateFin';

        // Ajout du filtre praticien si spécifié
        if ($praticienNum !== null && $praticienNum > 0) {
            $sql .= ' AND r.PRA_NUM = :praticienNum';
        }

        $sql .= ' GROUP BY r.VIS_MATRICULE, r.RAP_NUM
                  ORDER BY r.RAP_DATEVISITE DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':dateDebut', $dateDebut, PDO::PARAM_STR);
        $stmt->bindValue(':dateFin', $dateFin, PDO::PARAM_STR);

        if ($praticienNum !== null && $praticienNum > 0) {
            $stmt->bindValue(':praticienNum', $praticienNum, PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        die();
    }
}

/**
 * Récupère les détails d'un rapport de visite (pour consultation publique)
 * @param string $matricule Matricule du visiteur
 * @param int $numRapport Numéro du rapport
 * @return array|false Informations du rapport ou false si non trouvé
 */
function getRapportVisiteComplet($matricule, $numRapport)
{
    try {
        $pdo = connexionPDO();
        $sql = 'SELECT r.*,
                       p.PRA_NUM, p.PRA_NOM, p.PRA_PRENOM, p.PRA_ADRESSE, p.PRA_CP, p.PRA_VILLE,
                       m.MOT_LIBELLE,
                       e.ETAT_LIBELLE,
                       med1.MED_NOMCOMMERCIAL as MED1_NOM,
                       med2.MED_NOMCOMMERCIAL as MED2_NOM
                FROM rapport_visite r
                INNER JOIN praticien p ON r.PRA_NUM = p.PRA_NUM
                LEFT JOIN motif_visite m ON r.MOT_CODE = m.MOT_CODE
                LEFT JOIN etat e ON r.ETAT_CODE = e.ETAT_CODE
                LEFT JOIN medicament med1 ON r.MED_DEPOTLEGAL1 = med1.MED_DEPOTLEGAL
                LEFT JOIN medicament med2 ON r.MED_DEPOTLEGAL2 = med2.MED_DEPOTLEGAL
                WHERE r.VIS_MATRICULE = :matricule AND r.RAP_NUM = :num';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->bindValue(':num', $numRapport, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        die();
    }
}


/**
 * Récupère les nouveaux rapports validés d'une région (non consultés)
 * @param string $regCode Code de la région
 * @return array Liste des rapports
 */
function getNouveauxRapportsRegion($regCode)
{
    try {
        $pdo = connexionPDO();
        $sql = 'SELECT r.VIS_MATRICULE, r.RAP_NUM, r.RAP_DATEVISITE, r.RAP_BILAN, r.PRA_NUM,
                       r.MED_DEPOTLEGAL1, r.MED_DEPOTLEGAL2,
                       p.PRA_NOM, p.PRA_PRENOM,
                       m.MOT_LIBELLE,
                       c.COL_NOM, c.COL_PRENOM,
                       med1.MED_NOMCOMMERCIAL as MED1_NOM,
                       med2.MED_NOMCOMMERCIAL as MED2_NOM
                FROM rapport_visite r
                INNER JOIN collaborateur c ON r.VIS_MATRICULE = c.COL_MATRICULE
                INNER JOIN praticien p ON r.PRA_NUM = p.PRA_NUM
                LEFT JOIN motif_visite m ON r.MOT_CODE = m.MOT_CODE
                LEFT JOIN medicament med1 ON r.MED_DEPOTLEGAL1 = med1.MED_DEPOTLEGAL
                LEFT JOIN medicament med2 ON r.MED_DEPOTLEGAL2 = med2.MED_DEPOTLEGAL
                WHERE c.REG_CODE = :regCode
                AND r.ETAT_CODE = 2 -- Saisie définitive / Validé
                ORDER BY c.COL_NOM, c.COL_PRENOM, r.RAP_DATEVISITE DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':regCode', $regCode, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        die();
    }
}

/**
 * Récupère les nouveaux rapports validés d'un secteur (non consultés)
 * @param string $secCode Code du secteur
 * @return array Liste des rapports
 */
function getNouveauxRapportsSecteur($secCode)
{
    try {
        $pdo = connexionPDO();
        $sql = 'SELECT r.VIS_MATRICULE, r.RAP_NUM, r.RAP_DATEVISITE, r.RAP_BILAN, r.PRA_NUM,
                       r.MED_DEPOTLEGAL1, r.MED_DEPOTLEGAL2,
                       p.PRA_NOM, p.PRA_PRENOM,
                       m.MOT_LIBELLE,
                       c.COL_NOM, c.COL_PRENOM,
                       med1.MED_NOMCOMMERCIAL as MED1_NOM,
                       med2.MED_NOMCOMMERCIAL as MED2_NOM
                FROM rapport_visite r
                INNER JOIN collaborateur c ON r.VIS_MATRICULE = c.COL_MATRICULE
                INNER JOIN praticien p ON r.PRA_NUM = p.PRA_NUM
                LEFT JOIN motif_visite m ON r.MOT_CODE = m.MOT_CODE
                LEFT JOIN medicament med1 ON r.MED_DEPOTLEGAL1 = med1.MED_DEPOTLEGAL
                LEFT JOIN medicament med2 ON r.MED_DEPOTLEGAL2 = med2.MED_DEPOTLEGAL
                INNER JOIN region reg ON c.REG_CODE = reg.REG_CODE
                WHERE reg.SEC_CODE = :secCode
                AND r.ETAT_CODE = 2 -- Saisie définitive / Validé
                ORDER BY c.COL_NOM, c.COL_PRENOM, r.RAP_DATEVISITE DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':secCode', $secCode, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        die();
    }
}

/**
 * Marque un rapport comme consulté par le délégué
 * @param string $matricule Matricule du visiteur
 * @param int $numRapport Numéro du rapport
 * @return bool true en cas de succès
 */
function marquerRapportConsulte($matricule, $numRapport)
{
    try {
        $pdo = connexionPDO();
        $sql = 'UPDATE rapport_visite
                SET ETAT_CODE = 3 -- Consulté par le délégué
                WHERE VIS_MATRICULE = :matricule AND RAP_NUM = :num';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':matricule', $matricule, PDO::PARAM_STR);
        $stmt->bindValue(':num', $numRapport, PDO::PARAM_INT);

        return $stmt->execute();
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        return false;
    }
}

/**
 * Consulte l'historique des rapports de visite d'une région avec filtres
 * @param string $regCode Code de la région
 * @param string $dateDebut Date de début (format Y-m-d)
 * @param string $dateFin Date de fin (format Y-m-d)
 * @param string|null $matriculeVisiteur Matricule du visiteur (optionnel)
 * @return array Liste des rapports
 */
function consulterHistoriqueRapportsRegion($regCode, $dateDebut, $dateFin, $matriculeVisiteur = null)
{
    try {
        $pdo = connexionPDO();
        
        $sql = 'SELECT r.VIS_MATRICULE, r.RAP_NUM, r.RAP_DATEVISITE, r.RAP_BILAN, r.RAP_MOTIF, r.PRA_NUM,
                       r.MED_DEPOTLEGAL1, r.MED_DEPOTLEGAL2, r.ETAT_CODE,
                       p.PRA_NOM, p.PRA_PRENOM,
                       m.MOT_LIBELLE,
                       c.COL_NOM, c.COL_PRENOM, c.COL_MATRICULE,
                       e.ETAT_LIBELLE,
                       med1.MED_NOMCOMMERCIAL as MED1_NOM,
                       med2.MED_NOMCOMMERCIAL as MED2_NOM
                FROM rapport_visite r
                INNER JOIN collaborateur c ON r.VIS_MATRICULE = c.COL_MATRICULE
                INNER JOIN praticien p ON r.PRA_NUM = p.PRA_NUM
                LEFT JOIN motif_visite m ON r.MOT_CODE = m.MOT_CODE
                LEFT JOIN etat e ON r.ETAT_CODE = e.ETAT_CODE
                LEFT JOIN medicament med1 ON r.MED_DEPOTLEGAL1 = med1.MED_DEPOTLEGAL
                LEFT JOIN medicament med2 ON r.MED_DEPOTLEGAL2 = med2.MED_DEPOTLEGAL
                WHERE c.REG_CODE = :regCode
                AND r.RAP_DATEVISITE BETWEEN :dateDebut AND :dateFin';
        
        if ($matriculeVisiteur !== null) {
            $sql .= ' AND r.VIS_MATRICULE = :matricule';
        }
        
        $sql .= ' ORDER BY r.RAP_DATEVISITE DESC, c.COL_NOM, c.COL_PRENOM';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':regCode', $regCode, PDO::PARAM_STR);
        $stmt->bindValue(':dateDebut', $dateDebut, PDO::PARAM_STR);
        $stmt->bindValue(':dateFin', $dateFin, PDO::PARAM_STR);
        
        if ($matriculeVisiteur !== null) {
            $stmt->bindValue(':matricule', $matriculeVisiteur, PDO::PARAM_STR);
        }
        
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        die();
    }
}

/**
 * Consulte l'historique des rapports de visite d'un secteur avec filtres
 * @param string $secCode Code du secteur
 * @param string $dateDebut Date de début (format Y-m-d)
 * @param string $dateFin Date de fin (format Y-m-d)
 * @param string|null $matriculeVisiteur Matricule du visiteur (optionnel)
 * @return array Liste des rapports
 */
function consulterHistoriqueRapportsSecteur($secCode, $dateDebut, $dateFin, $matriculeVisiteur = null)
{
    try {
        $pdo = connexionPDO();
        
        $sql = 'SELECT r.VIS_MATRICULE, r.RAP_NUM, r.RAP_DATEVISITE, r.RAP_BILAN, r.RAP_MOTIF, r.PRA_NUM,
                       r.MED_DEPOTLEGAL1, r.MED_DEPOTLEGAL2, r.ETAT_CODE,
                       p.PRA_NOM, p.PRA_PRENOM,
                       m.MOT_LIBELLE,
                       c.COL_NOM, c.COL_PRENOM, c.COL_MATRICULE,
                       e.ETAT_LIBELLE,
                       med1.MED_NOMCOMMERCIAL as MED1_NOM,
                       med2.MED_NOMCOMMERCIAL as MED2_NOM
                FROM rapport_visite r
                INNER JOIN collaborateur c ON r.VIS_MATRICULE = c.COL_MATRICULE
                INNER JOIN region reg ON c.REG_CODE = reg.REG_CODE
                INNER JOIN praticien p ON r.PRA_NUM = p.PRA_NUM
                LEFT JOIN motif_visite m ON r.MOT_CODE = m.MOT_CODE
                LEFT JOIN etat e ON r.ETAT_CODE = e.ETAT_CODE
                LEFT JOIN medicament med1 ON r.MED_DEPOTLEGAL1 = med1.MED_DEPOTLEGAL
                LEFT JOIN medicament med2 ON r.MED_DEPOTLEGAL2 = med2.MED_DEPOTLEGAL
                WHERE reg.SEC_CODE = :secCode
                AND r.RAP_DATEVISITE BETWEEN :dateDebut AND :dateFin';
        
        if ($matriculeVisiteur !== null) {
            $sql .= ' AND r.VIS_MATRICULE = :matricule';
        }
        
        $sql .= ' ORDER BY r.RAP_DATEVISITE DESC, c.COL_NOM, c.COL_PRENOM';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':secCode', $secCode, PDO::PARAM_STR);
        $stmt->bindValue(':dateDebut', $dateDebut, PDO::PARAM_STR);
        $stmt->bindValue(':dateFin', $dateFin, PDO::PARAM_STR);
        
        if ($matriculeVisiteur !== null) {
            $stmt->bindValue(':matricule', $matriculeVisiteur, PDO::PARAM_STR);
        }
        
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        die();
    }
}

/**
 * Récupère la liste des visiteurs d'une région
 * @param string $regCode Code de la région
 * @return array Liste des visiteurs
 */
function getVisiteursRegion($regCode)
{
    try {
        $pdo = connexionPDO();
        $sql = 'SELECT DISTINCT c.COL_MATRICULE, c.COL_NOM, c.COL_PRENOM
                FROM collaborateur c
                WHERE c.REG_CODE = :regCode
                AND c.HAB_ID IN (1, 2) -- Visiteur ou Délégué Régional
                ORDER BY c.COL_NOM, c.COL_PRENOM';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':regCode', $regCode, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        die();
    }
}

/**
 * Récupère la liste des visiteurs d'un secteur
 * @param string $secCode Code du secteur
 * @return array Liste des visiteurs
 */
function getVisiteursSecteur($secCode)
{
    try {
        $pdo = connexionPDO();
        $sql = 'SELECT DISTINCT c.COL_MATRICULE, c.COL_NOM, c.COL_PRENOM
                FROM collaborateur c
                INNER JOIN region r ON c.REG_CODE = r.REG_CODE
                WHERE r.SEC_CODE = :secCode
                AND c.HAB_ID IN (1, 2) -- Visiteur ou Délégué Régional
                ORDER BY c.COL_NOM, c.COL_PRENOM';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':secCode', $secCode, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        die();
    }
}

