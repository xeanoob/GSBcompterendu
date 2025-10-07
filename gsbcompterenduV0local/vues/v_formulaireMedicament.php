<section class="bg-light">
    <div class="container">
        <div class="structure-hero pt-lg-5 pt-4">
            <h1 class="titre text-center">Formulaire de médicament</h1>
            <p class="text text-center">
                Formulaire permettant d'afficher toutes les informations
                à propos d'un médicament en particulier.
            </p>
        </div>
        <div class="row align-items-center justify-content-center">
            <div class="test col-12 col-sm-8 col-lg-6 col-xl-5 col-xxl-4 py-lg-5">
                <img class="img-fluid size-img-page" src="assets/img/medoc.jpeg">
            </div>
            <div class="test col-12 col-sm-8 col-lg-6 col-xl-5 col-xxl-4 py-lg-5 py-3">
                <?php if ($_SESSION['erreur']) {
                    echo '<p class="alert alert-danger text-center w-100">Un problème est survenu lors de la selection du médicament</p>';
                    $_SESSION['erreur'] = false;
                } ?>
                <form action="index.php?uc=medicaments&action=affichermedoc" method="post" class="formulaire-recherche col-12 m-0">
                    <label class="titre-formulaire" for="listemedoc">Médicaments disponible :</label>
                    <select required name="medicament" class="form-select mt-3">
                        <option value class="text-center">- Choisissez un médicament -</option>
                        <?php
                        foreach ($result as $key) {
                            echo '<option value="' . $key['MED_DEPOTLEGAL'] . '" class="form-control">' . $key['MED_DEPOTLEGAL'] . ' - ' . $key['MED_NOMCOMMERCIAL'] . '</option>';
                        }
                        ?>
                    </select>
                    <input class="btn btn-info text-light valider" type="submit" value="Afficher les informations">
                </form>
            </div>
        </div>
    </div>
</section>