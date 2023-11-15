<?php 

namespace Yosev\Login\Management\Service;

use PHPUnit\Framework\TestCase;
use Yosev\Login\Management\Config\Database;
use Yosev\Login\Management\Domain\User;
use Yosev\Login\Management\Exception\ValidationException;
use Yosev\Login\Management\Model\UserLoginRequest;
use Yosev\Login\Management\Model\UserPasswordUpdateRequest;
use Yosev\Login\Management\Model\UserProfileUpdateRequest;
use Yosev\Login\Management\Model\UserRegisterRequest;
use Yosev\Login\Management\Repository\SessionRepository;
use Yosev\Login\Management\Repository\UserRepository;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    public function setUp(): void
    {
        $db = Database::getConnection();
        $this->userRepository = new UserRepository($db);
        $this->userService = new UserService($this->userRepository);
        $this->sessionRepository = new SessionRepository((Database::getConnection()));
    }
    
    public function tearDown(): void
    {
        $this->sessionRepository->destroyAll();
        $this->userRepository->destroyAll();
    }

    public function testRegistraterSuccess()
    {
        $request = new UserRegisterRequest();
        $request->id = 'test';
        $request->name = 'test';
        $request->password = 'test';

        $response = $this->userService->register($request);

        self::assertEquals($request->id, $response->user->id);
        self::assertEquals($request->name, $response->user->name);
        self::assertNotEquals($request->password, $response->user->password);
        self::assertTrue(password_verify($request->password, $response->user->password));
    }

    public function testRegistraterFailed()
    {
        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = '';
        $request->name = '  ';
        $request->password = '  ';

        $this->userService->register($request);
    }

    public function testRegisterDuplicate()
    {
        $user = new User();
        $user->id = 'test';
        $user->name = 'test';
        $user->password = 'test';

        $this->userRepository->store($user);

        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = 'test';
        $request->name = 'test';
        $request->password = 'test';

        $this->userService->register($request);
    }

    public function testLoginNotFound()
    {
        $this->expectException(ValidationException::class);
        
        $request = new UserLoginRequest();
        $request->id = "test";
        $request->password = "test";

        $this->userService->login($request);
    }
    
    public function testLoginFailed()
    {
        $this->expectException(ValidationException::class);
        
        $user = new User();
        $user->id = 'test';
        $user->name = 'test';
        $user->password = password_hash('test', PASSWORD_BCRYPT);

        $this->userRepository->store($user);

        $request = new UserLoginRequest();
        $request->id = "test";
        $request->password = "wrong";
    
        $this->userService->login($request);
    }
    
    public function testLoginSuccess()
    {
        $user = new User();
        $user->id = 'test';
        $user->name = 'test';
        $user->password = password_hash('test', PASSWORD_BCRYPT);
    
        $this->userRepository->store($user);
    
        $request = new UserLoginRequest();
        $request->id = "test";
        $request->password = "test";
    
        $response = $this->userService->login($request);

        self::assertEquals($user->id, $response->user->id);
        self::assertEquals($user->password, $response->user->password);
    }

    public function testUpdateProfileSuccess()
    {
        $user = new User();
        $user->id = "test";
        $user->name = "test";
        $user->password = "test";
        $this->userRepository->store($user);

        $request = new UserProfileUpdateRequest();
        $request->id = "test";
        $request->name = "newman";
        $this->userService->updateProfile($request);

        $result = $this->userRepository->findById($user->id);
        self::assertEquals($result->name, $request->name);
    }

    public function testUpdateProfileFailed()
    {
        $this->expectException(ValidationException::class);
        $user = new User();
        $user->id = "test";
        $user->name = "test";
        $user->password = password_hash("test", PASSWORD_BCRYPT);
        $this->userRepository->store($user);

        $request = new UserProfileUpdateRequest();
        $request->id = "test";
        $request->name = "";
        $this->userService->updateProfile($request);
    }

    public function testUpdateProfileNotFound()
    {
        $this->expectException(ValidationException::class);

        $request = new UserProfileUpdateRequest();
        $request->id = "test";
        $request->name = "";
        $this->userService->updateProfile($request); 
    }

    public function testUpdatePasswordSuccess()
    {
        $user = new User();
        $user->id = "test";
        $user->name = "test";
        $user->password = password_hash("test", PASSWORD_BCRYPT);
        $this->userRepository->store($user);

        $request = new UserPasswordUpdateRequest();
        $request->id = "test";
        $request->oldPassword = "test";
        $request->newPassword = "newpassword";
        $this->userService->updatePassword($request);

        $result = $this->userRepository->findById($user->id);
        self::assertTrue(password_verify($request->newPassword, $result->password));
    }

    public function testUpdatePasswordFailed()
    {
        $this->expectException(ValidationException::class);

        $user = new User();
        $user->id = "test";
        $user->name = "test";
        $user->password = password_hash("test", PASSWORD_BCRYPT);
        $this->userRepository->store($user);
    
        $request = new UserPasswordUpdateRequest();
        $request->id = "test";
        $request->oldPassword = "";
        $request->newPassword = "";
        $this->userService->updatePassword($request);
        
    }

    public function testUpdatePasswordWrongOldPassword()
    {
        $this->expectException(ValidationException::class);
        
        $user = new User();
        $user->id = "test";
        $user->name = "test";
        $user->password = password_hash("test", PASSWORD_BCRYPT);
        $this->userRepository->store($user);
    
        $request = new UserPasswordUpdateRequest();
        $request->id = "test";
        $request->oldPassword = "forgot";
        $request->newPassword = "newpassword";
        $this->userService->updatePassword($request);
    }
    public function testUpdatePasswordNotFound()
    {
        $this->expectException(ValidationException::class);
        
        $request = new UserPasswordUpdateRequest();
        $request->id = "test";
        $request->oldPassword = "test";
        $request->newPassword = "newpassword";
        $this->userService->updatePassword($request);
    }
}
