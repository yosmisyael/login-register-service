<?php 

namespace Yosev\Login\Management\Repository;

use PHPUnit\Framework\TestCase;
use Yosev\Login\Management\Repository\SessionRepository;
use Yosev\Login\Management\Config\Database;
use Yosev\Login\Management\Domain\Session;
use Yosev\Login\Management\Domain\User;

class SessionRepositoryTest extends TestCase
{
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());

        $user = new User();
        $user->id = 'test';
        $user->password = password_hash('test', PASSWORD_BCRYPT);
        $user->name = 'test';

        $this->userRepository->store($user); 
    }

    protected function tearDown(): void
    {
        $this->sessionRepository->destroyAll();
        $this->userRepository->destroyAll();
    }

    public function testStoreSuccess()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "test";

        $this->sessionRepository->store($session);

        $result = $this->sessionRepository->findById($session->id);

        self::assertEquals($session->id, $result->id);
        self::assertEquals($session->userId, $result->userId);
    }

    public function testDeleteSuccess()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "test";
        
        $result = $this->sessionRepository->store($session);
        self::assertEquals($session->id, $result->id);
        self::assertEquals($session->userId, $result->userId);

        $this->sessionRepository->delete($session->id);
        $result = $this->sessionRepository->findById($session->id);
        self::assertNull($result);
    }

    public function testFindByIdNotFound()
    {
        $result = $this->sessionRepository->findById("notfound");
        self::assertNull($result);
    }
}
