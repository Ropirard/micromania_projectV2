<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Historique - Micromania</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Mon Historique</h1>
        <a href="/" class="px-6 py-2 bg-red-600 hover:bg-blue-500 text-white font-semibold transition-colors">
            Retour à l'accueil
        </a>
    </div>
    
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (empty($orders)): ?>
        <div class="bg-white rounded-lg shadow p-6 text-center py-8">
            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-gray-600 text-lg mb-6">Vous n'avez pas encore de commandes.</p>
            <a href="/" class="inline-block px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-semibold transition-colors">
                Voir le catalogue
            </a>
        </div>
    <?php else: ?>
        <div class="space-y-6">
            <?php foreach ($orders as $order): ?>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4 border-b pb-3">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Commande #<?= $order['id'] ?></h2>
                            <p class="text-sm text-gray-500">
                                Validée le <?= date('d/m/Y à H:i', strtotime($order['validated_at'])) ?>
                            </p>
                            
                            <!-- Statut de livraison -->
                            <div class="mt-2 inline-flex items-center">
                                <?php
                                // Correction de l'encodage si nécessaire
                                $deliveryStatus = $order['delivery_status'] ?? 'En cours de préparation';
                                if (!mb_check_encoding($deliveryStatus, 'UTF-8')) {
                                    $deliveryStatus = mb_convert_encoding($deliveryStatus, 'UTF-8', 'ISO-8859-1');
                                }
                                $statusConfig = match($deliveryStatus) {
                                    'En cours de préparation' => [
                                        'bg' => 'bg-yellow-100',
                                        'text' => 'text-yellow-800',
                                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                                    ],
                                    'Expédiée' => [
                                        'bg' => 'bg-blue-100',
                                        'text' => 'text-blue-800',
                                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>'
                                    ],
                                    'Livrée' => [
                                        'bg' => 'bg-green-100',
                                        'text' => 'text-green-800',
                                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
                                    ],
                                    default => [
                                        'bg' => 'bg-gray-100',
                                        'text' => 'text-gray-800',
                                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                                    ]
                                };
                                ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= $statusConfig['bg'] ?> <?= $statusConfig['text'] ?>">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <?= $statusConfig['icon'] ?>
                                    </svg>
                                    <?= htmlspecialchars($deliveryStatus) ?>
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <?php 
                            $orderTotal = 0;
                            foreach ($order['games'] as $game) {
                                $orderTotal += $game->price;
                            }
                            ?>
                            <span class="text-2xl font-bold text-green-600">
                                <?= number_format($orderTotal, 2, ',', ' ') ?> €
                            </span>
                            <p class="text-sm text-gray-500">
                                <?= count($order['games']) ?> article<?= count($order['games']) > 1 ? 's' : '' ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($order['games'] as $game): ?>
                            <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-3">
                                <?php if (!empty($game->media)): ?>
                                    <img src="<?= htmlspecialchars($game->media[0]->path) ?>" 
                                         alt="<?= htmlspecialchars($game->title) ?>" 
                                         class="w-16 h-16 object-cover rounded">
                                <?php else: ?>
                                    <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center flex-shrink-0">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-bold text-gray-900 truncate">
                                        <?= htmlspecialchars($game->title) ?>
                                    </h3>
                                    <p class="text-lg font-bold text-blue-600">
                                        <?= number_format($game->price, 2, ',', ' ') ?> €
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
