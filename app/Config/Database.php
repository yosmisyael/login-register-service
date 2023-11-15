<?php 

namespace Yosev\Login\Management\Config;

use PDO;

class Database
{
    
    private static ?PDO $pdo = null;

    public static function getConnection(string $env = "test"): PDO
    {
        
        if (self::$pdo == null) {
            require_once __DIR__ . '/DatabaseConfig.php';
            $config = getDatabaseConfig();
            self::$pdo = new PDO(
                $config['database'][$env]['url'],
                $config['database'][$env]['username'],
                $config['database'][$env]['password'],
            );
        }

        return self::$pdo;

    }

    public static function beginTransaction(): void
    {
        self::$pdo->beginTransaction();
    }

    public static function commitTransaction(): void
    {
        self::$pdo->commit();
    }

    public static function rollbackTransaction(): void
    {
        self::$pdo->rollBack();
    }

} 
