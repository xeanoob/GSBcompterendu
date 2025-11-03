-- ================================================================================
-- CORRECTION #3 : AJOUTER CONTRAINTE UNIQUE SUR LOG_LOGIN
-- ================================================================================
-- Date : 03/11/2025
-- Probleme : Pas de contrainte UNIQUE sur le champ LOG_LOGIN
-- Risque : On pourrait creer 2 logins identiques (ex: deux fois "villou")
-- Solution : Ajouter une contrainte UNIQUE pour empecher les doublons
-- ================================================================================

-- POURQUOI C'EST IMPORTANT ?
-- Sans contrainte UNIQUE, la base de donnees accepterait ceci :
-- INSERT INTO login VALUES (68, 'villou', 'mdp123', 'x999');
-- -> On aurait alors 2 logins "villou" !
-- -> Probleme lors de la connexion : lequel utiliser ?
-- -> Faille de securite


-- ================================================================================
-- ETAPE 1 : VERIFICATION AVANT - Verifier qu'il n'y a pas deja de doublons
-- ================================================================================
-- IMPORTANT : Si des doublons existent, la contrainte UNIQUE ne pourra pas
-- etre ajoutee. Il faudra d'abord supprimer les doublons.

SELECT
    LOG_LOGIN as 'Login',
    COUNT(*) as 'Nombre_occurrences'
FROM login
GROUP BY LOG_LOGIN
HAVING COUNT(*) > 1;

-- Resultat attendu : AUCUNE LIGNE (pas de doublons)
-- Si des lignes s'affichent : il y a des doublons, il faut les corriger d'abord


-- ================================================================================
-- ETAPE 2 : Afficher les contraintes actuelles de la table login
-- ================================================================================

SHOW CREATE TABLE login;

-- Cette commande affiche la structure complete de la table
-- On doit voir :
-- - PRIMARY KEY sur LOG_ID
-- - UNIQUE KEY sur COL_MATRICULE
-- - Mais PAS de UNIQUE sur LOG_LOGIN (c'est ce qu'on va ajouter)


-- ================================================================================
-- ETAPE 3 : AJOUTER LA CONTRAINTE UNIQUE sur LOG_LOGIN
-- ================================================================================

ALTER TABLE login
ADD CONSTRAINT login_unique_username UNIQUE (LOG_LOGIN);

-- Explication simple :
-- ALTER TABLE login = Modifier la table login
-- ADD CONSTRAINT = Ajouter une regle
-- login_unique_username = Nom de la regle (on peut le choisir)
-- UNIQUE (LOG_LOGIN) = Le champ LOG_LOGIN doit etre unique

-- A partir de maintenant :
-- - Chaque LOG_LOGIN doit etre different
-- - Impossible de creer 2 logins identiques
-- - La base renvoie une erreur si on essaye


-- ================================================================================
-- ETAPE 4 : VERIFICATION APRES - Verifier que la contrainte a ete ajoutee
-- ================================================================================

SHOW CREATE TABLE login;

-- Cette fois, on doit voir :
-- UNIQUE KEY `login_unique_username` (`LOG_LOGIN`)

-- OU utiliser cette requete pour voir toutes les contraintes :

SELECT
    CONSTRAINT_NAME as 'Nom_Contrainte',
    CONSTRAINT_TYPE as 'Type'
FROM information_schema.TABLE_CONSTRAINTS
WHERE TABLE_SCHEMA = 'gsbcr0'
  AND TABLE_NAME = 'login';

-- Resultat attendu :
-- PRIMARY sur LOG_ID
-- UNIQUE sur COL_MATRICULE
-- UNIQUE sur LOG_LOGIN (nouvelle contrainte)


-- ================================================================================
-- ETAPE 5 : TEST - Essayer de creer un doublon (doit echouer)
-- ================================================================================

-- Test 1 : Essayer d'inserer un login qui existe deja
-- Cette requete DOIT echouer avec une erreur

-- INSERT INTO login VALUES (999, 'villou', 'test123', 'test999');

-- Erreur attendue :
-- Duplicate entry 'villou' for key 'login_unique_username'

-- IMPORTANT : Cette ligne est en commentaire pour eviter l'erreur
-- Vous pouvez la decommenter pour tester


-- Test 2 : Inserer un login unique (doit fonctionner)
-- Cette requete doit marcher car 'testlogin' n'existe pas

-- INSERT INTO login VALUES (999, 'testlogin', 'test123', 'test999');

-- Puis supprimer le test :
-- DELETE FROM login WHERE LOG_ID = 999;

-- IMPORTANT : Ces lignes sont en commentaire, c'est juste pour l'exemple


-- ================================================================================
-- ETAPE 6 : Verification globale de l'integrite
-- ================================================================================

SELECT
    'Total logins' as 'Verification',
    COUNT(*) as 'Nombre'
FROM login
UNION ALL
SELECT
    'Logins uniques' as 'Verification',
    COUNT(DISTINCT LOG_LOGIN) as 'Nombre'
FROM login;

-- Les deux nombres doivent etre IDENTIQUES
-- Si Total logins = 67 et Logins uniques = 67 -> OK
-- Si differents -> Il y a des doublons (probleme)


-- ================================================================================
-- IMPACT DE CETTE MODIFICATION
-- ================================================================================
-- POSITIF :
-- + Securite renforcee contre les doublons de login
-- + Respect des bonnes pratiques SQL
-- + Integrite des donnees garantie
-- + Aucun impact sur les donnees existantes
--
-- NEGATIF :
-- - Aucun
--
-- RISQUE :
-- - Aucun (si pas de doublons existants)
--
-- REVERSIBILITE :
-- Pour supprimer la contrainte si besoin :
-- ALTER TABLE login DROP INDEX login_unique_username;
-- ================================================================================


-- ================================================================================
-- RESUME
-- ================================================================================
-- Cette modification ajoute une protection supplementaire a la table login.
-- Elle empeche la creation de logins en double.
-- C'est une bonne pratique de securite.
-- Aucun risque si executee correctement.
-- ================================================================================
