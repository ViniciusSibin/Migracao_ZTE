<?php 
    $arrayArquivos = "../Arquivos";
    $TodosArquivos = scandir($arrayArquivos);

    // Filtra apenas os diretórios (excluindo . e ..)
    $arquivos = array_filter($TodosArquivos, function($item) use ($arrayArquivos) {
        return is_dir($arrayArquivos . DIRECTORY_SEPARATOR . $item) && $item != "." && $item != "..";
    });

    if(isset($_GET['olt'])){
        $caminhoOlt = $arquivos . "/" . trim($_GET['olt']);
        $TodosArquivosOLT = scandir($caminhoOlt);

        $arquivos = array_filter($TodosArquivosOLT, function($item) use ($caminhoOlt) {
            return is_dir($caminhoOlt . DIRECTORY_SEPARATOR . $item) && $item != "." && $item != "..";
        });
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
    <link rel="stylesheet" href="../Assets/style/style.css">
</head>
<body>
   <div class="formulario-container">
        <div class="formulario-topo">
            <a href="../index.php"><img src="../Assets/img/logos/LogoMGP.png" alt="Logo MGP"></a>
            <h1>Arquivos Gerados</h1>
        </div>

        <?php 
            foreach($arquivos as $arquivo){
                echo "<p>$arquivo<hr></p>";
            }
        ?>
   </div>
   <div class="detalhamento-container">
        <div class="detalhamento-topo">
            <h1>Detalhamento</h1>
            <h2>Nome do Equipamento</h2>
            <!--Parte inicial-->
        </div>
    </div>
        
        
   </div>
</body>
</html>