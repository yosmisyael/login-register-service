<?php 

namespace Yosev\Login\Management\Controller;

require_once __DIR__ . '/../Helper/helper.php';


use PHPUnit\Framework\TestCase;
use Yosev\Login\Management\Config\Database;
use Yosev\Login\Management\Domain\Session;
use Yosev\Login\Management\Domain\User;
use Yosev\Login\Management\Model\UserProfileUpdateRequest;
use Yosev\Login\Management\Repository\SessionRepository;
use Yosev\Login\Management\Repository\UserRepository;
use Yosev\Login\Management\Service\SessionService;

class UserControllerTest extends TestCase
{
    private UserController $userController;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $this->userController = new UserController();
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionRepository(Database::getConnection());

        putenv("mode=test");
    }

    protected function tearDown(): void
    {
        $this->sessionRepository->destroyAll();
        $this->userRepository->destroyAll();
    }

    public function testRegister()
    {
        $this->userController->register();

        $this->expectOutputRegex("[Register]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[Name]");
        $this->expectOutputRegex("[Password]");
        $this->expectOutputRegex("[Register New User]");
    }
    
    public function testRegisterSuccess()
    {
        $_POST['id'] = 'test';
        $_POST['name'] = 'test';
        $_POST['password'] = 'test';

        $this->userController->postRegister();

        $this->expectOutputRegex("[Location: /users/login]");
    }
    
    public function testRegisterFailed()
    {
        $_POST['id'] = '';
        $_POST['name'] = 'test';
        $_POST['password'] = 'test';
        
        $this->userController->postRegister();
        
        $this->expectOutputRegex("[Register]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[Name]");
        $this->expectOutputRegex("[Password]");
        $this->expectOutputRegex("[Id, Name, and Password should not be empty!]");
    }

    public function testRegisterDuplicate()
    {
        $user = new User();
        $user->id = 'test';
        $user->name = 'test';
        $user->password = 'test';

        $this->userRepository->store($user);

        $_POST['id'] = 'test';
        $_POST['name'] = 'test';
        $_POST['password'] = 'test';

        $this->userController->postRegister();

        $this->expectOutputRegex("[Register]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[Name]");
        $this->expectOutputRegex("[Password]");
        $this->expectOutputRegex("[User Id already exist!]");
    }

    public function testLogin()
    {
        $this->userController->login();

        $this->expectOutputRegex("[Login User]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[Password]");
    }

    public function testLoginSuccess()
    {
        $user = new User();
        $user->id = 'test';
        $user->name = 'test';
        $user->password = password_hash('test', PASSWORD_BCRYPT);

        $this->userRepository->store($user);

        $_POST['id'] = 'test';
        $_POST['password'] = 'test';

        $this->userController->postLogin();

        $this->expectOutputRegex("[Location: /]");
        $this->expectOutputRegex("[X-YOSEV-SESSION: ]");
    }

    public function testLoginValidationError()
    {
        $_POST['id'] = '';
        $_POST['password'] = '';

        $this->userController->postLogin();

        $this->expectOutputRegex("[Login User]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[Password]");
        $this->expectOutputRegex("[Id and Password should not be empty!]");
    }
    
    public function testUserNotFound()
    {
        $_POST['id'] = 'notfound';
        $_POST['password'] = 'notfound';

        $this->userController->postLogin();

        $this->expectOutputRegex("[Login User]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[Password]");
        $this->expectOutputRegex("[User not found]");
    }

    public function testLoginWrongPassword()
    {
        $user = new User();
        $user->id = 'test';
        $user->name = 'test';
        $user->password = password_hash('test', PASSWORD_BCRYPT);

        $this->userRepository->store($user);

        $_POST['id'] = 'test';
        $_POST['password'] = 'wrongpassword';

        $this->userController->postLogin();

        $this->expectOutputRegex("[Login User]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[Password]");
        $this->expectOutputRegex("[Id or password is wrong]");
    }

    public function testUpdateProfile()
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

        $this->userController->updateProfile();
        $this->expectOutputRegex("[Profile]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[Nsme]");
        $this->expectOutputRegex("[test]");
        $this->expectOutputRegex("[test]");
    }

    public function testUpdateProfileSuccess()
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

        $_POST['name'] = "newman";
        $this->userController->postUpdateProfile();

        $this->expectOutputRegex("[Location: /]");

        $result = $this->userRepository->findById("test");
        self::assertEquals("newman", $result->name);
    }

    public function testUpdateProfileFailed()
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

        $_POST['name'] = "";
        $this->userController->postUpdateProfile();

        $this->expectOutputRegex("[Profile]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[test]");
        $this->expectOutputRegex("[Nsme]");
        $this->expectOutputRegex("[Id and Name should not be empty!]");
    }

    public function testLogout()
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

        $this->userController->logout();

        $this->expectOutputRegex("[Location: /]");
        $this->expectOutputRegex("[X-YOSEV-SESSION: ]");
    }

    public function testUpdatePassword()
    {
        $user = new User();
        $user->id = "test";
        $user->name = "test";
        $user->password = password_hash("test", PASSWORD_BCRYPT);
        $this->userRepository->store($user);

        $session = new Session();
        $session->id = uniqid();
        $session->userId = $user->id;
        $this->sessionRepository->store($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
        
        $this->userController->updatePassword();

        $this->expectOutputRegex("[Update User Password]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[test]");
        $this->expectOutputRegex("[Old Password]");
        $this->expectOutputRegex("[New Password]");
    }
    
    public function testUpdatePasswordSuccess()
    {
        $user = new User();
        $user->id = "test";
        $user->name = "test";
        $user->password = password_hash("test", PASSWORD_BCRYPT);
        $this->userRepository->store($user);

        $session = new Session();
        $session->id = uniqid();
        $session->userId = $user->id;
        $this->sessionRepository->store($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $_POST['oldPassword'] = "test";
        $_POST['newPassword'] = "newpassword";
        $this->userController->postUpdatePassword();

        $this->expectOutputRegex("[Location: /]");

        $result = $this->userRepository->findById($user->id);
        self::assertTrue(password_verify('newpassword', $result->password));
    }
    
    public function testUpdatePasswordFailed()
    {
        $user = new User();
        $user->id = "test";
        $user->name = "test";
        $user->password = password_hash("test", PASSWORD_BCRYPT);
        $this->userRepository->store($user);

        $session = new Session();
        $session->id = uniqid();
        $session->userId = $user->id;
        $this->sessionRepository->store($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $_POST['oldPassword'] = "";
        $_POST['newPassword'] = "";
        $this->userController->postUpdatePassword();

        $this->expectOutputRegex("[Location: /users/password]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[test]");
        $this->expectOutputRegex("[Id. old password and new password should not be empty!]");
    }
    
    public function testUpdatePasswordOldPasswordWrong()
    {
        $user = new User();
        $user->id = "test";
        $user->name = "test";
        $user->password = password_hash("test", PASSWORD_BCRYPT);
        $this->userRepository->store($user);

        $session = new Session();
        $session->id = uniqid();
        $session->userId = $user->id;
        $this->sessionRepository->store($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $_POST['oldPassword'] = "forgot";
        $_POST['newPassword'] = "newpassword";
        $this->userController->postUpdatePassword();

        $this->expectOutputRegex("[Location: /users/password]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[test]");
        $this->expectOutputRegex("[Old password is wrong]");
    }
}

