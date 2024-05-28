<?php

namespace controllers;

class EnvReader
{
    /**
     * Função para ler uma variável de ambiente de um arquivo .env
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

        // Verificar se o arquivo .env existe
        if (!file_exists($envFilePath)) {
            return null;
        }

        // Ler o conteúdo do arquivo .env
        $envFileContent = file($envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Procurar a variável de ambiente pelo nome
        foreach ($envFileContent as $line) {
            // Ignorar linhas de comentários
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Dividir a linha em chave e valor
            list($key, $value) = explode('=', $line, 2);

            // Verificar se a chave corresponde ao nome solicitado
            if (trim($key) === $name) {
                return trim($value);
            }
        }

        // Retornar null se a variável de ambiente não for encontrada
        return null;
    }
}

?>