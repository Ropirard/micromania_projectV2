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
            <a href="/logout" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold transition-colors inline-block cursor-pointer">
                Déconnexion
            </a>
        </div>
    <?php endif; ?>
</header>

<main class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 gap-6">
        <!-- Section menu utilisateur -->
        <section class="flex gap-6 items-center">
            <?php if(isset($_SESSION['auth_user'])): ?>
                <a href="/historique" class="text-xl text-gray-900 font-semibold hover:text-indigo-600"> Historique</a>
                <a href="/wishlist" class="text-xl text-gray-500 hover:text-green-600"> Wishlist </a>
                <a href="/panier" class="text-xl text-gray-600 hover:text-blue-600"> Panier </a>
            <?php else: ?>
                <a href="#" class="auth-required text-xl text-gray-400 font-semibold hover:text-indigo-600 cursor-not-allowed"> Historique</a>
                <a href="#" class="auth-required text-xl text-gray-400 hover:text-green-600 cursor-not-allowed"> Wishlist </a>
                <a href="#" class="auth-required text-xl text-gray-400 hover:text-blue-600 cursor-not-allowed"> Panier </a>
            <?php endif; ?>
        </section>

        <!-- Section catalogue de jeu -->
        <section class="max-w-[33vw]">
            <div class="bg-white rounded-lg shadow p-6 overflow-y-auto">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Catalogue de jeu</h2>
                
                <?php if (empty($games)): ?>
                    <div class="bg-gray-50 rounded-lg p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun jeu disponible</h3>
                        <p class="text-sm text-gray-500">Revenez plus tard pour découvrir nos nouveautés !</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-4 gap-4">
                        <?php 
                        $colors = ['text-red-600', 'text-blue-600', 'text-green-600', 'text-purple-600', 'text-pink-600', 'text-yellow-600', 'text-indigo-600', 'text-orange-600'];
                        foreach ($games as $game): 
                            $randomColor = $colors[array_rand($colors)];
                        ?>
                            <div class="bg-gray-50 rounded-lg shadow-md hover:shadow-xl transition-shadow overflow-hidden flex flex-col">
                                <?php if (!empty($game->media)): ?>
                                    <img src="<?= htmlspecialchars($game->media[0]->path) ?>" 
                                         alt="<?= htmlspecialchars($game->title) ?>" 
                                         class="w-full h-48 object-cover">
                                <?php else: ?>
                                    <div class="w-full h-48 bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="p-4 flex-1 flex flex-col">
                                    <div class="flex items-start justify-between mb-2">
                                        <h3 class="text-base font-bold text-gray-900 break-words flex-1">
                                            <?= htmlspecialchars($game->title) ?>
                                        </h3>
                                        <?php if(isset($_SESSION['auth_user'])): ?>
                                            <a href="#" onclick="return confirmAddToCart(<?= $game->id ?>)" class="ml-2 text-blue-600 hover:text-blue-800 transition-colors flex-shrink-0" title="Ajouter au panier">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 24 24">
                                                    <path fill="currentColor" d="M21.822 7.431A1 1 0 0 0 21 7H7.333L6.179 4.23A1.99 1.99 0 0 0 4.333 3H2v2h2.333l4.744 11.385A1 1 0 0 0 10 17h8c.417 0 .79-.259.937-.648l3-8a1 1 0 0 0-.115-.921M17.307 15h-6.64l-2.5-6h11.39z"/>
                                                    <circle cx="10.5" cy="19.5" r="1.5" fill="currentColor"/>
                                                    <circle cx="17.5" cy="19.5" r="1.5" fill="currentColor"/>
                                                </svg>
                                            </a>
                                        <?php else: ?>
                                            <button class="add-to-cart-auth-required ml-2 text-gray-400 hover:text-blue-600 transition-colors flex-shrink-0" title="Ajouter au panier">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 24 24">
                                                    <path fill="currentColor" d="M21.822 7.431A1 1 0 0 0 21 7H7.333L6.179 4.23A1.99 1.99 0 0 0 4.333 3H2v2h2.333l4.744 11.385A1 1 0 0 0 10 17h8c.417 0 .79-.259.937-.648l3-8a1 1 0 0 0-.115-.921M17.307 15h-6.64l-2.5-6h11.39z"/>
                                                    <circle cx="10.5" cy="19.5" r="1.5" fill="currentColor"/>
                                                    <circle cx="17.5" cy="19.5" r="1.5" fill="currentColor"/>
                                                </svg>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-3 break-words flex-1">
                                        <?= htmlspecialchars($game->description) ?>
                                    </p>
                                    
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-xl font-bold <?= $randomColor ?>">
                                            <?= number_format($game->price, 2, ',', ' ') ?> €
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            Stock: <?= $game->stock ?>
                                        </span>
                                    </div>
                                    
                                    <?php if (!empty($game->genres)): ?>
                                        <div class="flex flex-wrap gap-1 mb-2">
                                            <?php foreach (array_slice($game->genres, 0, 2) as $genre): ?>
                                                <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full">
                                                    <?= htmlspecialchars($genre->name) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($game->plateforms)): ?>
                                        <div class="flex flex-wrap gap-1">
                                            <?php foreach (array_slice($game->plateforms, 0, 2) as $plateform): ?>
                                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
                                                    <?= htmlspecialchars($plateform->name) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</main>

<script>
async function confirmAddToCart(gameId) {
    if (confirm('Voulez-vous vraiment ajouter ce jeu à votre panier ?')) {
        try {
            const response = await fetch('/panier/add/' + gameId, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.game) {
                    alert('Le jeu "' + data.game + '" a été ajouté à votre panier.');
                }
            } else {
                alert('Une erreur est survenue lors de l\'ajout au panier.');
            }
            
            // Rediriger vers la page d'accueil
            window.location.href = '/';
        } catch (error) {
            console.error('Erreur:', error);
            // En cas d'erreur, redirection simple
            window.location.href = '/panier/add/' + gameId;
        }
    }
    return false;
}

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
        
        document.querySelectorAll('.add-to-cart-auth-required').forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const confirmed = confirm('Vous devez être connecté pour ajouter des articles au panier.\n\nCliquez sur OK pour vous connecter, ou Annuler pour rester sur cette page.');
                
                if (confirmed) {
                    window.location.href = '/login';
                }
            });
        });
    }
});
</script>