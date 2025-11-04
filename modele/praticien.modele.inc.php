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
 */
function ajouterPraticien($num, $prenom, $nom, $adresse, $cp, $ville, $coef, $type)
{
    try {
        $pdo = connexionPDO();
        $sql = 'INSERT INTO praticien
                (PRA_NUM, PRA_PRENOM, PRA_NOM, PRA_ADRESSE, PRA_CP, PRA_VILLE, PRA_COEFNOTORIETE, TYP_CODE)
                VALUES (:num, :prenom, :nom, :adresse, :cp, :ville, :coef, :type)';
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
