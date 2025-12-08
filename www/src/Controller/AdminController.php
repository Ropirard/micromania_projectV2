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
}
