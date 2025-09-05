<main class="container mt-5 pt-5 mb-5">
    <section id="creer-compte-form" class="py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="text-center mb-4">
                    <img src="/public/images/logo_cds49.jpeg" alt="Logo CDS 49" style="max-width: 150px; border-radius: 10px;">
                </div>
                <h2 class="display-5 fw-light text-center mb-4">Inscription</h2>

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

                <form method="POST" action="creer-compte.html">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm-password" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" id="confirm-password" name="confirm_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="date_naissance" class="form-label">Date de naissance</label>
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