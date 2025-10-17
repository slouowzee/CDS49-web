<main class="container mt-5 pt-5 mb-5">
	<section id="mot-de-passe-oublie-form" class="py-5">
		<div class="row justify-content-center">
			<div class="col-lg-6">
				<div class="text-center mb-4">
					<img src="/public/images/logo_cds49.jpeg" alt="Logo CDS 49" style="max-width: 150px; border-radius: 10px;">
				</div>
				<h2 class="display-5 fw-light text-center mb-4">Mot de passe oublié</h2>

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

				<?php if ((empty($token) && empty($token_valide) && empty($show_token_form))) { ?>
					<form method="POST" action="mot-de-passe-oublie-api.html">
						<div class="mb-3">
							<label for="email" class="form-label">Adresse Email</label>
							<input type="email" class="form-control" id="email" name="email" required placeholder="Entrez votre adresse email">
						</div>
						<div class="d-grid">
							<button type="submit" class="btn btn-primary btn-lg">Réinitialiser le mot de passe</button>
						</div>
					</form>
					<p class="text-center mt-4">
						Vous vous souvenez de votre mot de passe ? <a href="connexion-api.html">Connectez-vous ici</a>.
					</p>
				<?php } elseif ((!empty($token) && empty($token_valide)) || !empty($show_token_form)) { ?>
					<form method="POST" action="mot-de-passe-oublie-api.html">
						<div class="mb-3">
							<label for="token" class="form-label">Jeton de réinitialisation</label>
							<input type="text" class="form-control" id="token" name="token" required placeholder="Entrez le jeton reçu par email">
							<div class="form-text">Le jeton expire après 15 minutes.</div>
						</div>
						<div class="d-grid">
							<button type="submit" class="btn btn-primary btn-lg">Valider le jeton</button>
						</div>
					</form>
					
					<!-- Formulaire pour redemander l'email -->
					<?php if (!empty($email_used)) { ?>
					<div class="mt-4 p-3 bg-light rounded">
						<p class="mb-3 text-muted"><small>Vous n'avez pas reçu l'email ?</small></p>
						<form method="POST" action="mot-de-passe-oublie-api.html">
							<input type="hidden" name="resend_email" value="<?= htmlspecialchars($email_used) ?>">
							<div class="d-grid">
								<button type="submit" class="btn btn-outline-primary btn-sm">Renvoyer l'email à <?= htmlspecialchars($email_used) ?></button>
							</div>
						</form>
					</div>
					<?php } ?>
					
					<p class="text-center mt-4">
						<a href="mot-de-passe-oublie-api.html" class="btn btn-outline-secondary btn-sm">← Retour à la saisie d'email</a>
					</p>
					<p class="text-center mt-3">
						Vous vous souvenez de votre mot de passe ? <a href="connexion.html">Connectez-vous ici</a>.
					</p>
				<?php } else { ?>
					<form method="POST" action="mot-de-passe-oublie-api.html">
						<input type="hidden" name="token" value="<?= htmlspecialchars($token_valide) ?>">
						
						<div class="mb-3">
							<label for="nouveau_mot_de_passe" class="form-label">Nouveau mot de passe</label>
							<input type="password" class="form-control" id="nouveau_mot_de_passe" name="nouveau_mot_de_passe" required placeholder="Entrez votre nouveau mot de passe">
							<div class="form-text">
								<strong>Le mot de passe doit contenir :</strong> au moins 8 caractères, 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial.
							</div>
						</div>
						
						<div class="mb-3">
							<label for="confirm_mot_de_passe" class="form-label">Confirmer le nouveau mot de passe</label>
							<input type="password" class="form-control" id="confirm_mot_de_passe" name="confirm_mot_de_passe" required placeholder="Confirmez votre nouveau mot de passe" minlength="6">
						</div>
						
						<div class="d-grid">
							<button type="submit" class="btn btn-success btn-lg">Réinitialiser le mot de passe</button>
						</div>
					</form>
					<p class="text-center mt-4">
						<a href="connexion-api.html">Retour à la connexion</a>
					</p>
				<?php } ?>
			</div>
		</div>
	</section>
</main>