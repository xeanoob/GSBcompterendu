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
function creerRapportVisite($matricule, $numRapport, $dateVisite, $bilan, $motifCode, $praticienNum, $etatCode = 1, $med1 = null, $med2 = null)
{
    try {
        $pdo = connexionPDO();

        $sql = 'INSERT INTO rapport_visite
                (VIS_MATRICULE, RAP_NUM, RAP_DATEVISITE, RAP_BILAN, MOT_CODE, PRA_NUM, ETAT_CODE, MED_DEPOTLEGAL1, MED_DEPOTLEGAL2)
                VALUES (:matricule, :num, :dateVisite, :bilan, :motifCode, :praticienNum, :etatCode, :med1, :med2)';

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

        return $stmt->execute();
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        return false;
    }
}

/**
 * Ajoute un échantillon offert lors d'une visite
 */
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

/**
 * Récupère les rapports d'un visiteur
 */
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
        $sql = 'SELECT r.*, p.PRA_NOM, p.PRA_PRENOM, m.MOT_LIBELLE, e.ETAT_LIBELLE
                FROM rapport_visite r
                INNER JOIN praticien p ON r.PRA_NUM = p.PRA_NUM
                LEFT JOIN motif_visite m ON r.MOT_CODE = m.MOT_CODE
                LEFT JOIN etat e ON r.ETAT_CODE = e.ETAT_CODE
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
