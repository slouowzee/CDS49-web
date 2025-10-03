<main class="container mt-3 mb-5">
    <section id="demande-acces-form" class="py-3">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="text-center mb-4">
                    <img src="/public/images/logo_cds49.jpeg" alt="Logo CDS 49" style="max-width: 150px; border-radius: 10px;">
                </div>
                <h2 class="display-5 fw-light text-center mb-4">Demande d'accès à l'API</h2>

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
                        <label for="email" class="form-label">Adresse Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prenom" class="form-label">Prénom</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Pourquoi souhaitez-vous accéder à l'API ?</label>
                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Décrivez brièvement votre projet ou vos besoins..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                        <div class="form-text">Ce champ est facultatif mais nous aide à mieux comprendre votre demande.</div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Envoyer ma demande</button>
                    </div>
                </form>
                
                <p class="text-center mt-4">
                    Déjà un accès ? <a href="connexion-api.html">Se connecter à l'API</a>
                </p>
            </div>
        </div>
    </section>
</main>
