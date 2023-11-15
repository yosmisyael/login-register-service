<?php 

namespace Yosev\Login\Management\Controller;

use PHPUnit\Framework\TestCase;
use Yosev\Login\Management\Config\Database;
use Yosev\Login\Management\Domain\Session;
use Yosev\Login\Management\Domain\User;
use Yosev\Login\Management\Repository\SessionRepository;
use Yosev\Login\Management\Repository\UserRepository;
use Yosev\Login\Management\Service\SessionService;

class HomeControllerTest extends TestCase
{
    private HomeController $homeController;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $this->homeController = new HomeController();
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionRepository(Database::getConnection());
    }

    protected function tearDown(): void
    {
        $this->sessionRepository->destroyAll();
        $this->userRepository->destroyAll();
    }

    public function testGuest()
    {
        $this->homeController->index();

        $this->expectOutputRegex("[Login Management]");
    }

    public function testUserLogin()
    {
        $user = new User();
        $user->id = "test";
        $user->name = "test";
        $user->password = "test";
        $this->userRepository->store($user);
        
        $session = new Session();
        $session->id = uniqid();
        $session->userId = $user->id;
        $this->sessionRepository->store($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->homeController->index();
        $this->expectOutputRegex("[Hallo, test]");
    }
}
