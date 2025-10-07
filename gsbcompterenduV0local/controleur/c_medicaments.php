<?php
if (!isset($_REQUEST['action']) || empty($_REQUEST['action'])) {
	$action = "formulairemedoc";
} else {
	$action = $_REQUEST['action'];
}
switch ($action) {
	case 'formulairemedoc': {

			$result = getAllNomMedicament();
			include("vues/v_formulaireMedicament.php");
			break;
		}

	case 'affichermedoc': {

			if (isset($_REQUEST['medicament']) && getAllInformationMedicamentDepot($_REQUEST['medicament'])) {
				$med = $_REQUEST['medicament'];
				$carac = getAllInformationMedicamentDepot($med);
				if (empty($carac[7])) {
					$carac[7] = 'Non défini(e)';
				}
				include("vues/v_afficherMedicament.php");
			} else {
				$_SESSION['erreur'] = true;
				header("Location: index.php?uc=medicaments&action=formulairemedoc");
			}
			break;
		}

	default: {

			header('Location: index.php?uc=medicaments&action=formulairemedoc');
			break;
		}
}
?>