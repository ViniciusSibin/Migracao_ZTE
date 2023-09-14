<?php 

$caminhoArquivoOrigem = '\\\172.31.255.8\arquivos\arquivos\Documentos da Rede MGP\ZTE\MIGRACAO\IGUA-OLT-DATACOM-PON2.txt';
$caminhoArquivoDestino = '\\\172.31.255.8\arquivos\arquivos\Documentos da Rede MGP\ZTE\MIGRACAO\Scripts\Script-IGUA-OLT-DATACOM-PON2_teste.txt';
$arquivoOrigem = fopen($caminhoArquivoOrigem, "r");
$arquivoDestino = fopen($caminhoArquivoDestino, "w");

if ($arquivoOrigem) {
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
            echo "Digite a senha do PPPOE para o usuário $usuario: ";
            $SenhaPPPOE = trim(fgets(STDIN));
            echo "Digite o nome de WI-FI para o usuário $usuario: ";
            $usuarioWIFI = trim(fgets(STDIN));
            echo "Digite a senha de WI-FI para o usuário $usuario: ";
            $senhaWIFI = trim(fgets(STDIN));

            $script .= "wan-ip 1 ipv4 mode pppoe username $usuario password $SenhaPPPOE vlan-profile 301 host 1\nsecurity-mgmt 1 state enable mode forward ingress-type iphost 1 protocol web\nssid auth wpa wifi_0/2 key $senhaWIFI\nssid ctrl wifi_0/2 name MGP_$usuarioWIFI\nssid auth wpa wifi_0/5 key $senhaWIFI\nssid ctrl wifi_0/5 name MGP_" . $usuarioWIFI . "_5G\n";
        } else {
            $script .= "vlan port eth_0/1 mode tag vlan 301\n";
        }

        $script .= "end\nwrite\n\n---------------------------------------------------------\n\n\n";
        
        fwrite($arquivoDestino, $script);
    }

    fclose($arquivoDestino);
} else {
    echo "Não foi possível abrir o arquivo para escrita.";
}