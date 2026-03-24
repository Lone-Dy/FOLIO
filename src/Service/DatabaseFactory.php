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

    // Initialise la connexion PDO avec les options de sécurité
    public static function create(array $config): PDO 
    {

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

            error_log("Erreur de connexion PDO: " . $e->getMessage());

            throw new Exception("Erreur de connexion à la base de données. ". $e->getCode());
        }
    }
}
?>
