<?php 
use JulienLinard\Core\View\ViewHelper;

// Générer les positions chaotiques stables en PHP
$today = date('Y-m-d');
$baseHash = crc32($today . 'register');

// Fonction pour calculer positions basées sur seed
function getPosition($baseHash, $seed) {
    $hashValue = ($baseHash * $seed + 12345) % 10000;
    $x = ($hashValue * 73 % 80) + 10;
    $y = ($hashValue * 89 % 80) + 5;
    return ['x' => $x, 'y' => $y];
}

// Pré-calculer toutes les positions
$positions = [
    0 => getPosition($baseHash, 0),
    1 => getPosition($baseHash, 1),
    2 => getPosition($baseHash, 2),
    3 => getPosition($baseHash, 3),
    4 => getPosition($baseHash, 4),
    5 => getPosition($baseHash, 5),
    6 => getPosition($baseHash, 6),
];
?>

<style>
    @media (max-width: 1024px) {
        .fixed {
            position: relative !important;
            top: auto !important;
            left: auto !important;
            transform: none !important;
        }
    }
</style>

<div class="min-h-screen py-12 px-4">
    <form action="/register" method="POST" class="relative" style="min-height: 100vh;">
        <?= ViewHelper::csrfField() ?>
        
        <!-- Titre -->
        <div class="fixed z-10 w-11/12 max-w-80 bg-gray-400 p-6 text-center text-white lg:w-auto" style="left: <?php echo $positions[0]['x']; ?>%; top: <?php echo $positions[0]['y']; ?>vh; transform: translate(-50%, -50%);">
            <h2 class="text-4xl font-bold">Inscription</h2>
            <p class="mt-2 text-gray-200">Création de votre compte</p>
        </div>
        
        <!-- Message d'erreur -->
        <?php if(isset($error)): ?>
        <div class="fixed z-10 w-11/12 max-w-80 bg-gray-400 p-6 shadow-xl lg:w-auto" style="left: <?php echo $positions[1]['x']; ?>%; top: <?php echo $positions[1]['y']; ?>vh; transform: translate(-50%, -50%);">
            <div class="border-l-4 border-red-700 p-2">
                <p class="text-sm text-red-900"><?= $error ?></p>
            </div>
        </div>
        <?php endif ?>
        
        <!-- Champ Mot de passe -->
        <div class="fixed z-10 w-11/12 max-w-80 bg-gray-400 p-6 shadow-xl lg:w-auto" style="left: <?php echo $positions[2]['x']; ?>%; top: <?php echo $positions[2]['y']; ?>vh; transform: translate(-50%, -50%);">
            <label class="mb-2 block text-sm font-semibold text-white" for="password">Mot de passe</label>
            <input class="w-full border border-gray-300 px-4 py-3 text-gray-900 outline-none placeholder-gray-400 transition-all duration-200 focus:border-transparent focus:ring-2 focus:ring-indigo-500" type="password" name="password" placeholder="********" required>
        </div>
        
        <!-- Champ Email -->
        <div class="fixed z-10 w-11/12 max-w-80 bg-gray-400 p-6 shadow-xl lg:w-auto" style="left: <?php echo $positions[3]['x']; ?>%; top: <?php echo $positions[3]['y']; ?>vh; transform: translate(-50%, -50%);">
            <label class="mb-2 block text-sm font-semibold text-white" for="email">Email</label>
            <input class="w-full border border-gray-300 px-4 py-3 text-gray-900 outline-none placeholder-gray-400 transition-all duration-200 focus:border-transparent focus:ring-2 focus:ring-indigo-500" type="email" name="email" placeholder="votre@email.com" required>
        </div>
        
        <!-- Champ Nom -->
        <div class="fixed z-10 w-11/12 max-w-80 bg-gray-400 p-6 shadow-xl lg:w-auto" style="left: <?php echo $positions[4]['x']; ?>%; top: <?php echo $positions[4]['y']; ?>vh; transform: translate(-50%, -50%);">
            <label class="mb-2 block text-sm font-semibold text-white" for="lastname">Nom</label>
            <input class="w-full border border-gray-300 px-4 py-3 text-gray-900 outline-none placeholder-gray-400 transition-all duration-200 focus:border-transparent focus:ring-2 focus:ring-indigo-500" type="text" name="lastname" placeholder="Smith" required>
        </div>
        
        <!-- Champ Prénom -->
        <div class="fixed z-10 w-11/12 max-w-80 bg-gray-400 p-6 shadow-xl lg:w-auto" style="left: <?php echo $positions[5]['x']; ?>%; top: <?php echo $positions[5]['y']; ?>vh; transform: translate(-50%, -50%);">
            <label class="mb-2 block text-sm font-semibold text-white" for="firstname">Prénom</label>
            <input class="w-full border border-gray-300 px-4 py-3 text-gray-900 outline-none placeholder-gray-400 transition-all duration-200 focus:border-transparent focus:ring-2 focus:ring-indigo-500" type="text" name="firstname" placeholder="John" required>
        </div>
        
        <!-- Bouton Submit -->
        <div class="fixed z-10 w-11/12 max-w-80 bg-gray-400 p-4 shadow-xl lg:w-auto" style="left: <?php echo $positions[6]['x']; ?>%; top: <?php echo $positions[6]['y']; ?>vh; transform: translate(-50%, -50%);">
            <button class="w-full transform bg-gray-500 px-6 py-3 font-semibold text-white shadow-lg transition-all duration-200 hover:bg-indigo-700 hover:shadow-xl hover:-translate-y-0.5" 
            type="submit">S'inscrire</button>
            
            <div class="mt-4 border-t border-gray-600 pt-4 text-center">
                <p class="text-sm text-white">Déjà un compte ? <a class="font-semibold text-indigo-200 transition-colors hover:text-indigo-100" href="/login">Se connecter</a></p>
            </div>
        </div>
    </form>
</div>