<?php
$errors = [];
$cep = '';
$result = null;
$history = [];
$filePath = __DIR__ . '/consultas.json';

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Carregar histórico se existir
if (is_file($filePath)) {
    $stored = @file_get_contents($filePath);
    if ($stored !== false) {
        $decoded = json_decode($stored, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $history = $decoded;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cep = trim($_POST['cep'] ?? '');

    if ($cep === '') {
        $errors[] = 'Informe o CEP.';
    } else {
        $cepOnlyDigits = preg_replace('/\D/', '', $cep);
        if (strlen($cepOnlyDigits) !== 8) {
            $errors[] = 'CEP deve conter 8 dígitos.';
        } else {
            $url = "https://viacep.com.br/ws/{$cepOnlyDigits}/json/";
            $json = @file_get_contents($url);

            if ($json === false) {
                $errors[] = 'Não foi possível consultar a API ViaCEP no momento.';
            } else {
                $data = json_decode($json, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $errors[] = 'Resposta inválida da API.';
                } elseif (!empty($data['erro'])) {
                    $errors[] = 'CEP não encontrado.';
                } else {
                    $result = [
                        'cep' => $cepOnlyDigits,
                        'logradouro' => $data['logradouro'] ?? '',
                        'bairro' => $data['bairro'] ?? '',
                        'cidade' => $data['localidade'] ?? '',
                        'estado' => $data['uf'] ?? '',
                        'consulta' => date('Y-m-d H:i:s'),
                    ];

                    // Salvar no histórico
                    $history[] = $result;
                    $saved = @file_put_contents($filePath, json_encode($history, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    if ($saved === false) {
                        $errors[] = 'Não foi possível salvar a consulta em consultas.json.';
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Exercício 10 - Histórico de CEP</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <div class="container">
        <h1>Consulta CEP + Histórico</h1>

        <form method="post" action="">
            <label for="cep">CEP</label>
            <input type="text" id="cep" name="cep" value="<?php echo h($cep); ?>" placeholder="Ex: 01001000" />
            <button type="submit">Consultar</button>
        </form>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $err): ?>
                    <div class="error"><?php echo h($err); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($result): ?>
            <div class="card result">
                <h2>Resultado</h2>
                <p><strong>CEP:</strong> <?php echo h($result['cep']); ?></p>
                <p><strong>Logradouro:</strong> <?php echo h($result['logradouro']); ?></p>
                <p><strong>Bairro:</strong> <?php echo h($result['bairro']); ?></p>
                <p><strong>Cidade:</strong> <?php echo h($result['cidade']); ?></p>
                <p><strong>Estado:</strong> <?php echo h($result['estado']); ?></p>
                <p><strong>Consultado em:</strong> <?php echo h($result['consulta']); ?></p>
            </div>
        <?php endif; ?>

        <h2>Histórico de consultas</h2>
        <?php if ($history): ?>
            <div class="history">
                <?php foreach (array_reverse($history) as $entry): ?>
                    <div class="card">
                        <p><strong>CEP:</strong> <?php echo h($entry['cep'] ?? ''); ?></p>
                        <p><strong>Logradouro:</strong> <?php echo h($entry['logradouro'] ?? ''); ?></p>
                        <p><strong>Bairro:</strong> <?php echo h($entry['bairro'] ?? ''); ?></p>
                        <p><strong>Cidade:</strong> <?php echo h($entry['cidade'] ?? ''); ?></p>
                        <p><strong>Estado:</strong> <?php echo h($entry['estado'] ?? ''); ?></p>
                        <p><strong>Data/Hora:</strong> <?php echo h($entry['consulta'] ?? ''); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="empty">Nenhuma consulta registrada ainda.</p>
        <?php endif; ?>
    </div>
</body>
</html>