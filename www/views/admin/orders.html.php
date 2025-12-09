<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - Micromania</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 min-h-screen">
<?php use JulienLinard\Core\View\ViewHelper; ?>
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
                    <form action="/logout" method="POST">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                        <button type="submit" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Toutes les commandes des utilisateurs</h2>
            <p class="text-gray-600">Gérez et consultez l'ensemble des commandes validées</p>
        </div>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-gray-600 text-lg">Aucune commande n'a encore été effectuée.</p>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($orders as $order): ?>
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <div class="flex justify-between items-start mb-4 border-b pb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 mb-1">Commande #<?= $order['id'] ?></h3>
                                <p class="text-sm text-gray-600">
                                    Validée le <?= date('d/m/Y à H:i', strtotime($order['validated_at'])) ?>
                                </p>
                                <div class="mt-2 flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">
                                        <?= htmlspecialchars($order['user']['firstname'] . ' ' . $order['user']['lastname']) ?>
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        (<?= htmlspecialchars($order['user']['email']) ?>)
                                    </span>
                                </div>
                                
                                <!-- Statut de livraison -->
                                <div class="mt-3">
                                    <form method="POST" action="/admin/orders/<?= $order['id'] ?>/status" class="status-form inline-block" data-order-id="<?= $order['id'] ?>">
                                        <?= ViewHelper::csrfField() ?>
                                        <label class="text-xs text-gray-600 block mb-1">Statut de livraison :</label>
                                        <select name="delivery_status" class="status-select text-sm border border-gray-300 rounded px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 <?php
                                            $statusColor = match($order['delivery_status'] ?? 'En cours de préparation') {
                                                'En cours de préparation' => 'bg-yellow-50 text-yellow-700 border-yellow-300',
                                                'Expédiée' => 'bg-blue-50 text-blue-700 border-blue-300',
                                                'Livrée' => 'bg-green-50 text-green-700 border-green-300',
                                                default => 'bg-gray-50 text-gray-700'
                                            };
                                            echo $statusColor;
                                        ?>">
                                            <option value="En cours de préparation" <?= ($order['delivery_status'] ?? '') === 'En cours de préparation' ? 'selected' : '' ?>>En cours de préparation</option>
                                            <option value="Expédiée" <?= ($order['delivery_status'] ?? '') === 'Expédiée' ? 'selected' : '' ?>>Expédiée</option>
                                            <option value="Livrée" <?= ($order['delivery_status'] ?? '') === 'Livrée' ? 'selected' : '' ?>>Livrée</option>
                                        </select>
                                    </form>
                                </div>
                            </div>
                            <div class="text-right">
                                <?php 
                                    $total = 0;
                                    foreach ($order['games'] as $game) {
                                        $total += $game->price;
                                    }
                                ?>
                                <p class="text-2xl font-bold text-indigo-600"><?= number_format($total, 2, ',', ' ') ?> €</p>
                                <p class="text-sm text-gray-500"><?= count($order['games']) ?> article<?= count($order['games']) > 1 ? 's' : '' ?></p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php foreach ($order['games'] as $game): ?>
                                <div class="bg-gray-50 rounded-lg p-4 flex items-start space-x-3">
                                    <?php if (!empty($game->media)): ?>
                                        <img src="<?= htmlspecialchars($game->media[0]->path) ?>" 
                                             alt="<?= htmlspecialchars($game->title) ?>" 
                                             class="w-20 h-20 object-cover rounded">
                                    <?php else: ?>
                                        <div class="w-20 h-20 bg-gradient-to-br from-gray-200 to-gray-300 rounded flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 text-sm mb-1">
                                            <?= htmlspecialchars($game->title) ?>
                                        </h4>
                                        <p class="text-indigo-600 font-bold"><?= number_format($game->price, 2, ',', ' ') ?> €</p>
                                        
                                        <?php if (!empty($game->genres)): ?>
                                            <div class="flex flex-wrap gap-1 mt-2">
                                                <?php foreach (array_slice($game->genres, 0, 2) as $genre): ?>
                                                    <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs rounded-full">
                                                        <?= htmlspecialchars($genre->name) ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gérer le changement de statut avec AJAX
        document.querySelectorAll('.status-select').forEach(function(select) {
            select.addEventListener('change', function() {
                const form = this.closest('.status-form');
                const orderId = form.dataset.orderId;
                const newStatus = encodeURIComponent(select.value);
                
                // Désactiver le select pendant la requête
                select.disabled = true;
                
                fetch('/admin/orders/' + orderId + '/status/' + newStatus, {
                    method: 'GET'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mettre à jour les couleurs du select
                        select.className = select.className.replace(/bg-\w+-\d+ text-\w+-\d+ border-\w+-\d+/g, '');
                        
                        const statusColors = {
                            'En cours de préparation': 'bg-yellow-50 text-yellow-700 border-yellow-300',
                            'Expédiée': 'bg-blue-50 text-blue-700 border-blue-300',
                            'Livrée': 'bg-green-50 text-green-700 border-green-300'
                        };
                        
                        select.classList.add(...statusColors[select.value].split(' '));
                        
                        // Afficher un message de succès temporaire
                        const successMsg = document.createElement('div');
                        successMsg.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50';
                        successMsg.textContent = 'Statut mis à jour avec succès';
                        document.body.appendChild(successMsg);
                        
                        setTimeout(() => successMsg.remove(), 3000);
                    } else {
                        alert('Erreur lors de la mise à jour: ' + (data.message || 'Erreur inconnue'));
                        // Recharger la page en cas d'erreur
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue lors de la mise à jour du statut.');
                    // Recharger la page en cas d'erreur
                    location.reload();
                })
                .finally(() => {
                    select.disabled = false;
                });
            });
        });
    });
    </script>
</body>
</html>
