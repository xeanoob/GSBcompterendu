<?php
    ob_start();
    session_start();
    require_once ('modele/medicament.modele.inc.php');
    require_once ('modele/connexion.modele.inc.php');

    if(!isset($_REQUEST['uc']) || empty($_REQUEST['uc']))
        $uc = 'accueil';
    else{
        $uc = $_REQUEST['uc'];
    }

    // Gestion de la déconnexion avant tout affichage HTML
    if ($uc == 'connexion' && isset($_REQUEST['action']) && $_REQUEST['action'] == 'deconnexion') {
        session_destroy();
        header('Location: index.php?uc=accueil');
        exit;
    }

    // Inclure le header après la gestion de déconnexion
    include("vues/v_header.php");

    switch($uc)
    {
        case 'connexion' :
        {
            include("controleur/c_connexion.php");
            break;
        }

        case 'praticiens' :
        {
            if (!empty($_SESSION['login'])) {
                include("controleur/c_praticiens.php");
            } else {
                include("vues/v_accesInterdit.php");
            }
            break;
        }

        case 'rapports' :
        {
            if (!empty($_SESSION['login'])) {
                include("controleur/c_rapports.php");
            } else {
                include("vues/v_accesInterdit.php");
            }
            break;
        }
        
        case 'medicaments' :
        {
            if (!empty($_SESSION['login'])) {
                include("controleur/c_medicaments.php");
            } else {
                include("vues/v_accesInterdit.php");
            }
            break;
        }

        default :
        {
            include("vues/v_accueil.php");
            break;
        }
    }
?>
<?php include("vues/v_footer.php") ;?>
</body>
</html>