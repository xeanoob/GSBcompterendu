<div id="work_banner" class="banner-wrapper bg-light w-100 py-12">
    <div class="banner-vertical-center-work container text-light d-flex justify-content-center align-items-center py-12 p-0">
        <div class="banner-content col-lg-8 col-12 m-lg-auto text-center">
            <h1 class="banner-heading h2 display-3 pb-5 semi-bold-600"><u>Projet GSB</u></h1>
            <p class="banner-body pb-2 light-300 px-2">
                <strong>
                    Le laboratoire Galaxy Swiss Bourdin (GSB) est issu de la fusion entre le géant américain Galaxy et le conglomérat européen Swiss Bourdin.
                    En 2009, les deux géants pharmaceutiques ont uni leurs forces pour créer un leader de ce secteur industriel.
                    L'entité Galaxy Swiss Bourdin Europe a établi son siège administratif à Paris.
                    Le siège social de la multinationale est situé à Philadelphie, Pennsylvalnie, aux Etats-Unis
                </strong>
            </p>
            <?php
            /* 
                $a = getColMatricule();
                $b = getCountMatricule();
                for($i = 0; $i < $b['nb']; $i ++){
                    setAllHabil($a,random_int(1,2),$i);
                    setAllLogin($a,$i);
                }   
                $a = getIdMedoc();
                $b = getNbMedoc();
                for($i = 0; $i < $b[0]; $i ++){
                    setMonnaieMedoc($a,random_int(15,99),$i);
                }   C'EST POUR LES LOGINS, HABILITATIONS ET MONNAIE DES MDOCS
            */
            if (empty($_SESSION['login']) && empty($_SESSION['habilitation'])) {
            ?>
                <a href="index.php?uc=connexion&action=connexion">
                    <button type="submit" class="btn rounded-pill btn-outline-light px-4 me-4 light-300">Se connecter</button>
                </a>
            <?php
            } else {
            ?>
                <a href="index.php?uc=connexion&action=profil">
                    <button type="submit" class="btn rounded-pill btn-outline-light px-4 me-4 light-300">Profil</button>
                </a>
            <?php
            }
            ?>
        </div>
    </div>
</div>
<section class="bg-light py-5 anti">
    <div class="feature-work container my-4">
        <div class="row d-flex d-flex align-items-center">
            <div class="col-lg-5">
                <h1 class="feature-work-heading h2 py-3 semi-bold-600">Projet BTS SIO 2ème année</h1>
                <p class="feature-work-body text-muted light-300">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                    tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse
                    ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis.
                </p>
                <p class="feature-work-footer text-muted light-300">
                    Consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore
                    et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo
                    viverra maecenas. Duis aute irure dolor in reprehenderit in voluptate velit esse
                    cillum dolore eu fugiat nulla pariatur.
                </p>
            </div>
            <div class="col-lg-6 offset-lg-1 align-left">
                <div class="row">
                    <img class="img-fluid" src="assets/img/logo_gsb.png">
                </div>
            </div>
        </div>
    </div>
</section>