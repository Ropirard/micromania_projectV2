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

use JulienLinard\Auth\AuthManager;
use JulienLinard\Core\Controller\Controller;
use JulienLinard\Router\Attributes\Route;
use JulienLinard\Router\Response;

class HomeController extends Controller
{
    public function __construct(
        private AuthManager $auth
    ) {}
    /**
     * Route racine : affiche la page d'accueil
     * 
     * CONCEPT : Route simple sans middleware
     */
    #[Route(path: '/', methods: ['GET'], name: 'home')]
    public function index(): Response
    {
        return $this->view('home/index', [
            'title' => 'WorstWicrowania',
            'message' => 'Hello World!',
            'csrf_token' => $_SESSION['_csrf_token'] ?? '',
            'user' => $this->auth->user()
        ]);
    }

    #[Route(path: '/historique', methods: ['GET'], name: 'historique')]
    public function historique(): Response
    {
        return $this->view('user/history', [
            'title' => 'Mon Historique',
            'csrf_token' => $_SESSION['_csrf_token'] ?? '',
            'user' => $this->auth->user()
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
        return $this->view('user/chart', [
            'title' => 'Mon Panier',
            'csrf_token' => $_SESSION['_csrf_token'] ?? '',
            'user' => $this->auth->user()
        ]);
    }
}