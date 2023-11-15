<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Yosev\Login\Management\App\Router;
use Yosev\Login\Management\Controller\HomeController;
use Yosev\Login\Management\Controller\UserController;
use Yosev\Login\Management\Middleware\AuthMiddleware;
use Yosev\Login\Management\Middleware\GuestMiddleware;

Router::add('/', 'GET', HomeController::class, 'index', []);
Router::add('/users/register', 'GET', UserController::class, 'register', [GuestMiddleware::class]);
Router::add('/users/register', 'POST', UserController::class, 'postRegister', [GuestMiddleware::class]);
Router::add('/users/login', 'GET', UserController::class, 'login', [GuestMiddleware::class]);
Router::add('/users/login', 'POST', UserController::class, 'postLogin', [GuestMiddleware::class]);
Router::add('/users/profile', 'GET', UserController::class, 'updateProfile', [AuthMiddleware::class]);
Router::add('/users/profile', 'POST', UserController::class, 'postUpdateProfile', [AuthMiddleware::class]);
Router::add('/users/password', 'GET', UserController::class, 'updatePassword', [AuthMiddleware::class]);
Router::add('/users/password', 'POST', UserController::class, 'postUpdatePassword', [AuthMiddleware::class]);
Router::add('/users/logout', 'GET', UserController::class, 'logout', [AuthMiddleware::class]);

Router::run();
