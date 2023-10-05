<?php
function excluirDiretorio($dir) {
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                $file_path = $dir . DIRECTORY_SEPARATOR . $file;
                if (is_dir($file_path)) {
                    // Se o item for um diretório, chame a função recursivamente
                    excluirDiretorio($file_path);
                } else {
                    // Se o item for um arquivo, exclua-o
                    unlink($file_path);
                }
            }
        }
        // Após excluir todos os arquivos e subdiretórios, exclua o diretório pai
        rmdir($dir);
        $novaURL = "gerenciar.php";
        header('Location: ' . $novaURL);
    } else {
        echo 'Diretório não encontrado.';
    }
}

if (isset($_GET['dir'])) {
    $diretorioExcluir = $_GET['dir'];

    // Verifique se o diretório a ser excluído está dentro da pasta "Arquivos"
    if (strpos(realpath($diretorioExcluir), realpath('../Arquivos')) === 0) {
        // Chame a função para excluir o diretório e seu conteúdo
        excluirDiretorio($diretorioExcluir);
    } else {
        echo 'Diretório não permitido.';
    }
} else {
    echo 'Parâmetro de diretório ausente.';
}

if (isset($_GET['arquivo'])) {
    $arquivoExcluir = $_GET['arquivo'];

    // Verifique se o arquivo a ser excluído está dentro da pasta "Arquivos"
    if (strpos(realpath($arquivoExcluir), realpath('../Arquivos')) === 0) {
        // Exclua o arquivo
        if (unlink($arquivoExcluir)) {
            // Redirecione de volta para a página de gerenciamento após a exclusão
            header('Location: gerenciar.php');
        } else {
            echo 'Erro ao excluir o arquivo.';
        }
    } else {
        echo 'Arquivo não permitido.';
    }
} else {
    echo 'Parâmetro de arquivo ausente.';
}
?>