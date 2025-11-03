<?php
/**
 * SCRIPT DE VERIFICATION DES DOUBLONS
 * Ce script vérifie s'il y a des doublons dans la base de données GSB
 */

require_once('modele/bd.inc.php');

echo "========================================\n";
echo "VERIFICATION DES DOUBLONS - BASE GSBCR0\n";
echo "========================================\n\n";

try {
    $pdo = connexionPDO();

    // =============================================
    // 1. VERIFICATION DES DOUBLONS DANS LOGIN
    // =============================================
    echo "1. VERIFICATION DES LOGINS EN DOUBLE\n";
    echo "--------------------------------------\n";

    // Vérifier les logins en double
    $reqLoginDouble = "SELECT LOG_LOGIN, COUNT(*) as nb
                       FROM login
                       GROUP BY LOG_LOGIN
                       HAVING COUNT(*) > 1";
    $resLoginDouble = $pdo->query($reqLoginDouble);
    $loginsDoubles = $resLoginDouble->fetchAll();

    if (count($loginsDoubles) > 0) {
        echo "⚠️  ATTENTION : " . count($loginsDoubles) . " login(s) en double trouvé(s) !\n\n";
        foreach ($loginsDoubles as $login) {
            echo "   - Login: '" . $login['LOG_LOGIN'] . "' apparait " . $login['nb'] . " fois\n";
        }
    } else {
        echo "✅ Aucun doublon trouvé dans les logins\n";
    }

    // Nombre total de logins
    $reqTotalLogin = "SELECT COUNT(*) as total FROM login";
    $resTotalLogin = $pdo->query($reqTotalLogin);
    $totalLogin = $resTotalLogin->fetch();
    echo "   Total de logins : " . $totalLogin['total'] . "\n\n";


    // =============================================
    // 2. VERIFICATION DES DOUBLONS DANS MATRICULES
    // =============================================
    echo "2. VERIFICATION DES MATRICULES EN DOUBLE\n";
    echo "--------------------------------------\n";

    // Vérifier les matricules en double dans collaborateur
    $reqMatriculeDouble = "SELECT COL_MATRICULE, COUNT(*) as nb
                           FROM collaborateur
                           GROUP BY COL_MATRICULE
                           HAVING COUNT(*) > 1";
    $resMatriculeDouble = $pdo->query($reqMatriculeDouble);
    $matriculesDoubles = $resMatriculeDouble->fetchAll();

    if (count($matriculesDoubles) > 0) {
        echo "⚠️  ATTENTION : " . count($matriculesDoubles) . " matricule(s) en double trouvé(s) !\n\n";
        foreach ($matriculesDoubles as $mat) {
            echo "   - Matricule: '" . $mat['COL_MATRICULE'] . "' apparait " . $mat['nb'] . " fois\n";
        }
    } else {
        echo "✅ Aucun doublon trouvé dans les matricules\n";
    }

    // Nombre total de collaborateurs
    $reqTotalCol = "SELECT COUNT(*) as total FROM collaborateur";
    $resTotalCol = $pdo->query($reqTotalCol);
    $totalCol = $resTotalCol->fetch();
    echo "   Total de collaborateurs : " . $totalCol['total'] . "\n\n";


    // =============================================
    // 3. VERIFICATION DES DOUBLONS DANS MEDICAMENTS
    // =============================================
    echo "3. VERIFICATION DES MEDICAMENTS EN DOUBLE\n";
    echo "--------------------------------------\n";

    // Vérifier les dépôts légaux en double
    $reqMedDouble = "SELECT MED_DEPOTLEGAL, COUNT(*) as nb
                     FROM medicament
                     GROUP BY MED_DEPOTLEGAL
                     HAVING COUNT(*) > 1";
    $resMedDouble = $pdo->query($reqMedDouble);
    $medsDoubles = $resMedDouble->fetchAll();

    if (count($medsDoubles) > 0) {
        echo "⚠️  ATTENTION : " . count($medsDoubles) . " dépôt(s) légal(aux) en double trouvé(s) !\n\n";
        foreach ($medsDoubles as $med) {
            echo "   - Dépôt légal: '" . $med['MED_DEPOTLEGAL'] . "' apparait " . $med['nb'] . " fois\n";
        }
    } else {
        echo "✅ Aucun doublon trouvé dans les dépôts légaux\n";
    }

    // Vérifier les noms commerciaux en double
    $reqNomDouble = "SELECT MED_NOMCOMMERCIAL, COUNT(*) as nb
                     FROM medicament
                     GROUP BY MED_NOMCOMMERCIAL
                     HAVING COUNT(*) > 1";
    $resNomDouble = $pdo->query($reqNomDouble);
    $nomsDoubles = $resNomDouble->fetchAll();

    if (count($nomsDoubles) > 0) {
        echo "⚠️  ATTENTION : " . count($nomsDoubles) . " nom(s) commercial(aux) en double trouvé(s) !\n\n";
        foreach ($nomsDoubles as $nom) {
            echo "   - Nom commercial: '" . $nom['MED_NOMCOMMERCIAL'] . "' apparait " . $nom['nb'] . " fois\n";
        }
    } else {
        echo "✅ Aucun doublon trouvé dans les noms commerciaux\n";
    }

    // Nombre total de médicaments
    $reqTotalMed = "SELECT COUNT(*) as total FROM medicament";
    $resTotalMed = $pdo->query($reqTotalMed);
    $totalMed = $resTotalMed->fetch();
    echo "   Total de médicaments : " . $totalMed['total'] . "\n\n";


    // =============================================
    // VERIFICATION BONUS : COHERENCE DES DONNEES
    // =============================================
    echo "\n4. VERIFICATION BONUS : COHERENCE DES DONNEES\n";
    echo "--------------------------------------\n";

    // Vérifier que tous les collaborateurs ont un login
    $reqColSansLogin = "SELECT COUNT(*) as nb
                        FROM collaborateur c
                        LEFT JOIN login l ON c.COL_MATRICULE = l.COL_MATRICULE
                        WHERE l.LOG_ID IS NULL";
    $resColSansLogin = $pdo->query($reqColSansLogin);
    $colSansLogin = $resColSansLogin->fetch();

    if ($colSansLogin['nb'] > 0) {
        echo "⚠️  " . $colSansLogin['nb'] . " collaborateur(s) sans login trouvé(s)\n";
    } else {
        echo "✅ Tous les collaborateurs ont un login\n";
    }

    // Vérifier que tous les logins ont un collaborateur
    $reqLoginSansCol = "SELECT COUNT(*) as nb
                        FROM login l
                        LEFT JOIN collaborateur c ON l.COL_MATRICULE = c.COL_MATRICULE
                        WHERE c.COL_MATRICULE IS NULL";
    $resLoginSansCol = $pdo->query($reqLoginSansCol);
    $loginSansCol = $resLoginSansCol->fetch();

    if ($loginSansCol['nb'] > 0) {
        echo "⚠️  " . $loginSansCol['nb'] . " login(s) sans collaborateur trouvé(s)\n";
    } else {
        echo "✅ Tous les logins ont un collaborateur associé\n";
    }

    echo "\n========================================\n";
    echo "VERIFICATION TERMINEE\n";
    echo "========================================\n";

} catch (PDOException $e) {
    echo "❌ ERREUR : " . $e->getMessage() . "\n";
}
?>
