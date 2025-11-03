-- ================================================================================
-- VERIFICATION FINALE APRES TOUTES LES CORRECTIONS
-- ================================================================================
-- Date : 03/11/2025
-- Objectif : Verifier que toutes les corrections ont ete appliquees correctement
-- ================================================================================

-- Ce fichier doit etre execute APRES avoir applique toutes les corrections :
-- 1. correction_code_postal_laon.sql
-- 2. identification_logins_orphelins.sql + suppression eventuelle
-- 3. correction_contrainte_unique_login.sql


-- ================================================================================
-- VERIFICATION #1 : CODE POSTAL DE LAON
-- ================================================================================

SELECT
    '1. CODE POSTAL LAON' as 'Test',
    CASE
        WHEN COL_CP = '02000' THEN 'OK - Corrige'
        WHEN COL_CP = '2000' THEN 'ECHEC - Pas encore corrige'
        ELSE 'ERREUR - Valeur inattendue'
    END as 'Resultat',
    COL_CP as 'Valeur_actuelle'
FROM collaborateur
WHERE COL_MATRICULE = 'n59';

-- Resultat attendu : OK - Corrige | 02000


-- ================================================================================
-- VERIFICATION #2 : LOGINS ORPHELINS
-- ================================================================================

SELECT
    '2. LOGINS ORPHELINS' as 'Test',
    CASE
        WHEN COUNT(*) = 0 THEN 'OK - Aucun login orphelin'
        WHEN COUNT(*) > 0 THEN CONCAT('ATTENTION - ', COUNT(*), ' login(s) orphelin(s) restant(s)')
    END as 'Resultat',
    COUNT(*) as 'Nombre'
FROM login l
LEFT JOIN collaborateur c ON l.COL_MATRICULE = c.COL_MATRICULE
WHERE c.COL_MATRICULE IS NULL;

-- Resultat attendu : OK - Aucun login orphelin | 0
-- OU : ATTENTION - 7 login(s) orphelin(s) restant(s) | 7
-- (selon si vous avez choisi de les supprimer ou non)


-- ================================================================================
-- VERIFICATION #3 : CONTRAINTE UNIQUE SUR LOG_LOGIN
-- ================================================================================

SELECT
    '3. CONTRAINTE UNIQUE' as 'Test',
    CASE
        WHEN COUNT(*) > 0 THEN 'OK - Contrainte ajoutee'
        ELSE 'ECHEC - Contrainte manquante'
    END as 'Resultat',
    CONSTRAINT_NAME as 'Nom_contrainte'
FROM information_schema.TABLE_CONSTRAINTS
WHERE TABLE_SCHEMA = 'gsbcr0'
  AND TABLE_NAME = 'login'
  AND CONSTRAINT_TYPE = 'UNIQUE'
  AND CONSTRAINT_NAME LIKE '%username%';

-- Resultat attendu : OK - Contrainte ajoutee | login_unique_username


-- ================================================================================
-- VERIFICATION #4 : PAS DE DOUBLONS DE LOGINS
-- ================================================================================

SELECT
    '4. DOUBLONS LOGINS' as 'Test',
    CASE
        WHEN COUNT(*) = 0 THEN 'OK - Aucun doublon'
        ELSE CONCAT('PROBLEME - ', COUNT(*), ' doublon(s) detecte(s)')
    END as 'Resultat',
    COUNT(*) as 'Nombre_doublons'
FROM (
    SELECT LOG_LOGIN, COUNT(*) as nb
    FROM login
    GROUP BY LOG_LOGIN
    HAVING COUNT(*) > 1
) as doublons;

-- Resultat attendu : OK - Aucun doublon | 0


-- ================================================================================
-- VERIFICATION #5 : PAS DE DOUBLONS DE MATRICULES
-- ================================================================================

SELECT
    '5. DOUBLONS MATRICULES' as 'Test',
    CASE
        WHEN COUNT(*) = 0 THEN 'OK - Aucun doublon'
        ELSE CONCAT('PROBLEME - ', COUNT(*), ' doublon(s) detecte(s)')
    END as 'Resultat',
    COUNT(*) as 'Nombre_doublons'
FROM (
    SELECT COL_MATRICULE, COUNT(*) as nb
    FROM collaborateur
    GROUP BY COL_MATRICULE
    HAVING COUNT(*) > 1
) as doublons;

-- Resultat attendu : OK - Aucun doublon | 0


-- ================================================================================
-- VERIFICATION #6 : COHERENCE LOGINS / COLLABORATEURS
-- ================================================================================

SELECT
    '6. COHERENCE LOGIN/COLLAB' as 'Test',
    CASE
        WHEN logins = collaborateurs THEN 'OK - Meme nombre'
        WHEN logins > collaborateurs THEN CONCAT('INFO - ', (logins - collaborateurs), ' logins en plus')
        ELSE CONCAT('PROBLEME - Moins de logins que de collaborateurs')
    END as 'Resultat',
    CONCAT(logins, ' logins / ', collaborateurs, ' collaborateurs') as 'Details'
FROM (
    SELECT
        (SELECT COUNT(*) FROM login) as logins,
        (SELECT COUNT(*) FROM collaborateur) as collaborateurs
) as counts;

-- Resultat attendu :
-- Si logins orphelins supprimes : OK - Meme nombre | 60 logins / 60 collaborateurs
-- Si logins orphelins gardes : INFO - 7 logins en plus | 67 logins / 60 collaborateurs


-- ================================================================================
-- VERIFICATION #7 : TOUS LES COLLABORATEURS ONT UN LOGIN
-- ================================================================================

SELECT
    '7. COLLABORATEURS SANS LOGIN' as 'Test',
    CASE
        WHEN COUNT(*) = 0 THEN 'OK - Tous les collaborateurs ont un login'
        ELSE CONCAT('PROBLEME - ', COUNT(*), ' collaborateur(s) sans login')
    END as 'Resultat',
    COUNT(*) as 'Nombre'
FROM collaborateur c
LEFT JOIN login l ON c.COL_MATRICULE = l.COL_MATRICULE
WHERE l.LOG_ID IS NULL;

-- Resultat attendu : OK - Tous les collaborateurs ont un login | 0


-- ================================================================================
-- VERIFICATION #8 : STRUCTURE DES CLES PRIMAIRES
-- ================================================================================

SELECT
    '8. CLES PRIMAIRES' as 'Test',
    CASE
        WHEN COUNT(*) >= 3 THEN 'OK - Toutes les cles primaires presentes'
        ELSE 'PROBLEME - Cles primaires manquantes'
    END as 'Resultat',
    GROUP_CONCAT(TABLE_NAME) as 'Tables_avec_PK'
FROM information_schema.TABLE_CONSTRAINTS
WHERE TABLE_SCHEMA = 'gsbcr0'
  AND TABLE_NAME IN ('login', 'collaborateur', 'medicament')
  AND CONSTRAINT_TYPE = 'PRIMARY KEY';

-- Resultat attendu : OK - Toutes les cles primaires presentes


-- ================================================================================
-- TABLEAU DE BORD COMPLET
-- ================================================================================

SELECT '========================================' as 'TABLEAU DE BORD FINAL'
UNION ALL
SELECT CONCAT('Date verification : ', NOW())
UNION ALL
SELECT '========================================'
UNION ALL
SELECT CONCAT('Nombre de collaborateurs : ', COUNT(*)) FROM collaborateur
UNION ALL
SELECT CONCAT('Nombre de logins : ', COUNT(*)) FROM login
UNION ALL
SELECT CONCAT('Nombre de medicaments : ', COUNT(*)) FROM medicament
UNION ALL
SELECT '========================================'
UNION ALL
SELECT CONCAT('Logins orphelins : ',
    (SELECT COUNT(*) FROM login l
     LEFT JOIN collaborateur c ON l.COL_MATRICULE = c.COL_MATRICULE
     WHERE c.COL_MATRICULE IS NULL))
UNION ALL
SELECT CONCAT('Collaborateurs sans login : ',
    (SELECT COUNT(*) FROM collaborateur c
     LEFT JOIN login l ON c.COL_MATRICULE = l.COL_MATRICULE
     WHERE l.LOG_ID IS NULL))
UNION ALL
SELECT '========================================'
UNION ALL
SELECT 'Toutes les verifications sont terminees'
UNION ALL
SELECT '========================================';


-- ================================================================================
-- LISTE DES CORRECTIONS APPLIQUEES (CHECKLIST)
-- ================================================================================

SELECT 'CHECKLIST DES CORRECTIONS' as 'Titre'
UNION ALL
SELECT '---------------------------------------'
UNION ALL
SELECT CONCAT('[',
    CASE WHEN (SELECT COL_CP FROM collaborateur WHERE COL_MATRICULE = 'n59') = '02000'
    THEN 'X' ELSE ' ' END,
    '] Code postal LAON corrige')
UNION ALL
SELECT CONCAT('[',
    CASE WHEN (SELECT COUNT(*) FROM login l
               LEFT JOIN collaborateur c ON l.COL_MATRICULE = c.COL_MATRICULE
               WHERE c.COL_MATRICULE IS NULL) = 0
    THEN 'X' ELSE ' ' END,
    '] Logins orphelins supprimes')
UNION ALL
SELECT CONCAT('[',
    CASE WHEN EXISTS (
        SELECT 1 FROM information_schema.TABLE_CONSTRAINTS
        WHERE TABLE_SCHEMA = 'gsbcr0'
          AND TABLE_NAME = 'login'
          AND CONSTRAINT_TYPE = 'UNIQUE'
          AND CONSTRAINT_NAME LIKE '%username%'
    ) THEN 'X' ELSE ' ' END,
    '] Contrainte UNIQUE ajoutee sur LOG_LOGIN')
UNION ALL
SELECT '---------------------------------------';


-- ================================================================================
-- EXPORT DES RESULTATS (optionnel)
-- ================================================================================

-- Pour sauvegarder les resultats de cette verification :
-- SELECT * FROM verification_finale INTO OUTFILE '/tmp/verification_finale.csv';


-- ================================================================================
-- FIN DE LA VERIFICATION
-- ================================================================================
-- Si tous les tests affichent "OK", les corrections sont reussies !
-- Si des tests affichent "ECHEC" ou "PROBLEME", il faut reappliquer
-- les corrections concernees.
-- ================================================================================
