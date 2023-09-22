<?php
if(!empty($_POST)) {
    //Obtendo o diretório do arquivo de origem
    if(isset($_FILES['arquivoOrigem'])){
        $arquivoOrigemFiles = $_FILES['arquivoOrigem']; 
        $caminhoArquivoOrigem = $arquivoOrigemFiles['tmp_name'];

        //Abrindo o arquivo de origem
        $arquivoOrigem = fopen($caminhoArquivoOrigem, "r");
    }
    
    $oltOrigem = $_POST['nome_olt_origem'];
    $nomeArquivoDestino = $_POST['nome_arquivo_destino'];
    $fabricante = $_POST['fabricante'];
    $vlan = $_POST['vlan'];
   
    //Substitui espaços pos underline
    $oltOrigem = str_replace(" ", "_", $oltOrigem);
    $nomeArquivoDestino = str_replace(" ", "_", $nomeArquivoDestino);

    //Verificando se existe o diretório para a OLT escolhida
    $diretorio = "Arquivos/$fabricante/$oltOrigem";

    if (!file_exists($diretorio)) {
        // Tenta criar o diretório com permissões 0755 (permissões padrão, você pode alterá-las conforme necessário)
        mkdir($diretorio, 0755);
    } 

    $arquivoDestino = fopen($diretorio . "/Script_$nomeArquivoDestino.txt", "a");

    if (isset($arquivoOrigem) && $arquivoOrigem) {
        $UsuariosOnusCompletas = array();
        $OnusCompletas = array();
        $finalizado = False;
        
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
            $script = "conf t\ninterface gpon_olt-$pon\nonu $id type F601 sn $sn\nexit\ninterface gpon_onu-$pon:$id\nname $usuario\nvport-mode manual\nvport 1 map-type vlan\ntcont 1 profile 1G\ngemport 1 tcont 1\nvport-map 1 1 vlan $vlan\nexit\ninterface vport-$pon.$id:1\nservice-port 1 user-vlan $vlan vlan $vlan\nexit\npon-onu-mng gpon_onu-$pon:$id\nservice 1 gemport 1 vlan $vlan\nvlan port eth_0/1 mode tag vlan $vlan\nend\nwrite\n\n\n\n";

            /*if(strpos($sn, "MKP") !== false){
                $UsuariosOnusCompletas[] = $usuario; 
                $OnusCompletas[] = "$pon,$id,$sn,$usuario";        
                continue;
            } */
          
            fwrite($arquivoDestino, $script);
        }

        if(count($OnusCompletas) > 0){
            echo "<script>alert('Por favor preencha o formulário das onus completas!!!');</script>";
        } else {
            $finalizado = True;
        }
    }

    if(isset($_POST['onuCompleta'])){
        $OnusCompletas = $_POST['OnusCompletas'];
        foreach($OnusCompletas as $dados){
            $parts = explode(",", $dados);
    
            // Atribui as partes às variáveis
            $pon = $parts[0];
            $id = $parts[1];
            $sn = $parts[2];
            $usuarioOnuCompleta = $parts[3];
            

            foreach($_POST as $loginUsuarios => $informacoesWIFI){
                $loginUsuario = str_replace("_", ".", $loginUsuarios);
                if($usuarioOnuCompleta == $loginUsuario){

                    $SenhaPPPOE = $informacoesWIFI[0];
                    $usuarioWIFI = $informacoesWIFI[1];
                    $senhaWIFI = $informacoesWIFI[2];

                    echo "<script>console.log('$SenhaPPPOE - $usuarioWIFI - $senhaWIFI');</script>";

                    $script = "conf t\ninterface gpon_olt-$pon\nonu $id type F6600P sn $sn\nexit\ninterface gpon_onu-$pon:$id\nname $usuarioOnuCompleta\nvport-mode manual\nvport 1 map-type vlan\ntcont 1 profile 1G\ngemport 1 tcont 1\nvport-map 1 1 vlan 301\nexit\ninterface vport-$pon.$id:1\nservice-port 1 user-vlan 301 vlan 301\nexit\npon-onu-mng gpon_onu-$pon:$id\nservice 1 gemport 1 vlan 301\nwan-ip 1 ipv4 mode pppoe username $usuarioOnuCompleta password $SenhaPPPOE vlan-profile 301 host 1\nsecurity-mgmt 1 state enable mode forward ingress-type iphost 1 protocol web\nssid auth wpa wifi_0/2 key $senhaWIFI\nssid ctrl wifi_0/2 name MGP_$usuarioWIFI\nssid auth wpa wifi_0/5 key $senhaWIFI\nssid ctrl wifi_0/5 name MGP_" . $usuarioWIFI . "_5G\nend\nwrite\n\n\n\n";

                    //Escreve as novas informações no arquivo
                    fwrite($arquivoDestino, $script);
                }
            }
        } 
        $finalizado = True;
        
    }
    
    if(isset($finalizado) && $finalizado === True){

        $confirmaFinalizacao = True;
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
            <label>Nome da OLT de origem: </label>
            <input type="text" name="nome_olt_origem" placeholder="OLT DATACOM IGUA">
            <label>Nome do arquivo de destino: </label>
            <input type="text" name="nome_arquivo_destino" placeholder="<Equip_Origem>_<PON>">
                <div class="inputs-oltEvlan">
                    <div class="area-input-olt">
                        <label class="label-fabricante">Selecione o fabricante da OLT de Origem:</label>
                        <select name="fabricante" class="olt_origem">
                            <option value="DATACOM">DATACOM</option>
                            <option value="FIBERHOME">FIBERHOME</option>
                            <option value="INTELBRAS">INTELBRAS</option>
                            <option value="PARKS">PARKS</option>
                            <option value="VSOLUTION">VSOLUTION</option>
                            <option value="HUAWEI">HUAWEI</option>
                            <option value="ZTE">ZTE</option>
                        </select>
                    </div>
                    <div class="area-input-vlan">
                        <label class="label-vlan" for="vlan">VLAN:</label>
                        <input type="text" class="vlan" name="vlan" value="301" id="vlan">
                    </div>
                </div>
            <label>Selecione o backup da PON:</label>
            <input type="file" id="arquivoInput" name="arquivoOrigem">
            <input type="submit" value="Enviar">
            <a class="download-arquivo" href="Pages/gerenciar.php">Arquivos Gerados</a>
        </form>
   </div>
   <div class="confirmacao-container">
        <div class="confirmacao-topo">
            <h1>Status</h1>
            <p>Caso seja necessária a autenticação, aparecerá os campos nesta área para preenchimento</p>
            <!--Parte inicial-->
        </div>

        <div class="confirmacao-envio"  style='display:<?php if(isset($confirmaFinalizacao) && $confirmaFinalizacao) {echo "flex;";} else { echo "none"; } ?>'>
            <h1>Informações cadastradas com sucesso!</h1>
            <img src="Assets/img/icones/confirme.png" alt="Icone confirmação">
        </div>
        
        <?php 
            if(isset($confirmaFinalizacao) && $confirmaFinalizacao) {
                $oltOrigem = $_POST['nome_olt_origem'];
                $fabricante = $_POST['fabricante'];

                $nomeArquivoDestino = "Script_$nomeArquivoDestino.txt"; // Certifique-se de definir o nome do arquivo aqui

                //Substitui espaços pos underline
                $oltOrigem = str_replace(" ", "_", $oltOrigem);
                $nomeArquivoDestino = str_replace(" ", "_", $nomeArquivoDestino);

                // Verifique se o diretório para a OLT escolhida existe
                $diretorio = "Arquivos/$fabricante/$oltOrigem";

                if (file_exists($diretorio) && is_dir($diretorio)) {
                    $arquivoPath = $diretorio . "/" . $nomeArquivoDestino;

                    // Verifique se o arquivo existe
                    if (file_exists($arquivoPath)) {
                        $arquivoFinalizado = file_get_contents($arquivoPath);
                        $arquivoFinalizado = str_replace("\n", "<br>", $arquivoFinalizado);
                        ?>

                        <p><?php echo $arquivoFinalizado; 
                    }
                }?></p>

            
            <a class="download-arquivo" href="<?php echo $diretorio . "/$nomeArquivoDestino"; ?>" download="<?php echo "$nomeArquivoDestino" ?>">Download do Arquivo</a>

        <?php } ?>

        <form action="" method="post" enctype="multipart/form-data" class="formulario-extra" style='display:<?php if(isset($UsuariosOnusCompletas) && count($UsuariosOnusCompletas) > 0) { echo "block";} else { echo "none"; } ?>'>
            <?php foreach($UsuariosOnusCompletas as $onu){ ?>
                <div class="container-usuario">
                    <h3>Usuário: <span><?php echo $onu; ?></span></h3>
                    <input type="text" name="<?php echo $onu ?>[]" placeholder="Digite a senha PPPoE" required>
                    <input type="text" name="<?php echo $onu ?>[]" placeholder="Digite o SSID do Wi-Fi" required>
                    <input type="text" name="<?php echo $onu ?>[]" placeholder="Digite a senha do Wi-Fi" required>
                </div><!--Repetir essa div-->
            <?php } ?>
            <input type="hidden" name="nome_olt_origem" value="<?php if(isset($_POST['nome_olt_origem'])) echo $_POST['nome_olt_origem'] ?>">
            <input type="hidden" name="nome_arquivo_destino" value="<?php if(isset($_POST['nome_arquivo_destino'])) echo $_POST['nome_arquivo_destino'] ?>">
            <input type="hidden" name="fabricante" value="<?php if(isset($_POST['fabricante'])) echo $_POST['fabricante'] ?>">
            <input type="hidden" name="vlan" value="<?php if(isset($_POST['vlan'])) echo $_POST['vlan'] ?>">
            <?php
                if (isset($OnusCompletas) && is_array($OnusCompletas)) {
                    foreach ($OnusCompletas as $valor) {
                        echo '<input type="hidden" name="OnusCompletas[]" value="' . htmlspecialchars($valor) . '">';
                    }
                }
            ?>
            <input type="submit" value="Enviar" name="onuCompleta">
        </form>
   </div>
</body>
</html>