<?php
if(!empty($_POST) && !empty($_FILES)) {
    //Obtendo o diretório do arquivo de origem
    $arquivoOrigemFiles = $_FILES['arquivoOrigem']; 
    $caminhoArquivoOrigem = $arquivoOrigemFiles['tmp_name'];

    $nomeArquivoDestino = $_POST['nome_arquivo'];
    $fabricante = $_POST['fabricante'];
   
    $arquivoOrigem = fopen($caminhoArquivoOrigem, "r");
    $arquivoDestino = fopen("Arquivos/$fabricante/Script_$nomeArquivoDestino.txt", "w");

    if ($arquivoOrigem) {
        $onuCompleta = array();
        // Lê o conteúdo do arquivo
        $conteudo = fread($arquivoOrigem, filesize($caminhoArquivoOrigem));
        fclose($arquivoOrigem);
    
        $conteudoExplodido = explode("--------  ------   -------------   ----------   --------------------------   ------------------------------------------------", $conteudo);
    
        $linhas = explode("\n", trim($conteudoExplodido[count($conteudoExplodido) - 1]));
    
        foreach($linhas as $linha){
            // Divide a string em partes com base nos espaços em branco
            $parts = preg_split('/\s+/', $linha);
            
            // Atribui as partes às variáveis
            $pon = $parts[0];
            $id = $parts[1];
            $sn = $parts[2];
            $usuario = $parts[5];

            // Remova os espaços extras dos valores, se necessário
            $pon = trim($pon);
            $id = trim($id) + 1;
            $sn = trim($sn);
            $usuario = trim($usuario);

            //Monta o script com as insformações do usuário
            $script = "conf t\ninterface gpon_olt-$pon\nonu $id type F601 sn $sn\nexit\ninterface gpon_onu-$pon:$id\nname $usuario\nvport-mode manual\nvport 1 map-type vlan\ntcont 1 profile 1G\ngemport 1 tcont 1\nvport-map 1 1 vlan 301\nexit\ninterface vport-$pon.$id:1\nservice-port 1 user-vlan 301 vlan 301\nexit\npon-onu-mng gpon_onu-$pon:$id\nservice 1 gemport 1 vlan 301\n";

            if(strpos($sn, "MKP") !== false){
                $onuCompleta = $usuario;
                $SenhaPPPOE = solicitaDados($usuario, "senha do PPPOE");
                $usuarioWIFI = solicitaDados($usuario, "nome de WI-FI");
                $senhaWIFI = solicitaDados($usuario, "senha do WI-FI");            

                $script .= "wan-ip 1 ipv4 mode pppoe username $usuario password $SenhaPPPOE vlan-profile 301 host 1\nsecurity-mgmt 1 state enable mode forward ingress-type iphost 1 protocol web\nssid auth wpa wifi_0/2 key $senhaWIFI\nssid ctrl wifi_0/2 name MGP_$usuarioWIFI\nssid auth wpa wifi_0/6 key $senhaWIFI\nssid ctrl wifi_0/6 name MGP_" . $usuarioWIFI . "_5G\n";
            } else {
                $script .= "vlan port eth_0/1 mode tag vlan 301\n";
            }

            $script .= "end\nwrite\n\n---------------------------------------------------------\n\n\n";
            
            fwrite($arquivoDestino, $script);
            
            if(!empty($onuCompleta)){
                echo "<script>alert('Por favor preencha o formulário das onus completas!!!');</script>";
            }
        }
    }

    if($_POST['onuCompleta']){
        $script = "conf t\ninterface gpon_olt-$pon\nonu $id type F601 sn $sn\nexit\ninterface gpon_onu-$pon:$id\nname $usuario\nvport-mode manual\nvport 1 map-type vlan\ntcont 1 profile 1G\ngemport 1 tcont 1\nvport-map 1 1 vlan 301\nexit\ninterface vport-$pon.$id:1\nservice-port 1 user-vlan 301 vlan 301\nexit\npon-onu-mng gpon_onu-$pon:$id\nservice 1 gemport 1 vlan 301\nwan-ip 1 ipv4 mode pppoe username $usuario password $SenhaPPPOE vlan-profile 301 host 1\nsecurity-mgmt 1 state enable mode forward ingress-type iphost 1 protocol web\nssid auth wpa wifi_0/2 key $senhaWIFI\nssid ctrl wifi_0/2 name MGP_$usuarioWIFI\nssid auth wpa wifi_0/6 key $senhaWIFI\nssid ctrl wifi_0/6 name MGP_" . $usuarioWIFI . "_5G\nend\nwrite\n\n---------------------------------------------------------\n\n\n";
    }
}
?>

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
