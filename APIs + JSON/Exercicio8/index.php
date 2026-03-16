<?php
$errors = [];
$result = null;
$cep = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cep = trim($_POST['cep'] ?? '');

    if ($cep === '') {
        $errors[] = 'Informe o CEP.';
    } else {
        $cepOnlyDigits = preg_replace('/\D/', '', $cep);

        if (strlen($cepOnlyDigits) !== 8) {
            $errors[] = 'CEP deve ter 8 dígitos.';
        } else {
            $url = "https://viacep.com.br/ws/{$cepOnlyDigits}/json/";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $curlError = curl_error($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($response === false) {
                $errors[] = 'Erro na requisição cURL: ' . htmlspecialchars($curlError, ENT_QUOTES, 'UTF-8');
            } elseif ($statusCode !== 200) {
                $errors[] = 'Resposta inesperada da API (HTTP ' . $statusCode . ').';
            } else {
                $data = json_decode($response, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $errors[] = 'Falha ao ler resposta JSON.';
                } elseif (!empty($data['erro'])) {
                    $errors[] = 'CEP não encontrado.';
                } else {
                    $result = [
                        'logradouro' => $data['logradouro'] ?? '',
                        'bairro' => $data['bairro'] ?? '',
                        'cidade' => $data['localidade'] ?? '',
                        'estado' => $data['uf'] ?? '',
                    ];
                }
            }
        }
    }
}

function h($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exercício 8 - cURL ViaCEP</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Consulta CEP (cURL)</h1>
        <form method="post" action="">
            <input type="text" name="cep" placeholder="Digite o CEP (ex: 01001000)" value="<?php echo h($cep); ?>" />
            <button type="submit">Consultar</button>
        </form>

        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $err): ?>
                <div class="error"><?php echo h($err); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($result): ?>
            <div class="result">
                <h2>Endereço encontrado</h2>
                <ul>
                    <li><strong>Logradouro:</strong> <?php echo h($result['logradouro']); ?></li>
                    <li><strong>Bairro:</strong> <?php echo h($result['bairro']); ?></li>
                    <li><strong>Cidade:</strong> <?php echo h($result['cidade']); ?></li>
                    <li><strong>Estado:</strong> <?php echo h($result['estado']); ?></li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>