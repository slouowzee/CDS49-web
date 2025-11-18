<main class="container pt-4">
    <section id="espace-connecte">
        <h1 class="mb-4">Mon Espace</h1>

        <style>
            .cursor-pointer {
                cursor: pointer;
            }
            .fc-event:hover {
                opacity: 0.8;
            }
        </style>

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

        <?php if (!empty($info)) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($info) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <div class="row">
            <?php
            // Inclusion de la sidebar pour l'espace compte utilisateur (menu de navigation)
            $page_active = 'planning';
            include '_sidebar_compte.php';
            ?>

            <div class="col-md-9">
                <div class="tab-content">
                    <!-- Section Forfait Actif -->
                    <div class="card mb-4">
                        <div class="card-header fw-bold">
                            Mon Forfait Actif
                        </div>
                        <div class="card-body">
                            <?php if ($forfait) { ?>
                                <!-- Cas 1: Forfait actif -->
                                <div id="forfait-actif-info">
                                    <h5 class="card-title" id="nom-forfait-actif"><?= htmlspecialchars($forfait->libelleforfait) ?></h5>
                                    <?php if ($forfait->nbheures != null) { ?>
                                        <p class="card-text">Il vous reste <strong id="heures-restantes"><?= $planning['nbLeconsRestantes'] ?> heures</strong> de conduite.</p>
                                    <?php } ?>

                                    <?php if ($planning['prochainRdv']) { ?>
                                        <p class="card-text">Prochain RDV pédagogique le : <span id="rdv-pedagogique"><?= date('d/m/Y à H:i', strtotime($planning['prochainRdv'])) ?></span></p>
                                    <?php } ?>
                                </div>
                            <?php } else { ?>
                                <!-- Cas 2: Aucun forfait -->
                                <div id="aucun-forfait-info">
                                    <p class="card-text">Vous n'avez actuellement aucun forfait actif.</p>
                                    <div>
                                        <a href="/forfaits.html" class="btn btn-primary">Choisir un forfait</a>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <!-- Fin Section Forfait Actif -->

                    <?php if ($forfait) { ?>
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="display-5 mb-0">Planning des passages</h2>
                                <button type="button" 
                                        class="btn btn-primary btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#demandeHeureModal"
                                        <?= $demandeEnCours ? 'disabled' : '' ?>>
                                    <i class="fas fa-plus-circle me-1"></i>
                                    <?= $demandeEnCours ? 'Demande en cours' : 'Demander des heures' ?>
                                </button>
                            </div>
                            <p>Voici vos heures de conduite planifiées :</p>
                            <!-- Conteneur pour FullCalendar -->
                            <div id='calendar'></div>
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-info" role="alert">
                            Vous devez d'abord choisir un forfait pour accéder à votre planning de passages.
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Modal de demande d'heures supplémentaires -->
<div class="modal fade" id="demandeHeureModal" tabindex="-1" aria-labelledby="demandeHeureModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/mon-compte/demande-heure-supplementaire.html">
                <div class="modal-header">
                    <h5 class="modal-title" id="demandeHeureModalLabel">Demander des heures supplémentaires</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Vous pouvez ajouter un commentaire pour préciser vos disponibilités ou besoins particuliers.</p>
                    <div class="mb-3">
                        <label for="commentaire" class="form-label">Commentaire (optionnel)</label>
                        <textarea class="form-control" 
                                  id="commentaire" 
                                  name="commentaire" 
                                  rows="4" 
                                  placeholder="Ex: Disponible en semaine après 18h ou le samedi matin..."
                                  maxlength="500"></textarea>
                        <div class="form-text">Maximum 500 caractères</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Envoyer la demande</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            height: 850, // Hauteur du calendrier,
            expandRows: true, // Permet d'étendre les lignes pour afficher tous les événements
            initialView: 'timeGridWeek', // Vue agenda hebdomadaire
            slotMinTime: '08:00:00', // Heure de début
            slotMaxTime: '20:00:00', // Heure de fin
            slotDuration: '01:00:00', // Durée des créneaux
            allDaySlot: false, // Masquer le créneau "toute la journée"
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'timeGridWeek'
            },
            events: [
                <?php foreach ($planning['planning'] as $lecon) { ?> {
                        id: '<?= $lecon['idlecon'] ?>',
                        title: '<?= htmlspecialchars($lecon['title']) ?>',
                        start: '<?= date('Y-m-d\TH:i:s', strtotime($lecon['start'])) ?>',
                        end: '<?= date('Y-m-d\TH:i:s', strtotime($lecon['end'])) ?>'
                    },
                <?php } ?>
            ],
            locale: 'fr',
            buttonText: {
                today: 'Aujourd\'hui',
                month: 'Mois',
                week: 'Semaine',
                day: 'Jour',
                list: 'Liste'
            },
            eventClick: function(info) {
                // Redirection vers la page de détails de la leçon
                window.location.href = '/mon-compte/planning/details.html?idlecon=' + info.event.id;
            },
            eventClassNames: 'cursor-pointer'
        });
        calendar.render();
    });
</script>