<main class="container mt-5 pt-5 mb-5">
    <section id="connexion-form" class="py-5">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="text-center mb-4">
                    <img src="/public/images/logo_cds49.jpeg" alt="Logo CDS 49" style="max-width: 150px; border-radius: 10px;">
                </div>
                <h2 class="display-5 fw-light text-center mb-4">Formulaire de demande d'accès à l'API</h2>
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

                <form method="POST" action="demande-acces-api.html">
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse Email<span class="text-primary">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
					<div class="mb-3">
						<label for="nom" class="form-label">Nom</label>
						<input type="text" class="form-control" id="nom" name="nom">
					</div>
					<div class="mb-3">
						<label for="prenom" class="form-label">Prénom</label>
						<input type="text" class="form-control" id="prenom" name="prenom">
					</div>
					<div class="mb-3">
						<label for="raison" class="form-label">Raison de la demande <span class="text-primary">*</span></label>
						<textarea class="form-control" id="raison" name="raison" rows="4" required></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Envoyer la demande</button>
                    </div>
                </form>
                <p class="text-center mt-4">
                    Vous avez déjà un compte vérifié ? <a href="connexion-api.html">Connectez-vous ici</a>.
                </p>
            </div>
        </div>
    </section>
</main>