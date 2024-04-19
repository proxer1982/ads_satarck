<?php
if (!defined('ABSPATH')) die;

class FormRender
{
    private $areas;
    private $paises;
    private $tipo_rec;
    private $app_auto;
    private $pl_datos;
    private $colaboradores;

    public function __construct()
    {
        $this->areas =  array(1 => "UEN E", 2 => "UEN T", 3 => "Club de Amigos");
        $this->paises =  array("All" => "Transversal", "Col" => "Colombia", "Ecu" => "Ecuador", "Pan" => "Panamá", "USA" => "Estados Unidos");
        //$this->tipo_rec = array("landing_page" => "Landing-page", "form_web" => "Formulario web", "form_ac" => "Formulario AC", "form_rd" => "Formulario RD Station", "tyc" => "Página de T&C", "auto_zap" => "Automatización Zapier", "auto_pa" => "Automatización Power Automate", "auto_ac" => "Automatización AC", "auto_rd" => "Automatización RD Station", "vista_sql" =>  "Vistas SQL", "excel" => "Excel Microsoft", "hc_google" => "Hoja de cálculo Google");
        $this->tipo_rec = array("landing_page" => "Landing-page", "form_web" => "Formulario web", "form_ac" => "Formulario AC", "form_rd" => "Formulario RD Station", "tyc" => "Página de T&C");
        $this->app_auto = array("zapier" => "Zapier", "pa" => "Power Automate", "ac" => "Active Campain", "rd" => "RD Station", "otro" => "Otro");
        $this->pl_datos = array("excel" => "Excel", "google" =>  "Google", "mysql" => "MySQL", "json" => "jSON", "Llista_ac" => "Lista AC", "otro" => "Otro");

        if (get_option('users_ads_satrack') === false ||  get_option('users_ads_satrack') === "") {
            $arreglo = array();
        } else {
            $arreglo = get_option('users_ads_satrack');
        }

        foreach ($arreglo as $item) {
            $this->colaboradores[$item['id']] = $item['nombre'];
        }
    }

    public function get_datos($array, $ind)
    {
        if (isset($this->{$array}[$ind]) && !empty($this->{$array}[$ind])) {
            return $this->{$array}[$ind];
        } else {
            return null;
        }
    }

    public function form_open($action, $print = true, $id = "")
    {
        $resp = "<form method='post' id=" . $id . "><input type='hidden' name='action' value='$action'>";
        if ($print) : echo $resp;
        else : return $resp;
        endif;
    }

    public function select($tipo, $requerido = false, $valor = "", $print = true, $nombre = null)
    {
        if ($nombre != null) {
            $resp = "<select name='{$nombre}' class='form-select' ";
        } else {
            $resp = "<select name='{$tipo}[]' class='form-select' ";
        }

        if ($requerido) : $resp .= "required>";
        else : $resp .= ">";
        endif;
        if ($valor == "") : $resp .= "<option value='' selected>--</option>";
        else : $resp .= "<option value=''>--</option>";
        endif;
        foreach ($this->{$tipo} as $key => $area) {
            if ($key == $valor) : $resp .= "<option value='{$key}' selected>{$area}</option>";
            else : $resp .= "<option value='{$key}'>{$area}</option>";
            endif;
        }
        $resp .= "</select>";

        if ($print) : echo $resp;
        else : return $resp;
        endif;
    }


    public function input($tipo, $nombre, $requerido = false, $valor = "", $place = "", $class = "", $print = true)
    {
        if ($tipo == "date" && $valor != "") {
            $valor = date('Y-m-d', strtotime($valor));
        }

        if ($place != "") {
            $place = "placeholder='" . $place . "'";
        }

        $resp = "<input type='$tipo'";
        if ($requerido) : $resp .= "required";
        else : $resp .= "";
        endif;
        $resp .= " name='{$nombre}' id='{$nombre}' value='$valor' class='{$class}' {$place} >";

        if ($print) : echo $resp;
        else : return $resp;
        endif;
    }

    public function radio_paises($datos = array(), $print = true)
    {
        if ($datos === null) {
            $datos = array();
        }

        $resp = "<div class='col-md-8'>";
        foreach ($this->paises as $ind => $pais) {
            $resp .= "<div class='form-check form-switch'>
            <input class='form-check-input' name='paises[]' type='checkbox' value='{$ind}'";
            if (in_array($ind, $datos)) : $resp .= "checked >";
            else : $resp .= ">";
            endif;
            $resp .= "<label class='form-check-label' for='flexSwitchCheckDisabled'>{$pais}</label></div>";
        }
        $resp .= "</div>";

        if ($print) {
            echo $resp;
        } else {
            return $resp;
        }
    }

    public function multirecursos($datos = array(), $print = true)
    {
        if ($datos == null) {
            $largo = 0;
        } else {
            $largo = sizeof($datos);
        }


        if ($largo == 0) {
            $datos[] = (object) array('tipo_rec' => '', 'URL' => '', 'resp_recurso' => '');
            $largo = 1;
        }

        $resp = "<div class='row'><div class='col-sm-8'><h3>Recursos </h3></div><div class='col-sm-4 d-flex justify-content-end'><input type='button' name='agregar_recurso' id='agregar_recurso' class='btn btn-info button button-default' value='Agregar recurso'></div></div>";
        $resp .= "<table id='tbl_recursos' data-items='" . $largo . "' class='table'><thead>
        <tr>
            <th style='width:15px;'> </th><th>Tipo de recurso</th><th>URL</th>
            <th>Responsable</th><th> </th></thead><tbody class='sortable'></tr>";

        foreach ($datos as $ind =>  $recurso) {
            $item = $ind + 1;
            $resp .= "<tr class='row_recurso_{$item}'>
            <td style='color:#dedede; vertical-align:middle;'><i class='fas fa-ellipsis-v'></i></td><td>";
            $resp .= $this->select("tipo_rec", false, $recurso->tipo_rec, false);
            $resp .= "</td>
            <td><input type='text' class='form-control regular-text' name='URL[]' value='{$recurso->URL}'></td><td>";
            $resp .= $this->select("colaboradores", false, $recurso->resp_recurso, false, 'resp_recurso[]');
            //<td><input type='text' class='form-control regular-text' name='resp_recurso[]' value='{$recurso->resp_recurso}' required></td>
            $resp .= "</td><td class='del_campo' style='width:40px;'><a class='text-danger button btn-default btn-eliminar-recurso' style='display:inline-flex; align-items:center;' data-item='.row_recurso_{$item}'><span class='dashicons dashicons-no'></span></a></td>
            </tr>";
        }

        $resp .= "</tbody></table>";

        if ($print) : echo $resp;
        else : return $resp;
        endif;
    }

    public function multiautomatiza($datos = array(), $print = true)
    {
        if ($datos == null) {
            $largo = 0;
        } else {
            $largo = sizeof($datos);
        }

        if ($largo == 0) {
            $datos[] = (object) array('nombre_auto' => '', 'app_auto' => '', 'url_auto' => '', 'acciones_auto' => '');
            $largo = 1;
        }

        $resp = "<div class='row'><div class='col-sm-8'><h3>Automatizaciones</h3></div><div class='col-sm-4 d-flex justify-content-end'><input type='button' name='agregar_auto' id='agregar_auto' class='btn btn-info button button-default' value='Agregar automatización'></div></div>";
        $resp .= "<table id='tbl_auto' class='table' data-items='{$largo}'><thead>
        <tr>
            <th style='width:15px;'> </th>
            <th>Nombre de la automatización</th>
            <th>Aplicación</th>
            <th>URL</th>
            <th>Descripción</th>
            <th> </th>
            </tr></thead><tbody class='sortable'>";

        foreach ($datos as $ind =>  $recurso) {
            $item = $ind + 1;

            $resp .= "<tr class='row_auto_{$item}'>
            <td style='color:#dedede; vertical-align:middle;'><i class='fas fa-ellipsis-v'></i></td>
            <td><input type='text' class='form-control regular-text' name='nombre_auto[]' value='{$recurso->nombre_auto}'></td>
            <td>";
            $resp .= $this->select("app_auto", false, $recurso->app_auto, false);
            $resp .= "</td>
            <td><input type='url' class='form-control regular-text' name='url_auto[]' value='{$recurso->url_auto}'></td>
            <td><textarea class='form-control regular-text' name='acciones_auto[]'>{$recurso->acciones_auto}</textarea></td>
            <td class='del_campo' style='width:40px;'><a class='text-danger button btn-default btn-eliminar-auto' style='display:inline-flex; align-items:center;' data-item='.row_auto_{$item}'><span class='dashicons dashicons-no'></span></a></td>
            </tr>";
        }
        $resp .= "</tbody></table>";

        if ($print) : echo $resp;
        else : return $resp;
        endif;
    }

    public function multidatos($datos = array(), $print = true)
    {
        if ($datos == null) {
            $largo = 0;
        } else {
            $largo = sizeof($datos);
        }

        if ($largo == 0) {
            $datos[] = (object) array('pl_datos' => '', 'URL_datos' => '');
            $largo = 1;
        }

        $resp = "<div class='row'><div class='col-sm-8'><h3>Datos</h3></div><div class='col-sm-4 d-flex justify-content-end'><input type='button' name='agregar_datos' id='agregar_datos' class='btn btn-info button button-default' value='Agregar datos'></div></div>";
        $resp .= "<table id='tbl_datos' class='table' data-items='{$largo}'><thead>
		<tr><th>Plataforma</th><th>URL</th><th> </th></tr></thead><tbody class='sortable'>";

        foreach ($datos as $ind =>  $recurso) {
            $item = $ind + 1;

            $resp .= "<tr class='row_datos_{$item}'><td>";
            $resp .= $this->select("pl_datos", false, $recurso->pl_datos, false);
            $resp .= "</td>
		    <td><input type='text' class='form-control regular-text' name='URL_datos[]' value='{$recurso->URL_datos}'></td>
		    <td class='del_campo' style='width:40px;'><a class='text-danger button btn-default btn-eliminar-datos' style='display:inline-flex; align-items:center;' data-item='.row_datos_{$item}'><span class='dashicons dashicons-no'></span></a></td>
            </tr>";
        }

        $resp .= "</tbody></table>";

        if ($print) : echo $resp;
        else : return $resp;
        endif;
    }


    public function datos_json($data = array())
    {
        if (sizeof($data) > 0) {
            $data_temp = array();

            for ($i = 0; $i < sizeof($_POST[$data[0]]); $i++) {
                $data_temp[$i] = array();

                foreach ($data as $clave) {
                    $valor = str_replace("\\", "", $_POST[$clave][$i]);
                    $data_temp[$i][$clave] = $valor;
                }
            }
            return json_encode($data_temp);
        } else {
            return null;
        }
    }

    public function get_datos_by_parm($array, $valor)
    {
        $dato = false;
        foreach ($this->{$array} as $key =>  $val) {
            if ($val == $valor) {
                $dato = $key;
            }
        }

        return $dato;
    }
}
