<!DOCTYPE html>
<html>
<head>
    <title>Gerenciar Arquivos</title>
</head>
<body>
    <h1>Gerenciar Arquivos</h1>
    <?php
    $caminhoArquivos = '../Arquivos';
    $pastaAtual = isset($_GET['pasta']) ? $caminhoArquivos . '/' . $_GET['pasta'] : $caminhoArquivos;
    $arquivos = scandir($pastaAtual);

    if ($pastaAtual !== $caminhoArquivos) {
        echo '<a href="gerenciar.php?pasta=' . urlencode(dirname($pastaAtual)) . '">Voltar para a pasta anterior</a><br>';
        echo '<a href="../index.php">Voltar para o sistema</a><br>';
    } else {
        echo '<a href="../index.php">Voltar para o sistema</a><br>';
    }

    foreach ($arquivos as $arquivo) {
        if ($arquivo != '.' && $arquivo != '..') {
            $caminhoCompleto = $pastaAtual . '/' . $arquivo;
            if (is_dir($caminhoCompleto)) {
                echo '<p><strong>Pasta: </strong><a href="gerenciar.php?pasta=' . urlencode($caminhoCompleto) . '">' . $arquivo . '</a></p>';
            } elseif (pathinfo($arquivo, PATHINFO_EXTENSION) == 'txt') {
                echo '<p><strong>Arquivo: </strong>' . $arquivo . ' - <a href="' . $caminhoCompleto . '" target="_blank">Visualizar</a></p>';
            }
        }
    }
    ?>
</body>
</html>