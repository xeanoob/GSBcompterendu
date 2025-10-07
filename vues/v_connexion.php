<?php
if (isset($_POST['connexion'])) {
    if (empty($_POST['username'])) {
        $userEmpty = "Veuillez saisir votre identifiant !";
    } elseif (empty($_POST['password'])) {
        $userEmpty = "Veuillez saisir votre mot de passe !";
    } else {
        $arr = checkConnexion($_POST['username'], $_POST['password']);
        if (empty($arr)) {
            $userEmpty = "Informations incorrectes !";
        } else {
            $_SESSION['habilitation'] = $arr['habilitation'];
            $_SESSION['login'] = $arr['id_log'];
            $_SESSION['matricule'] = $arr['matricule'];
            $_SESSION['erreur'] = false;
            header('Location: index.php?uc=connexion&action=profil');
        }
    }
}

?>
<section class="bg-light">
    <div class="container">
        <div class="structure-hero pt-lg-5 pt-4">
            <h1 class="titre text-center">Formulaire de connexion</h1>
            <p class="text text-center">Formulaire permettant de se connecter au site et d'accèder au données.</p>
        </div>
        <div class="row align-items-center justify-content-center">
            <div class="test col-12 col-sm-8 col-lg-6 col-xl-5 col-xxl-4 py-lg-5">
                <img class="img-fluid" src="assets/img/login.png">
            </div>
            <div class="test col-12 col-sm-8 col-lg-6 col-xl-5 col-xxl-4 py-lg-5 py-3">
                <?php if (isset($userEmpty)) {
                    echo '<p class="alert alert-danger text-center w-100">' . $userEmpty . '</p>';
                } ?>
                <form class="form-signin formulaire m-auto" action="index.php?uc=connexion&action=connexion" method="post">
                    <h2 class="form-signin-heading">Se connecter</h2>
                    <input type="text" class="form-control" name="username" placeholder="Identifiant" autofocus="" />
                    <input type="password" class="form-control" name="password" placeholder="Mot de passe" />
                    <input class="btn btn-lg btn-info btn-block text-light" type="submit" name="connexion" value="Connexion">
                </form>
            </div>
        </div>
    </div>
</section>