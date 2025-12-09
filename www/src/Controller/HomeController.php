<?php

/**
 * ============================================
 * HOME CONTROLLER
 * ============================================
 * 
 * CONCEPT PÉDAGOGIQUE : Controller simple
 * 
 * Ce contrôleur gère la route racine "/" et affiche la page d'accueil.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Chart;
use App\Entity\Game;
use App\Entity\Genre;
use App\Entity\Media;
use App\Entity\Plateform;
use App\Entity\User;
use JulienLinard\Auth\AuthManager;
use JulienLinard\Core\Controller\Controller;
use JulienLinard\Doctrine\EntityManager;
use JulienLinard\Router\Attributes\Route;
use JulienLinard\Router\Response;

class HomeController extends Controller
{
    public function __construct(
        private AuthManager $auth,
        private EntityManager $em
    ) {}
    /**
     * Route racine : affiche la page d'accueil
     * 
     * CONCEPT : Route simple sans middleware
     */
    #[Route(path: '/', methods: ['GET'], name: 'home')]
    public function index(): Response
    {
        // Récupérer tous les jeux disponibles
        $gameRepository = $this->em->getRepository(Game::class);
        $games = $gameRepository->findAll();
        
        // Charger les relations pour chaque jeu
        $pdo = $this->em->getConnection()->getPdo();
        $genreRepository = $this->em->getRepository(Genre::class);
        $plateformRepository = $this->em->getRepository(Plateform::class);
        $mediaRepository = $this->em->getRepository(Media::class);
        
        foreach ($games as $game) {
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
            $stmtMedia = $pdo->prepare("SELECT * FROM media WHERE game_id = ?");
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
        
        return $this->view('home/index', [
            'title' => 'WorstWicrowania',
            'message' => 'Hello World!',
            'csrf_token' => $_SESSION['_csrf_token'] ?? '',
            'user' => $this->auth->user(),
            'games' => $games
        ]);
    }

    #[Route(path: '/historique', methods: ['GET'], name: 'historique')]
    public function historique(): Response
    {
        $user = $this->auth->user();
        $pdo = $this->em->getConnection()->getPdo();
        $orders = [];
        
        if ($user) {
            // Récupérer tous les paniers validés de l'utilisateur
            $stmtCharts = $pdo->prepare("SELECT * FROM charts WHERE user_id = ? AND status = 'validated' ORDER BY validated_at DESC");
            $stmtCharts->execute([$user->id]);
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
        }
        
        return $this->view('user/history', [
            'title' => 'Mon Historique',
            'csrf_token' => $_SESSION['_csrf_token'] ?? '',
            'user' => $user,
            'orders' => $orders
        ]);
    }

    #[Route(path: '/wishlist', methods: ['GET'], name: 'wishlist')]
    public function wishlist(): Response
    {
        return $this->view('user/wishlist', [
            'title' => 'Ma Wishlist',
            'csrf_token' => $_SESSION['_csrf_token'] ?? '',
            'user' => $this->auth->user()
        ]);
    }

    #[Route(path: '/panier', methods: ['GET'], name: 'panier')]
    public function panier(): Response
    {
        $user = $this->auth->user();
        $pdo = $this->em->getConnection()->getPdo();
        $games = [];
        
        if ($user) {
            // Récupérer le panier actif de l'utilisateur
            $stmtChart = $pdo->prepare("SELECT * FROM charts WHERE user_id = ? AND status = 'active' LIMIT 1");
            $stmtChart->execute([$user->id]);
            $chartData = $stmtChart->fetch(\PDO::FETCH_ASSOC);
            
            if ($chartData) {
                // Récupérer les jeux du panier
                $stmtGames = $pdo->prepare("
                    SELECT g.* FROM games g
                    INNER JOIN charts_games cg ON g.id = cg.game_id
                    WHERE cg.chart_id = ?
                ");
                $stmtGames->execute([$chartData['id']]);
                $gamesData = $stmtGames->fetchAll(\PDO::FETCH_ASSOC);
                
                $gameRepository = $this->em->getRepository(Game::class);
                $genreRepository = $this->em->getRepository(Genre::class);
                $plateformRepository = $this->em->getRepository(Plateform::class);
                $mediaRepository = $this->em->getRepository(Media::class);
                
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
                        
                        $games[] = $game;
                    }
                }
            }
        }
        
        return $this->view('user/chart', [
            'title' => 'Mon Panier',
            'csrf_token' => $_SESSION['_csrf_token'] ?? '',
            'user' => $user,
            'games' => $games
        ]);
    }

    /**
     * Ajouter un jeu au panier
     */
    #[Route(path: '/panier/add/{id}', methods: ['GET'], name: 'add_to_cart')]
    public function addToCart(int $id): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->auth->check()) {
            return $this->redirect('/login');
        }

        $user = $this->auth->user();
        $pdo = $this->em->getConnection()->getPdo();
        
        // Vérifier si le jeu existe
        $gameRepository = $this->em->getRepository(Game::class);
        $game = $gameRepository->find($id);
        
        if (!$game) {
            $_SESSION['error'] = "Le jeu n'existe pas.";
            return $this->redirect('/');
        }
        
        // Vérifier si l'utilisateur a déjà un panier actif
        $stmtChart = $pdo->prepare("SELECT * FROM charts WHERE user_id = ? AND status = 'active' LIMIT 1");
        $stmtChart->execute([$user->id]);
        $chartData = $stmtChart->fetch(\PDO::FETCH_ASSOC);
        
        if (!$chartData) {
            // Créer un nouveau panier
            $stmtInsert = $pdo->prepare("INSERT INTO charts (user_id, status) VALUES (?, 'active')");
            $stmtInsert->execute([$user->id]);
            $chartId = (int)$pdo->lastInsertId();
        } else {
            $chartId = $chartData['id'];
        }
        
        // Ajouter le jeu au panier (permet les doublons)
        $stmtAdd = $pdo->prepare("INSERT INTO charts_games (chart_id, game_id) VALUES (?, ?)");
        $stmtAdd->execute([$chartId, $id]);
        
        // Si c'est une requête AJAX, retourner du JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'game' => $game->title]);
            exit;
        }
        
        return $this->redirect('/?message=added&game=' . urlencode($game->title));
    }

    /**
     * Retirer un jeu du panier
     */
    #[Route(path: '/panier/remove/{id}', methods: ['GET'], name: 'remove_from_cart')]
    public function removeFromCart(int $id): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->auth->check()) {
            return $this->redirect('/login');
        }

        $user = $this->auth->user();
        $pdo = $this->em->getConnection()->getPdo();
        
        // Récupérer le panier actif
        $stmtChart = $pdo->prepare("SELECT * FROM charts WHERE user_id = ? AND status = 'active' LIMIT 1");
        $stmtChart->execute([$user->id]);
        $chartData = $stmtChart->fetch(\PDO::FETCH_ASSOC);
        
        if ($chartData) {
            // Retirer une seule instance du jeu du panier
            $stmtRemove = $pdo->prepare("DELETE FROM charts_games WHERE chart_id = ? AND game_id = ? LIMIT 1");
            $stmtRemove->execute([$chartData['id'], $id]);
            $_SESSION['success'] = "Le jeu a été retiré de votre panier.";
        }
        
        return $this->redirect('/panier');
    }

    /**
     * Valider le panier
     */
    #[Route(path: '/panier/validate', methods: ['GET'], name: 'validate_cart')]
    public function validateCart(): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->auth->check()) {
            return $this->redirect('/login');
        }

        $user = $this->auth->user();
        $pdo = $this->em->getConnection()->getPdo();
        
        // Récupérer le panier actif
        $stmtChart = $pdo->prepare("SELECT * FROM charts WHERE user_id = ? AND status = 'active' LIMIT 1");
        $stmtChart->execute([$user->id]);
        $chartData = $stmtChart->fetch(\PDO::FETCH_ASSOC);
        
        if ($chartData) {
            // Valider le panier (changer le status et ajouter la date de validation)
            $stmtValidate = $pdo->prepare("UPDATE charts SET status = 'validated', validated_at = NOW() WHERE id = ?");
            $stmtValidate->execute([$chartData['id']]);
            $_SESSION['success'] = "Votre commande a été validée avec succès !";
            
            return $this->redirect('/historique');
        } else {
            $_SESSION['error'] = "Votre panier est vide.";
            return $this->redirect('/panier');
        }
    }
}