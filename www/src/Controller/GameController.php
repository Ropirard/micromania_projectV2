<?php 

namespace App\Controller;

use GameRepository;
use JulienLinard\Auth\AuthManager;
use JulienLinard\Validator\Validator;
use JulienLinard\Doctrine\EntityManager;
use JulienLinard\Core\Controller\Controller;

class GameController extends Controller
{
    private AuthManager $auth;
    private EntityManager $em;
    private GameRepository $gameRepository;
    private Validator $validator;
    private FileUploader $fileUploader;

    public function __construct()
    {
        $app = Application::getInstanceOrFail();
        $container = $app->getContainer();

        $this->auth = $container->make(AuthManager::class); 
        $this->em = $container->make(EntityManager::class);
        $this->gameRepository = $this->em->createRepository(GameRepository::class, Game::class);
        $this->validator = new Validator();
        $this->fileUploader = new FileUploader();
    }
        
    #[Route(path: "/admin/create", methods: ['GET'], name: "admin.game.create", middleware: [AuthMiddleware::class])]
    public function createGameForm(): Response
    {
        return $this->view('admin/create', [
            'title' => 'Ajouter un jeu au catalogue',
            'csrf_token' => $_SESSION['_csrf_token'] ?? '',
            'user' => $this->auth->user()
        ]);
    }
}