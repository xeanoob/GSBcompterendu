<?php

include_once 'bd.inc.php';

function getAllInformationCompte($matricule)
{

    try {
        $monPdo = connexionPDO();
        $req = 'SELECT c.`COL_MATRICULE` as `matricule`,c.`COL_NOM` as `nom`,`COL_PRENOM` as `prenom`,c.`COL_ADRESSE` as `adresse`,c.`COL_CP` as `cp`,c.`COL_VILLE` as `ville`, concat(DAY(COL_DATEEMBAUCHE),\'/\',MONTH(`COL_DATEEMBAUCHE`),\'/\',YEAR(`COL_DATEEMBAUCHE`)) as `date_embauche`, h.HAB_LIB as `habilitation` ,s.SEC_LIBELLE as `secteur`, r.REG_NOM as `region` FROM collaborateur c LEFT JOIN secteur s ON s.`SEC_CODE`=c.`SEC_CODE` LEFT JOIN habilitation h ON h.HAB_ID=c.HAB_ID LEFT JOIN region r ON r.REG_CODE=c.REG_CODE WHERE c.COL_MATRICULE="' . $matricule . '"';
        $res = $monPdo->query($req);
        $result = $res->fetch();

        return $result;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

function checkConnexion($username, $mdp)
{

    try {
        $getInfo = connexionPDO();
        $req = $getInfo->prepare('SELECT l.LOG_ID as \'id_log\', l.COL_MATRICULE as \'matricule\', c.HAB_ID as \'habilitation\' FROM login l INNER JOIN collaborateur c ON l.COL_MATRICULE = c.COL_MATRICULE WHERE l.LOG_LOGIN = :identifiant AND l.LOG_MOTDEPASSE = "' . hash('sha512', $mdp) . '"');
        $req->bindParam(':identifiant', $username, PDO::PARAM_STR);
        $req->execute();
        $res = $req->fetch();

        return $res;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

function checkMatriculeInscription($matricule)
{

    try {
        $getInfo = connexionPDO();
        $req = $getInfo->prepare('select `COL_MATRICULE` as \'matricule\' from login where `COL_MATRICULE`=:matricule');
        $req->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        $req->execute();
        $res = $req->fetch();

        return $res;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

function checkMatricule($matricule)
{

    try {
        $getInfo = connexionPDO();
        $req = $getInfo->prepare('select `COL_MATRICULE` as \'matricule\' from collaborateur where `COL_MATRICULE`=:matricule');
        $req->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        $req->execute();
        $res = $req->fetch();

        return $res;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

function checkUserInscription($username)
{

    try {
        $getInfo = connexionPDO();
        $req = $getInfo->prepare('SELECT `LOG_LOGIN` from login where `LOG_LOGIN`=:username');
        $req->bindParam(':username', $username, PDO::PARAM_STR);
        $req->execute();
        $res = $req->fetch();

        return $res;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

function getAllMatriculeCollaborateur()
{

    try {

        $monPdo = connexionPDO();
        $req = 'SELECT COL_MATRICULE FROM collaborateur ORDER BY COL_MATRICULE';
        $res = $monPdo->query($req);
        $result = $res->fetchAll();

        return $result;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

function getColMatricule()
{

    try {

        $monPdo = connexionPDO();
        $req = 'SELECT COL_MATRICULE FROM collaborateur ORDER BY COL_MATRICULE';
        $res = $monPdo->query($req);
        $result = $res->fetchAll();

        return $result;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

function getCountMatricule()
{

    try {

        $monPdo = connexionPDO();
        $req = 'SELECT COUNT(COL_MATRICULE) as \'nb\' FROM collaborateur';
        $res = $monPdo->query($req);
        $result = $res->fetch();

        return $result;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage();
        die();
    }
}

/* ANCIENNES FONCTIONS QUI A PERMIS DE SET LES LOGINS, LES HABILITATIONS ET LA MONNAIE DES MEDOCS

function concatMotDePasseBrut($mat) : string {

    try 
    {	

        $monPdo = connexionPDO();
        $req = 'SELECT COL_NOM, COL_PRENOM FROM collaborateur WHERE COL_MATRICULE = "'.$mat.'"';
        $res = $monPdo->query($req);
        $result = $res->fetch();

        $c = substr($result['COL_NOM'], 0, 3) . substr($result['COL_PRENOM'], 0, 3) . '!';
        return $c;
    } 

    catch (PDOException $e) 
    {
           print "Erreur !: " . $e->getMessage();
            die();
    }

}

function concatLogin($mat) : string {

    try 
    {	

        $monPdo = connexionPDO();
        $req = 'SELECT COL_NOM, COL_PRENOM FROM collaborateur WHERE COL_MATRICULE = "'.$mat.'"';
        $res = $monPdo->query($req);
        $result = $res->fetch();

        $c = substr($result['COL_NOM'], 0, 3) . substr($result['COL_PRENOM'], 0, 3);
        $c = strtolower($c);
        return $c;
    } 

    catch (PDOException $e) 
    {
           print "Erreur !: " . $e->getMessage();
            die();
    }

}

function setAllLogin($a,$i){
    $monPdo = connexionPDO();
        $id=$i+1;
        //echo 'Id : '.$a[$i][0].' | Login : '.concatLogin($a[$i][0]).' | Mot de passe : '.concatMotDePasseBrut($a[$i][0]).'</br>';
        
        $req = 'INSERT INTO login VALUES('.$id.',"'.concatLogin($a[$i][0]).'","'.hash('sha512', concatMotDePasseBrut($a[$i][0])).'","'.$a[$i][0].'"); UPDATE collaborateur SET LOG_ID='.$id.' WHERE COL_MATRICULE="'.$a[$i][0].'"' ;
        $res = $monPdo->query($req);
    

}
function setAllHabil($a,$id,$i){
    $monPdo = connexionPDO();
        $req = 'UPDATE collaborateur SET HAB_ID='.$id.' WHERE COL_MATRICULE="'.$a[$i][0].'"' ;
        $res = $monPdo->query($req);
    

}
function getIdMedoc(){
        $monPdo = connexionPDO();
        $req = 'SELECT `MED_DEPOTLEGAL` FROM `medicament`;' ;
        $res = $monPdo->query($req);
        $result = $res->fetchAll();
        return $result;    

}
function getNbMedoc(){
    $monPdo = connexionPDO();
    $req = 'SELECT COUNT(`MED_DEPOTLEGAL`) FROM `medicament`;' ;
    $res = $monPdo->query($req);
    $result = $res->fetch();
    return $result; 

}
function setMonnaieMedoc($a,$id,$i){
    $monPdo = connexionPDO();
    $id=$id+0.99;
        $req = 'UPDATE medicament SET `MED_PRIXECHANTILLON`='.$id.' WHERE `MED_DEPOTLEGAL`="'.$a[$i][0].'"' ;
        $res = $monPdo->query($req);
} FONCTIONS PLUS UTILES */
