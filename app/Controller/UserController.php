<?php 

namespace Yosev\Login\Management\Controller;

use Yosev\Login\Management\App\View;
use Yosev\Login\Management\Config\Database;
use Yosev\Login\Management\Exception\ValidationException;
use Yosev\Login\Management\Model\UserLoginRequest;
use Yosev\Login\Management\Model\UserPasswordUpdateRequest;
use Yosev\Login\Management\Model\UserProfileUpdateRequest;
use Yosev\Login\Management\Model\UserRegisterRequest;
use Yosev\Login\Management\Repository\SessionRepository;
use Yosev\Login\Management\Repository\UserRepository;
use Yosev\Login\Management\Service\SessionService;
use Yosev\Login\Management\Service\UserService;

class UserController
{
    private UserService $userService;
    private SessionService $sessionService;
    
    public function __construct()
    {
        $db = Database::getConnection();
        $userRepository = new UserRepository($db);
        $this->userService = new UserService($userRepository);

        $sessionRepository = new SessionRepository($db);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function register()
    {
        View::render("User/register", [
            "title" => "Register New User"
        ]);
    }

    public function postRegister()
    {
        $request = new UserRegisterRequest();
        $request->id = $_POST["id"];
        $request->name = $_POST["name"];
        $request->password = $_POST["password"];

        try {
            $this->userService->register($request);
            View::redirect("/users/login");
        } catch (ValidationException $exception) {
            View::render("User/register", [
                "title" => "Register New User",
                "error" => $exception->getMessage()
            ]);
        }
    }

    public function login()
    {
        View::render("User/login", [
            "title" => "Login User"
        ]);
    }

    public function postLogin()
    {
        $request = new UserLoginRequest();
        $request->id = $_POST["id"];
        $request->password = $_POST["password"];

        try {
            $response = $this->userService->login($request);
            $this->sessionService->create($response->user->id);
            View::redirect("/");
        } catch (ValidationException $exception) {
            View::render("/User/login", [
                "title" => "Login User",
                "error" => $exception->getMessage()
            ]);
        }
    }

    public function updateProfile()
    {
        $user = $this->sessionService->current();

        View::render("User/profile", [
            "title" => "Update User Profile",
            "user" => [
                "id" => $user->id,
                "name" =>$user->name
            ] 
        ]);
    }

    public function postUpdateProfile()
    {
        $user = $this->sessionService->current();

        $request = new UserProfileUpdateRequest();
        $request->id = $user->id;
        $request->name = $_POST["name"];

        try {
            $this->userService->updateProfile($request);
            View::redirect("/");
        } catch (ValidationException $exception) {
            View::render("User/profile", [
                "title" => "Update User Profile",
                "user" => [
                    "id" => $user->id,
                    "name" =>$user->name
                ], 
                "error" => $exception->getMessage()
            ]);
        }
    }

    public function updatePassword()
    {
        $user = $this->sessionService->current();

        View::render("User/password", [
            "title" => "Update User Password",
            "user" => [
                "id" => $user->id
            ]
        ]);
    }

    public function postUpdatePassword()
    {
        $user = $this->sessionService->current();
        
        $request = new UserPasswordUpdateRequest();
        $request->id = $user->id;
        $request->oldPassword = $_POST['oldPassword'];
        $request->newPassword = $_POST['newPassword'];

        try {
            $this->userService->updatePassword($request);
            View::redirect('/');
        } catch (ValidationException $exception) {
            View::render('User/password', [
                "title" => "Update User Password",
                "user" => [
                    "id" => $user->id
                ],
                "error" => $exception->getMessage()
            ]);
        }
    }

    public function logout()
    {
        $this->sessionService->destroy();
        View::redirect("/");
    }
}
