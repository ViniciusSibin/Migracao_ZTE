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
    <div class="gerenciamento-container">
        <div class="titulo">
            <h1>Gerenciar Arquivos</h1>
            <a href="../index.php" class="botao-sistema">Voltar para o sistema</a>
        </div>
        <div class="area-gerenciamento">
        <?php
        // Função para validar caminho seguro
        function isSafePath($path) {
            return realpath($path) === false ? false : true;
        }

        // Caminho para a pasta "Arquivos"
        $caminhoArquivos = '../Arquivos';

        // Verifique se o parâmetro "pasta" está definido na URL e é seguro
        if (isset($_GET['pasta']) && isSafePath($caminhoArquivos . '/' . $_GET['pasta'])) {
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
                    if (strpos($caminhoCompleto, $caminhoArquivos) === 0) {
                        echo '<p>Pasta: <a href="gerenciar.php?pasta=' . urlencode($caminhoCompleto) . '">' . $arquivo . '</a>';
                        // Adicione opção de exclusão apenas para subpastas
                        if ($pastaAtual !== $caminhoArquivos) {
                            echo ' - <a class="btn-excluir" href="excluir.php?dir=' . urlencode($caminhoCompleto) . '">Excluir</a>';
                        }
                        echo '</p>';

                    }
                } elseif (pathinfo($caminhoCompleto, PATHINFO_EXTENSION) == 'txt') {
                    // Exibir link para visualizar o arquivo
                    echo '<p><span class="arquivos">Arquivo:</span> ' . $arquivo . ' - <a class="btn-visualizar" href="gerenciar.php?pasta=' . urlencode($_GET['pasta']) . '&arquivo=' . urlencode($arquivo) . '">Visualizar</a></p>';
                }
            }
        }
        ?>
        </div>
    </div><!--tela direita(gerenciamento)-->
    <div class="arquivo-container">
        <div class="titulo">
            <h1>Arquivo</h1>
        </div>
        <?php 
            if(isset($_GET['arquivo'])) {
                $arquivoSelecionado = file_get_contents($pastaAtual.'/'.$_GET['arquivo']);
                if ($arquivoSelecionado !== false) {
                    echo '<pre class="texto-arquivo">' . htmlspecialchars($arquivoSelecionado) . '</pre>';
                    echo '<div id="botoes-arquivo">
                    <a class="btn-excluirArquivo" onclick="exibirConfirmacao()">Excluir</a>
                    <a class="btn-downloadArquivo" href="download.php?arquivo=' . urlencode($pastaAtual.'/'.$_GET['arquivo']) . '">Download</a>
                    </div>';
                    echo '<div id="area-confirmacao-exclusao">
                    <p>Tem certeza que deseja excluir o arquivo: <strong>'.$arquivo.'</strong></p>
                    <a class="botao-cancelar-exclusao" onclick="recarregarPagina()"">Não</a>
                    <a class="botao-confirmar-exclusao" href="excluir.php?arquivo=' . urlencode($pastaAtual.'/'.$_GET['arquivo']) . '">Sim</a>
                    </div>';
                } else {
                    echo '<p>Erro ao ler o arquivo.</p>';
                }
            }
        ?>
    </div>
    <script>
        function exibirConfirmacao(){
            let botoesArquivo = document.querySelector('#botoes-arquivo')
            let areaConfirmacao = document.querySelector('#area-confirmacao-exclusao');
            areaConfirmacao.style.display = 'block';
            botoesArquivo.style.display = 'none';
        }
        function recarregarPagina(){
            location.reload();
        }
        function exibirConfirmacaoExcluirDiretorio(){

        }
    </script>
</body>
</html>