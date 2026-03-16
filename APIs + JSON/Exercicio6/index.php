<?php
$error = '';
$posts = [];

$url = 'https://jsonplaceholder.typicode.com/posts';
$json = @file_get_contents($url);

if ($json === false) {
    $error = 'Não foi possível acessar a API de posts.';
} else {
    $data = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        $error = 'Erro ao decodificar JSON.';
    } elseif (!is_array($data)) {
        $error = 'Resposta inesperada da API.';
    } else {
        $filtered = [];
        foreach ($data as $post) {
            if ((int)($post['userId'] ?? 0) === 1) {
                $filtered[] = $post;
            }
            if (count($filtered) >= 10) {
                break;
            }
        }
        $posts = $filtered;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exercício 6 - Posts API</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Posts (userId = 1, 10 primeiros)</h1>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div class="post-card">
                    <div class="meta">ID: <?php echo htmlspecialchars($post['id'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></div>
                    <h2><?php echo htmlspecialchars($post['title'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p><?php echo nl2br(htmlspecialchars($post['body'] ?? '-', ENT_QUOTES, 'UTF-8')); ?></p>
                </div>
            <?php endforeach; ?>

            <?php if (empty($posts)): ?>
                <p>Nenhum post encontrado.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
