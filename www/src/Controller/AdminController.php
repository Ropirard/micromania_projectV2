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
            'title' => 'Ajouter un jeu',
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
            'title' => 'Administration',
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
        }
        
        return $this->view('admin/catalogue', [
            'title' => 'Catalogue',
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
        }
        
        // Récupérer tous les genres et plateformes pour les filtres
        $genres = $genreRepository->findAll();
        $plateforms = $plateformRepository->findAll();
        
        return $this->view('admin/catalogue_edit', [
            'title' => 'Édition du catalogue',
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
        
        // Récupérer tous les genres et plateformes
        $genres = $genreRepository->findAll();
        $plateforms = $plateformRepository->findAll();
        
        // S'assurer qu'un token CSRF existe
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        
        $response = $this->view('admin/edit', [
            'title' => 'Modifier un jeu',
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
        
        // Debug: Logger les données reçues
        error_log("=== MISE À JOUR JEU ID: $id ===");
        error_log("Titre reçu: " . ($title ?? 'NULL'));
        error_log("Description: " . ($description ?? 'NULL'));
        error_log("Prix: " . ($price ?? 'NULL'));
        error_log("Stock: " . ($stock ?? 'NULL'));
        error_log("Genres: " . json_encode($genreIds));
        error_log("Plateformes: " . json_encode($plateformIds));
        
        // Validation basique
        if (empty($title) || empty($description) || empty($price) || empty($stock)) {
            error_log("ERREUR: Champs manquants");
            return $this->redirect('/admin/game/edit/' . $id . '?error=Tous les champs sont requis');
        }
        
        // Obtenir la connexion PDO pour la mise à jour directe
        $pdo = $this->em->getConnection()->getPdo();
        
        // Mettre à jour le jeu directement avec SQL (Doctrine ne détecte pas les changements)
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
        
        error_log("Jeu mis à jour avec SQL: $title");
        
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
        
        // Rediriger vers la liste avec un message de succès
        return $this->redirect('/admin/edit/list?success=Jeu mis à jour avec succès');
    }
}
