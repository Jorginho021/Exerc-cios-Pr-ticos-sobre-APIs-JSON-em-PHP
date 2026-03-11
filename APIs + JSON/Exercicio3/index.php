<?php
// Lê o arquivo JSON
$arquivo = 'alunos.json';
$alunos = [];

if (file_exists($arquivo)) {
    $jsonContent = file_get_contents($arquivo);
    $alunos = json_decode($jsonContent, true);
    
    // Se não for um array válido, inicializa um novo
    if (!is_array($alunos)) {
        $alunos = [];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Alunos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Listagem de Alunos Cadastrados</h1>

        <?php if (empty($alunos)): ?>
            <div class="mensagem-vazia">
                <p>Nenhum aluno cadastrado ainda.</p>
                <a href="../Exercicio2/">Ir para Cadastro</a>
            </div>
        <?php else: ?>
            <div class="tabela-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Idade</th>
                            <th>Curso</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alunos as $aluno): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($aluno['nome']); ?></td>
                                <td><?php echo htmlspecialchars($aluno['idade']); ?></td>
                                <td><?php echo htmlspecialchars($aluno['curso']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="info">
                <p>Total de alunos: <strong><?php echo count($alunos); ?></strong></p>
            </div>
        <?php endif; ?>

        <div class="links">
            <a href="../Exercicio2/" class="btn-link">Cadastrar Novo Aluno</a>
        </div>
    </div>
</body>
</html>
