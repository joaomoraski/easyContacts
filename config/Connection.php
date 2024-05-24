<?php

namespace config;

use controllers\EnvReader;
use PDO;
use PDOException;

class Connection
{

    function pdo_connect_mysql()
    {
        $envReader = new EnvReader();
        $DATABASE_HOST = $envReader->getEnvVariable("DATABASE_HOST", false);
        $DATABASE_USER = $envReader->getEnvVariable("DATABASE_USER", false);
        $DATABASE_PASS = $envReader->getEnvVariable("DATABASE_PASSWORD", false);
        $DATABASE_NAME = $envReader->getEnvVariable("DATABASE_NAME", false);
        try {
            return new PDO('mysql:host=' . $DATABASE_HOST . ';dbname=' . $DATABASE_NAME . ';charset=utf8', $DATABASE_USER, $DATABASE_PASS);
        } catch (PDOException $exception) {
            // If there is an error with the connection, stop the script and display the error.
            exit('Failed to connect to database! ' . $exception->getMessage());
        }
    }
}

