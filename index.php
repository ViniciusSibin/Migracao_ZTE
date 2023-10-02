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
    $slot = $_POST['slot'];
    $pon = $_POST['pon'];

   
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
        $finalizado = False;
        
        // Lê o conteúdo do arquivo
        $conteudo = fread($arquivoOrigem, filesize($caminhoArquivoOrigem));
        fclose($arquivoOrigem);
    
        if($fabricante == "DATACOM"){
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
                $script = "conf t\ninterface gpon_olt-$pon\nonu $id type F601 sn $sn\nexit\ninterface gpon_onu-$pon:$id\nname $usuario\nvport-mode manual\nvport 1 map-type vlan\ntcont 1 profile 1G\ngemport 1 tcont 1\nvport-map 1 1 vlan $vlan\nexit\ninterface vport-$pon.$id:1\nservice-port 1 user-vlan $vlan vlan $vlan\nexit\npon-onu-mng gpon_onu-$pon:$id\nservice 1 gemport 1 vlan $vlan\nvlan port eth_0/1 mode tag vlan $vlan\nend\n\n\n";
            
                fwrite($arquivoDestino, $script);
                
            }
            $finalizado = True;
        } elseif ($fabricante == "VSOLUTION"){

            $linhas = explode("\n", $conteudo);
        
            if(strpos($conteudo,"EPON") !== false){

                foreach($linhas as $linha){
                    // Divide a string em partes com base nos espaços em branco
                    $parts = preg_split('/\s+/', $linha);

                    $interface = $parts[0];
                    // Atribui as partes às variáveis
                    $interfaceArray = explode(':', $interface);

                    $id = $interfaceArray[1]; 

                    //Montando o serial number da forma correta
                    $sn = $parts[1] . substr($parts[3], 4);
                    $usuario = $parts[6];
    
                    // Remova os espaços extras dos valores, se necessário
                    $pon = trim($pon);
                    $id = trim($id);
                    $sn = trim($sn);
                    $usuario = trim($usuario);
    
                    //Monta o script com as insformações do usuário
                    $script = "conf t\ninterface gpon_olt-1/$slot/$pon\nonu $id type F601 sn $sn\nexit\ninterface gpon_onu-1/$slot/$pon:$id\nname $usuario\nvport-mode manual\nvport 1 map-type vlan\ntcont 1 profile 1G\ngemport 1 tcont 1\nvport-map 1 1 vlan $vlan\nexit\ninterface vport-1/$slot/$pon.$id:1\nservice-port 1 user-vlan $vlan vlan $vlan\nexit\npon-onu-mng gpon_onu-1/$slot/$pon:$id\nservice 1 gemport 1 vlan $vlan\nvlan port eth_0/1 mode tag vlan $vlan\nend\n\n\n";
                
                    fwrite($arquivoDestino, $script);        
                }
            } else {
                var_dump($_POST);
            }
            
            $finalizado = True;
        }
    }

    if(isset($finalizado) && $finalizado === True){
        fwrite($arquivoDestino, "write");
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
            <div class="inputsLadoALado">
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
                    <input type="number" class="vlan" name="vlan" value="301" id="vlan">
                </div>
            </div>
            <div class="inputsLadoALado">
                <div class="area-input-pon">
                    <label  for="vlan">PON:</label>
                    <input type="number" name="pon" value="1">
                </div>
                <div class="area-input-slot">
                    <label >SLOT que vai migrar:</label>
                    <input type="number" name="slot" value="1">
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
   </div>
</body>
</html>