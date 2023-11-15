<?php 

namespace Yosev\Login\Management\Service;

require_once __DIR__ . '/../Helper/helper.php';

use PHPUnit\Framework\TestCase;
use Yosev\Login\Management\Config\Database;
use Yosev\Login\Management\Domain\Session;
use Yosev\Login\Management\Domain\User;
use Yosev\Login\Management\Repository\SessionRepository;
use Yosev\Login\Management\Repository\UserRepository;

class SessionServiceTest extends TestCase
{
    private SessionService $sessionService;
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($this->sessionRepository, $this->userRepository);
        
        $user = new User();
        $user->id = 'test';
        $user->name = 'test';
        $user->password = 'test';
    
        $this->userRepository->store($user);
    }

    protected function tearDown(): void
    {
        $this->sessionRepository->destroyAll();
        $this->userRepository->destroyAll();
    }

    public function testCreate()
    {
        $session = $this->sessionService->create('test');
        $result = $this->sessionRepository->findById($session->id);
        
        $this->expectOutputRegex("[X-YOSEV-SESSION: $session->id]");
        self::assertEquals($session->userId, $result->userId);
    }

    public function testDelete()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "test";
        $this->sessionRepository->store($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
        $this->sessionService->destroy();

        $this->expectOutputRegex("[X-YOSEV-SESSION: ]");

        $result = $this->sessionRepository->findById($session->id);

        self::assertNull($result);
    }
    
    public function testCurrent()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "test";
        $this->sessionRepository->store($session);

        $_COOKIE['X-YOSEV-SESSION'] = $session->id;

        $result = $this->sessionService->current();

        self::assertEquals($result->id, $session->userId);
    }

}
