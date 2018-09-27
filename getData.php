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
    
    $query_tables = "SELECT tables.id_table,
                            tables.table,
                            tables.description_table,
                            submodulos.submodulo,
                            submodulos.descripcion_submodulo,
                            modulos.modulo,
                            modulos.descripcion                                 
                       from tables
                 inner join submodulos on
                            tables.id_submodulo = submodulos.id_submodulo
                 inner join modulos on
                            submodulos.id_modulo = modulos.id_modulo ";

    $total_tcodes = mysql_query($query_tcodes);
    $total_tables = mysql_query($query_tables);

    $tcodes = array();
    $tables = array();
    $ind=0;
    $ind2=0;

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
    
    while($rowTa = mysql_fetch_array($total_tables)) 
         {
           $data_t['table'] = $rowTa['table'];
           $data_t['table_descripcion'] = $rowTa['description_table'];
           $data_t['submodulo'] = $rowTa['submodulo'];
           $data_t['descripcion_submodulo'] = $rowT['descripcion_submodulo'];
           $data_t['modulo'] = $rowTa['modulo'];
           $data_t['modulo_descipcion'] = $rowTa['descripcion'];
        
           $tables[$ind2] =  $data_t ;
           $ind2++;
         }

         utf8_converter($tcodes,$tables);
}

function utf8_converter($tcodes,$tables)
{
    array_walk_recursive($tcodes, function(&$item, $key)
    {
        if(!mb_detect_encoding($item, 'utf-8', true)){
                $item = utf8_encode($item);
        }
    });

    array_walk_recursive($tables, function(&$item, $key)
    {
        if(!mb_detect_encoding($item, 'utf-8', true)){
                $item = utf8_encode($item);
        }
    });
 
 
    createJson($tcodes,$tables);
}

function createJson($tcodes,$tables)
{
    $jsonencoded = json_encode($tcodes,JSON_UNESCAPED_UNICODE);
    $fh = fopen("tcodes.json", 'w');
    fwrite($fh, $jsonencoded);
    fclose($fh);
    echo "Archivo JSON Tcode Creado";

    $jsonencoded = json_encode($tables,JSON_UNESCAPED_UNICODE);
    $fh = fopen("tables.json", 'w');
    fwrite($fh, $jsonencoded);
    fclose($fh);
    echo "Archivo JSON Tables Creado";

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