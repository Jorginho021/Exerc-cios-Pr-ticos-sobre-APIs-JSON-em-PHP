<?php
$mensagem = '';

// Verifica se o formulário foi enviado
if (isset($_POST['submit'])) {
    // Obtém e limpa os dados
    $nome = trim($_POST['nome'] ?? '');
    $idade = trim($_POST['idade'] ?? '');
    $curso = trim($_POST['curso'] ?? '');

    // Valida se todos os campos foram preenchidos
    if (empty($nome) || empty($idade) || empty($curso)) {
        $mensagem = '<div class="erro">Erro: Todos os campos devem ser preenchidos!</div>';
    } else {
        // Cria um novo aluno
        $novoAluno = [
            'nome' => $nome,
            'idade' => (int)$idade,
            'curso' => $curso
        ];

        // Lê o arquivo JSON existente
        $arquivo = 'alunos.json';
        if (file_exists($arquivo)) {
            $jsonContent = file_get_contents($arquivo);
            $alunos = json_decode($jsonContent, true);
            
            // Se não for um array válido, inicializa um novo
            if (!is_array($alunos)) {
                $alunos = [];
            }
        } else {
            $alunos = [];
        }

        // Adiciona o novo aluno
        $alunos[] = $novoAluno;

        // Salva o arquivo JSON
        if (file_put_contents($arquivo, json_encode($alunos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
            $mensagem = '<div class="sucesso">Aluno cadastrado com sucesso!</div>';
        } else {
            $mensagem = '<div class="erro">Erro ao salvar o arquivo!</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Alunos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Cadastro de Alunos</h1>

        <?php if ($mensagem): ?>
            <?php echo $mensagem; ?>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="nome">Nome do Aluno:</label>
                <input type="text" id="nome" name="nome" required>
            </div>

            <div class="form-group">
                <label for="idade">Idade:</label>
                <input type="number" id="idade" name="idade" min="1" max="120" required>
            </div>

            <div class="form-group">
                <label for="curso">Curso:</label>
                <input type="text" id="curso" name="curso" required>
            </div>

            <button type="submit" name="submit" class="btn">Cadastrar</button>
        </form>
    </div>
</body>
</html>
