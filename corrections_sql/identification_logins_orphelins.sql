-- ================================================================================
-- IDENTIFICATION DES LOGINS ORPHELINS
-- ================================================================================
-- Date : 03/11/2025
-- Probleme : Il y a 67 logins mais seulement 60 collaborateurs
-- Objectif : Trouver les 7 logins qui n'ont PAS de collaborateur associe
-- ================================================================================

-- QU'EST-CE QU'UN LOGIN ORPHELIN ?
-- C'est un compte de connexion (dans la table login) qui n'a pas de
-- collaborateur correspondant (dans la table collaborateur).
-- Ces comptes ne devraient pas exister car ils permettraient a quelqu'un
-- de se connecter sans etre un employe.


-- ================================================================================
-- ETAPE 1 : Compter les logins et les collaborateurs
-- ================================================================================

SELECT 'Nombre de logins' as 'Type', COUNT(*) as 'Total'
FROM login
UNION ALL
SELECT 'Nombre de collaborateurs' as 'Type', COUNT(*) as 'Total'
FROM collaborateur
UNION ALL
SELECT 'Difference (logins orphelins)' as 'Type',
       (SELECT COUNT(*) FROM login) - (SELECT COUNT(*) FROM collaborateur) as 'Total';

-- Resultat attendu :
-- Nombre de logins : 67
-- Nombre de collaborateurs : 60
-- Difference : 7


-- ================================================================================
-- ETAPE 2 : IDENTIFIER LES LOGINS ORPHELINS
-- ================================================================================
-- Cette requete trouve tous les logins qui N'ONT PAS de collaborateur

SELECT
    l.LOG_ID as 'ID Login',
    l.LOG_LOGIN as 'Identifiant',
    l.COL_MATRICULE as 'Matricule reference',
    CASE
        WHEN c.COL_MATRICULE IS NULL THEN 'ORPHELIN - Pas de collaborateur'
        ELSE 'OK - Collaborateur existe'
    END as 'Statut'
FROM login l
LEFT JOIN collaborateur c ON l.COL_MATRICULE = c.COL_MATRICULE
WHERE c.COL_MATRICULE IS NULL
ORDER BY l.LOG_ID;

-- Explication de la requete :
-- LEFT JOIN = Garder tous les logins, meme s'ils n'ont pas de collaborateur
-- WHERE c.COL_MATRICULE IS NULL = Filtrer seulement ceux SANS collaborateur
-- Resultat : La liste des 7 logins orphelins


-- ================================================================================
-- ETAPE 3 : Details complets des logins orphelins
-- ================================================================================
-- Cette requete affiche tous les details des logins orphelins

SELECT
    l.LOG_ID,
    l.LOG_LOGIN,
    l.COL_MATRICULE as 'Matricule_Invalide',
    'Ce matricule n\'existe pas dans la table collaborateur' as 'Probleme'
FROM login l
LEFT JOIN collaborateur c ON l.COL_MATRICULE = c.COL_MATRICULE
WHERE c.COL_MATRICULE IS NULL
ORDER BY l.LOG_ID;


-- ================================================================================
-- ETAPE 4 : Verification inverse - Collaborateurs sans login
-- ================================================================================
-- Par securite, on verifie aussi s'il y a des collaborateurs SANS login

SELECT
    c.COL_MATRICULE,
    c.COL_NOM,
    c.COL_PRENOM,
    CASE
        WHEN l.LOG_ID IS NULL THEN 'SANS LOGIN'
        ELSE 'OK - A un login'
    END as 'Statut'
FROM collaborateur c
LEFT JOIN login l ON c.COL_MATRICULE = l.COL_MATRICULE
WHERE l.LOG_ID IS NULL;

-- Resultat attendu : Aucun (tous les collaborateurs doivent avoir un login)


-- ================================================================================
-- ETAPE 5 : Vue d'ensemble de la coherence
-- ================================================================================

SELECT
    'Logins avec collaborateur' as 'Categorie',
    COUNT(*) as 'Nombre'
FROM login l
INNER JOIN collaborateur c ON l.COL_MATRICULE = c.COL_MATRICULE
UNION ALL
SELECT
    'Logins SANS collaborateur (orphelins)' as 'Categorie',
    COUNT(*) as 'Nombre'
FROM login l
LEFT JOIN collaborateur c ON l.COL_MATRICULE = c.COL_MATRICULE
WHERE c.COL_MATRICULE IS NULL
UNION ALL
SELECT
    'Collaborateurs SANS login' as 'Categorie',
    COUNT(*) as 'Nombre'
FROM collaborateur c
LEFT JOIN login l ON c.COL_MATRICULE = l.COL_MATRICULE
WHERE l.LOG_ID IS NULL;

-- Resultat attendu :
-- Logins avec collaborateur : 60
-- Logins SANS collaborateur : 7
-- Collaborateurs SANS login : 0


-- ================================================================================
-- DECISION A PRENDRE
-- ================================================================================
-- Une fois les 7 logins orphelins identifies, il faut decider :
--
-- OPTION 1 : SUPPRIMER ces logins
-- Raison : Ils ne servent a rien et representent un risque de securite
-- Fichier : correction_supprimer_logins_orphelins.sql (a creer apres)
--
-- OPTION 2 : LES GARDER
-- Raison : Ce sont peut-etre d'anciens employes qu'on veut garder
-- dans l'historique
-- Solution : Ajouter un champ 'actif' pour les desactiver
--
-- RECOMMANDATION : OPTION 1 (supprimer)
-- ================================================================================


-- ================================================================================
-- NOTES IMPORTANTES
-- ================================================================================
-- 1. Ne PAS executer de DELETE dans ce fichier
-- 2. Ce fichier sert uniquement a IDENTIFIER les logins orphelins
-- 3. La suppression se fera dans un fichier separe apres validation
-- ================================================================================
