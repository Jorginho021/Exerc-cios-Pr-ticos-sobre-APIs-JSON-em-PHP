<?php
$errors = [];
$result = null;
$cep = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cep = trim($_POST['cep'] ?? '');

    if ($cep === '') {
        $errors[] = 'Informe o CEP.';
    } else {
        // extrai apenas dígitos
        $cepOnlyNumbers = preg_replace('/\D/', '', $cep);

        if (strlen($cepOnlyNumbers) !== 8) {
            $errors[] = 'CEP deve conter 8 dígitos.';
        } else {
            $url = "https://viacep.com.br/ws/{$cepOnlyNumbers}/json/";
            $json = @file_get_contents($url);

            if ($json === false) {
                $errors[] = 'Não foi possível consultar o serviço ViaCEP. Tente novamente mais tarde.';
            } else {
                $data = json_decode($json, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $errors[] = 'Resposta inválida da API.';
                } elseif (!empty($data['erro'])) {
                    $errors[] = 'CEP não encontrado.';
                } else {
                    $result = [
                        'logradouro' => $data['logradouro'] ?? '',
                        'bairro' => $data['bairro'] ?? '',
                        'localidade' => $data['localidade'] ?? '',
                        'uf' => $data['uf'] ?? '',
                    ];
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exercício 4 - Consulta CEP</title>
    <link rel="stylesheet" href="sttyles.css">
</head>
<body>
    <div class="container">
        <h1>Consulta CEP (ViaCEP)</h1>
        <form method="post" action="">
            <div>
                <label for="cep">CEP:</label>
                <input type="text" id="cep" name="cep" value="<?php echo htmlspecialchars($cep, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Ex: 01001-000" />
            </div>
            <button type="submit">Buscar</button>
        </form>

        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <div class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($result): ?>
            <div class="success">
                <h2>Endereço encontrado</h2>
                <dl>
                    <dt>Logradouro</dt>
                    <dd><?php echo htmlspecialchars($result['logradouro'], ENT_QUOTES, 'UTF-8'); ?></dd>
                    <dt>Bairro</dt>
                    <dd><?php echo htmlspecialchars($result['bairro'], ENT_QUOTES, 'UTF-8'); ?></dd>
                    <dt>Cidade</dt>
                    <dd><?php echo htmlspecialchars($result['localidade'], ENT_QUOTES, 'UTF-8'); ?></dd>
                    <dt>Estado</dt>
                    <dd><?php echo htmlspecialchars($result['uf'], ENT_QUOTES, 'UTF-8'); ?></dd>
                </dl>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
