<?php

namespace controllers;

class EnvReader
{
    /**
     *
     * @param string $name O nome da variável de ambiente
     * @return string|null O valor da variável de ambiente ou null se não encontrada
     */
    function getEnvVariable($name, $example)
    {
        $envFilePath = __DIR__ . '/../.env'; // Caminho para o arquivo .env
        if ($example) {
            $envFilePath .= '.example';
        }

        if (!file_exists($envFilePath)) {
            return null;
        }

        $envFileContent = file($envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($envFileContent as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($key, $value) = explode('=', $line, 2);

            if (trim($key) === $name) {
                return trim($value);
            }
        }
        return null;
    }
}

?>