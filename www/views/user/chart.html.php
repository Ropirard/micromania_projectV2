<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Mon panier</h1>
    
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($_SESSION['info'])): ?>
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($_SESSION['info']) ?>
            <?php unset($_SESSION['info']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <div class="bg-white rounded-lg shadow p-6">
        <?php if (empty($games)): ?>
            <div class="text-center py-8">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <p class="text-gray-600 text-lg mb-6">Votre panier est vide.</p>
                <a href="/" class="inline-block px-6 py-3 bg-gray-600 hover:bg-gray-500 text-whiteg font-semibold transition-colors">
                    Retour à l'accueil
                </a>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php 
                $total = 0;
                foreach ($games as $game): 
                    $total += $game->price;
                ?>
                    <div class="flex items-center gap-4 border-b pb-4">
                        <?php if (!empty($game->media)): ?>
                            <img src="<?= htmlspecialchars($game->media[0]->path) ?>" 
                                 alt="<?= htmlspecialchars($game->title) ?>" 
                                 class="w-24 h-24 object-cover rounded">
                        <?php else: ?>
                            <div class="w-24 h-24 bg-gray-200 rounded flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                        
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900"><?= htmlspecialchars($game->title) ?></h3>
                            <p class="text-sm text-gray-600"><?= htmlspecialchars($game->description) ?></p>
                            
                            <?php if (!empty($game->genres)): ?>
                                <div class="flex flex-wrap gap-1 mt-2">
                                    <?php foreach ($game->genres as $genre): ?>
                                        <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full">
                                            <?= htmlspecialchars($genre->name) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($game->plateforms)): ?>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    <?php foreach ($game->plateforms as $plateform): ?>
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
                                            <?= htmlspecialchars($plateform->name) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="text-right">
                            <p class="text-2xl font-bold text-blue-600">
                                <?= number_format($game->price, 2, ',', ' ') ?> €
                            </p>
                            <a href="/panier/remove/<?= $game->id ?>" 
                               onclick="return confirm('Voulez-vous vraiment retirer ce jeu du panier ?')"
                               class="text-sm text-red-600 hover:text-red-800 mt-2 inline-block">
                                Retirer
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="border-t pt-4 mt-4">
                    <div class="flex justify-between items-center">
                        <span class="text-xl font-bold text-gray-900">Total:</span>
                        <span class="text-3xl font-bold text-blue-600">
                            <?= number_format($total, 2, ',', ' ') ?> €
                        </span>
                    </div>
                    
                    <div class="mt-6 flex gap-4">
                        <a href="/" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-semibold transition-colors">
                            Continuer mes achats
                        </a>
                        <a href="/panier/validate" onclick="return confirm('Voulez-vous vraiment valider votre panier ?')" class="px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-lg font-semibold transition-colors">
                            Valider le panier
                        </a>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
