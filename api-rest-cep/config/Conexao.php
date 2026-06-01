<?php

class Conexao {
    private static $instance = null;

    private string $host     = 'localhost';
    private string $dbname   = 'api_cep';
    private string $user     = 'root';
    private string $password = '';
    private string $charset  = 'utf8mb4';

    private function __construct() {}

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                (new self())->host,
                (new self())->dbname,
                (new self())->charset
            );

            // Pegando valores via variáveis de ambiente (opcional para produção)
            $user     = $_ENV['DB_USER']     ?? (new self())->user;
            $password = $_ENV['DB_PASSWORD'] ?? (new self())->password;

            try {
                $pdo = new PDO($dsn, $user, $password, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
                self::$instance = $pdo;
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['erro' => 'Falha na conexão com o banco de dados: ' . $e->getMessage()]);
                exit;
            }
        }

        return self::$instance;
    }
}
