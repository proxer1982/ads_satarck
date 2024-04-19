<?php
if (!defined('ABSPATH')) die;

function eliminar_acentos($cadena)
{
    $cadena = str_replace(" ", "_", $cadena);
    //Reemplazamos la A y a
    $cadena = str_replace(
        array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
        array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
        $cadena
    );

    //Reemplazamos la E y e
    $cadena = str_replace(
        array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
        array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
        $cadena
    );

    //Reemplazamos la I y i
    $cadena = str_replace(
        array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
        array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
        $cadena
    );

    //Reemplazamos la O y o
    $cadena = str_replace(
        array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
        array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
        $cadena
    );

    //Reemplazamos la U y u
    $cadena = str_replace(
        array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
        array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
        $cadena
    );

    //Reemplazamos la N, n, C y c
    $cadena = str_replace(
        array('Ñ', 'ñ', 'Ç', 'ç'),
        array('N', 'n', 'C', 'c'),
        $cadena
    );


    $cadena = strtolower($cadena);

    return $cadena;
}

function codigo($variable)
{
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
}

function unique_multidim_array($array, $key, $all = false): array
{
    $uniq_array = array();
    $dup_array = array();
    $key_array = array();

    foreach ($array as $val) {
        if (!in_array($val[$key], $key_array)) {
            $key_array[] = $val[$key];
            $uniq_array[] = $val;
            /*
            # 1st list to check:
            # echo "ID or sth: " . $val['building_id'] . "; Something else: " . $val['nodes_name'] . (...) "\n";
*/
        } else {
            $dup_array[] = $val;
            /*
            # 2nd list to check:
            # echo "ID or sth: " . $val['building_id'] . "; Something else: " . $val['nodes_name'] . (...) "\n";
*/
        }
    }
    if ($all) {
        return array($uniq_array, $dup_array, /* $key_array */);
    } else {
        return $uniq_array;
    }
}

function cortar_texto($texto, $largo = 16)
{
    if (strlen($texto) > $largo) {
        $txt = substr($texto, 0, $largo);
        $txt .= "...";
        return $txt;
    } else {
        return $texto;
    }
}
