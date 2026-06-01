-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS api_cep CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE api_cep;

-- Criação da tabela de CEPs
CREATE TABLE IF NOT EXISTS cep (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cep VARCHAR(9) NOT NULL UNIQUE,
    logradouro VARCHAR(150) NOT NULL,
    bairro VARCHAR(100) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    estado CHAR(2) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Dados de exemplo para testes
INSERT INTO cep (cep, logradouro, bairro, cidade, estado) VALUES
('13175-443', 'Rua das Flores', 'Centro', 'Sumaré', 'SP'),
('01001-000', 'Praça da Sé', 'Sé', 'São Paulo', 'SP'),
('20040-020', 'Rua da Assembleia', 'Centro', 'Rio de Janeiro', 'RJ'),
('30130-110', 'Avenida Afonso Pena', 'Centro', 'Belo Horizonte', 'MG'),
('80010-010', 'Rua XV de Novembro', 'Centro', 'Curitiba', 'PR');
