import re
import os

# --- CONFIGURATION ---
# Assurez-vous que votre fichier s'appelle bien ainsi ou modifiez le nom ici
FICHIER_SOURCE = 'gsbv0v3.sql'
FICHIER_CIBLE = 'gsbv0v3_repare_exhaustif.sql'

# --- DICTIONNAIRE DE CORRECTION SPÉCIFIQUE GSB ---
# Basé sur l'analyse complète du fichier fourni.
# L'ordre est important (du plus long au plus court).

REPLACEMENTS = [
    # 1. ENTÊTES, SQL ET STRUCTURE
    (r"Base de donn\?es", "Base de données"),
    (r"G\?n\?r\? le", "Généré le"),
    (r"H\?te", "Hôte"),
    (r"D\?chargement des donn\?es", "Déchargement des données"),
    (r"Saisie d\?finitive", "Saisie définitive"),
    (r"Valid\?", "Validé"),
    (r"Consult\?", "Consulté"),
    (r"D\?l\?gu\?", "Délégué"),
    (r"R\?gional", "Régional"),
    (r"Archiv\?", "Archivé"),
    (r"Nouveaut\?", "Nouveauté"),
    (r"P\?riodicit\?", "Périodicité"),
    (r"cl\?", "clé"),
    (r"libell\?", "libellé"),
    (r"modifi\?", "modifié"),
    (r"cr\?ation", "création"),
    (r"num\?ro", "numéro"),
    (r"cat\?gorie", "catégorie"),
    (r"donn\?es", "données"),
    (r"syst\?me", "système"),
    (r"param\?tre", "paramètre"),
    (r"entri\?e", "entrée"),
    (r"poss\?der", "posséder"),

    # 2. RUES, VILLES ET ADRESSES (Cas très spécifiques trouvés dans le fichier)
    (r"1\?re Arm\?e", "1ère Armée"),
    (r"Fran\?aise", "Française"),
    (r"Ste Th\?r\?se", "Ste Thérèse"),
    (r"Berni\?res", "Bernières"),
    (r"H\?rouville", "Hérouville"),
    (r"Mar\?ais", "Marçais"),
    (r"St P\?res", "St Pères"),
    (r"Pigaci\?re", "Pigacière"),
    (r"Dech\?tre", "Dechêtre"),
    (r"Thi\?s", "Thiers"),           # "Av Thiers" est très courant, Thiès est au Sénégal
    (r"Ch\?teau", "Château"),
    (r"B\?t A", "Bât A"),
    (r"r\?sid", "résid"),            # pour "résidence"
    (r"Besan\?on", "Besançon"),
    (r"Orl\?ans", "Orléans"),
    (r"Alen\?on", "Alençon"),
    (r"N\?mes", "Nîmes"),
    (r"Cr\?teil", "Créteil"),
    (r"Sa\?ne", "Saône"),
    (r"Rh\?ne", "Rhône"),
    (r"M\?lingue", "Mélingue"),
    (r"Picoti\?re", "Picotière"),
    (r"Chiss\?e", "Chissée"),
    (r"B\?n\?dictins", "Bénédictins"),
    (r"Caponi\?re", "Caponière"),
    (r"G\?ants", "Géants"),
    (r"Elys\?es", "Elysées"),
    (r"P\?chers", "Pêchers"),
    (r"cit\?", "cité"),
    (r"Lib\?ration", "Libération"),
    (r"R\?publique", "République"),
    (r"G\?n\?ral", "Général"),
    (r"H\?tel", "Hôtel"),
    (r"Cotes-d'Armor", "Côtes-d'Armor"),
    (r"Finistere", "Finistère"),
    (r"Herault", "Hérault"),
    (r"Lozere", "Lozère"),
    (r"Vendee", "Vendée"),
    (r"Ardeche", "Ardèche"),
    (r"Ariege", "Ariège"),
    (r"Correze", "Corrèze"),
    (r"Drome", "Drôme"),
    (r"Nievre", "Nièvre"),
    (r"Pyrenees", "Pyrénées"),
    (r"Comt\?", "Comté"),
    (r"R\?union", "Réunion"),
    (r"Cal\?donie", "Calédonie"),
    (r"C\?te d'Azur", "Côte d'Azur"),
    (r"S\?vre", "Sèvre"),

    # 3. MÉDICAL, MOLÉCULES ET SPÉCIALITÉS
    (r"m\?dicament", "médicament"),
    (r"M\?decin", "Médecin"),
    (r"g\?n\?rique", "générique"),
    (r"Comprim\?", "Comprimé"),
    (r"G\?lule", "Gélule"),
    (r"pr\?levement", "prélèvement"),
    (r"r\?sultat", "résultat"),
    (r"b\?ta-lactamines", "bêta-lactamines"),
    (r"p\?nicilline", "pénicilline"),
    (r"c\?phalosporines", "céphalosporines"),
    (r"Cortico\?de", "Corticoïde"),
    (r"\? usage local", "à usage local"),
    (r"ac\?tonide", "acétonide"),
    (r"N\?omycine", "Néomycine"),
    (r"T\?traca\?ne", "Tétracaïne"),
    (r"Lidoca\?ne", "Lidocaïne"),
    (r"r\?tino\?des", "rétinoïdes"),
    (r"Erythromycine", "Érythromycine"),
    (r"\?rythromycine", "érythromycine"),
    (r"ph\?nylc\?tonurie", "phénylcétonurie"),
    (r"Parox\?tine", "Paroxétine"),
    (r"gastroduod\?nal", "gastroduodénal"),
    (r"Antid\?presseur", "Antidépresseur"),
    (r"bact\?riennes", "bactériennes"),
    (r"sp\?cifiques", "spécifiques"),
    (r"h\?morragies", "hémorragies"),
    (r"h\?patique", "hépatique"),
    (r"r\?nale", "rénale"),
    (r"hyperuric\?mie", "hyperuricémie"),
    (r"pr\?vention", "prévention"),
    (r"\?nur\?sies", "énurésies"),
    (r"d\?pistage", "dépistage"),
    (r"diab\?tologie", "diabétologie"),
    (r"anesth\?siologie", "anesthésiologie"),
    (r"r\?animation", "réanimation"),
    (r"gyn\?cologie", "gynécologie"),
    (r"obst\?trique", "obstétrique"),
    (r"h\?matologie", "hématologie"),
    (r"ost\?opathie", "ostéopathie"),
    (r"r\?adaptation", "réadaptation"),
    (r"radioth\?rapie", "radiothérapie"),
    (r"ang\?iologie", "angéiologie"),
    (r"canc\?rologie", "cancérologie"),
    (r"orthop\?dique", "orthopédique"),
    (r"esth\?tique", "esthétique"),
    (r"v\?n\?r\?ologie", "vénéréologie"),
    (r"m\?tabolismes", "métabolismes"),
    (r"\?valuation", "évaluation"),
    (r"gastro-ent\?rologie", "gastro-entérologie"),
    (r"h\?patologie", "hépatologie"),
    (r"nucl\?aire", "nucléaire"),
    (r"p\?diatrie", "pédiatrie"),
    (r"m\?dicale", "médicale"),
    (r"antipyr\?tiques", "antipyrétiques"),
    (r"antiacn\?ique", "antiacnéique"),
    (r"surinfect\?es", "surinfectées"),
    (r"cont re-indiqu\?", "contre-indiqué"), # Espace spécifique vu dans le fichier
    (r"ac\?tylsalicylique", "acétylsalicylique"),
    (r"Oxyt\?tracycline", "Oxytétracycline"),
    (r"M\?clozine", "Méclozine"),
    (r"Cod\?ine", "Codéine"),
    (r"trom\?tamol", "trométamol"),
    (r"param\?dical", "paramédical"),
    (r"sp\?cialit\?", "spécialité"),

    # 4. NOMS ET PRÉNOMS
    (r"Fran\?ois", "François"),
    (r"J\?rôme", "Jérôme"),
    (r"H\?l\?ne", "Hélène"),
    (r"Th\?r\?se", "Thérèse"),
    (r"Andr\?", "André"),
    (r"Fr\?d\?ric", "Frédéric"),
    (r"Fr\?d\?rique", "Frédérique"),
    (r"Val\?rie", "Valérie"),
    (r"G\?rard", "Gérard"),
    (r"St\?phane", "Stéphane"),
    (r"C\?line", "Céline"),
    (r"Agn\?s", "Agnès"),
    (r"No\?l", "Noël"),
    (r"Jo\?l", "Joël"),
    (r"Genevi\?ve", "Geneviève"),
    (r"Rapha\?l", "Raphaël"),
    (r"Mich\?le", "Michèle"),
    (r"Ga\?lle", "Gaëlle"),
    (r"Doroth\?e", "Dorothée"),
    (r"Chlo\?", "Chloé"),
    (r"L\?a", "Léa"),
    (r"L\?o", "Léo"),
    (r"B\?n\?dicte", "Bénédicte"),
    (r"Gaff\?", "Gaffé"),
    (r"Lem\?e", "Lemée"),
    (r"N\?e", "Née"),
    (r"P\?re", "Père"),   # Pour "Père et fils" ou noms comme "Le Père"
    (r"P\?ri", "Péri"),   # Gabriel Péri
    (r"Jaur\?s", "Jaurès"),
    (r"Val\?ry", "Valéry"),
    (r"Ren\?", "René"),

    # 5. MOTS COURANTS ET GRAMMAIRE (En dernier)
    (r"activit\?", "activité"),
    (r"fr\?quence", "fréquence"),
    (r"pr\?sent", "présent"),
    (r"r\?guli\?re", "régulière"),
    (r"mani\?re", "manière"),
    (r"tr\?s", "très"),
    (r"apr\?s", "après"),
    (r" d\?j\? ", " déjà "),
    (r"\?tre", "être"),
    (r"fi\?vre", "fièvre"),
    (r"s\?v\?re", "sévère"),
    (r"Personne \?g\?e", "Personne âgée"),
    (r"\?g\?", "âgé"),
    (r"piq\?res", "piqûres"),
    (r"co\?t", "coût"),
    (r"r\?le", "rôle"),
    (r"s\?lectif", "sélectif"),
    (r"s\?rotonine", "sérotonine"),
    (r"associ\?", "associé"),
    (r"cutan\?es", "cutanées"),
    (r"trait\?s", "traités"),
    (r"acn\?", "acné"),
    (r"ulc\?re", "ulcère"),
    (r"\?pisodes", "épisodes"),
    (r"d\?pressifs", "dépressifs"),
    (r"s\?v\?res", "sévères"),
    (r"ad\?nome", "adénome"),
    (r"pr\?c\?dentes", "précédentes"),
    (r"parac\?tamol", "paracétamol"),
    (r"r\?tention", "rétention"),
    (r"pr\?vention", "prévention"),
    (r"\?tats", "états"),
    (r"\?tes", "êtes"),
    (r"r\?gime", "régime"),
    (r"applic\?s", "appliqués"),
    (r"soci\?t\?", "société"),
    (r"t\?l\?phone", "téléphone"),
    (r"pr\?nom", "prénom"),
    (r"F\?d\?rico", "Fédérico"),
    
    # 6. MOIS
    (r"ao\?t", "août"),
    (r"d\?cembre", "décembre"),
    (r"f\?vrier", "février"),

    # 7. FINITIONS (Les plus risqués, à la fin)
    (r" \? ", " à "),       # "2 ? 3 fois" -> "2 à 3 fois"
    (r"t\? ", "té "),       # "sant? " -> "santé "
    (r"e\? ", "ée "),       # "all? " -> "allée "
]

def reparer_gsb():
    if not os.path.exists(FICHIER_SOURCE):
        print(f"ERREUR : Le fichier {FICHIER_SOURCE} est introuvable.")
        return

    print(f"--- DÉMARRAGE DE LA RÉPARATION DU FICHIER {FICHIER_SOURCE} ---")
    
    try:
        # On lit le fichier en ignorant les erreurs pour ne pas planter
        with open(FICHIER_SOURCE, 'r', encoding='utf-8', errors='ignore') as f:
            contenu = f.read()
    except Exception as e:
        print(f"Erreur de lecture : {e}")
        return

    compteur_total = 0
    
    # On boucle sur toutes les règles de remplacement
    for pattern, correction in REPLACEMENTS:
        try:
            # On cherche combien de fois le motif apparait
            regex = re.compile(pattern, re.IGNORECASE)
            matches = len(regex.findall(contenu))
            
            if matches > 0:
                # On remplace
                contenu = regex.sub(correction, contenu)
                compteur_total += matches
        except re.error as e:
            print(f"Erreur sur le motif '{pattern}': {e}")

    # Sauvegarde
    try:
        with open(FICHIER_CIBLE, 'w', encoding='utf-8') as f:
            f.write(contenu)
        
        print(f"\nTERMINÉ ! {compteur_total} corrections effectuées.")
        print(f"Nouveau fichier : {FICHIER_CIBLE}")
        print("-" * 40)
        
        # Petit scan final pour vous dire s'il reste des ?
        lignes = contenu.split('\n')
        nb_restant = 0
        print("VERIFICATION DES LIGNES DOUTEUSES RESTANTES :")
        for i, ligne in enumerate(lignes):
            if "?" in ligne and "phpMyAdmin" not in ligne and "INSERT INTO" in ligne:
                # On affiche seulement s'il y a encore des ? dans les données
                print(f"Ligne {i+1}: {ligne.strip()[:100]}...")
                nb_restant += 1
        
        if nb_restant == 0:
            print(">> Aucune donnée suspecte détectée. Le fichier semble propre à 100%.")
        else:
            print(f">> Il reste {nb_restant} lignes à vérifier manuellement.")

    except Exception as e:
        print(f"Erreur d'écriture : {e}")

if __name__ == "__main__":
    reparer_gsb()