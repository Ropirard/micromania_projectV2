<?php use JulienLinard\Core\View\ViewHelper ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - Administration</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<header class="mt-8 ml-8 bg-gray-200 h-20 flex items-center justify-between">
    <div class="flex items-center">
        <div class="flex flex-col ml-8">
            <a href="/admin" class="text-4xl font-bold text-gray-800 hover:text-gray-600 transition-colors"> <?= htmlspecialchars($title) ?><span class="text-xs">.com</span></a>
            <?php if (!isset($_SESSION['auth_user'])): ?>
                <a href="/login" class="text-xl text-blue-600 mt-2">Connexion</a>
            <?php endif; ?>
        </div>
        <?php if (!isset($_SESSION['auth_user'])): ?>
            <a href="/register" class="text-5xl text-red-600 ml-16">Inscription</a>
        <?php endif; ?>
    </div>
    
    <?php if (isset($_SESSION['auth_user'])): ?>
        <div class="flex items-center gap-6 mr-8">
            <span class="text-gray-700 font-medium">Bienvenue, <?= htmlspecialchars($user->firstname ?? 'Utilisateur') ?></span>
            <a href="/logout" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold transition-colors inline-block cursor-pointer">
                Déconnexion
            </a>
        </div>
    <?php endif; ?>
</header>

<div class="min-h-screen bg-stone-100">

    <main class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white shadow-xl overflow-hidden">
            <div class="bg-pink-600 px-6 py-8">
                <h2 class="text-2xl font-bold text-white">Modifier le jeu</h2>
                <p class="text-indigo-100 mt-2">Mettre à jour les informations</p>
            </div>
            <form action="/admin/game/update/<?= $game->id ?>" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                <?php if(isset($error)): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg flex">
                        <p class="text-sm text-red-700"><?= $error ?></p>
                    </div>
                <?php endif ?>
                
                <!-- Image actuelle et upload -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Image du jeu
                    </label>
                    
                    <?php if (!empty($game->media)): ?>
                        <div class="bg-white rounded-lg p-4 mb-4 border border-gray-200">
                            <div class="flex items-start gap-4">
                                <div class="relative group">
                                    <img src="<?= htmlspecialchars($game->media[0]->path) ?>" 
                                         alt="<?= htmlspecialchars($game->title) ?>" 
                                         class="w-40 h-40 object-cover rounded-lg border-2 border-indigo-200 shadow-md">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 mb-1">Image actuelle</p>
                                    <p class="text-xs text-gray-600 mb-1">
                                        <span class="font-medium">Fichier :</span> <?= htmlspecialchars($game->media[0]->original_filename) ?>
                                    </p>
                                    <p class="text-xs text-gray-600 mb-1">
                                        <span class="font-medium">Type :</span> <?= htmlspecialchars($game->media[0]->mime_type ?? 'image') ?>
                                    </p>
                                    <?php if (!empty($game->media[0]->size)): ?>
                                        <p class="text-xs text-gray-600">
                                            <span class="font-medium">Taille :</span> <?= round($game->media[0]->size / 1024, 2) ?> Ko
                                        </p>
                                    <?php endif; ?>
                                    <div class="mt-3 flex items-center gap-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Image chargée
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-t border-gray-200 pt-4">
                            <p class="text-xs text-indigo-600 mb-2 font-medium">💡 Modifier l'image</p>
                            <p class="text-xs text-gray-500 mb-3">Téléchargez une nouvelle image pour remplacer l'actuelle. L'ancienne sera automatiquement supprimée.</p>
                    <?php else: ?>
                        <div class="bg-white rounded-lg p-6 mb-4 border-2 border-dashed border-gray-300 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm text-gray-600 mb-1">Aucune image pour ce jeu</p>
                            <p class="text-xs text-gray-500">Ajoutez une image pour améliorer la présentation</p>
                        </div>
                    <?php endif; ?>
                    
                    <label for="media" class="cursor-pointer inline-flex items-center px-4 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg shadow-md hover:shadow-lg text-sm font-semibold transition-all duration-200 transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <?= !empty($game->media) ? 'Changer l\'image' : 'Ajouter une image' ?>
                    </label>
                    <input id="media" name="media" type="file" class="sr-only" accept="image/jpeg,image/jpg,image/png,image/avif,image/webp" onchange="displayFileName(this)">
                    <span id="file-name" class="ml-3 text-sm text-gray-600"></span>
                    <p class="mt-3 text-xs text-gray-500">
                        <strong>Formats acceptés :</strong> JPG, JPEG, PNG, AVIF, WebP • <strong>Taille max :</strong> 5 Mo
                    </p>
                    <?php if (!empty($game->media)): ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <script>
                function displayFileName(input) {
                    const fileName = input.files[0]?.name;
                    const fileNameDisplay = document.getElementById('file-name');
                    if (fileName) {
                        fileNameDisplay.textContent = '📎 ' + fileName;
                        fileNameDisplay.classList.add('font-medium', 'text-indigo-600');
                    }
                }
                </script>
                
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
                    <a href="/admin/edit/list" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-all duration-200">
                        Mettre à jour le jeu
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
