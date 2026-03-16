<?php
$error = '';
$users = [];

$url = 'https://jsonplaceholder.typicode.com/users';
$json = @file_get_contents($url);

if ($json === false) {
    $error = 'Não foi possível acessar a API pública. Verifique sua conexão e tente novamente.';
} else {
    $data = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        $error = 'Falha ao decodificar a resposta JSON.';
    } elseif (!is_array($data)) {
        $error = 'Resposta da API inesperada.';
    } else {
        $users = $data;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exercício 5 - Usuários JSONPlaceholder</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Usuários (JSONPlaceholder)</h1>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php else: ?>
            <div class="users-grid">
                <?php foreach ($users as $user): ?>
                    <div class="user-card">
                        <h2><?php echo htmlspecialchars($user['name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></h2>
                        <p><strong>E-mail:</strong> <?php echo htmlspecialchars($user['email'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><strong>Telefone:</strong> <?php echo htmlspecialchars($user['phone'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><strong>Cidade:</strong> <?php echo htmlspecialchars($user['address']['city'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
