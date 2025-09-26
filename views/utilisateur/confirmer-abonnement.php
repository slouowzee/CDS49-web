<main class="container mt-5 pt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($step) && $step === 'success'): ?>
                <!-- Forfait activé avec succès -->
                <div class="text-center">
                    <div class="bg-success text-white rounded p-4 mb-4">
                        <h2>✓ Abonnement Confirmé</h2>
                        <p class="mb-0">Votre forfait est maintenant actif</p>
                    </div>
                    <div class="mt-3">
                        <a href="/mon-compte/" class="btn btn-primary me-2">Mon Espace</a>
                        <a href="/" class="btn btn-secondary">Accueil</a>
                    </div>
                </div>

            <?php elseif (isset($step) && $step === 'error'): ?>
                <!-- Affichage des erreurs -->
                <div class="text-center">
                    <div class="bg-danger text-white rounded p-4 mb-4">
                        <h2>⚠ Erreur</h2>
                        <p class="mb-0">Une erreur est survenue</p>
                    </div>
                    <a href="/forfaits.html" class="btn btn-primary">Retour aux Forfaits</a>
                </div>

            <?php else: ?>
                <!-- Confirmation finale après connexion/inscription -->
                <div class="text-center">
                    <h2 class="mb-4">Confirmer votre Abonnement</h2>
                    <p class="text-success mb-4">✓ Vous êtes maintenant connecté</p>
                    
                    <?php if (isset($forfait)): ?>
                        <div class="border rounded p-4 mb-4">
                            <h4 class="text-primary"><?= htmlspecialchars($forfait->libelleforfait) ?></h4>
                            <p class="text-muted"><?= htmlspecialchars($forfait->descriptionforfait) ?></p>
                            <h5 class="text-dark">
                                <?php if ($forfait->prixforfait): ?>
                                    <?= $forfait->prixforfait ?> €
                                <?php elseif ($forfait->prixhoraire): ?>
                                    <?= $forfait->prixhoraire ?> € / heure
                                <?php else: ?>
                                    Prix sur demande
                                <?php endif; ?>
                            </h5>
                        </div>
                    <?php endif; ?>

                    <p class="mb-4">Confirmez-vous votre abonnement à ce forfait ?</p>
                    
                    <form method="POST">
                        <button type="submit" class="btn btn-success me-2">✓ Confirmer</button>
                        <a href="/forfaits.html" class="btn btn-secondary">Annuler</a>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>