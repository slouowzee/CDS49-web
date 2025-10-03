<main class="container mt-5 pt-5 mb-5">
    <section id="creer-compte-api-form" class="py-5">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="text-center mb-4">
                    <img src="/public/images/logo_cds49.jpeg" alt="Logo CDS 49" style="max-width: 150px; border-radius: 10px;">
                </div>
                <h2 class="display-5 fw-light text-center mb-4">Créer votre compte API</h2>
                <p class="text-center text-muted mb-4">Finalisez votre accès à l'API documentaire</p>

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

                <?php if (!empty($email_invalide)) { ?>
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Email non trouvé dans les demandes d'accès ou demande déjà traitée.
                        <br><a href="demande-acces-api.html" class="alert-link">Faire une nouvelle demande</a>
                    </div>
                <?php } else { ?>
                    <form method="POST" action="creer-compte-api.html">
                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($email ?? '') ?>" readonly 
                                   style="background-color: #f8f9fa;">
                            <div class="form-text">Cette adresse email a été pré-remplie depuis votre demande d'accès.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="form-text">
                                Le mot de passe doit contenir au moins 8 caractères, 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="accept_terms" name="accept_terms" required>
                            <label class="form-check-label" for="accept_terms">
                                J'accepte les conditions d'utilisation de l'API
                            </label>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Créer mon compte API</button>
                        </div>
                    </form>
                    
                    <p class="text-center mt-4">
                        Vous avez déjà un compte ? <a href="connexion-api.html">Se connecter à l'API</a>
                    </p>
                <?php } ?>
            </div>
        </div>
    </section>
</main>