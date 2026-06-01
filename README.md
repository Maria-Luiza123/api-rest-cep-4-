# 📦 API REST — Consulta de CEPs

API REST desenvolvida em **PHP puro** com banco de dados **MySQL**, implementando os métodos **GET**, **POST** e **DELETE** conforme solicitado na atividade de sala de aula.

---

## 🗂 Estrutura do Projeto

```
api-rest-cep/
├── config/
│   └── Conexao.php      # Classe singleton de conexão PDO com MySQL
├── index.php            # Roteador principal da API (GET, POST, DELETE)
├── database.sql         # Script de criação do banco e dados de exemplo
├── .htaccess            # Reescrita de URL para o Apache
└── README.md            # Documentação
```

---

## ⚙️ Requisitos

- PHP >= 7.4
- MySQL >= 5.7 (ou MariaDB >= 10.3)
- Servidor Apache com `mod_rewrite` habilitado (ex: XAMPP, Laragon, WAMP)
- Extensão PDO e PDO_MySQL habilitadas no PHP

---

## 🚀 Instalação e Configuração

### 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/api-rest-cep.git
cd api-rest-cep
```

### 2. Crie o banco de dados

Abra o **phpMyAdmin** ou execute no terminal MySQL:

```bash
mysql -u root -p < database.sql
```

Isso irá:
- Criar o banco de dados `api_cep`
- Criar a tabela `cep`
- Inserir 5 registros de exemplo para testes

### 3. Configure a conexão

Edite o arquivo `config/Conexao.php` com suas credenciais:

```php
private string $host     = 'localhost';
private string $dbname   = 'api_cep';
private string $user     = 'root';      // seu usuário MySQL
private string $password = '';           // sua senha MySQL
```

### 4. Coloque o projeto no servidor

Copie a pasta para o diretório do seu servidor:
- **XAMPP:** `C:\xampp\htdocs\api-rest-cep\`
- **Laragon:** `C:\laragon\www\api-rest-cep\`

Acesse: `http://localhost/api-rest-cep/`

---

## 📡 Endpoints da API

Base URL: `http://localhost/api-rest-cep/`

---

### 🟢 GET — Buscar todos os CEPs

**Request:**
```
GET /
```

**Response (200):**
```json
[
  {
    "id": "1",
    "cep": "13175443",
    "logradouro": "Rua das Flores",
    "bairro": "Centro",
    "cidade": "Sumaré",
    "estado": "SP",
    "criado_em": "2026-06-01 10:00:00"
  }
]
```

---

### 🟢 GET — Buscar CEP específico

**Request:**
```
GET /?cep=01001000
```
ou
```
GET /?cep=01001-000
```

**Response (200):**
```json
{
  "id": "2",
  "cep": "01001000",
  "logradouro": "Praça da Sé",
  "bairro": "Sé",
  "cidade": "São Paulo",
  "estado": "SP",
  "criado_em": "2026-06-01 10:00:00"
}
```

**Response (404) — CEP não encontrado:**
```json
{ "erro": "CEP não encontrado" }
```

---

### 🔵 POST — Cadastrar novo CEP

**Request:**
```
POST /
Content-Type: application/json
```

**Body:**
```json
{
  "cep": "12345-678",
  "logradouro": "Rua Exemplo",
  "bairro": "Bairro Teste",
  "cidade": "Campinas",
  "estado": "SP"
}
```

**Response (201):**
```json
{
  "mensagem": "CEP cadastrado com sucesso",
  "id": "6"
}
```

**Response (400) — Campos ausentes:**
```json
{ "erro": "Campos obrigatórios ausentes: bairro, estado" }
```

**Response (409) — CEP duplicado:**
```json
{ "erro": "CEP já cadastrado no sistema" }
```

---

### 🔴 DELETE — Deletar CEP

**Request:**
```
DELETE /?cep=12345678
```

**Response (200):**
```json
{ "mensagem": "CEP deletado com sucesso" }
```

**Response (404) — CEP não encontrado:**
```json
{ "erro": "CEP não encontrado" }
```

---

## 🧪 Testando com o Thunder Client / Postman / Insomnia

| Método | URL | Body |
|--------|-----|------|
| GET | `http://localhost/api-rest-cep/` | — |
| GET | `http://localhost/api-rest-cep/?cep=01001000` | — |
| POST | `http://localhost/api-rest-cep/` | JSON com os campos |
| DELETE | `http://localhost/api-rest-cep/?cep=12345678` | — |

---

## 🗄 Estrutura da Tabela

```sql
CREATE TABLE cep (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    cep         VARCHAR(9)   NOT NULL UNIQUE,
    logradouro  VARCHAR(150) NOT NULL,
    bairro      VARCHAR(100) NOT NULL,
    cidade      VARCHAR(100) NOT NULL,
    estado      CHAR(2)      NOT NULL,
    criado_em   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 👤 Autor

Desenvolvido como atividade prática de **API REST** — Curso de Desenvolvimento Web.
