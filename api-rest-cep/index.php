<?php

// ─── Roteamento ────────────────────────────────────────────────────────────
require_once __DIR__ . '/config/Conexao.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$pdo    = Conexao::getInstance();

$cep = isset($_GET['cep']) ? trim($_GET['cep']) : null;
if ($cep !== null) {
    $cep = preg_replace('/\D/', '', $cep);
}

// ─── Funções de resposta HTML ──────────────────────────────────────────────
function htmlLayout(string $titulo, string $conteudo, string $acoAtiva = 'get'): void {
    $badgeGet    = $acoAtiva === 'get'    ? 'nav-active' : '';
    $badgePost   = $acoAtiva === 'post'   ? 'nav-active' : '';
    $badgeDelete = $acoAtiva === 'delete' ? 'nav-active' : '';
    echo <<<HTML
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>API REST — CEP</title>
        <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Syne:wght@700;800&display=swap" rel="stylesheet">
        <style>
            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

            :root {
                --bg: #0f0f0f;
                --surface: #181818;
                --border: #2a2a2a;
                --accent: #c8f060;
                --text: #f0f0f0;
                --muted: #555;
                --green: #4ade80;
                --red: #f87171;
                --orange: #fb923c;
                --blue: #60a5fa;
                --purple: #c084fc;
            }

            body {
                background: var(--bg);
                color: var(--text);
                font-family: 'DM Mono', monospace;
                min-height: 100vh;
                padding: 2rem;
            }

            header {
                max-width: 860px;
                margin: 0 auto 2rem;
            }

            h1 {
                font-family: 'Syne', sans-serif;
                font-size: 2.2rem;
                font-weight: 800;
            }

            h1 span { color: var(--accent); }

            p.sub {
                color: var(--muted);
                font-size: .8rem;
                margin-top: .3rem;
            }

            /* Nav */
            .nav {
                max-width: 860px;
                margin: 0 auto 1.5rem;
                display: flex;
                gap: .7rem;
                flex-wrap: wrap;
            }

            .nav a {
                text-decoration: none;
                display: flex;
                align-items: center;
                gap: .5rem;
                padding: .45rem 1rem;
                border-radius: 6px;
                font-family: 'Syne', sans-serif;
                font-size: .78rem;
                font-weight: 700;
                letter-spacing: .04em;
                border: 1px solid var(--border);
                color: var(--muted);
                background: var(--surface);
                transition: border-color .2s, color .2s;
            }

            .nav a:hover { border-color: var(--accent); color: var(--text); }

            .nav a.nav-active.get-link    { border-color: var(--green);  color: var(--green); }
            .nav a.nav-active.post-link   { border-color: var(--blue);   color: var(--blue); }
            .nav a.nav-active.delete-link { border-color: var(--red);    color: var(--red); }

            .badge-method {
                font-family: 'Syne', sans-serif;
                font-size: .65rem;
                font-weight: 700;
                padding: .2rem .5rem;
                border-radius: 4px;
                letter-spacing: .05em;
            }

            .get-badge    { background: #14532d; color: var(--green); }
            .post-badge   { background: #1c3a5e; color: var(--blue); }
            .delete-badge { background: #450a0a; color: var(--red); }

            /* Card */
            .card {
                background: var(--surface);
                border: 1px solid var(--border);
                border-radius: 10px;
                padding: 1.6rem;
                max-width: 860px;
                margin: 0 auto;
            }

            .card-header {
                display: flex;
                align-items: center;
                gap: .7rem;
                margin-bottom: 1.2rem;
            }

            .card-title {
                font-family: 'Syne', sans-serif;
                font-size: 1rem;
                font-weight: 700;
            }

            /* Form */
            form {
                display: flex;
                flex-direction: column;
                gap: .9rem;
                max-width: 480px;
            }

            label {
                display: block;
                font-size: .65rem;
                color: var(--muted);
                text-transform: uppercase;
                letter-spacing: .08em;
                margin-bottom: .3rem;
            }

            input {
                width: 100%;
                background: var(--bg);
                border: 1px solid var(--border);
                color: var(--text);
                font-family: 'DM Mono', monospace;
                font-size: .85rem;
                padding: .6rem .8rem;
                border-radius: 6px;
                outline: none;
                transition: border-color .2s;
            }

            input:focus { border-color: var(--accent); }
            input::placeholder { color: var(--muted); }

            .field { display: flex; flex-direction: column; }

            button[type=submit] {
                align-self: flex-start;
                background: var(--accent);
                color: #0f0f0f;
                border: none;
                font-family: 'Syne', sans-serif;
                font-weight: 700;
                font-size: .82rem;
                padding: .65rem 1.4rem;
                border-radius: 6px;
                cursor: pointer;
                transition: opacity .2s;
                letter-spacing: .03em;
                margin-top: .3rem;
            }

            button[type=submit]:hover { opacity: .85; }

            button[type=submit].btn-delete {
                background: var(--red);
                color: #fff;
            }

            /* Table */
            table {
                width: 100%;
                border-collapse: collapse;
                font-size: .8rem;
                margin-top: .5rem;
            }

            thead th {
                background: #111;
                border: 1px solid var(--border);
                padding: .5rem .75rem;
                color: var(--accent);
                font-size: .65rem;
                letter-spacing: .1em;
                text-transform: uppercase;
                text-align: left;
            }

            tbody td {
                border: 1px solid var(--border);
                padding: .5rem .75rem;
                color: #ccc;
            }

            tbody tr:nth-child(even) td { background: #101010; }

            .badge-uf {
                display: inline-block;
                background: #1a2a1a;
                color: var(--green);
                border-radius: 4px;
                padding: .1rem .45rem;
                font-size: .7rem;
            }

            .id-col { color: var(--muted); font-size: .72rem; }

            /* Mensagens */
            .msg {
                padding: .9rem 1.1rem;
                border-radius: 6px;
                font-size: .85rem;
                margin-top: .5rem;
                border-left: 3px solid;
            }

            .msg.erro    { background: #1a0a0a; color: var(--red);   border-color: var(--red); }
            .msg.sucesso { background: #0a1a0a; color: var(--green); border-color: var(--green); }
        </style>
    </head>
    <body>

    <header>
        <h1>API REST — <span>CEP</span></h1>
        <p class="sub">// gerenciamento de CEPs via HTTP — PHP + MySQL + PDO</p>
    </header>

    <nav class="nav">
        <a href="?" class="get-link $badgeGet">
            <span class="badge-method get-badge">GET</span> Listar todos
        </a>
        <a href="?acao=form_post" class="post-link $badgePost">
            <span class="badge-method post-badge">POST</span> Cadastrar
        </a>
        <a href="?acao=form_delete" class="delete-link $badgeDelete">
            <span class="badge-method delete-badge">DELETE</span> Remover
        </a>
    </nav>

    <div class="card">
        <div class="card-header">
            <span class="card-title">$titulo</span>
        </div>
        $conteudo
    </div>

    </body>
    </html>
    HTML;
}

function tabelaCeps(array $dados): string {
    if (empty($dados)) {
        return '<p class="msg erro">Nenhum registro encontrado.</p>';
    }
    $linhas = '';
    foreach ($dados as $row) {
        $linhas .= "<tr>
            <td class=\"id-col\">#{$row['id']}</td>
            <td>{$row['cep']}</td>
            <td>{$row['logradouro']}</td>
            <td>{$row['bairro']}</td>
            <td>{$row['cidade']}</td>
            <td><span class='badge-uf'>{$row['estado']}</span></td>
            <td class=\"id-col\">{$row['criado_em']}</td>
        </tr>";
    }
    return "<table>
        <thead><tr>
            <th>#</th><th>CEP</th><th>Logradouro</th>
            <th>Bairro</th><th>Cidade</th><th>UF</th><th>Cadastrado em</th>
        </tr></thead>
        <tbody>$linhas</tbody>
    </table>";
}

// ─── Ações de formulários HTML (POST e DELETE via form) ───────────────────
$acao = $_GET['acao'] ?? null;

// Formulário POST
if ($acao === 'form_post') {
    $conteudo = <<<HTML
    <form method="POST" action="?acao=salvar">
        <div class="field"><label>CEP</label><input type="text" name="cep" placeholder="12345-678" required></div>
        <div class="field"><label>Logradouro</label><input type="text" name="logradouro" placeholder="Nome da rua" required></div>
        <div class="field"><label>Bairro</label><input type="text" name="bairro" placeholder="Bairro" required></div>
        <div class="field"><label>Cidade</label><input type="text" name="cidade" placeholder="Cidade" required></div>
        <div class="field"><label>Estado (UF)</label><input type="text" name="estado" placeholder="SP" maxlength="2" required></div>
        <button type="submit">Cadastrar CEP</button>
    </form>
    HTML;
    htmlLayout('Cadastrar novo CEP', $conteudo, 'post');
    exit;
}

// Formulário DELETE
if ($acao === 'form_delete') {
    $conteudo = <<<HTML
    <form method="POST" action="?acao=deletar">
        <div class="field"><label>CEP para remover</label><input type="text" name="cep" placeholder="12345-678" required></div>
        <button type="submit" class="btn-delete">Remover CEP</button>
    </form>
    HTML;
    htmlLayout('Remover CEP', $conteudo, 'delete');
    exit;
}

// Processa POST (salvar)
if ($acao === 'salvar' && $method === 'POST') {
    $campos   = ['cep','logradouro','bairro','cidade','estado'];
    $faltando = array_filter($campos, fn($c) => empty($_POST[$c]));

    if ($faltando) {
        $conteudo = '<p class="msg erro">Campos obrigatórios ausentes: ' . implode(', ', $faltando) . '</p>';
    } else {
        $cepF = preg_replace('/\D/', '', $_POST['cep']);
        if (strlen($cepF) !== 8) {
            $conteudo = '<p class="msg erro">CEP inválido. Use o formato 00000-000.</p>';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO cep (cep, logradouro, bairro, cidade, estado) VALUES (?,?,?,?,?)");
                $stmt->execute([
                    $cepF,
                    trim($_POST['logradouro']),
                    trim($_POST['bairro']),
                    trim($_POST['cidade']),
                    strtoupper(trim($_POST['estado'])),
                ]);
                $conteudo = '<p class="msg sucesso">✅ CEP cadastrado com sucesso! ID: ' . $pdo->lastInsertId() . '</p>';
            } catch (PDOException $e) {
                $msg = $e->getCode() === '23000' ? 'CEP já cadastrado.' : $e->getMessage();
                $conteudo = "<p class='msg erro'>Erro: $msg</p>";
            }
        }
    }
    htmlLayout('Resultado', $conteudo, 'post');
    exit;
}

// Processa DELETE (deletar via form)
if ($acao === 'deletar' && $method === 'POST') {
    $cepF = preg_replace('/\D/', '', $_POST['cep'] ?? '');
    if (empty($cepF)) {
        $conteudo = '<p class="msg erro">CEP não informado.</p>';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM cep WHERE REPLACE(cep, '-', '') = ?");
        $stmt->execute([$cepF]);
        if (!$stmt->fetch()) {
            $conteudo = '<p class="msg erro">CEP não encontrado.</p>';
        } else {
            $pdo->prepare("DELETE FROM cep WHERE REPLACE(cep, '-', '') = ?")->execute([$cepF]);
            $conteudo = '<p class="msg sucesso">✅ CEP removido com sucesso!</p>';
        }
    }
    htmlLayout('Resultado', $conteudo, 'delete');
    exit;
}

// ─── GET — Busca por CEP específico via query string ──────────────────────
if ($method === 'GET' && !empty($cep)) {
    $stmt = $pdo->prepare("SELECT * FROM cep WHERE REPLACE(cep, '-', '') = ?");
    $stmt->execute([$cep]);
    $resultado = $stmt->fetch();

    if ($resultado) {
        $conteudo = tabelaCeps([$resultado]);
    } else {
        $conteudo = '<p class="msg erro">CEP não encontrado.</p>';
    }
    htmlLayout("Resultado para CEP: $cep", $conteudo, 'get');
    exit;
}

// ─── GET — Lista todos ────────────────────────────────────────────────────
if ($method === 'GET') {
    $stmt = $pdo->prepare("SELECT * FROM cep ORDER BY id ASC");
    $stmt->execute();
    $resultado = $stmt->fetchAll();

    $conteudo = tabelaCeps($resultado);
    htmlLayout('Todos os CEPs cadastrados', $conteudo, 'get');
    exit;
}

// ─── Método não permitido ─────────────────────────────────────────────────
http_response_code(405);
htmlLayout('Erro', '<p class="msg erro">Método não permitido. Use GET, POST ou DELETE.</p>');
