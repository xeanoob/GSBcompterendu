<head>
    <title>Projet GSB</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/boxicon.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/gsb.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body>
    <nav id="main_nav" class="navbar navbar-expand-lg navbar-light bg-white shadow">
        <div class="menuCont container">
            <a class="navbar-brand h1 my-2" href="index.php?uc=accueil">
                <span class="text-dark h4 fw-bold">Projet</span> <span class="text-info h4 fw-bold">GSB</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-toggler-success" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="align-self-center collapse navbar-collapse flex-fill  d-lg-flex justify-content-lg-between" id="navbar-toggler-success">
                <div class="flex-fill d-flex justify-content-end">
                    <ul class="nav navbar-nav d-flex justify-content-between mx-xl-5 text-center text-dark">
                        <li class="nav-item ">
                            <a class="nav-link btn-outline-info rounded-pill px-3 fw-bold" href="index.php?uc=accueil">Accueil</a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link btn-outline-info rounded-pill px-3 fw-bold" href="index.php?uc=medicaments&action=formulairemedoc">Médicaments</a>
                        </li>
                       
                        
                        <li class="nav-item ">
                            <a class="nav-link btn-outline-info rounded-pill px-3 fw-bold" href="index.php?uc=connexion&action=profil">Profil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn-outline-info rounded-pill px-3 fw-bold" href="index.php?uc=connexion&action=deconnexion" onclick="return confirm('Voulez-vous vraiment vous déconnecter ?');">Déconnexion</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>