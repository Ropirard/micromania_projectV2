<?php use JulienLinard\Core\View\ViewHelper ?>
<div class="min-h-screen bg-gradient-to-br from-indigo-200 via-white to-purple-200">
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-10 ">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="/admin/edit/list" class="text-indigo-600 hover:text-gray-900 transition-colors">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18l-6-6l6-6"/></svg>
                    </a>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent"><?= htmlspecialchars($title)  ?></h1>
                </div>
                <div class="flex items-center space-x-6">
                    <p class="text-sm text-gray-600">
                        Bonjour, <span class="font-semibold text-gray-900"><?= htmlspecialchars($user->firstname ?? $user->email) ?></span>
                    </p>
                    <form action="/logout" method="POST" onsubmit="return confirm('Êtes vous sûr de vouloir vous déconnecter ?')">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                        <button type="submit" class="text-sm text-gray-600 hover:text-gay-900 transition-colors">
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-br from-indigo-600 to-purple-600 px-6 py-8">
                <h2 class="text-2xl font-bold text-white">Modifier le jeu</h2>
                <p class="text-indigo-100 mt-2">Mettre à jour les informations</p>
            </div>
            <form action="/admin/game/update/<?= $game->id ?>" method="POST" class="p-6 space-y-6">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                <?php if(isset($error)): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg flex">
                        <p class="text-sm text-red-700"><?= $error ?></p>
                    </div>
                <?php endif ?>
                <!-- Input pour title -->
                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">Titre du jeu</label>
                    <input id="title" value="<?= htmlspecialchars($game->title ?? '') ?>" type="text" name="title" placeholder="Ex : The Legend of Zelda" required  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 
                    focus:ring-indigo-500 focus:border-transparent transition-all duration-200 outline-none
                    text-gray-900 placeholder-gray-400">
                </div>
                <!-- Input pour la descrption -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows=6 placeholder="Description du jeu..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 
                    focus:ring-indigo-500 focus:border-transparent transition-all duration-200 outline-none
                    text-gray-900 placeholder-gray-400 resize-none"><?= htmlspecialchars($game->description ?? '') ?></textarea>
                </div>

                <!-- Input pour le prix et stock -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">Prix (€)</label>
                        <input id="price" type="number" step="0.01" min="0" name="price" value="<?= htmlspecialchars($game->price ?? '') ?>" placeholder="59.99" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 
                        focus:ring-indigo-500 focus:border-transparent transition-all duration-200 outline-none
                        text-gray-900 placeholder-gray-400">
                    </div>
                    <div>
                        <label for="stock" class="block text-sm font-semibold text-gray-700 mb-2">Stock</label>
                        <input id="stock" type="number" min="0" name="stock" value="<?= htmlspecialchars($game->stock ?? '') ?>" placeholder="50" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 
                        focus:ring-indigo-500 focus:border-transparent transition-all duration-200 outline-none
                        text-gray-900 placeholder-gray-400">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Genre(s)
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <?php if(isset($genres) && !empty($genres)): ?>
                            <?php 
                            // Récupérer les IDs des genres du jeu
                            $gameGenreIds = array_map(fn($g) => $g->id, $game->genres ?? []);
                            ?>
                            <?php foreach($genres as $genre): ?>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        name="genres[]" 
                                        value="<?= htmlspecialchars($genre->id) ?>"
                                        <?= in_array($genre->id, $gameGenreIds) ? 'checked' : '' ?>
                                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    <span class="text-sm text-gray-700"><?= htmlspecialchars($genre->name) ?></span>
                                </label>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-sm text-gray-500 col-span-2">Aucun genre disponible</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Plateforme(s)
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <?php if(isset($plateforms) && !empty($plateforms)): ?>
                            <?php 
                            // Récupérer les IDs des plateformes du jeu
                            $gamePlateformIds = array_map(fn($p) => $p->id, $game->plateforms ?? []);
                            ?>
                            <?php foreach($plateforms as $plateform): ?>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        name="plateforms[]" 
                                        value="<?= htmlspecialchars($plateform->id) ?>"
                                        <?= in_array($plateform->id, $gamePlateformIds) ? 'checked' : '' ?>
                                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    <span class="text-sm text-gray-700"><?= htmlspecialchars($plateform->name) ?></span>
                                </label>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-sm text-gray-500 col-span-2">Aucune plateforme disponible</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Boutons de soumission ou annulation -->
                <div class="flex items-center justify-center space-x-4 pt-4 border-t border-gray-200">
                    <a href="/admin/edit/list" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-all duration-200">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Mettre à jour le jeu
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
