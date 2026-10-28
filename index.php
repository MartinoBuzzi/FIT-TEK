<?php session_start(); ?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>FIT-TEK - Home</title>
    <?php include 'partials/header_login.php'; ?>
</head>
<body class="d-flex flex-column h-100 bg-light">

    <!-- includo navbar-->
    <?php include 'partials/navbar.php'; ?>

    <main class="flex-shrink-0">
        
        <!-- carosello bootstrap-->
        <!-- data-bs-ride="carousel" attiva l'avanzamento automatico -->
        <div id="carouselFittek" class="carousel slide shadow-sm mb-5" data-bs-ride="carousel">

            <div class="carousel-indicators">
                <!-- data-bs-slide-to="0" specifica a quale slide si deve passare al click sull'indicatore -->
                <button type="button" data-bs-target="#carouselFittek" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselFittek" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselFittek" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>
            
            <div class="carousel-inner">
                
                <div class="carousel-item active">
                    <img src="img/slide1.jpg" class="d-block w-100 custom-carousel-img" alt="Palestra">
                    <div class="carousel-caption">
                        <h2>Allenati con noi</h2>
                        <p class="lead">Attrezzature moderne per i tuoi obiettivi sportivi</p>
                    </div>
                </div>
                
                <div class="carousel-item">
                    <img src="img/slide2.jpg" class="d-block w-100 custom-carousel-img" alt="Coach">
                    <div class="carousel-caption">
                        <h2>Coach esperti</h2>
                        <p class="lead">Ti seguiamo con programmi personalizzati in ogni fase</p>
                    </div>
                </div>
                
                <div class="carousel-item">
                    <img src="img/slide3.jpg" class="d-block w-100 custom-carousel-img" alt="Shop">
                    <div class="carousel-caption">
                        <h2>Il nostro shop</h2>
                        <p class="lead">Abbigliamento tecnico, integratori e accessori esclusivi</p>
                    </div>
                </div>
                
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#carouselFittek" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Precedente</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselFittek" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Successivo</span>
            </button>

        </div>


        <!--hero card coach-->
        <div class="container text-center mt-5 mb-4">
            <h2 class="fw-bold text-uppercase tracking-wide">Il Nostro Team di Esperti</h2>
            <p class="text-muted">Professionisti qualificati al servizio dei tuoi obiettivi fitness</p>
        </div>

        <section class="container my-5">
            <div class="row align-items-center">
                <div class="col-md-5 d-flex justify-content-center mb-4 mb-md-0">
                    <img src="img/coach1.jpg"
                         class="rounded-circle shadow-sm"
                         style="width: 280px; height: 280px; object-fit: cover;"
                         alt="Coach Marco">
                </div>
                <div class="col-md-7 ps-md-5 text-center text-md-start">
                    <h3 class="coach-nome">Marco Rossi</h3>
                    <p class="coach-ruolo">Personal Trainer & Strength Coach</p>
                    <p class="coach-desc">Marco ha oltre 10 anni di esperienza nel settore del fitness. Specializzato in allenamento della forza e ipertrofia, ha seguito atleti di ogni livello aiutandoli a raggiungere i loro obiettivi in modo sicuro ed efficace.</p>
                </div>
            </div>
        </section>

        <div class="container"><hr class="coach-divider"></div>

        <section class="container my-5">
            <div class="row align-items-center-reverse flex-column-reverse flex-md-row">
                <div class="col-md-7 pe-md-5 text-center text-md-start">
                    <h3 class="coach-nome">Sara Bianchi</h3>
                    <p class="coach-ruolo">Yoga & Functional Training</p>
                    <p class="coach-desc">Sara è specializzata in yoga e allenamento funzionale. Con la sua esperienza aiuta i clienti a migliorare flessibilità, postura e benessere mentale attraverso un approccio olistico al movimento.</p>
                </div>
                <div class="col-md-5 d-flex justify-content-center mb-4 mb-md-0">
                    <img src="img/coach2.jpg"
                         class="rounded-circle shadow-sm"
                         style="width: 280px; height: 280px; object-fit: cover;"
                         alt="Coach Sara">
                </div>
            </div>
        </section>

        <div class="container"><hr class="coach-divider"></div>

        <section class="container my-5">
            <div class="row align-items-center">
                <div class="col-md-5 d-flex justify-content-center mb-4 mb-md-0">
                    <img src="img/coach3.jpg"
                         class="rounded-circle shadow-sm"
                         style="width: 280px; height: 280px; object-fit: cover;"
                         alt="Coach Luca">
                </div>
                <div class="col-md-7 ps-md-5 text-center text-md-start">
                    <h3 class="coach-nome">Luca Ferrari</h3>
                    <p class="coach-ruolo">Cardio & Endurance Coach</p>
                    <p class="coach-desc">Luca è un ex atleta professionista specializzato in allenamento cardiovascolare e resistenza. Prepara i suoi clienti per maratone, triathlon e competizioni sportive con programmi personalizzati e progressivi.</p>
                </div>
            </div>
        </section>
        
    </main> 

    <!--includo footer-->
    <?php include 'partials/footer.php'; ?>
    
</body>
</html>