<?php
include("../connection.php");

getData($conexion);

function getData($conexion)
{
    $query_tcodes = "SELECT tcodes.id_tcode,
                            tcodes.tcode,
                            tcodes.description,
                            submodulos.submodulo,
                            submodulos.descripcion_submodulo,
                            modulos.modulo,
                            modulos.descripcion                                 
                       from tcodes
                 inner join submodulos on
                                   tcodes.id_submodulo = submodulos.id_submodulo
                 inner join modulos on
                            submodulos.id_modulo = modulos.id_modulo ";

    $total_tcodes = mysql_query($query_tcodes);

    $tcodes = array();
    $ind=0;

    while($rowT = mysql_fetch_array($total_tcodes)) 
         {
           $data['tcode'] = $rowT['tcode'];
           $data['tcode_descripcion'] = $rowT['description'];
           $data['submodulo'] = $rowT['submodulo'];
           $data['descripcion_submodulo'] = $rowT['descripcion_submodulo'];
           $data['modulo'] = $rowT['modulo'];
           $data['modulo_descipcion'] = $rowT['descripcion'];
        
           $tcodes[$ind] =  $data ;
           $ind++;
         }

         utf8_converter($tcodes);
}

function utf8_converter($tcodes)
{
    array_walk_recursive($tcodes, function(&$item, $key)
    {
        if(!mb_detect_encoding($item, 'utf-8', true)){
                $item = utf8_encode($item);
        }
    });
 
    createJson($tcodes);
}

function createJson($tcodes)
{
    $jsonencoded = json_encode($tcodes,JSON_UNESCAPED_UNICODE);
    $fh = fopen("SAPgit/tcodes.json", 'w');
    fwrite($fh, $jsonencoded);
    fclose($fh);
    echo "Archivo JSON Creado";
    updateRepository();
}
function updateRepository()
{
    $fecha=date("d-m-Y");
    exec("git add .");
    exec('git commit -m "'.$fecha.'" ');
    exec('git push -u origin master');
    echo "Repositorio Actualizado";
}
?>