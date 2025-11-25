<?php

include_once 'bd.inc.php';

/**
 * Récupère la liste des praticiens (numéro + nom + prénom)
 * pour alimenter la liste déroulante.
 */
function getAllPraticiens()
{
    try {
        $pdo = connexionPDO();
        $sql = 'SELECT PRA_NUM, PRA_NOM, PRA_PRENOM 
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
 * Récupère toutes les infos d’un praticien à partir de son numéro.
 */
function getPraticienByNum($num)
{
    try {
        $pdo = connexionPDO();
        $sql = 'SELECT PRA_NUM, PRA_PRENOM, PRA_NOM, PRA_ADRESSE, 
                       PRA_CP, PRA_VILLE, PRA_COEFNOTORIETE, TYP_CODE
                FROM praticien
                WHERE PRA_NUM = :num';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':num', $num, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        die();
    }
}

/**
 * Récupère la liste des types de praticiens
 * pour alimenter la liste déroulante TYP_CODE.
 */
function getAllTypesPraticien()
{
    try {
        $pdo = connexionPDO();
        $sql = 'SELECT TYP_CODE, TYP_LIBELLE 
                FROM type_praticien
                ORDER BY TYP_LIBELLE';
        $res = $pdo->query($sql);
        return $res->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        die();
    }
}

/**
 * Ajoute un nouveau praticien.
 * Le numéro PRA_NUM est généré automatiquement par AUTO_INCREMENT.
 * @return int Le numéro du praticien créé
 */
function ajouterPraticien($prenom, $nom, $adresse, $cp, $ville, $coef, $type)
{
    try {
        $pdo = connexionPDO();
        $sql = 'INSERT INTO praticien
                (PRA_PRENOM, PRA_NOM, PRA_ADRESSE, PRA_CP, PRA_VILLE, PRA_COEFNOTORIETE, TYP_CODE)
                VALUES (:prenom, :nom, :adresse, :cp, :ville, :coef, :type)';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':prenom', $prenom, PDO::PARAM_STR);
        $stmt->bindValue(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindValue(':adresse', $adresse, PDO::PARAM_STR);
        $stmt->bindValue(':cp', $cp, PDO::PARAM_STR);
        $stmt->bindValue(':ville', $ville, PDO::PARAM_STR);
        $stmt->bindValue(':coef', $coef, PDO::PARAM_STR);
        $stmt->bindValue(':type', $type, PDO::PARAM_STR);
        $stmt->execute();
        
        // Retourner l'ID auto-généré
        return (int) $pdo->lastInsertId();
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        die();
    }
}

/**
 * Met à jour un praticien existant.
 */
function modifierPraticien($num, $prenom, $nom, $adresse, $cp, $ville, $coef, $type)
{
    try {
        $pdo = connexionPDO();
        $sql = 'UPDATE praticien
                SET PRA_PRENOM = :prenom,
                    PRA_NOM = :nom,
                    PRA_ADRESSE = :adresse,
                    PRA_CP = :cp,
                    PRA_VILLE = :ville,
                    PRA_COEFNOTORIETE = :coef,
                    TYP_CODE = :type
                WHERE PRA_NUM = :num';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':num', $num, PDO::PARAM_INT);
        $stmt->bindValue(':prenom', $prenom, PDO::PARAM_STR);
        $stmt->bindValue(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindValue(':adresse', $adresse, PDO::PARAM_STR);
        $stmt->bindValue(':cp', $cp, PDO::PARAM_STR);
        $stmt->bindValue(':ville', $ville, PDO::PARAM_STR);
        $stmt->bindValue(':coef', $coef, PDO::PARAM_STR);
        $stmt->bindValue(':type', $type, PDO::PARAM_STR);
        $stmt->execute();
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        die();
    }
}

/**
 * Récupère toutes les informations détaillées d'un praticien avec son type
 * @param int $num Numéro du praticien
 * @return array|false Informations du praticien ou false si non trouvé
 */
function getPraticienComplet($num)
{
    try {
        $pdo = connexionPDO();
        $sql = 'SELECT p.*, t.TYP_LIBELLE, t.TYP_LIEU
                FROM praticien p
                LEFT JOIN type_praticien t ON p.TYP_CODE = t.TYP_CODE
                WHERE p.PRA_NUM = :num';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':num', $num, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        die();
    }
}

/**
 * Récupère toutes les spécialités disponibles
 * @return array Liste des spécialités
 */
function getAllSpecialites()
{
    try {
        $pdo = connexionPDO();
        $sql = 'SELECT SPE_CODE, SPE_LIBELLE
                FROM specialite
                ORDER BY SPE_LIBELLE';
        $res = $pdo->query($sql);
        return $res->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        die();
    }
}

/**
 * Récupère les spécialités d'un praticien
 * @param int $praticienNum Numéro du praticien
 * @return array Liste des spécialités avec leurs informations (diplôme, coef prescription)
 */
function getSpecialitesPraticien($praticienNum)
{
    try {
        $pdo = connexionPDO();
        $sql = 'SELECT s.SPE_CODE, s.SPE_LIBELLE, p.POS_DIPLOME, p.POS_COEFPRESCRIPTIO
                FROM posseder p
                INNER JOIN specialite s ON p.SPE_CODE = s.SPE_CODE
                WHERE p.PRA_NUM = :num
                ORDER BY s.SPE_LIBELLE';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':num', $praticienNum, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        die();
    }
}

/**
 * Ajoute une spécialité à un praticien
 * @param int $praticienNum Numéro du praticien
 * @param string $speCode Code de la spécialité
 * @param string $diplome Diplôme obtenu
 * @param float $coefPrescription Coefficient de prescription
 * @return bool Succès de l'opération
 */
function ajouterSpecialitePraticien($praticienNum, $speCode, $diplome = '', $coefPrescription = 0)
{
    try {
        $pdo = connexionPDO();
        $sql = 'INSERT INTO posseder (PRA_NUM, SPE_CODE, POS_DIPLOME, POS_COEFPRESCRIPTIO)
                VALUES (:praNum, :speCode, :diplome, :coef)';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':praNum', $praticienNum, PDO::PARAM_INT);
        $stmt->bindValue(':speCode', $speCode, PDO::PARAM_STR);
        $stmt->bindValue(':diplome', $diplome, PDO::PARAM_STR);
        $stmt->bindValue(':coef', $coefPrescription, PDO::PARAM_STR);
        return $stmt->execute();
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        return false;
    }
}

/**
 * Supprime toutes les spécialités d'un praticien
 * @param int $praticienNum Numéro du praticien
 * @return bool Succès de l'opération
 */
function supprimerToutesSpecialitesPraticien($praticienNum)
{
    try {
        $pdo = connexionPDO();
        $sql = 'DELETE FROM posseder WHERE PRA_NUM = :num';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':num', $praticienNum, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        print "Erreur ! : " . $e->getMessage();
        return false;
    }
}
