<div class="min-h-screen bg-gradient-to-br from-indigo-200 via-white to-purple-200">
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-10 ">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="/admin" class="text-indigo-600 hover:text-gray-900 transition-colors">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 12l8-8l8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5"/></svg>
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
                <h2 class="text-2xl font-bold text-white">Créer un nouveau jeu</h2>
                <p class="text-indigo-100 mt-2">Ajouter un jeu au catalogue</p>
            </div>
            <form action="/admin/create" method="post" enctype="multipart/form-data" class="p-6 space-y-6">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                <?php if(isset($error)): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg flex">
                        <p class="text-sm text-red-700"><?= $error ?></p>
                    </div>
                <?php endif ?>
                <!-- Input pour title -->
                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">Titre du jeu</label>
                    <input id="title" value="<?= htmlspecialchars($title_value ?? '') ?>" type="text" name="title" placeholder="Ex : The Legend of Zelda" required  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 
                    focus:ring-indigo-500 focus:border-transparent transition-all duration-200 outline-none
                    text-gray-900 placeholder-gray-400">
                </div>
                <!-- Input pour la descrption -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows=6 placeholder="Description du jeu..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 
                    focus:ring-indigo-500 focus:border-transparent transition-all duration-200 outline-none
                    text-gray-900 placeholder-gray-400 resize-none"><?= htmlspecialchars($descrption_value ?? '') ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Genre(s)
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <?php if(isset($genres) && !empty($genres)): ?>
                            <?php foreach($genres as $genre): ?>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        name="genres[]" 
                                        value="<?= htmlspecialchars($genre->id) ?>"
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
                            <?php foreach($plateforms as $plateform): ?>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        name="plateforms[]" 
                                        value="<?= htmlspecialchars($plateform->id) ?>"
                                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    <span class="text-sm text-gray-700"><?= htmlspecialchars($plateform->name) ?></span>
                                </label>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-sm text-gray-500 col-span-2">Aucune plateforme disponible</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">Prix (€)</label>
                        <input id="price" type="number" step="0.01" min="0" name="price" placeholder="59.99" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 
                        focus:ring-indigo-500 focus:border-transparent transition-all duration-200 outline-none
                        text-gray-900 placeholder-gray-400">
                    </div>
                    <div>
                        <label for="stock" class="block text-sm font-semibold text-gray-700 mb-2">Stock</label>
                        <input id="stock" type="number" min="0" name="stock" placeholder="50" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 
                        focus:ring-indigo-500 focus:border-transparent transition-all duration-200 outline-none
                        text-gray-900 placeholder-gray-400">
                    </div>
                </div>
          
                <div>
                    <label for="media" class="block text-sm font-semibold text-gray-700 mb-2">
                        Jaquette
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="media" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <span>Choisir une image</span>
                                    <input id="media" name="media" type="file" class="sr-only" accept="image/jpeg,image/jpg,image/png,image/avif,image/webp">
                                </label>
                                <p class="pl-1">ou glisser-déposer</p>
                            </div>
                            <p class="text-xs text-gray-500">JPEG, JPG, PNG, AVIF, WEBP jusqu'à 10MB</p>
                        </div>
                    </div>
                </div>

                <!-- Boutons de soumission ou annulation -->
                <div class="flex items-center justify-center space-x-4 pt-4 border-t border-gray-200">
                    <a href="/dashboard" class="px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors duration-200">Annuler</a>
                    <div>
                    <button type="submit" class="w-full px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold shadow-lg hower:shadow-xl duration-200 transition-all transform hower:-translate-y-0.5" >Enregistrer</button>
                    </div>
                </div>

            </form>
        </div>
    </main>
</div>

