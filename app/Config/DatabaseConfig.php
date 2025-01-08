<?php

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/../../");
$dotenv->load();

function getDatabaseConfig(): array {
    return [
        "database" => [
            "test" => [
                "url" => $_ENV['DB_CONNECTION'] . ":host=" . $_ENV['DB_HOST'] . ":" . $_ENV['DB_PORT'] . ";dbname=" . $_ENV['DB_NAME_TEST'],
                "username" => $_ENV['DB_USER'],
                "password" => $_ENV['DB_PASSWORD']
            ],
            "prod" => [
                "url" => $_ENV['DB_CONNECTION'] . ":host=" . $_ENV['DB_HOST'] . ":" . $_ENV['DB_PORT'] . ";dbname=" . $_ENV['DB_NAME_TEST'],
                "username" => $_ENV['DB_USER'],
                "password" => $_ENV['DB_PASSWORD']
            ]
        ]
    ];
}
