<?php

/**
 * ============================================
 * ADMIN CONTROLLER
 * ============================================
 * 
 * Contrôleur pour l'administration
 * Accessible uniquement aux utilisateurs avec le rôle admin
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Genre;
use App\Entity\Plateform;
use App\Entity\Media;
use JulienLinard\Auth\AuthManager;
use JulienLinard\Router\Request;
use JulienLinard\Router\Response;
use JulienLinard\Router\Attributes\Route;
use JulienLinard\Core\Controller\Controller;
use JulienLinard\Doctrine\EntityManager;
use JulienLinard\Auth\Middleware\AuthMiddleware;
use JulienLinard\Core\Middleware\CsrfMiddleware;

class AdminController extends Controller
{
    public function __construct(
        private AuthManager $auth,
        private EntityManager $em
    ) {}
    
    /**
     * Page de création d'un jeu
     * 
     * CONCEPT : Route protégée par AuthMiddleware (utilisateurs connectés uniquement)
     */
    #[Route(path: '/admin/create', methods: ['GET'], name: 'admin.game.create', middleware: [new AuthMiddleware()])]
    public function createGameForm(): Response
    {
        // Vérifier que l'utilisateur est admin
        $user = $this->auth->user();
        if (!$user || $user->role !== 'admin') {
            return $this->redirect('/');
        }
        
        // S'assurer qu'un token CSRF existe
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        
        // Récupérer tous les genres depuis la base de données
        $genreRepository = $this->em->getRepository(Genre::class);
        $genres = $genreRepository->findAll();
        
        // Récupérer toutes les plateformes depuis la base de données
        $plateformRepository = $this->em->getRepository(Plateform::class);
        $plateforms = $plateformRepository->findAll();
        
        $response = $this->view('admin/create', [
            'title' => 'WorstMicromania',
            'csrf_token' => $_SESSION['_csrf_token'] ?? '',
            'user' => $user,
            'genres' => $genres,
            'plateforms' => $plateforms

        ]);
        
        // Ajouter des headers pour empêcher le cache
        $response->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->setHeader('Pragma', 'no-cache');
        $response->setHeader('Expires', '0');
        
        return $response;
    }
    
    /**
     * Traitement de la création d'un jeu (POST)
     */
    #[Route(path: '/admin/create', methods: ['POST'], name: 'admin.game.store', middleware: [new AuthMiddleware()])]
    public function createGame(Request $request): Response
    {
        // Vérifier que l'utilisateur est admin
        $user = $this->auth->user();
        if (!$user || $user->role !== 'admin') {
            return $this->redirect('/');
        }
        
        // Récupérer les données du formulaire
        $title = $request->getPost('title');
        $description = $request->getPost('description');
        $price = $request->getPost('price');
        $stock = $request->getPost('stock');
        $genreIds = $request->getPost('genres', []);
        $plateformIds = $request->getPost('plateforms', []);
        
        // Validation basique
        if (empty($title) || empty($description) || empty($price) || empty($stock)) {
            return $this->redirect('/admin/create?error=Tous les champs sont requis');
        }
        
        // Créer le jeu
        $game = new Game();
        $game->title = $title;
        $game->description = $description;
        $game->price = (float) $price;
        $game->stock = (int) $stock;
        
        // Persister le jeu d'abord pour obtenir l'ID
        $this->em->persist($game);
        $this->em->flush();
        
        // Gérer l'upload de l'image
        if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['media'];
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/avif', 'image/webp'];
            
            if (in_array($file['type'], $allowedTypes)) {
                // Créer le dossier uploads s'il n'existe pas
                $uploadDir = __DIR__ . '/../../public/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Générer un nom de fichier unique
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = uniqid('game_', true) . '.' . $extension;
                $filepath = $uploadDir . $filename;
                
                // Déplacer le fichier uploadé
                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    // Créer l'entité Media
                    $media = new Media();
                    $media->filename = $filename;
                    $media->original_filename = $file['name'];
                    $media->mime_type = $file['type'];
                    $media->size = $file['size'];
                    $media->path = '/uploads/' . $filename;
                    $media->type = 'image';
                    $media->created_at = new \DateTime();
                    $media->game = $game;
                    
                    $this->em->persist($media);
                    $this->em->flush();
                    
                    error_log("Image uploadée : " . $media->path);
                }
            }
        }
        
        // Obtenir la connexion PDO pour les requêtes manuelles
        $pdo = $this->em->getConnection()->getPdo();
        
        // Associer les genres via la table de jointure
        if (!empty($genreIds)) {
            $stmtGenre = $pdo->prepare("INSERT INTO games_genres (game_id, genre_id) VALUES (?, ?)");
            foreach ($genreIds as $genreId) {
                $stmtGenre->execute([$game->id, (int) $genreId]);
            }
        }
        
        // Associer les plateformes via la table de jointure
        if (!empty($plateformIds)) {
            $stmtPlateform = $pdo->prepare("INSERT INTO games_plateforms (game_id, plateform_id) VALUES (?, ?)");
            foreach ($plateformIds as $plateformId) {
                $stmtPlateform->execute([$game->id, (int) $plateformId]);
            }
        }
        
        // Gérer l'upload d'image
        if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/';
            
            // Créer le dossier s'il n'existe pas
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $file = $_FILES['media'];
            $filename = uniqid('game_') . '_' . basename($file['name']);
            $filepath = $uploadDir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                // Créer l'entité Media
                $media = new Media();
                $media->filename = $filename;
                $media->original_filename = $file['name'];
                $media->mime_type = $file['type'];
                $media->size = $file['size'];
                $media->path = '/uploads/' . $filename;
                $media->type = 'image';
                $media->created_at = new \DateTime();
                $media->game = $game;
                
                $this->em->persist($media);
                $this->em->flush();
            }
        }
        
        // Rediriger vers le dashboard avec un message de succès
        return $this->redirect('/admin?success=Jeu créé avec succès');
    }
    
    /**
     * Dashboard admin
     */
    #[Route(path: '/admin', methods: ['GET'], name: 'admin.dashboard', middleware: [new AuthMiddleware()])]
    public function dashboard(): Response
    {
        // Vérifier que l'utilisateur est admin
        $user = $this->auth->user();
        if (!$user || $user->role !== 'admin') {
            return $this->redirect('/');
        }
        
        return $this->view('admin/dashboard', [
            'title' => 'WorstMicromania',
            'csrf_token' => $_SESSION['_csrf_token'] ?? '',
            'user' => $user
        ]);
    }
    
    /**
     * Page catalogue
     */
    #[Route(path: '/catalogue', methods: ['GET'], name: 'admin.catalogue', middleware: [new AuthMiddleware()])]
    public function catalogue(): Response
    {
        // Vérifier que l'utilisateur est admin
        $user = $this->auth->user();
        if (!$user || $user->role !== 'admin') {
            return $this->redirect('/');
        }
        
        // Récupérer tous les jeux
        $gameRepository = $this->em->getRepository(Game::class);
        $games = $gameRepository->findAll();
        
        // Charger manuellement les genres et plateformes pour chaque jeu
        $pdo = $this->em->getConnection()->getPdo();
        $genreRepository = $this->em->getRepository(Genre::class);
        $plateformRepository = $this->em->getRepository(Plateform::class);
        $mediaRepository = $this->em->getRepository(Media::class);
        
        foreach ($games as $game) {
            // Récupérer les genres du jeu
            $stmtGenres = $pdo->prepare("
                SELECT g.* FROM genres g
                INNER JOIN games_genres gg ON g.id = gg.genre_id
                WHERE gg.game_id = ?
            ");
            $stmtGenres->execute([$game->id]);
            $gameGenresData = $stmtGenres->fetchAll(\PDO::FETCH_ASSOC);
            
            $game->genres = [];
            foreach ($gameGenresData as $genreData) {
                $genre = $genreRepository->find($genreData['id']);
                if ($genre) {
                    $game->genres[] = $genre;
                }
            }
            
            // Récupérer les plateformes du jeu
            $stmtPlateforms = $pdo->prepare("
                SELECT p.* FROM plateforms p
                INNER JOIN games_plateforms gp ON p.id = gp.plateform_id
                WHERE gp.game_id = ?
            ");
            $stmtPlateforms->execute([$game->id]);
            $gamePlateformsData = $stmtPlateforms->fetchAll(\PDO::FETCH_ASSOC);
            
            $game->plateforms = [];
            foreach ($gamePlateformsData as $plateformData) {
                $plateform = $plateformRepository->find($plateformData['id']);
                if ($plateform) {
                    $game->plateforms[] = $plateform;
                }
            }
            
            // Récupérer les médias du jeu
            $stmtMedia = $pdo->prepare("
                SELECT * FROM media WHERE game_id = ?
            ");
            $stmtMedia->execute([$game->id]);
            $mediaData = $stmtMedia->fetchAll(\PDO::FETCH_ASSOC);
            
            $game->media = [];
            foreach ($mediaData as $mediaItem) {
                $media = $mediaRepository->find($mediaItem['id']);
                if ($media) {
                    $game->media[] = $media;
                }
            }
        }
        
        return $this->view('admin/catalogue', [
            'title' => 'WorstMicromania',
            'csrf_token' => $_SESSION['_csrf_token'] ?? '',
            'user' => $user,
            'games' => $games
        ]);
    }
    
    /**
     * Page de listing des jeux à éditer (catalogue_edit)
     */
    #[Route(path: '/admin/edit/list', methods: ['GET'], name: 'admin.games.list', middleware: [new AuthMiddleware()])]
    public function listGames(): Response
    {
        // Vérifier que l'utilisateur est admin
        $user = $this->auth->user();
        if (!$user || $user->role !== 'admin') {
            return $this->redirect('/');
        }
        
        // Récupérer tous les jeux avec leurs relations
        $gameRepository = $this->em->getRepository(Game::class);
        $games = $gameRepository->findAll();
        
        // Charger manuellement les genres et plateformes pour chaque jeu
        $pdo = $this->em->getConnection()->getPdo();
        $genreRepository = $this->em->getRepository(Genre::class);
        $plateformRepository = $this->em->getRepository(Plateform::class);
        $mediaRepository = $this->em->getRepository(Media::class);
        
        foreach ($games as $game) {
            // Récupérer les genres du jeu
            $stmtGenres = $pdo->prepare("
                SELECT g.* FROM genres g
                INNER JOIN games_genres gg ON g.id = gg.genre_id
                WHERE gg.game_id = ?
            ");
            $stmtGenres->execute([$game->id]);
            $gameGenresData = $stmtGenres->fetchAll(\PDO::FETCH_ASSOC);
            
            $game->genres = [];
            foreach ($gameGenresData as $genreData) {
                $genre = $genreRepository->find($genreData['id']);
                if ($genre) {
                    $game->genres[] = $genre;
                }
            }
            
            // Récupérer les plateformes du jeu
            $stmtPlateforms = $pdo->prepare("
                SELECT p.* FROM plateforms p
                INNER JOIN games_plateforms gp ON p.id = gp.plateform_id
                WHERE gp.game_id = ?
            ");
            $stmtPlateforms->execute([$game->id]);
            $gamePlateformsData = $stmtPlateforms->fetchAll(\PDO::FETCH_ASSOC);
            
            $game->plateforms = [];
            foreach ($gamePlateformsData as $plateformData) {
                $plateform = $plateformRepository->find($plateformData['id']);
                if ($plateform) {
                    $game->plateforms[] = $plateform;
                }
            }
            
            // Récupérer les médias du jeu
            $stmtMedia = $pdo->prepare("
                SELECT * FROM media WHERE game_id = ?
            ");
            $stmtMedia->execute([$game->id]);
            $mediaData = $stmtMedia->fetchAll(\PDO::FETCH_ASSOC);
            
            $game->media = [];
            foreach ($mediaData as $mediaItem) {
                $media = $mediaRepository->find($mediaItem['id']);
                if ($media) {
                    $game->media[] = $media;
                }
            }
        }
        
        // Récupérer tous les genres et plateformes pour les filtres
        $genres = $genreRepository->findAll();
        $plateforms = $plateformRepository->findAll();
        
        return $this->view('admin/catalogue_edit', [
            'title' => 'WorstMicromania',
            'csrf_token' => $_SESSION['_csrf_token'] ?? '',
            'user' => $user,
            'games' => $games,
            'genres' => $genres,
            'plateforms' => $plateforms
        ]);
    }
    
    /**
     * Page d'édition d'un jeu spécifique
     */
    #[Route(path: '/admin/game/edit/{id}', methods: ['GET'], name: 'admin.game.edit', middleware: [new AuthMiddleware()])]
    public function editGameForm(int $id): Response
    {
        // Vérifier que l'utilisateur est admin
        $user = $this->auth->user();
        if (!$user || $user->role !== 'admin') {
            return $this->redirect('/');
        }
        
        // Récupérer le jeu à éditer
        $gameRepository = $this->em->getRepository(Game::class);
        $game = $gameRepository->find($id);
        
        if (!$game) {
            return $this->redirect('/admin/edit/list?error=Jeu non trouvé');
        }
        
        // Charger manuellement les genres et plateformes du jeu depuis la base
        $pdo = $this->em->getConnection()->getPdo();
        
        // Récupérer les genres du jeu
        $stmtGenres = $pdo->prepare("
            SELECT g.* FROM genres g
            INNER JOIN games_genres gg ON g.id = gg.genre_id
            WHERE gg.game_id = ?
        ");
        $stmtGenres->execute([$id]);
        $gameGenresData = $stmtGenres->fetchAll(\PDO::FETCH_ASSOC);
        
        // Convertir en objets Genre
        $genreRepository = $this->em->getRepository(Genre::class);
        $game->genres = [];
        foreach ($gameGenresData as $genreData) {
            $genre = $genreRepository->find($genreData['id']);
            if ($genre) {
                $game->genres[] = $genre;
            }
        }
        
        // Récupérer les plateformes du jeu
        $stmtPlateforms = $pdo->prepare("
            SELECT p.* FROM plateforms p
            INNER JOIN games_plateforms gp ON p.id = gp.plateform_id
            WHERE gp.game_id = ?
        ");
        $stmtPlateforms->execute([$id]);
        $gamePlateformsData = $stmtPlateforms->fetchAll(\PDO::FETCH_ASSOC);
        
        // Convertir en objets Plateform
        $plateformRepository = $this->em->getRepository(Plateform::class);
        $game->plateforms = [];
        foreach ($gamePlateformsData as $plateformData) {
            $plateform = $plateformRepository->find($plateformData['id']);
            if ($plateform) {
                $game->plateforms[] = $plateform;
            }
        }
        
        // Récupérer les médias du jeu
        $stmtMedia = $pdo->prepare("
            SELECT * FROM media WHERE game_id = ?
        ");
        $stmtMedia->execute([$id]);
        $mediaData = $stmtMedia->fetchAll(\PDO::FETCH_ASSOC);
        
        $mediaRepository = $this->em->getRepository(Media::class);
        $game->media = [];
        foreach ($mediaData as $mediaItem) {
            $media = $mediaRepository->find($mediaItem['id']);
            if ($media) {
                $game->media[] = $media;
            }
        }
        
        // Récupérer tous les genres et plateformes
        $genres = $genreRepository->findAll();
        $plateforms = $plateformRepository->findAll();
        
        // S'assurer qu'un token CSRF existe
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        
        $response = $this->view('admin/edit', [
            'title' => 'WorstMicromania',
            'csrf_token' => $_SESSION['_csrf_token'] ?? '',
            'user' => $user,
            'game' => $game,
            'genres' => $genres,
            'plateforms' => $plateforms
        ]);
        
        // Ajouter des headers pour empêcher le cache
        $response->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->setHeader('Pragma', 'no-cache');
        $response->setHeader('Expires', '0');
        
        return $response;
    }
    
    /**
     * Mise à jour d'un jeu (POST)
     */
    #[Route(path: '/admin/game/update/{id}', methods: ['POST'], name: 'admin.game.update', middleware: [new AuthMiddleware()])]
    public function updateGame(int $id, Request $request): Response
    {
        // Vérifier que l'utilisateur est admin
        $user = $this->auth->user();
        if (!$user || $user->role !== 'admin') {
            return $this->redirect('/');
        }
        
        // Récupérer le jeu à modifier
        $gameRepository = $this->em->getRepository(Game::class);
        $game = $gameRepository->find($id);
        
        if (!$game) {
            return $this->redirect('/admin/edit/list?error=Jeu non trouvé');
        }
        
        // Récupérer les données du formulaire
        $title = $request->getPost('title');
        $description = $request->getPost('description');
        $price = $request->getPost('price');
        $stock = $request->getPost('stock');
        $genreIds = $request->getPost('genres', []);
        $plateformIds = $request->getPost('plateforms', []);
        
        // Validation basique
        if (empty($title) || empty($description) || empty($price) || empty($stock)) {
            error_log("ERREUR: Champs manquants");
            return $this->redirect('/admin/game/edit/' . $id . '?error=Tous les champs sont requis');
        }
        
        // Obtenir la connexion PDO pour la mise à jour directe
        $pdo = $this->em->getConnection()->getPdo();
        
        // Mettre à jour le jeu directement avec SQL (Doctrine ne détecte étonnamment pas les changements)
        $stmtUpdateGame = $pdo->prepare("
            UPDATE games 
            SET title = ?, description = ?, price = ?, stock = ? 
            WHERE id = ?
        ");
        $stmtUpdateGame->execute([
            $title,
            $description,
            (float) $price,
            (int) $stock,
            $game->id
        ]);
        
        // Supprimer les anciennes relations de genres
        $stmtDeleteGenres = $pdo->prepare("DELETE FROM games_genres WHERE game_id = ?");
        $stmtDeleteGenres->execute([$game->id]);
        
        // Ajouter les nouveaux genres
        if (!empty($genreIds)) {
            $stmtGenre = $pdo->prepare("INSERT INTO games_genres (game_id, genre_id) VALUES (?, ?)");
            foreach ($genreIds as $genreId) {
                $stmtGenre->execute([$game->id, (int) $genreId]);
            }
        }
        
        // Supprimer les anciennes relations de plateformes
        $stmtDeletePlateforms = $pdo->prepare("DELETE FROM games_plateforms WHERE game_id = ?");
        $stmtDeletePlateforms->execute([$game->id]);
        
        // Ajouter les nouvelles plateformes
        if (!empty($plateformIds)) {
            $stmtPlateform = $pdo->prepare("INSERT INTO games_plateforms (game_id, plateform_id) VALUES (?, ?)");
            foreach ($plateformIds as $plateformId) {
                $stmtPlateform->execute([$game->id, (int) $plateformId]);
            }
        }
        
        // Gérer l'upload d'image
        if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
            // Supprimer l'ancienne image si elle existe
            $stmtGetOldMedia = $pdo->prepare("SELECT * FROM media WHERE game_id = ?");
            $stmtGetOldMedia->execute([$game->id]);
            $oldMedia = $stmtGetOldMedia->fetch(\PDO::FETCH_ASSOC);
            
            if ($oldMedia) {
                // Supprimer le fichier physique
                $oldFilePath = __DIR__ . '/../../public' . $oldMedia['path'];
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
                // Supprimer l'entrée en base
                $stmtDeleteMedia = $pdo->prepare("DELETE FROM media WHERE id = ?");
                $stmtDeleteMedia->execute([$oldMedia['id']]);
            }
            
            // Uploader la nouvelle image
            $uploadDir = __DIR__ . '/../../public/uploads/';
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $file = $_FILES['media'];
            $filename = uniqid('game_') . '_' . basename($file['name']);
            $filepath = $uploadDir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                // Créer l'entité Media
                $media = new Media();
                $media->filename = $filename;
                $media->original_filename = $file['name'];
                $media->mime_type = $file['type'];
                $media->size = $file['size'];
                $media->path = '/uploads/' . $filename;
                $media->type = 'image';
                $media->created_at = new \DateTime();
                $media->game = $game;
                
                $this->em->persist($media);
                $this->em->flush();
            }
        }
        
        // Rediriger vers la liste avec un message de succès
        return $this->redirect('/admin/edit/list?success=Jeu mis à jour avec succès');
    }
    
    /**
     * Suppression d'un jeu (GET avec confirmation)
     */
    #[Route(path: '/admin/game/delete/{id}', methods: ['GET'], name: 'admin.game.delete', middleware: [new AuthMiddleware()])]
    public function deleteGame(int $id): Response
    {
        // Vérifier que l'utilisateur est admin
        $user = $this->auth->user();
        if (!$user || $user->role !== 'admin') {
            return $this->redirect('/');
        }
        
        // Récupérer le jeu à supprimer
        $gameRepository = $this->em->getRepository(Game::class);
        $game = $gameRepository->find($id);
        
        if (!$game) {
            return $this->redirect('/admin/edit/list?error=Jeu non trouvé');
        }
        
        $pdo = $this->em->getConnection()->getPdo();
        
        // Supprimer les images associées
        $stmtGetMedia = $pdo->prepare("SELECT * FROM media WHERE game_id = ?");
        $stmtGetMedia->execute([$id]);
        $mediaFiles = $stmtGetMedia->fetchAll(\PDO::FETCH_ASSOC);
        
        foreach ($mediaFiles as $mediaFile) {
            // Supprimer le fichier physique
            $filePath = __DIR__ . '/../../public' . $mediaFile['path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        
        // Supprimer les entrées media en base
        $stmtDeleteMedia = $pdo->prepare("DELETE FROM media WHERE game_id = ?");
        $stmtDeleteMedia->execute([$id]);
        
        // Supprimer les relations genres
        $stmtDeleteGenres = $pdo->prepare("DELETE FROM games_genres WHERE game_id = ?");
        $stmtDeleteGenres->execute([$id]);
        
        // Supprimer les relations plateformes
        $stmtDeletePlateforms = $pdo->prepare("DELETE FROM games_plateforms WHERE game_id = ?");
        $stmtDeletePlateforms->execute([$id]);
        
        // Supprimer les relations avec les charts (paniers)
        $stmtDeleteCharts = $pdo->prepare("DELETE FROM charts_games WHERE game_id = ?");
        $stmtDeleteCharts->execute([$id]);
        
        // Supprimer le jeu
        $stmtDeleteGame = $pdo->prepare("DELETE FROM games WHERE id = ?");
        $stmtDeleteGame->execute([$id]);
        
        // Rediriger avec un message de succès
        return $this->redirect('/admin/edit/list?success=Jeu supprimé avec succès');
    }
    
    /**
     * Page d'historique de toutes les commandes (Admin)
     */
    #[Route(path: '/admin/orders', methods: ['GET'], name: 'admin.orders', middleware: [new AuthMiddleware()])]
    public function allOrders(): Response
    {
        // Vérifier que l'utilisateur est admin
        $user = $this->auth->user();
        if (!$user || $user->role !== 'admin') {
            return $this->redirect('/');
        }
        
        $pdo = $this->em->getConnection()->getPdo();
        $orders = [];
        
        // Récupérer toutes les commandes validées de tous les utilisateurs
        $stmtCharts = $pdo->prepare("
            SELECT c.*, u.firstname, u.lastname, u.email 
            FROM charts c
            INNER JOIN users u ON c.user_id = u.id
            WHERE c.status = 'validated' 
            ORDER BY c.validated_at DESC
        ");
        $stmtCharts->execute();
        $chartsData = $stmtCharts->fetchAll(\PDO::FETCH_ASSOC);
        
        $gameRepository = $this->em->getRepository(Game::class);
        $genreRepository = $this->em->getRepository(Genre::class);
        $plateformRepository = $this->em->getRepository(Plateform::class);
        $mediaRepository = $this->em->getRepository(Media::class);
        
        foreach ($chartsData as $chartData) {
            $order = [
                'id' => $chartData['id'],
                'validated_at' => $chartData['validated_at'],
                'delivery_status' => $chartData['delivery_status'] ?? 'En cours de préparation',
                'user' => [
                    'firstname' => $chartData['firstname'],
                    'lastname' => $chartData['lastname'],
                    'email' => $chartData['email']
                ],
                'games' => []
            ];
            
            // Récupérer les jeux de cette commande
            $stmtGames = $pdo->prepare("
                SELECT g.* FROM games g
                INNER JOIN charts_games cg ON g.id = cg.game_id
                WHERE cg.chart_id = ?
            ");
            $stmtGames->execute([$chartData['id']]);
            $gamesData = $stmtGames->fetchAll(\PDO::FETCH_ASSOC);
            
            foreach ($gamesData as $gameData) {
                $game = $gameRepository->find($gameData['id']);
                
                if ($game) {
                    // Récupérer les genres
                    $stmtGenres = $pdo->prepare("
                        SELECT g.* FROM genres g
                        INNER JOIN games_genres gg ON g.id = gg.genre_id
                        WHERE gg.game_id = ?
                    ");
                    $stmtGenres->execute([$game->id]);
                    $gameGenresData = $stmtGenres->fetchAll(\PDO::FETCH_ASSOC);
                    
                    $game->genres = [];
                    foreach ($gameGenresData as $genreData) {
                        $genre = $genreRepository->find($genreData['id']);
                        if ($genre) {
                            $game->genres[] = $genre;
                        }
                    }
                    
                    // Récupérer les plateformes
                    $stmtPlateforms = $pdo->prepare("
                        SELECT p.* FROM plateforms p
                        INNER JOIN games_plateforms gp ON p.id = gp.plateform_id
                        WHERE gp.game_id = ?
                    ");
                    $stmtPlateforms->execute([$game->id]);
                    $gamePlateformsData = $stmtPlateforms->fetchAll(\PDO::FETCH_ASSOC);
                    
                    $game->plateforms = [];
                    foreach ($gamePlateformsData as $plateformData) {
                        $plateform = $plateformRepository->find($plateformData['id']);
                        if ($plateform) {
                            $game->plateforms[] = $plateform;
                        }
                    }
                    
                    // Récupérer les médias
                    $game->media = $mediaRepository->findBy(['game_id' => $game->id]);
                    
                    $order['games'][] = $game;
                }
            }
            
            $orders[] = $order;
        }
        
        return $this->view('admin/orders', [
            'title' => 'WorstMicromania',
            'csrf_token' => $_SESSION['_csrf_token'] ?? '',
            'user' => $user,
            'orders' => $orders
        ]);
    }
    
    /**
     * Mettre à jour le statut de livraison d'une commande
     */
    #[Route(path: '/admin/orders/{id}/status/{status}', methods: ['GET'], name: 'admin.orders.update_status', middleware: [new AuthMiddleware()])]
    public function updateOrderStatus(int $id, string $status): Response
    {
        // Vérifier que l'utilisateur est admin
        $user = $this->auth->user();
        if (!$user || $user->role !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Accès refusé']);
            exit;
        }
        
        // Décoder le statut (URL encode)
        $deliveryStatus = urldecode($status);
        
        // Valider le statut
        $validStatuses = ['En cours de préparation', 'Expédiée', 'Livrée'];
        if (!in_array($deliveryStatus, $validStatuses)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Statut invalide']);
            exit;
        }
        
        $pdo = $this->em->getConnection()->getPdo();
        
        // Mettre à jour le statut de livraison
        $stmt = $pdo->prepare("UPDATE charts SET delivery_status = ? WHERE id = ?");
        $stmt->execute([$deliveryStatus, $id]);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => 'Statut mis à jour avec succès'
        ]);
        exit;
    }
}
