<?php
// Récupération des données passées par le contrôleur
$moniteurs = $moniteurs ?? [];
$vehicules = $vehicules ?? [];
?>

<main class="container mt-5 pt-5">
    <header class="text-center mb-5">
        <h1 class="display-4 fw-light">Notre Équipe</h1>
        <p class="lead text-muted">Découvrez notre équipe professionnelle et notre flotte de véhicules modernes.</p>
    </header>
    
    <!-- Navigation par onglets -->
    <ul class="nav nav-tabs justify-content-center mb-4" id="myTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="moniteurs-tab" data-bs-toggle="tab" data-bs-target="#moniteurs" type="button" role="tab" aria-controls="moniteurs" aria-selected="true">
                <i class="fas fa-chalkboard-teacher me-2"></i>Nos Moniteurs
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="vehicules-tab" data-bs-toggle="tab" data-bs-target="#vehicules" type="button" role="tab" aria-controls="vehicules" aria-selected="false">
                <i class="fas fa-car me-2"></i>Nos Véhicules
            </button>
        </li>
    </ul>

    <!-- Contenu des onglets -->
    <div class="tab-content" id="myTabContent">
        <!-- Onglet Moniteurs -->
        <div class="tab-pane fade show active" id="moniteurs" role="tabpanel" aria-labelledby="moniteurs-tab">
            <section class="py-5">
                <h2 class="display-5 fw-light text-center mb-5">Nos Moniteurs Expérimentés</h2>
                
                <?php if (empty($moniteurs)): ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        Aucun moniteur n'est actuellement disponible.
                    </div>
                <?php else: ?>
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 justify-content-center">
                        <?php foreach ($moniteurs as $moniteur): ?>
                            <div class="col">
                                <div class="card h-100 shadow-sm">
                                    <img src="/public/images/placeholder_moniteur.jpg" class="card-img-top" alt="Photo de <?= htmlspecialchars($moniteur['prenommoniteur'] . ' ' . $moniteur['nommoniteur']) ?>" style="height: 250px; object-fit: cover;">
                                    <div class="card-body d-flex flex-column text-center">
                                        <h5 class="card-title fw-bold text-primary">
                                            <?= htmlspecialchars($moniteur['prenommoniteur'] . ' ' . $moniteur['nommoniteur']) ?>
                                        </h5>
                                        <p class="card-text flex-grow-1">
                                            <i class="fas fa-envelope me-2 text-muted"></i>
                                            <a href="mailto:<?= htmlspecialchars($moniteur['emailmoniteur']) ?>" class="text-decoration-none">
                                                <?= htmlspecialchars($moniteur['emailmoniteur']) ?>
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>

        <!-- Onglet Véhicules -->
        <div class="tab-pane fade" id="vehicules" role="tabpanel" aria-labelledby="vehicules-tab">
            <section class="py-5">
                <h2 class="display-5 fw-light text-center mb-5">Nos Véhicules</h2>
                
                <?php if (empty($vehicules)): ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        Aucun véhicule n'est actuellement disponible.
                    </div>
                <?php else: ?>
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 justify-content-center">
                        <?php foreach ($vehicules as $vehicule): ?>
                            <div class="col">
                                <div class="card h-100 shadow-sm">
                                    <img src="/public/images/placeholder_vehicules.jpg" class="card-img-top" alt="<?= htmlspecialchars($vehicule['designation']) ?>" style="height: 250px; object-fit: cover;">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title fw-bold text-primary text-center">
                                            <?= htmlspecialchars($vehicule['designation']) ?>
                                        </h5>
                                        <div class="card-text flex-grow-1">
                                            <ul class="list-unstyled mt-3 mb-4">
                                                <li><i class="fas fa-car me-2 text-success"></i><strong>Immatriculation:</strong> <?= htmlspecialchars($vehicule['immatriculation']) ?></li>
                                                <li><i class="fas fa-users me-2 text-success"></i><strong>Passagers:</strong> <?= htmlspecialchars($vehicule['nbpassagers']) ?></li>
                                                <li><i class="fas fa-cogs me-2 text-success"></i><strong>Transmission:</strong> <?= $vehicule['manuel'] ? 'Manuelle' : 'Automatique' ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>
</main>
