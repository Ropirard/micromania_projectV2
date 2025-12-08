<?php
use JulienLinard\Core\View\ViewHelper;
?>

<header class="mt-8 ml-8 bg-gray-200 h-20 flex items-center justify-between">
    <div class="flex items-center">
        <div class="flex flex-col ml-8">
            <h1 class="text-4xl font-bold text-gray-800"> <?= htmlspecialchars($title) ?><span class="text-xs">.com</span></h1>
            <?php if(!isset($_SESSION['auth_user'])): ?>
                <a href="/login" class="text-xl text-blue-600 mt-2">Connexion</a>
            <?php endif; ?>
        </div>
        <?php if(!isset($_SESSION['auth_user'])): ?>
            <a href="/register" class="text-5xl text-red-600 ml-16">Inscription</a>
        <?php endif; ?>
    </div>
    
    <?php if(isset($_SESSION['auth_user'])): ?>
        <div class="flex items-center gap-6 mr-8">
            <span class="text-gray-700 font-medium">Bienvenue, <?= htmlspecialchars($user->firstname ?? 'Utilisateur') ?></span>
            <form action="/logout" method="POST" class="inline">
                <?= ViewHelper::csrfField() ?>
                <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold transition-colors">
                    Déconnexion
                </button>
            </form>
        </div>
    <?php endif; ?>
</header>

<main class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-3 gap-6">
        <!-- Section gauche -->
        <section>
            <?php if(isset($_SESSION['auth_user'])): ?>
                <a href="/historique" class="text-xl text-gray-900 font-semibold mb-4 hover:text-indigo-600 block"> Historique</a>
                <a href="/wishlist" class="text-xl text-gray-500 mb-6 hover:text-green-600 block"> Wishlist </a>
                <a href="/panier" class="text-xl text-gray-600 hover:text-blue-600 block"> Panier </a>
            <?php else: ?>
                <a href="#" class="auth-required text-xl text-gray-400 font-semibold mb-4 hover:text-indigo-600 block cursor-not-allowed"> Historique</a>
                <a href="#" class="auth-required text-xl text-gray-400 mb-6 hover:text-green-600 block cursor-not-allowed"> Wishlist </a>
                <a href="#" class="auth-required text-xl text-gray-400 hover:text-blue-600 block cursor-not-allowed"> Panier </a>
            <?php endif; ?>
            
            <!-- Bloc supplémentaire -->
            <div class="mt-8 p-4 bg-white rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Catalogue de jeu</h3>
            </div>
        </section>

        <!-- Section centre -->
        <section>
        </section>

        <!-- Section droite -->
        <section>        
        </section>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si l'utilisateur est connecté (à adapter selon ton système)
    const isLoggedIn = <?= json_encode(isset($_SESSION['auth_user']) && $_SESSION['auth_user'] !== null) ?>;
    
    if (!isLoggedIn) {
        document.querySelectorAll('.auth-required').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const confirmed = confirm('Vous devez être connecté pour accéder à cette page.\n\nCliquez sur OK pour vous connecter, ou Annuler pour rester sur cette page.');
                
                if (confirmed) {
                    window.location.href = '/login';
                }
            });
        });
    }
});
</script>