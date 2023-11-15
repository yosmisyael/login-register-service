<?php 

namespace Yosev\Login\Management\Middleware;

use Yosev\Login\Management\App\View;
use Yosev\Login\Management\Config\Database;
use Yosev\Login\Management\Repository\SessionRepository;
use Yosev\Login\Management\Repository\UserRepository;
use Yosev\Login\Management\Service\SessionService;

class AuthMiddleware implements Middleware
{
    private SessionService $sessionService;

    public function __construct()
    {
        $userRepository = new UserRepository(Database::getConnection());
        $sessionRepository = new SessionRepository(Database::getConnection());
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function before(): void
    {
        $user = $this->sessionService->current();

        if (!$user) {
            View::redirect('/users/login');
        }

    }
}
