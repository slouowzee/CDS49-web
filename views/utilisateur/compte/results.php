<main class="container pt-4">
    <section id="espace-connecte">
        <h1 class="mb-4">Mon Espace</h1>

        <div class="row">
            <?php
            // Inclusion de la sidebar pour l'espace compte utilisateur (menu de navigation)
            $page_active = 'results';
            include '_sidebar_compte.php';
            ?>

            <div class="col-md-9">
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

                    <!-- Section Mes Résultats -->
                    <div class="card mb-4">
                        <div class="card-header fw-bold">
                            <i class="fas fa-chart-line me-2"></i>Mes Résultats aux Quiz
                        </div>
                        <div class="card-body">
                            <?php if (!empty($results) && count($results) > 0) { ?>
                                <p class="text-muted mb-3">
                                    Vous avez effectué <strong><?= count($results) ?></strong> quiz. 
                                </p>
                                
                                <div class="table-responsive">
                                    <table id="resultsTable" class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Score</th>
                                                <th>Catégorie</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($results as $result) { 
                                                $score = (int)$result['score'];
                                                $nbquestions = (int)$result['nbquestions'];
                                                $pourcentage = $nbquestions > 0 ? round(($score / $nbquestions) * 100, 1) : 0;
                                                $dateFormatted = date('d/m/Y à H:i', strtotime($result['dateresultat']));
                                                $categorie = $result['libcategorie'] ?? 'Non spécifiée';
                                                
                                                // Déterminer la classe CSS selon le score
                                                $scoreClass = '';
                                                if ($pourcentage >= 75) {
                                                    $scoreClass = 'text-success fw-bold';
                                                } elseif ($pourcentage >= 50) {
                                                    $scoreClass = 'text-warning fw-bold';
                                                } else {
                                                    $scoreClass = 'text-danger fw-bold';
                                                }
                                            ?>
                                            <tr>
                                                <td data-sort="<?= strtotime($result['dateresultat']) ?>">
                                                    <?= htmlspecialchars($dateFormatted) ?>
                                                </td>
                                                <td class="<?= $scoreClass ?>" data-sort="<?= $score ?>">
                                                    <?= $score ?> / <?= $nbquestions ?>
                                                </td>
                                                <td data-sort="<?= htmlspecialchars($categorie) ?>">
                                                    <?= htmlspecialchars($categorie) ?>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>

                            <?php } else { ?>
                                <div class="alert alert-info" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Vous n'avez pas encore effectué de quiz.
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- jQuery et TableSorter -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/css/theme.bootstrap_4.min.css">

<script>
$(document).ready(function() {
    // Initialiser tablesorter avec tri par date décroissant par défaut
    $("#resultsTable").tablesorter({
        theme: 'bootstrap',
        headerTemplate: '{content} {icon}',
        widgets: ['zebra', 'columns'],
        sortList: [[0, 1]], // Tri par date (colonne 0) décroissant (1)
        headers: {
            0: { sorter: 'digit' }, // Date (utilise data-sort avec timestamp)
            1: { sorter: 'digit' }, // Score
            2: { sorter: 'text' }  // Catégorie
        }
    });
});
</script>
