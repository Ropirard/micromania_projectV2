<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - Micromania</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 min-h-screen">
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="/admin" class="text-indigo-600 hover:text-gray-900 transition-colors">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 12l8-8l8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5"/>
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        <?= htmlspecialchars($title) ?>
                    </h1>
                </div>
                <div class="flex items-center space-x-6">
                    <p class="text-sm text-gray-600">
                        Bonjour, <span class="font-semibold text-gray-900"><?= htmlspecialchars($user->firstname ?? $user->email) ?></span>
                    </p>
                    <a href="/logout" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')" class="text-sm text-gray-600 hover:text-gray-900 transition-colors cursor-pointer">
                        Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <?php if (empty($games)): ?>
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun jeu</h3>
                <p class="mt-1 text-sm text-gray-500">Commencez par ajouter un jeu au catalogue.</p>
                <div class="mt-6">
                    <a href="/admin/create" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        + Ajouter un jeu
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="space-y-8">
                <?php 
                $colors = ['text-red-600', 'text-blue-600', 'text-green-600', 'text-purple-600', 'text-pink-600', 'text-yellow-600', 'text-indigo-600', 'text-orange-600'];
                foreach ($games as $game): 
                    $randomColor = $colors[array_rand($colors)];
                ?>
                    <div class="bg-black rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden p-6">
                        <div class="flex gap-6">
                            <?php if (!empty($game->media) && isset($game->media[0])): ?>
                                <div class="flex-shrink-0">
                                    <img src="<?= htmlspecialchars($game->media[0]->path) ?>" 
                                         alt="<?= htmlspecialchars($game->title) ?>" 
                                         class="w-32 h-32 object-cover rounded-lg">
                                </div>
                            <?php endif; ?>
                            
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-white mb-2">
                                    <?= htmlspecialchars($game->title) ?>
                                </h3>
                                <p class="text-gray-400 text-sm mb-4">
                                    <?= htmlspecialchars($game->description) ?>
                                </p>
                                <div class="flex items-center gap-6 mb-4">
                                    <span class="text-2xl font-bold <?= $randomColor ?>">
                                        <?= number_format($game->price, 2, ',', ' ') ?> €
                                    </span>
                                    <span class="text-sm text-gray-400">
                                        Stock: <span class="font-semibold text-white"><?= htmlspecialchars($game->stock) ?></span>
                                    </span>
                                </div>
                                
                                <?php if (!empty($game->genres)): ?>
                                    <div class="mb-3">
                                        <p class="text-xs text-gray-500 mb-1">Genres:</p>
                                        <div class="flex flex-wrap gap-1">
                                            <?php foreach ($game->genres as $genre): ?>
                                                <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full">
                                                    <?= htmlspecialchars($genre->name) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($game->plateforms)): ?>
                                    <div class="mb-4">
                                        <p class="text-xs text-gray-500 mb-1">Plateformes:</p>
                                        <div class="flex flex-wrap gap-1">
                                            <?php foreach ($game->plateforms as $plateform): ?>
                                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
                                                    <?= htmlspecialchars($plateform->name) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>