<!DOCTYPE html>
<html>
<head>
    <title>Gerenciar Arquivos</title>
</head>
<body>
    <h1>Gerenciar Arquivos</h1>
    <a href="../index.php">Voltar para o sistema</a>
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
                        echo '<p><strong>Pasta: </strong><a href="gerenciar.php?pasta=' . urlencode($caminhoCompleto) . '">' . $arquivo . '</a> - <a href="excluir.php?dir=' . urlencode($caminhoCompleto) . '">Excluir</a></p>';
                    }
                }else {
                    // Se a pasta for uma subpasta de "Arquivos", exibir a opção de exclusão da subpasta
                    echo '<p><strong>Pasta: </strong><a href="gerenciar.php?pasta=' . urlencode($caminhoCompleto) . '">' . $arquivo . '</a></p>';
                }
            } elseif (pathinfo($arquivo, PATHINFO_EXTENSION) == 'txt') {
                // Exibir link para visualizar o arquivo
                echo '<p><strong>Arquivo: </strong>' . $arquivo . ' - <a href="' . $caminhoCompleto . '" target="_blank">Visualizar</a></p>';
            }
        }
    }

    ?>

</body>
</html>