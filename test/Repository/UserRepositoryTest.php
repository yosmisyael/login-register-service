<?php

namespace Yosev\Login\Management\Repository;

use PHPUnit\Framework\TestCase;
use Yosev\Login\Management\Config\Database;
use Yosev\Login\Management\Domain\User;

class UserRepositoryTest extends TestCase
{
    private UserRepository $userReposiroty;
    private SessionRepository $sessoinRepository;

    protected function setUp(): void
    {
        $this->userReposiroty = new UserRepository(Database::getConnection());
        $this->sessoinRepository = new SessionRepository(Database::getConnection());
    }

    public function tearDown(): void
    {
        $this->userReposiroty->destroyAll();
        $this->sessoinRepository->destroyAll();
    }

    public function testStoreSuccess()
    {
        $user = new User();
        $user->id = 'yosev';
        $user->name = 'yosev';
        $user->password = 'yosev';

        $this->userReposiroty->store($user);    

        $result = $this->userReposiroty->findById($user->id);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->password, $result->password);
    }

    public function testNotFound()
    {
        $user = $this->userReposiroty->findById('not know');
        self::assertNull($user);
    }

    public function testUpdate()
    {
        $user = new User();
        $user->id = "test";
        $user->name = "test";
        $user->password = "test";

        $this->userReposiroty->store($user);

        $user->name = "newman";
        $this->userReposiroty->update($user);
        
        $result = $this->userReposiroty->findById($user->id);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->password, $result->password);

    }
} 
