<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Migração ZTE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Assets/style/style.css">
</head>
<body>
   <div class="formulario-container">
        <div class="formulario-topo">
            <img src="Assets/img/logos/LogoMGP.png" alt="Logo MGP">
            <h1>Migração para OLT ZTE</h1>
        </div>
        <form action="" method="post" enctype="multipart/form-data" class="formulario-form">
            <label>Nome do arquivo de destino: </label>
            <input type="text" name="nome_arquivo" placeholder="<Equip_Origem>_<PON>">
            <label>Selecione o fabricante da OLT de Origem:</label>
            <select name="fabricante">
                <option value="" disabled selected>Selecione aqui</option>
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
   </div>
   <div class="confirmacao-container">
        <div class="confirmacao-topo">
            <h1>Status</h1>
            <p>Caso seja necessária a autenticação, aparecerá os campos nesta área para preenchimento</p>
            <!--Parte inicial-->
        </div>

        <div class="confirmacao-envio">
            <h1>Informações cadastradas com sucesso!</h1>
            <img src="Assets/img/icones/confirme.png" alt="Icone confirmação">
        </div>
        
        <form action="" method="post" class="formulario-extra">
            <div class="container-usuario">
                <h3>Usuário: <span>user aqui</span></h3>
                <input type="password" name="senha_pppoe" placeholder="Digite a senha PPPoE" required>
                <input type="text" name="ssid_wifi" placeholder="Digite o SSID do Wi-Fi" required>
                <input type="password" name="senha_wifi" placeholder="Digite a senha do Wi-Fi" required>
            </div><!--Repetir essa div-->
            <input type="submit" value="Enviar">
        </form>
   </div>
</body>
</html>
