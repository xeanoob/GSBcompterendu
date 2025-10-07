<?php
if (!isset($_REQUEST['action']) || empty($_REQUEST['action'])) {
	$action = "connexion";
} else {
	$action = $_REQUEST['action'];
}
switch ($action) {
	case 'connexion': {

			if (isset($_SESSION['login'])) {
				header('Location: index.php?uc=connexion&action=profil');
			} else {
				include("vues/v_connexion.php");
			}
			break;
		}

	case 'deconnexion': {

			session_destroy();
			header('location: index.php?uc=accueil');
			break;
		}

	case 'profil': {

			if (!isset($_SESSION['matricule'])) {
				header('location: index.php?uc=connexion&action=connexion');
			} else {
				$info = getAllInformationCompte($_SESSION['matricule']);
				$_SESSION['region'] = $info[9];
				for ($i = 7; $i <= 8; $i++) {
					if (empty($info[$i])) {
						$info[$i] = 'Non défini(e)';
					}
				}
				include("vues/v_profil.php");
			}
			break;
		}

	default: {
			header('location: index.php?uc=connexion&action=connexion');
			break;
		}
}
?>