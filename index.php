<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Migração ZTE</title>
</head>
<body>
    <form action="" method="post" enctype="multipart/form-data">
        <img src="Assets\img\logos\LogoMGP.png" alt="Logo da Empresa">
        <h1>Migração para OLT ZTE</h1>
        <label>Nome do arquivo de destino: </label>
        <input type="text" name="nome_arquivo" placeholder="<Equip_Origem>_<PON>">
        <label>Selecione o fabricante da OLT de Origem:</label>
        <select name="fabricante">
            <option value="DATACOM">DATACOM</option>
            <option value="FIBERHOME">FIBERHOME</option>
            <option value="INTELBRAS">INTELBRAS</option>
            <option value="PARKS">PARKS</option>
            <option value="VSOLUTION">VSOLUTION</option>
            <option value="HUAWEI">HUAWEI</option>
            <option value="ZTE">ZTE</option>
        </select>
        <label>Selecione o backup da PON:</label>
        <input type="file" id="arquivoInput" name="arquivoOrigem">
        <input type="submit" value="Enviar">
    </form>

    <a href="Arquivos/Script-IGUA-OLT-DATACOM-PON2_teste.txt" download="Script-IGUA-OLT-DATACOM-PON3_teste.txt">Baixar</a>

</body>
</html>

<?php
var_dump($_FILES);