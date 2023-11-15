<?php 

function getDatabaseConfig(): array {
    return [
        "database" => [
            "test" => [
                "url" => "mysql:host=127.0.0.1:3306;dbname=login_management_test",
                "username" => "user",
                "password" => "user"
            ],
            "prod" => [
                "url" => "mysql:host=127.0.0.1:3306;dbname=login_management",
                "username" => "user",
                "password" => "user"
            ]
        ]
    ];
}
