<?php
namespace App\Service;

use \PDO;
use \PDOException;
use \Exception;


class DatabaseFactory 
{
    /**
     * Creates a new PDO connection based on provided configuration
     * * @param array $config The database configuration from .env
     * @return PDO
     * @throws Exception If a required configuration key is missing or connection fails
     */
    public static function create(array $config): PDO 
    {
        // Defensive check: Ensure all required keys exist
        $requiredKeys = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
        foreach ($requiredKeys as $key) {
            if (!isset($config[$key]) || empty($config[$key])) {
                throw new Exception("Database configuration error: Missing key '$key'.");
            }
        }

        $host=$config['DB_HOST'];
        $name=$config['DB_NAME'];
        $user=$config['DB_USER'];
        $pass=$config['DB_PASS'];

        $dsn = "mysql:host=$host;dbname=$name;charset=utf8mb4";

        try {
            return new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            // Log the real error for the developer
            error_log("PDO Connection Error: " . $e->getMessage());
            // Throw a clean message for the application
            throw new Exception("Unable to connect to the database. ".$e->getMessage(),$e->getCode());
        }
    }
}
?>
