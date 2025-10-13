<main class="container mt-5 pt-5 mb-5">
    <section id="connexion-form" class="py-5">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="text-center mb-4">
                    <img src="/public/images/logo_cds49.jpeg" alt="Logo CDS 49" style="max-width: 150px; border-radius: 10px;">
                </div>
                <h2 class="display-5 fw-light text-center mb-4">Accès aux documents de l'API</h2>

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

                <form method="POST" action="connexion-api.html">
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Se connecter</button>
                    </div>
                </form>
                <p class="text-center mt-4">
                    Vous n'avez pas encore demandé l'accès ? <a href="demande-acces-api.html">Demandez-le ici</a>.
                </p>
                <p class="text-center mt-2">
                    <a href="mot-de-passe-oublie-api.html">Mot de passe oublié ?</a>
                </p>
            </div>
        </div>
    </section>
</main>