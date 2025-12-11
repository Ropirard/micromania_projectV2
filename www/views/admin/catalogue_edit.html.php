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
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
                    <div class="bg-black rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden p-6 flex gap-6">
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
                            <p class="text-gray-600 text-sm mb-4">
                                <?= htmlspecialchars($game->description) ?>
                            </p>
                            <div class="flex items-center gap-6 mb-4">
                                <span class="text-2xl font-bold <?= $randomColor ?>">
                                    <?= number_format($game->price, 2, ',', ' ') ?> €
                                </span>
                                <span class="text-sm text-gray-500">
                                    Stock: <span class="font-semibold"><?= htmlspecialchars($game->stock) ?></span>
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
                        
                        <div class="flex flex-col gap-3 justify-center items-center">
                            <a href="/admin/game/edit/<?= $game->id ?>" class="text-blue-400 hover:text-blue-300 transition-colors p-2" title="Éditer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="M23 5v2h-1v1h-1v1h-1v1h-1V9h-1V8h-1V7h-1V6h-1V5h-1V4h1V3h1V2h1V1h2v1h1v1h1v1h1v1zm-6 5V9h-1V8h-1V7h-1V6h-2v1h-1v1h-1v1H9v1H8v1H7v1H6v1H5v1H4v1H3v1H2v1H1v6h6v-1h1v-1h1v-1h1v-1h1v-1h1v-1h1v-1h1v-1h1v-1h1v-1h1v-1h1v-2zm-2 2v1h-1v1h-1v1h-1v1h-1v1h-1v1H9v1H8v1H7v1H3v-4h1v-1h1v-1h1v-1h1v-1h1v-1h1v-1h1v-1h1V9h1V8h2v1h1v1h1v2z"/>
                                </svg>
                                        </a>
                            <a href="javascript:void(0)" onclick="confirmDelete(<?= $game->id ?>, '<?= addslashes(htmlspecialchars($game->title)) ?>')" class="text-red-400 hover:text-red-300 transition-colors p-2 inline-block" title="Supprimer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 11v6m-4-6v6M6 7v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7M4 7h16M7 7l2-4h6l2 4"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
    
    <script>
    function confirmDelete(gameId, gameTitle) {
        const message = `⚠️ Êtes-vous sûr de vouloir supprimer "${gameTitle}" ?\n\nCette action est irréversible et supprimera :\n• Le jeu\n• Ses images\n• Ses références dans les paniers`;
        
        if (confirm(message)) {
            window.location.href = '/admin/game/delete/' + gameId;
        }
    }
    </script>
</body>
</html>