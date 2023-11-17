<?php 

namespace Yosev\Login\Management\Service;

use Exception;
use Yosev\Login\Management\Config\Database;
use Yosev\Login\Management\Domain\User;
use Yosev\Login\Management\Exception\ValidationException;
use Yosev\Login\Management\Helper\Logger;
use Yosev\Login\Management\Model\UserLoginRequest;
use Yosev\Login\Management\Model\UserLoginResponse;
use Yosev\Login\Management\Model\UserPasswordUpdateRequest;
use Yosev\Login\Management\Model\UserPasswordUpdateResponse;
use Yosev\Login\Management\Model\UserProfileUpdateRequest;
use Yosev\Login\Management\Model\UserProfileUpdateResponse;
use Yosev\Login\Management\Model\UserRegisterRequest;
use Yosev\Login\Management\Model\UserRegisterResponse;
use Yosev\Login\Management\Repository\UserRepository;

class UserService
{

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(UserRegisterRequest $request): UserRegisterResponse
    {
        $this->validateUserRegistrationRequest($request);

        try {
            Database::beginTransaction();

            $user = $this->userRepository->findById($request->id);
            if ($user) {
                throw new ValidationException("User Id already exist!");
            }

            $user = new User();
            $user->id = $request->id;
            $user->name = $request->name;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);
    
            $this->userRepository->store($user);
    
            $response = new UserRegisterResponse();
            $response->user = $user;
    
            Database::commitTransaction();
            
            Logger::getLogger(__CLASS__)->info("new user has been created", ["userId" => $user->id]);
            
            return $response;

        } catch (Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserRegistrationRequest(UserRegisterRequest $request)
    {
        if ($request->id == null || $request->name == null || $request->password == null || 
            trim($request->id) == '' || trim($request->name) == '' || trim($request->password) == '') {
            throw new ValidationException("Id, Name, and Password should not be empty!");
        }
    }

    public function login(UserLoginRequest $request): UserLoginResponse
    {
        $this->validateUserLoginRequest($request);
        $user = $this->userRepository->findById($request->id);

        if (!$user) {
            throw new ValidationException("User not found");
        } 
        
        if (password_verify($request->password, $user->password)) {
            $response = new UserLoginResponse();
            $response->user = $user;

            Logger::getLogger(__CLASS__)->info("new user login activity", ["userId" => $user->id]);

            return $response;
        } else {
            throw new ValidationException("Id or password is wrong"); 
        }
    }

    public function validateUserLoginRequest(UserLoginRequest $request)
    {
        if ($request->id == null || $request->password == null || 
            trim($request->id) == '' || trim($request->password) == '') {
            throw new ValidationException("Id and Password should not be empty!");
        }
    }

    public function updateProfile(UserProfileUpdateRequest $request): UserProfileUpdateResponse
    {
        $this->validateUserProfileUpdateRequest($request);

        try {
            Database::beginTransaction();
            
            $user = $this->userRepository->findById($request->id);

            if (!$user) {
                throw new ValidationException("User is not found");
            }

            $user->name = $request->name;
            $this->userRepository->update($user);

            Database::commitTransaction();

            $response = new UserProfileUpdateResponse();
            $response->user = $user;
            
            Logger::getLogger(__CLASS__)->info("new user update activity", ["userId" => $user->id]);

            return $response;
        } catch (Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }

    }

    private function validateUserProfileUpdateRequest(UserProfileUpdateRequest $request)
    {
        if ($request->id == null || $request->name == null || 
            trim($request->id) == '' || trim($request->name) == '') {
            throw new ValidationException("Id and Name should not be empty!");
        }
    }

    public function updatePassword(UserPasswordUpdateRequest $request): UserPasswordUpdateResponse
    {
        $this->validateUserPasswordUpdateRequest($request);

        try {
            Database::beginTransaction();

            $user = $this->userRepository->findById($request->id);
            
            if (!$user) {
                throw new ValidationException("User is not found");
            }

            if (!password_verify($request->oldPassword, $user->password)) {
                throw new ValidationException("Old password is wrong");
            }

            $user->password = password_hash($request->newPassword, PASSWORD_BCRYPT);
            $this->userRepository->update($user);

            Database::commitTransaction();

            $response = new UserPasswordUpdateResponse();
            $response->user = $user;
            return $response;
        } catch (Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserPasswordUpdateRequest(UserPasswordUpdateRequest $request)
    {
        if ($request->id == null || $request->newPassword == null || $request->oldPassword == null || 
            trim($request->id) == '' || trim($request->newPassword) == '' || trim($request->oldPassword) == '') {
            throw new ValidationException("Id. old password and new password should not be empty!");
        }
    }
}