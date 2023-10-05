<?php
if(isset($_GET['arquivo'])) {
    $arquivo = $_GET['arquivo'];

    // Verifique se o arquivo existe
    if (file_exists($arquivo)) {
        // Configurar cabeçalhos HTTP para download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($arquivo) . '"');
        header('Content-Length: ' . filesize($arquivo));

        // Lê e envia o arquivo para o navegador
        readfile($arquivo);
        exit;
    } else {
        echo 'Arquivo não encontrado.';
    }
} else {
    echo 'Parâmetro de arquivo ausente.';
}
?>