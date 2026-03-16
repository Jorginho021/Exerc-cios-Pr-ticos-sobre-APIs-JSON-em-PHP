<?php
$error = '';
$message = '';
$users = [];

$path = __DIR__ . '/usuarios_api.json';

// Seletor de ação: buscar API ou exibir o arquivo salvo.
$action = $_GET['action'] ?? 'load';

if ($action === 'fetch') {
    $url = 'https://jsonplaceholder.typicode.com/users';
    $json = @file_get_contents($url);

    if ($json === false) {
        $error = 'Erro ao buscar API de usuários.';
    } else {
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $error = 'Erro ao decodificar JSON da API.';
        } elseif (!is_array($data)) {
            $error = 'Formato de dados inesperado da API.';
        } else {
            $saved = file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            if ($saved === false) {
                $error = 'Erro ao salvar arquivo usuarios_api.json.';
            } else {
                $message = 'Dados salvos em usuarios_api.json com sucesso!';
            }
        }
    }
    $action = 'load';
}

if ($action === 'load') {
    if (!file_exists($path)) {
        $error = $error ?: 'Arquivo usuarios_api.json não encontrado. Use "Buscar API e salvar" antes.';
    } else {
        $savedJson = @file_get_contents($path);
        if ($savedJson === false) {
            $error = 'Erro ao ler arquivo usuarios_api.json.';
        } else {
            $users = json_decode($savedJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $error = 'Erro ao decodificar usuarios_api.json.';
                $users = [];
            }
        }
    }
}

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Exercício 9 - Persistência JSON</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <div class="container">
        <h1>Usuários API + JSON Local</h1>

        <p>
            <a class="btn" href="?action=fetch">Buscar API e salvar em usuarios_api.json</a>
            <a class="btn" href="?action=load">Carregar e exibir usuarios_api.json</a>
        </p>

        <?php if ($message): ?>
            <div class="success"><?php echo h($message); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error"><?php echo h($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($users) && is_array($users)): ?>
            <div class="grid">
                <?php foreach ($users as $user): ?>
                    <div class="card">
                        <h2><?php echo h($user['name'] ?? '-'); ?></h2>
                        <p><strong>E-mail:</strong> <?php echo h($user['email'] ?? '-'); ?></p>
                        <p><strong>Telefone:</strong> <?php echo h($user['phone'] ?? '-'); ?></p>
                        <p><strong>Cidade:</strong> <?php echo h($user['address']['city'] ?? '-'); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>