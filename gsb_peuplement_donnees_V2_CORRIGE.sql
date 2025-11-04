-- ============================================================================
-- SCRIPT DE PEUPLEMENT V2 - BASE DE DONNÉES GSB (CORRIGÉ)
-- ============================================================================
-- Ce script ajoute UNIQUEMENT les données de test manquantes
-- Il respecte EXACTEMENT la structure réelle de la base
-- Les données déjà présentes ne sont PAS réinsérées
-- ============================================================================

USE gsbv0v3;

-- ============================================================================
-- TABLES DÉJÀ PEUPLÉES (NE PAS TOUCHER)
-- ============================================================================
-- ✅ habilitation (3 lignes déjà présentes)
-- ✅ dosage (10 lignes déjà présentes : 1=500mg, 2=1000mg, etc.)
-- ✅ collaborateur (58 lignes)
-- ✅ region (18 lignes)
-- ✅ departement (101 lignes)
-- ✅ secteur (4 lignes)
-- ✅ specialite (46 lignes)
-- ✅ type_praticien (5 lignes)
-- ✅ type_individu (5 lignes)
-- ✅ Quelques rapports de visite existants (4 lignes)

-- ============================================================================
-- 1. TABLES DE RÉFÉRENCE À COMPLÉTER
-- ============================================================================

-- ETAT (pour les rapports de visite)
INSERT INTO `etat` (`ETAT_CODE`, `ETAT_LIBELLE`) VALUES
(1, 'En cours de saisie'),
(2, 'Saisie définitive - Validé'),
(3, 'Consulté par le délégué')
ON DUPLICATE KEY UPDATE ETAT_LIBELLE = VALUES(ETAT_LIBELLE);

-- MOTIF_VISITE
INSERT INTO `motif_visite` (`MOT_CODE`, `MOT_LIBELLE`) VALUES
(1, 'Périodicité (visite tous les 6-8 mois)'),
(2, 'Nouveauté produit'),
(3, 'Actualisation législation'),
(4, 'Remontage (baisse de prescription)'),
(5, 'Sollicitation du praticien'),
(6, 'Autre')
ON DUPLICATE KEY UPDATE MOT_LIBELLE = VALUES(MOT_LIBELLE);

-- TYPE_FRAIS
INSERT INTO `type_frais` (`TF_CODE`, `TF_LIBELLE`, `TF_FORFAIT`) VALUES
(1, 'Repas Restaurant', 25.00),
(2, 'Nuitée Hôtel', 80.00),
(3, 'Forfait Étape', 110.00),
(4, 'Indemnité Kilométrique', 0.62)
ON DUPLICATE KEY UPDATE TF_LIBELLE = VALUES(TF_LIBELLE);

-- FAMILLE (famille de médicaments)
INSERT INTO `famille` (`FAM_CODE`, `FAM_LIBELLE`) VALUES
('AA', 'Antalgique'),
('AB', 'Antibiotique'),
('AC', 'Anticoagulant'),
('AD', 'Antidépresseur'),
('AH', 'Antihistaminique'),
('AI', 'Anti-inflammatoire'),
('AN', 'Anesthésique'),
('AP', 'Antipsychotique'),
('AS', 'Antispasmodique'),
('AV', 'Antiviral'),
('CA', 'Cardiovasculaire'),
('DI', 'Diurétique'),
('HO', 'Hormonal'),
('HY', 'Hypnotique'),
('IM', 'Immunosuppresseur'),
('VA', 'Vaccin')
ON DUPLICATE KEY UPDATE FAM_LIBELLE = VALUES(FAM_LIBELLE);

-- PRESENTATION
INSERT INTO `presentation` (`PRE_CODE`, `PRE_LIBELLE`) VALUES
('CO', 'Comprimé'),
('GE', 'Gélule'),
('SI', 'Sirop'),
('IN', 'Injectable'),
('CR', 'Crème'),
('PO', 'Pommade'),
('SU', 'Suppositoire'),
('SP', 'Spray'),
('CL', 'Collyre'),
('SA', 'Sachet'),
('PL', 'Plaquette'),
('FL', 'Flacon')
ON DUPLICATE KEY UPDATE PRE_LIBELLE = VALUES(PRE_LIBELLE);

-- ============================================================================
-- 2. MÉDICAMENTS
-- ============================================================================

INSERT INTO `medicament` (`MED_DEPOTLEGAL`, `MED_NOMCOMMERCIAL`, `MED_COMPOSITION`, `MED_EFFETS`, `MED_CONTREINDIC`, `MED_PRIXECHANTILLON`, `FAM_CODE`) VALUES
('DOLIP001', 'DOLIPRANE', 'Paracétamol', 'Antalgique et antipyrétique. Traite la douleur et la fièvre.', 'Insuffisance hépatique sévère. Allergie au paracétamol.', 3.50, 'AA'),
('AMOX001', 'AMOXICILLINE', 'Amoxicilline trihydrate', 'Antibiotique de la famille des pénicillines. Traite les infections bactériennes.', 'Allergie aux pénicillines. Mononucléose infectieuse.', 8.20, 'AB'),
('DOLIP002', 'EFFERALGAN', 'Paracétamol', 'Antalgique et antipyrétique. Forme effervescente.', 'Insuffisance hépatique. Phénylcétonurie pour forme aspartam.', 4.10, 'AA'),
('IBUP001', 'ADVIL', 'Ibuprofène', 'Anti-inflammatoire non stéroïdien. Traite douleur, inflammation et fièvre.', 'Ulcère gastrique évolutif. Insuffisance cardiaque sévère. Grossesse 3ème trimestre.', 5.80, 'AI'),
('ATOR001', 'TAHOR', 'Atorvastatine', 'Hypocholestérolémiant. Réduit le cholestérol et prévient les maladies cardiovasculaires.', 'Maladie hépatique évolutive. Grossesse et allaitement.', 12.50, 'CA'),
('ESOMEP01', 'INEXIUM', 'Ésoméprazole', 'Inhibiteur de la pompe à protons. Traite le reflux gastro-œsophagien et les ulcères.', 'Allergie à l\'ésoméprazole. Association avec le nelfinavir.', 9.30, 'AS'),
('LEVOT001', 'LEVOTHYROX', 'Lévothyroxine sodique', 'Hormone thyroïdienne de synthèse. Traite l\'hypothyroïdie.', 'Hyperthyroïdie non traitée. Insuffisance surrénale non traitée.', 6.70, 'HO'),
('SEROP001', 'SEROPLEX', 'Escitalopram', 'Antidépresseur inhibiteur sélectif de la recapture de la sérotonine.', 'Association avec IMAO. Allergie à l\'escitalopram. Allongement QT.', 15.40, 'AD'),
('DOLIP003', 'DAFALGAN', 'Paracétamol', 'Antalgique et antipyrétique. Traite les douleurs modérées.', 'Insuffisance hépatocellulaire. Allergie au paracétamol.', 3.80, 'AA'),
('CERTI001', 'ZYRTEC', 'Cétirizine', 'Antihistaminique H1. Traite les allergies (rhinite, urticaire).', 'Insuffisance rénale sévère. Allergie à la cétirizine ou hydroxyzine.', 7.90, 'AH'),
('AZITH001', 'ZITHROMAX', 'Azithromycine', 'Antibiotique de la famille des macrolides.', 'Allergie aux macrolides. Insuffisance hépatique sévère.', 10.20, 'AB'),
('PANTE001', 'EUPANTOL', 'Pantoprazole', 'Inhibiteur de la pompe à protons. Traite les troubles gastriques.', 'Allergie au pantoprazole. Association certains antiviraux.', 8.60, 'AS'),
('METFO001', 'GLUCOPHAGE', 'Metformine', 'Antidiabétique oral. Traite le diabète de type 2.', 'Acidocétose diabétique. Insuffisance rénale sévère. Insuffisance cardiaque.', 11.30, 'HO'),
('LORAZE01', 'TEMESTA', 'Lorazépam', 'Anxiolytique benzodiazépine. Traite l\'anxiété et les troubles du sommeil.', 'Insuffisance respiratoire sévère. Myasthénie. Grossesse 1er trimestre.', 6.50, 'HY'),
('VENTOL01', 'VENTOLINE', 'Salbutamol', 'Bronchodilatateur bêta-2 mimétique. Traite l\'asthme et la BPCO.', 'Allergie au salbutamol. Précaution en cas de troubles cardiovasculaires.', 9.80, 'AV')
ON DUPLICATE KEY UPDATE MED_NOMCOMMERCIAL = VALUES(MED_NOMCOMMERCIAL);

-- ============================================================================
-- 3. FORMULER (Médicament + Présentation)
-- ============================================================================

INSERT INTO `formuler` (`MED_DEPOTLEGAL`, `PRE_CODE`) VALUES
('DOLIP001', 'CO'),
('DOLIP001', 'SA'),
('AMOX001', 'GE'),
('AMOX001', 'SI'),
('DOLIP002', 'CO'),
('IBUP001', 'CO'),
('IBUP001', 'GE'),
('ATOR001', 'CO'),
('ESOMEP01', 'GE'),
('LEVOT001', 'CO'),
('SEROP001', 'CO'),
('DOLIP003', 'CO'),
('CERTI001', 'CO'),
('CERTI001', 'SI'),
('AZITH001', 'CO'),
('AZITH001', 'SI'),
('PANTE001', 'CO'),
('METFO001', 'CO'),
('LORAZE01', 'CO'),
('VENTOL01', 'SP')
ON DUPLICATE KEY UPDATE MED_DEPOTLEGAL = VALUES(MED_DEPOTLEGAL);

-- ============================================================================
-- 4. INTERAGIR (Interactions médicamenteuses)
-- ============================================================================

INSERT INTO `interagir` (`MED_DEPOTLEGAL`, `MED_DEPOTLEGAL2`) VALUES
('IBUP001', 'ATOR001'),
('SEROP001', 'LORAZE01'),
('METFO001', 'IBUP001'),
('AMOX001', 'METFO001')
ON DUPLICATE KEY UPDATE MED_DEPOTLEGAL = VALUES(MED_DEPOTLEGAL);

-- ============================================================================
-- 5. PRESCRIRE (Posologie - utilise les dosages EXISTANTS 1-10)
-- ============================================================================

-- Note : Les dosages 1-10 existent déjà dans la base
-- Dosage 1 = 500 (unité 1)
-- Dosage 2 = 1000 (unité 1)
-- Dosage 3 = 250 (unité 1)
-- Dosage 4 = 5 (unité 2)
-- Dosage 5 = 10 (unité 2)
-- Dosage 6 = 100 (unité 3)
-- Dosage 7 = 50 (unité 1)
-- Dosage 8 = 2.5 (unité 1)
-- Dosage 9 = 20 (unité 1)

INSERT INTO `prescrire` (`MED_DEPOTLEGAL`, `DOS_CODE`, `TIN_Code`, `PRE_POSOLOGIE`) VALUES
-- DOLIPRANE (500mg)
('DOLIP001', 1, 1, '1 comprimé 3 fois par jour, maximum 4g/jour'),
('DOLIP001', 3, 2, '1 comprimé 3 fois par jour, maximum 3g/jour'),
('DOLIP001', 1, 4, '1 comprimé 2-3 fois par jour selon fonction rénale'),

-- AMOXICILLINE (500mg)
('AMOX001', 1, 1, '1 gélule 3 fois par jour pendant 7 jours'),
('AMOX001', 3, 2, '1 gélule 3 fois par jour pendant 7 jours'),

-- ADVIL
('IBUP001', 9, 1, '1 comprimé 3 fois par jour au cours des repas'),
('IBUP001', 5, 2, '1 comprimé 2-3 fois par jour'),

-- TAHOR
('ATOR001', 5, 1, '1 comprimé par jour le soir'),

-- INEXIUM
('ESOMEP01', 9, 1, '1 gélule par jour le matin à jeun'),

-- LEVOTHYROX
('LEVOT001', 7, 1, '1 comprimé par jour le matin à jeun'),

-- SEROPLEX
('SEROP001', 5, 1, '1 comprimé par jour, dose progressive'),

-- ZYRTEC
('CERTI001', 5, 1, '1 comprimé par jour'),
('CERTI001', 4, 2, '1 comprimé par jour'),

-- ZITHROMAX
('AZITH001', 1, 1, '1 comprimé par jour pendant 3 jours'),

-- GLUCOPHAGE
('METFO001', 1, 1, '1 comprimé 2-3 fois par jour au cours des repas'),

-- TEMESTA
('LORAZE01', 4, 1, '1 comprimé 1-2 fois par jour selon anxiété'),

-- VENTOLINE
('VENTOL01', 4, 1, '1-2 bouffées si besoin, maximum 8/jour')
ON DUPLICATE KEY UPDATE PRE_POSOLOGIE = VALUES(PRE_POSOLOGIE);

-- ============================================================================
-- 6. PRATICIENS
-- ============================================================================

INSERT INTO `praticien` (`PRA_NUM`, `PRA_NOM`, `PRA_PRENOM`, `PRA_ADRESSE`, `PRA_CP`, `PRA_VILLE`, `PRA_COEFNOTORIETE`, `TYP_CODE`) VALUES
(101, 'MARTIN', 'Jean', '15 rue de la Santé', '75014', 'PARIS', 8.5, 'MV'),
(102, 'BERNARD', 'Sophie', '23 avenue des Hôpitaux', '69003', 'LYON', 9.2, 'MH'),
(103, 'DUBOIS', 'Pierre', '8 place du Marché', '33000', 'BORDEAUX', 7.8, 'MV'),
(104, 'THOMAS', 'Marie', '45 boulevard Pasteur', '59000', 'LILLE', 8.9, 'MH'),
(105, 'ROBERT', 'Alain', '12 rue Voltaire', '44000', 'NANTES', 7.5, 'MV'),
(106, 'PETIT', 'Isabelle', '67 avenue de la République', '13008', 'MARSEILLE', 9.0, 'PH'),
(107, 'DURAND', 'François', '34 rue des Lilas', '67000', 'STRASBOURG', 8.2, 'MV'),
(108, 'LEROY', 'Catherine', 'CHU Service Cardiologie', '31000', 'TOULOUSE', 9.5, 'MH'),
(109, 'MOREAU', 'Laurent', '19 place Saint-Pierre', '35000', 'RENNES', 7.9, 'MV'),
(110, 'SIMON', 'Nathalie', '56 rue Nationale', '49000', 'ANGERS', 8.1, 'PO'),
(111, 'LAURENT', 'Michel', '28 cours Lafayette', '69006', 'LYON', 8.7, 'MV'),
(112, 'LEFEBVRE', 'Sylvie', 'Pharmacie Centrale', '75015', 'PARIS', 8.4, 'PO'),
(113, 'MICHEL', 'David', '91 rue du Faubourg', '54000', 'NANCY', 7.6, 'MV'),
(114, 'GARCIA', 'Anne', 'Cabinet Médical du Centre', '21000', 'DIJON', 8.0, 'MV'),
(115, 'DAVID', 'Christine', 'CHR Service Pédiatrie', '45000', 'ORLÉANS', 9.1, 'MH')
ON DUPLICATE KEY UPDATE PRA_NOM = VALUES(PRA_NOM);

-- ============================================================================
-- 7. POSSEDER (Spécialités des praticiens)
-- ============================================================================

INSERT INTO `posseder` (`SPE_CODE`, `PRA_NUM`, `POS_DIPLOME`, `POS_COEFPRESCRIPTION`) VALUES
('CAC', 101, 'DES', 8.5),
('PME', 102, 'DES', 9.0),
('GMO', 103, 'DU', 7.5),
('CAC', 104, 'DES', 8.8),
('GEH', 105, 'DES', 7.8),
('BM', 106, 'Pharmacien', 8.5),
('NRL', 107, 'DES', 8.0),
('CAC', 108, 'PU-PH', 9.5),
('ODM', 109, 'CES', 7.9),
('BM', 110, 'Pharmacien', 8.0),
('RHU', 111, 'DES', 8.5),
('BM', 112, 'Pharmacien', 8.3),
('DV', 113, 'DES', 7.6),
('PME', 114, 'DES', 8.0),
('PME', 115, 'PU-PH', 9.2)
ON DUPLICATE KEY UPDATE POS_DIPLOME = VALUES(POS_DIPLOME);

-- ============================================================================
-- 8. LOGIN (avec LOG_ID et LOG_MOTDEPASSE)
-- ============================================================================

-- Mot de passe par défaut : 'gsb2024' (à hasher en production)
INSERT INTO `login` (`LOG_ID`, `LOG_LOGIN`, `LOG_MOTDEPASSE`, `COL_MATRICULE`) VALUES
(1, 'a131', 'gsb2024', 'a131'),
(2, 'a17', 'gsb2024', 'a17'),
(3, 'b16', 'gsb2024', 'b16'),
(4, 'd13', 'gsb2024', 'd13'),
(5, 'e39', 'gsb2024', 'e39'),
(6, 'f39', 'gsb2024', 'f39'),
(7, 'g19', 'gsb2024', 'g19'),
(8, 'j50', 'gsb2024', 'j50'),
(9, 'r58', 'gsb2024', 'r58')
ON DUPLICATE KEY UPDATE LOG_MOTDEPASSE = VALUES(LOG_MOTDEPASSE);

-- ============================================================================
-- 9. TRAVAILLER (affectations collaborateurs/régions)
-- ============================================================================

INSERT INTO `travailler` (`VIS_MATRICULE`, `REG_CODE`, `TRA_JJMMAA`, `TRA_ROLE`) VALUES
('a131', 'BG', '2020-01-15', 'Visiteur'),
('a17', 'RA', '2019-06-01', 'Visiteur'),
('b16', 'BG', '2021-03-10', 'Délégué Régional'),
('d13', 'PL', '2018-09-20', 'Visiteur'),
('e39', 'IF', '2017-11-05', 'Visiteur'),
('f39', 'RA', '2020-02-14', 'Visiteur'),
('g19', 'IF', '2019-07-30', 'Délégué Régional'),
('j50', 'HF', '2018-04-12', 'Visiteur'),
('r58', 'BG', '2021-08-25', 'Visiteur')
ON DUPLICATE KEY UPDATE TRA_ROLE = VALUES(TRA_ROLE);

-- ============================================================================
-- 10. RAPPORT_VISITE (avec RAP_DATEVISITE, sans RAP_DATESAISIE ni RAP_COEFCONFIANCE)
-- ============================================================================

-- Note : Il existe déjà 4 rapports (numéros 1-3 pour a131, 1 pour a17)
-- On commence à partir du numéro 4 pour a131 et 2 pour les autres

INSERT INTO `rapport_visite` (`VIS_MATRICULE`, `RAP_NUM`, `RAP_DATEVISITE`, `RAP_BILAN`, `RAP_MOTIF`, `ETAT_CODE`, `MED_DEPOTLEGAL1`, `MED_DEPOTLEGAL2`, `MOT_CODE`, `PRA_NUM`) VALUES
-- Rapports visiteur a131 (Villechalane Louis)
('a131', 4, '2024-01-15', 'Très bonne réception du praticien. Discussion approfondie sur les nouvelles molécules antalgiques. Le Dr Martin montre un vif intérêt pour DOLIPRANE. Visite prévue dans 6 mois.', NULL, 2, 'DOLIP001', 'IBUP001', 2, 101),
('a131', 5, '2024-02-20', 'Praticien très occupé mais à l\'écoute. Présentation rapide d\'AMOXICILLINE. Échantillons bien accueillis. À revoir prochainement.', NULL, 2, 'AMOX001', NULL, 1, 103),
('a131', 6, '2024-03-10', 'Excellente visite. Le Dr Petit est prescripteur régulier. Discussion sur les antihistaminiques. Coefficient de confiance en hausse.', NULL, 3, 'CERTI001', NULL, 3, 106),

-- Rapports visiteur a17 (Andre David)
('a17', 2, '2024-01-22', 'Premier contact avec ce praticien. Présentation générale des produits cardiovasculaires. Intérêt modéré mais porte ouverte pour futures visites.', NULL, 2, 'ATOR001', NULL, 1, 108),
('a17', 3, '2024-02-18', 'Visite de suivi. Le Dr Leroy apprécie TAHOR. Discussion sur les interactions médicamenteuses. Demande documentation complémentaire.', NULL, 2, 'ATOR001', 'METFO001', 1, 108),

-- Rapports visiteur b16 (Bioret Luc - Délégué)
('b16', 1, '2024-01-25', 'Visite en tant que délégué. Présentation de la stratégie commerciale 2024. Le Dr Moreau adhère à notre approche. Excellente collaboration.', NULL, 2, 'LEVOT001', NULL, 2, 109),

-- Rapports visiteur d13 (Debelle Jeanne)
('d13', 1, '2024-01-30', 'Visite axée sur les troubles digestifs. Présentation INEXIUM et EUPANTOL. Le Dr Robert est intéressé par les IPP de dernière génération.', NULL, 2, 'ESOMEP01', 'PANTE001', 2, 105),
('d13', 2, '2024-02-25', 'Rapport en cours de saisie. Visite ce matin, finalisation en attente.', NULL, 1, 'DOLIP001', NULL, 1, 109),

-- Rapports visiteur e39 (Dudouit Frederic)
('e39', 1, '2024-02-05', 'Visite à la pharmacie. Présentation gamme complète. Mme Lefebvre commande régulièrement. Excellente relation commerciale.', NULL, 3, 'DOLIP001', 'CERTI001', 1, 112),
('e39', 2, '2024-03-01', 'Le Dr Martin prescrit peu nos produits cardiovasculaires. Travail de persuasion nécessaire. Échantillons distribués.', NULL, 2, 'ATOR001', NULL, 4, 101),

-- Rapports visiteur f39 (Fremont Fernande)
('f39', 1, '2024-02-10', 'Très bonne visite au CHU. Le Dr Bernard est enthousiaste sur les nouveaux antibiotiques. Demande essais cliniques.', NULL, 2, 'AZITH001', 'AMOX001', 2, 102),
('f39', 2, '2024-03-05', 'Visite au Dr Laurent. Discussion sur les anti-inflammatoires. Prescription régulière d\'ADVIL. Relation de confiance établie.', NULL, 2, 'IBUP001', NULL, 1, 111),

-- Rapports visiteur g19 (Gheysen Galassus - Délégué)
('g19', 1, '2024-02-12', 'Réunion avec le Dr Martin pour faire le point sur les prescriptions du trimestre. Analyse des retours patients. Très constructif.', NULL, 3, NULL, NULL, 1, 101),

-- Rapports visiteur j50 (Landre Philippe)
('j50', 1, '2024-02-15', 'Première visite au CHU de Lille. Le Dr Thomas travaille en cardiologie. Intérêt marqué pour les statines. Prescription probable.', NULL, 2, 'ATOR001', NULL, 1, 104),
('j50', 2, '2024-03-08', 'Le Dr Thomas a sollicité cette visite suite à un cas clinique complexe. Discussion approfondie. Excellente collaboration.', NULL, 2, 'ATOR001', 'ESOMEP01', 5, 104),

-- Rapports visiteur r58 (Quiquandon Joel)
('r58', 1, '2024-02-20', 'Visite de routine. Le Dr Moreau prescrit régulièrement. RAS. Prochaine visite dans 6 mois.', NULL, 2, 'DOLIP001', NULL, 1, 109),
('r58', 2, '2024-03-12', 'Présentation VENTOLINE. Le Dr Moreau travaille avec des asthmatiques. Très intéressé. Commande d\'échantillons.', NULL, 1, 'VENTOL01', NULL, 2, 109)
ON DUPLICATE KEY UPDATE RAP_BILAN = VALUES(RAP_BILAN);

-- ============================================================================
-- 11. OFFRIR (échantillons offerts lors des visites)
-- ============================================================================

INSERT INTO `offrir` (`VIS_MATRICULE`, `RAP_NUM`, `MED_DEPOTLEGAL`, `OFF_QTE`) VALUES
-- Rapport 4 de a131
('a131', 4, 'DOLIP001', 20),
('a131', 4, 'IBUP001', 15),

-- Rapport 5 de a131
('a131', 5, 'AMOX001', 10),

-- Rapport 6 de a131
('a131', 6, 'CERTI001', 12),

-- Rapport 2 de a17
('a17', 2, 'ATOR001', 8),

-- Rapport 3 de a17
('a17', 3, 'ATOR001', 10),
('a17', 3, 'METFO001', 5),

-- Rapport 1 de b16
('b16', 1, 'LEVOT001', 15),

-- Rapport 1 de d13
('d13', 1, 'ESOMEP01', 12),
('d13', 1, 'PANTE001', 10),

-- Rapport 2 de d13
('d13', 2, 'DOLIP001', 18),

-- Rapport 1 de e39
('e39', 1, 'DOLIP001', 25),
('e39', 1, 'CERTI001', 20),

-- Rapport 2 de e39
('e39', 2, 'ATOR001', 10),

-- Rapport 1 de f39
('f39', 1, 'AZITH001', 8),
('f39', 1, 'AMOX001', 12),

-- Rapport 2 de f39
('f39', 2, 'IBUP001', 15),

-- Rapport 1 de j50
('j50', 1, 'ATOR001', 10),

-- Rapport 2 de j50
('j50', 2, 'ATOR001', 8),
('j50', 2, 'ESOMEP01', 6),

-- Rapport 1 de r58
('r58', 1, 'DOLIP001', 20),

-- Rapport 2 de r58
('r58', 2, 'VENTOL01', 5)
ON DUPLICATE KEY UPDATE OFF_QTE = VALUES(OFF_QTE);

-- ============================================================================
-- FIN DU SCRIPT DE PEUPLEMENT V2 (CORRIGÉ)
-- ============================================================================

-- Vérification des insertions
SELECT 'VÉRIFICATION DES DONNÉES INSÉRÉES' AS Info;

SELECT 'MÉDICAMENTS' AS Table_Name, COUNT(*) AS Nb_Lignes FROM medicament
UNION ALL
SELECT 'PRATICIENS', COUNT(*) FROM praticien
UNION ALL
SELECT 'RAPPORTS DE VISITE', COUNT(*) FROM rapport_visite
UNION ALL
SELECT 'ÉCHANTILLONS OFFERTS', COUNT(*) FROM offrir
UNION ALL
SELECT 'COMPTES LOGIN', COUNT(*) FROM login
UNION ALL
SELECT 'HABILITATIONS', COUNT(*) FROM habilitation
UNION ALL
SELECT 'DOSAGES', COUNT(*) FROM dosage
UNION ALL
SELECT 'ÉTATS', COUNT(*) FROM etat
UNION ALL
SELECT 'MOTIFS', COUNT(*) FROM motif_visite;

-- Affichage de quelques statistiques
SELECT 
    c.COL_NOM,
    c.COL_PRENOM,
    COUNT(r.RAP_NUM) AS Nb_Rapports,
    SUM(o.OFF_QTE) AS Total_Echantillons
FROM collaborateur c
LEFT JOIN rapport_visite r ON c.COL_MATRICULE = r.VIS_MATRICULE
LEFT JOIN offrir o ON r.VIS_MATRICULE = o.VIS_MATRICULE AND r.RAP_NUM = o.RAP_NUM
GROUP BY c.COL_MATRICULE
HAVING Nb_Rapports > 0
ORDER BY Nb_Rapports DESC;

COMMIT;

-- ============================================================================
-- ✅ SCRIPT TERMINÉ - BASE DE DONNÉES OPÉRATIONNELLE !
-- ============================================================================
