<main class="container mt-5 pt-5 mb-5">
    <section id="creer-compte-form" class="py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="text-center mb-4">
                    <img src="/public/images/logo_cds49.jpeg" alt="Logo CDS 49" style="max-width: 150px; border-radius: 10px;">
                </div>
				<h2 class="display-5 fw-light text-center mb-4">Inscription</h2>
				<h3 class="fs-6 text-muted text-center mb-4">Les champs marqués avec (*) sont obligatoires.</h3>

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

				<?php if (!empty($_SESSION['forfait_selectionne'])): ?>
					<div class="alert alert-info" role="alert">
						<i class="fas fa-info-circle me-2"></i>
						Vous avez sélectionné un forfait. Après inscription, vous pourrez confirmer votre abonnement.
					</div>
				<?php endif; ?>

                <form method="POST" action="creer-compte.html">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom <span class="text-primary">*</span></label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom <span class="text-primary">*</span></label>
                        <input type="text" class="form-control" id="prenom" name="prenom" required>
                    </div>
					<div class="mb-3">
						<label for="telephone" class="form-label">Numéro de téléphone <span class="text-muted">(optionnel)</span></label>
						<input type="tel" class="form-control" id="telephone" name="telephone" 
							pattern="[0-9\s\+\-\.\(\)]+"
							title="Format français : 01 23 45 67 89">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse Email <span class="text-primary">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe <span class="text-primary">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="form-text">
                            <strong>Le mot de passe doit contenir :</strong> au moins 8 caractères, 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm-password" class="form-label">Confirmer le mot de passe <span class="text-primary">*</span></label>
                        <input type="password" class="form-control" id="confirm-password" name="confirm_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="date_naissance" class="form-label">Date de naissance <span class="text-primary">*</span></label>
                        <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Créer mon compte</button>
                    </div>
                </form>
                <p class="text-center mt-4">
                    Déjà un compte ? <a href="connexion.html">Connectez-vous ici</a>.
                </p>
            </div>
        </div>
    </section>
</main>

<script>
document.getElementById('telephone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    
    if (value.length > 0) {
        // Limiter à 10 chiffres
        value = value.substring(0, 10);
        
        // Formater : XX XX XX XX XX
        let formatted = value.replace(/(\d{2})(?=\d)/g, '$1 ').trim();
        e.target.value = formatted;
    }
});
</script>