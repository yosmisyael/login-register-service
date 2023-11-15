<?php 

namespace Yosev\Login\Management\Controller;

use Yosev\Login\Management\App\View;
use Yosev\Login\Management\Config\Database;
use Yosev\Login\Management\Repository\SessionRepository;
use Yosev\Login\Management\Repository\UserRepository;
use Yosev\Login\Management\Service\SessionService;

class HomeController
{

    private SessionService $sessionService;

    public function __construct()
    {
        $db = Database::getConnection();
        $sessionRepository = new SessionRepository($db);
        $userRepository = new UserRepository($db);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function index()
    {
        $user = $this->sessionService->current();

        if (!$user) {
            View::render('Home/index', [
                'title' => 'PHP MVC Login Management'
            ]);
        } else {
            View::render('Home/dashboard', [
                'title' => 'Dashboard',
                'user' => [
                    'name' => $user->name
                ]
            ]);

        }
    }

}
