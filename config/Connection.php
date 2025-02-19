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
        $DATABASE_HOST = getenv("DATABASE_HOST");
        $DATABASE_USER = getenv("DATABASE_USER");
        $DATABASE_PASS = getenv("DATABASE_PASSWORD");
        $DATABASE_NAME = getenv("DATABASE_NAME");
        try {
            return new PDO('mysql:host=' . $DATABASE_HOST . ';dbname=' . $DATABASE_NAME . ';charset=utf8', $DATABASE_USER, $DATABASE_PASS);
        } catch (PDOException $exception) {
            // If there is an error with the connection, stop the script and display the error.
//            exit('Failed to connect to database! ' . $exception);
            return null;
        }
    }
}

