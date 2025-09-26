<main class="container pt-4">
    <section id="espace-connecte">
        <h1 class="mb-4">Mon Espace</h1>

        <div class="row">
            <?php
            // Inclusion de la sidebar pour l'espace compte utilisateur (menu de navigation)
            $page_active = 'profil';
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

                    <form method="POST" action="/mon-compte/profil.html">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars(isset($nomeleve) ? $nomeleve : ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars(isset($prenomeleve) ? $prenomeleve : ''); ?>">
			</div>
			<div class="mb-3">
			    <label for="telephone" class="form-label">Numéro de téléphone <span class="text-muted">(optionnel)</span></label>
			    <input type="tel" class="form-control" id="telephone" name="telephone" 
			           value="<?= htmlspecialchars(isset($teleleve) && !empty($teleleve) ? \utils\SessionHelpers::formatFrenchPhone($teleleve) : ''); ?>"
			           placeholder="01 23 45 67 89"
			           pattern="[0-9\s\+\-\.\(\)]+"
			           title="Format français : 01 23 45 67 89">
			</div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars(isset($emaileleve) ? $emaileleve : ''); ?>">
			</div>
			<div class="mb-3">
			    <label for="datenaissance" class="form-label">Date de naissance</label>
			    <input type="date" class="form-control" id="datenaissance" name="datenaissance" value="<?= htmlspecialchars(isset($datenaissanceeleve) ? $datenaissanceeleve : ''); ?>">
			</div>
			<div class="text-center pt-3">
			    <button type="submit" class="btn btn-primary">Mettre à jour le profil</button>
			</div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
// Formatage automatique du numéro de téléphone
document.getElementById('telephone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, ''); // Supprimer tout sauf les chiffres
    
    if (value.length > 0) {
        // Limiter à 10 chiffres
        value = value.substring(0, 10);
        
        // Formater : XX XX XX XX XX
        let formatted = value.replace(/(\d{2})(?=\d)/g, '$1 ').trim();
        e.target.value = formatted;
    }
});
</script>