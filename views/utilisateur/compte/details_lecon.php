<main class="container pt-4">
    <section id="espace-connecte">
        <h1 class="mb-4">Mon Espace</h1>

        <div class="tab-content">
                    <?php if (!empty($error)) { ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php } ?>

                    <?php if (!empty($success)) { ?>
                        <div class="alert alert-success" role="alert">
                            <?= htmlspecialchars($success) ?>
                        </div>
                    <?php } ?>

                    <!-- En-tête avec bouton retour -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="display-6">Détails de la leçon</h2>
                        <a href="/mon-compte/planning.html" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour au planning
                        </a>
                    </div>

                    <!-- Carte des détails de la leçon -->
                    <div class="card mb-4">
                        <div class="card-header fw-bold">
                            <i class="fas fa-info-circle me-2"></i>Informations de la leçon
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Moniteur -->
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-tie text-primary me-3 fs-4"></i>
                                        <div>
                                            <small class="text-muted d-block">Moniteur</small>
                                            <strong><?= htmlspecialchars($lecon->prenommoniteur . ' ' . $lecon->nommoniteur) ?></strong>
                                        </div>
                                    </div>
                                </div>

                                <!-- Date et heure -->
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-alt text-success me-3 fs-4"></i>
                                        <div>
                                            <small class="text-muted d-block">Date et heure</small>
                                            <strong><?= date('d/m/Y à H:i', strtotime($lecon->heuredebut)) ?></strong>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lieu de rendez-vous -->
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt text-danger me-3 fs-4"></i>
                                        <div>
                                            <small class="text-muted d-block">Lieu de rendez-vous</small>
                                            <strong><?= htmlspecialchars($lecon->lieurdv ?? 'Non spécifié') ?></strong>
                                        </div>
                                    </div>
                                </div>

                                <!-- Véhicule -->
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-car text-info me-3 fs-4"></i>
                                        <div>
                                            <small class="text-muted d-block">Véhicule</small>
                                            <strong>
                                                <?= htmlspecialchars($lecon->designation ?? 'Véhicule') ?>
                                                <span class="text-muted small">(<?= htmlspecialchars($lecon->immatriculation) ?>)</span>
                                            </strong>
                                            <div class="text-muted small">
                                                <i class="fas fa-cog me-1"></i>
                                                <?= $lecon->manuel ? 'Boîte manuelle' : 'Boîte automatique' ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Carte interactive -->
                    <?php if (!empty($lecon->lieurdv)) { ?>
                    <div class="card mb-4">
                        <div class="card-header fw-bold">
                            <i class="fas fa-map me-2"></i>Localisation du rendez-vous
                        </div>
                        <div class="card-body p-0">
                            <div id="map" style="height: 400px; width: 100%;"></div>
                        </div>
                    </div>
                    <?php } ?>

                    <!-- Bouton d'annulation (si la leçon est dans plus de 48h) -->
                    <?php
                    $now = time();
                    $leconTime = strtotime($lecon->heuredebut);
                    $diffHours = ($leconTime - $now) / 3600;
                    
                    if ($diffHours > 48) {
                    ?>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            <i class="fas fa-times-circle me-2"></i>Annuler la leçon
                        </button>
                    </div>
                    <?php } else { ?>
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Cette leçon ne peut plus être annulée car elle est prévue dans moins de 48 heures.
                    </div>
                    <?php } ?>

        </div>
    </section>
</main>

<!-- Modal de confirmation d'annulation -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="cancelModalLabel">Confirmer l'annulation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Voulez-vous vraiment annuler cette leçon ?</p>
                <p class="text-muted small mb-0">Un email de confirmation sera envoyé à votre adresse.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                <form method="POST" action="/mon-compte/planning/annuler-lecon.html" style="display: inline;">
                    <input type="hidden" name="idlecon" value="<?= $lecon->idlecon ?>">
                    <button type="submit" class="btn btn-danger">Oui, annuler</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($lecon->lieurdv)) { ?>
<!-- Leaflet CSS et JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser la carte centrée sur Angers par défaut
    const map = L.map('map').setView([47.4784, -0.5632], 13);
    
    // Ajouter le fond de carte OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Géocoder l'adresse
    const address = '<?= addslashes($lecon->lieurdv) ?>';
    
    // Utiliser l'API Nominatim pour géocoder l'adresse
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`)
        .then(response => response.json())
        .then(data => {
            if (data && data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lon = parseFloat(data[0].lon);
                
                // Centrer la carte sur l'adresse trouvée
                map.setView([lat, lon], 15);
                
                // Ajouter un marqueur
                L.marker([lat, lon]).addTo(map)
                    .bindPopup(`<b>Lieu de rendez-vous</b><br>${address}`)
                    .openPopup();
            } else {
                console.warn('Adresse non trouvée, carte centrée sur Angers');
            }
        })
        .catch(error => {
            console.error('Erreur lors du géocodage:', error);
        });
});
</script>
<?php } ?>
