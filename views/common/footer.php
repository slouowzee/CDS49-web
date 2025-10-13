<footer class="bg-dark text-white text-center py-4">
    <div class="container">
        <p class="mb-0">&copy; 2025 CDS 49 - Chevrollier Driving School. Tous droits réservés.</p>
    <p class="mb-0">Conception 1FoSio - <a href="/connexion-api.html" class="text-white">Documentation API</a>
    <?php
    // Affiche le lien de déconnexion API seulement si un compte API est connecté
    if (!function_exists('\utils\SessionHelpers::init')) {
        // Si la classe n'est pas autochargée, on démarre la session de façon sûre
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    } else {
        \utils\SessionHelpers::init();
    }

    if (\utils\SessionHelpers::isLoginApi()) {
        echo ' | <a href="/deconnexion-api.html" class="text-white">Déconnexion API</a>';
    }
    ?>
    </p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>