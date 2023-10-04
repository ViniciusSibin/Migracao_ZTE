<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Migração ZTE - Arquivos</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/style/gerenciar.css">
</head>
<body>
    <div class="titulo">
        <h1>Gerenciar Arquivos</h1>
    </div>
    <div class="area-btnSistema">
        <a href="../index.php" class="botao-sistema">Voltar para o sistema</a>
    </div>
    <div class="area-gerenciamento">
    <?php
    // Caminho para a pasta "Arquivos"
    $caminhoArquivos = '../Arquivos';

    // Verifique se o parâmetro "pasta" está definido na URL
    if (isset($_GET['pasta'])) {
        $pastaAtual = $caminhoArquivos . '/' . $_GET['pasta'];
    } else {
        $pastaAtual = $caminhoArquivos;
    }
 
    // Lista os arquivos e subpastas na pasta atual
    $arquivos = scandir($pastaAtual);
    foreach ($arquivos as $arquivo) {
        if ($arquivo != '.' && $arquivo != '..') {
            $caminhoCompleto = $pastaAtual . '/' . $arquivo;

            if (is_dir($caminhoCompleto)) {
                // Verifica se a pasta atual é uma subpasta de "Arquivos"
                if ($pastaAtual !== $caminhoArquivos) {
                    $pastaAnterior = dirname($pastaAtual);
                    $nomePastaAnterior = basename($pastaAnterior);
                    
                    if ($nomePastaAnterior === "Arquivos") {
                        echo '<p>Pasta: <a href="gerenciar.php?pasta=' . urlencode($caminhoCompleto) . '">' . $arquivo . '</a> - <a class="btn-excluir" href="excluir.php?dir=' . urlencode($caminhoCompleto) . '">Excluir</a></p>';
                    }
                }else {
                    // Se a pasta for uma subpasta de "Arquivos", exibir a opção de exclusão da subpasta
                    echo '<p>Pasta: <a href="gerenciar.php?pasta=' . urlencode($caminhoCompleto) . '">' . $arquivo . '</a></p>';
                }
            } elseif (pathinfo($arquivo, PATHINFO_EXTENSION) == 'txt') {
                // Exibir link para visualizar o arquivo
                echo '<p><span class="arquivos">Arquivo:</span> ' . $arquivo . ' - <a class="btn-visualizar" href="' . $caminhoCompleto . '" target="_blank">Visualizar</a></p>';
            }
        }
    }

    ?>
    </div>
</body>
</html>