-- ================================================================================
-- CORRECTION #1 : CODE POSTAL DE LAON
-- ================================================================================
-- Date : 03/11/2025
-- Probleme : Le collaborateur n59 (MAFFEZZOLI Thibaud) a un code postal incorrect
-- Ville : LAON (departement 02 - Aisne)
-- Code postal actuel : '2000'
-- Code postal correct : '02000'
-- ================================================================================

-- ETAPE 1 : Verification AVANT correction
-- ================================================================================
-- Cette requete affiche l'etat actuel du collaborateur

SELECT
    COL_MATRICULE as 'Matricule',
    COL_NOM as 'Nom',
    COL_PRENOM as 'Prenom',
    COL_VILLE as 'Ville',
    COL_CP as 'Code Postal ACTUEL (incorrect)'
FROM collaborateur
WHERE COL_MATRICULE = 'n59';

-- Resultat attendu AVANT correction :
-- n59 | MAFFEZZOLI | Thibaud | LAON | 2000


-- ================================================================================
-- ETAPE 2 : CORRECTION du code postal
-- ================================================================================
-- Cette requete modifie le code postal pour le mettre a 02000

UPDATE collaborateur
SET COL_CP = '02000'
WHERE COL_MATRICULE = 'n59';

-- Explication simple :
-- UPDATE = modifier
-- SET COL_CP = '02000' = changer le code postal a 02000
-- WHERE COL_MATRICULE = 'n59' = seulement pour le collaborateur n59


-- ================================================================================
-- ETAPE 3 : Verification APRES correction
-- ================================================================================
-- Cette requete verifie que le changement a bien ete effectue

SELECT
    COL_MATRICULE as 'Matricule',
    COL_NOM as 'Nom',
    COL_PRENOM as 'Prenom',
    COL_VILLE as 'Ville',
    COL_CP as 'Code Postal CORRIGE'
FROM collaborateur
WHERE COL_MATRICULE = 'n59';

-- Resultat attendu APRES correction :
-- n59 | MAFFEZZOLI | Thibaud | LAON | 02000


-- ================================================================================
-- ETAPE 4 : Verification globale des codes postaux de l'Aisne (02)
-- ================================================================================
-- Cette requete affiche tous les collaborateurs du departement 02

SELECT
    COL_MATRICULE,
    COL_NOM,
    COL_PRENOM,
    COL_VILLE,
    COL_CP,
    CASE
        WHEN LEFT(COL_CP, 2) = '02' THEN 'OK'
        WHEN COL_CP LIKE '2%' AND LENGTH(COL_CP) = 4 THEN 'ERREUR : manque un 0'
        ELSE 'A verifier'
    END as 'Statut'
FROM collaborateur
WHERE COL_VILLE IN ('LAON', 'SAINT QUENTIN') OR COL_CP LIKE '2%'
ORDER BY COL_CP;

-- Cette requete permet de voir s'il y a d'autres codes postaux incorrects
-- dans le departement de l'Aisne (02)


-- ================================================================================
-- FIN DE LA CORRECTION
-- ================================================================================
-- Nombre de lignes modifiees : 1
-- Impact : Correction cosmétique, pas d'impact sur le fonctionnement
-- Risque : Aucun
-- ================================================================================
