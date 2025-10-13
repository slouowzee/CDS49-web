<main class="container mt-5 pt-5 mb-5">
    <section id="connexion-form" class="py-5">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="text-center mb-4">
                    <img src="/public/images/logo_cds49.jpeg" alt="Logo CDS 49" style="max-width: 150px; border-radius: 10px;">
                </div>
                <h2 class="display-5 fw-light text-center mb-4">Création du compte</h2>

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

                <form method="POST" action="creer-compte-api.html<?php echo !empty($token) ? '?token='.urlencode($token) : ''; ?>">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse Email<span class="text-primary">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="<?= htmlspecialchars($email ?? '') ?>" value="<?= htmlspecialchars($email ?? '') ?>" readonly required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe<span class="text-primary">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
					<div class="mb-3">
						<label for="confirm_password" class="form-label">Confirmer le mot de passe<span class="text-primary">*</span></label>
						<input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
					</div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Créer le compte</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>