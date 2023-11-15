<?php

namespace Yosev\Login\Management\Middleware;

require_once __DIR__ . '/../Helper/helper.php';

use PHPUnit\Framework\TestCase;
use Yosev\Login\Management\Config\Database;
use Yosev\Login\Management\Domain\Session;
use Yosev\Login\Management\Domain\User;
use Yosev\Login\Management\Repository\SessionRepository;
use Yosev\Login\Management\Repository\UserRepository;
use Yosev\Login\Management\Service\SessionService;

class GuestMiddlewareTest extends TestCase
{
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;
    private GuestMiddleware $guestMiddleware;

    protected function setUp(): void
    {
        putenv("mode=test");
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->guestMiddleware = new GuestMiddleware();
    }
    
    protected function tearDown(): void
    {
        $this->sessionRepository->destroyAll();
        $this->userRepository->destroyAll();    
    }

    public function testBefore()
    {
        $this->guestMiddleware->before();
        $this->expectOutputRegex("[]");
    }

    public function testBeforeLoginUser()
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

        $this->guestMiddleware->before();
        $this->expectOutputRegex("[Location: /]");
    }
} 
