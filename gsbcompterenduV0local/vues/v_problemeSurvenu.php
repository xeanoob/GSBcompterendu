<section class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-md-center">
            <div class="col-md-auto background-error">
                <h1 class="h2 semi-bold-600 title-error">Un problème est survenu.</h1>
                <p class="light-300 alert alert-danger"><?php if (isset($succes)){echo $succes;} ?></p>                                
                <a class="btn rounded-pill btn-outline-dark px-4 me-4 dark-300" href="index.php?uc=accueil">Accueil</a>
            </div>
        </div>
    </div>
</section>