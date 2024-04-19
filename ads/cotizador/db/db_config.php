<?php
if (!defined('ABSPATH')) die;
class DB_Config
{
    private $tabla, $datos, $id_pais;
    public function __construct($pais = null)
    {
        global $wpdb;
        $this->tabla = $wpdb->prefix . "cotizaciones_opt_satrack";
        $this->insert_hs_table_into_db();
        $this->id_pais = $pais;
        if ($pais !== null) {
            $this->load_datos();
        }
    }

    public function set_id_pais($id_pais)
    {
        $this->id_pais = $id_pais;
    }

    private function insert_hs_table_into_db()
    {
        global $wpdb;

        // establecer el juego de caracteres predeterminado y la intercalación para la tabla
        $charset_collate = $wpdb->get_charset_collate();
        // Compruebe que la tabla no exista antes de continuar
        $sql = "CREATE TABLE if not exists `{$this->tabla}` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nombre_opcion` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
            `tipo_opcion` varchar(40) COLLATE utf8mb4_unicode_520_ci NOT NULL,
            `id_opcion` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
            `paises` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
            `datos_opcion` text COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
            `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `date_updated` datetime DEFAULT CURRENT_TIMESTAMP,
            `status` tinyint(3) DEFAULT '1',
            PRIMARY KEY (`id`)
        ) $charset_collate;";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
        $is_error = empty($wpdb->last_error);
        return $is_error;
    }

    public function get_datos($search = "", $type_aray = false, $del = false)
    {
        if (!isset($this->datos->inst_equipos) || !$this->datos->inst_equipos || sizeof($this->datos->inst_equipos) <= 0) {
            $this->load_datos($search, $type_aray);
        }

        if ($search !== "") {
            $this->load_datos($search, $type_aray);
        }

        if ($del) {
            $this->load_datos($search, $type_aray, true);
        }

        if ($type_aray) {
            return (array) $this->datos;
        } else {
            return $this->datos;
        }
    }

    public function get_datos_by_type($tipo, $type_aray = false)
    {
        $this->load_datos_by_type($tipo, $type_aray);

        return $this->datos;
    }
    public function set_data_cotizador($datos)
    {
        global $wpdb;
        $tipo_campo = $datos['type'];
        $id_campo = strtolower($datos['id']);
        $nombre_campo = $datos['nombre'];
        $list_paises = $datos['paises'];
        $paises = explode(',', str_replace(" ", "", $list_paises));
        $data = [];
        //$data['paises'] = $paises;

        $new_datos = array();

        switch (strtolower($tipo_campo)):
            case 'equipo':
                foreach ($paises as $pais) {
                    $kpais = strtolower($pais);

                    if (isset($datos['pr_' . $kpais]) && !empty($datos['pr_' . $kpais])) {
                        $data['precios'][$pais] = floatval(str_replace(',', '.', $datos['pr_' . $kpais]));
                        $data['instala'][$pais] = (isset($datos['pr_inst_' . $kpais]) && $datos['pr_inst_' . $kpais] === 'false') ? false : true;
                    }
                }

                if (isset($datos['planes_uni']) && !empty($datos['planes_uni'])) {
                    $data['planes'] = $datos['planes_uni'];
                }
                break;
            case 'valor instalacion equipo':
                foreach ($paises as $pais) {
                    $kpais = strtolower($pais);

                    if (isset($datos['pr_inst_' . $kpais]) && !empty($datos['pr_inst_' . $kpais])) {
                        $data['precios'][$pais]  = floatval(str_replace(',', '.', $datos['pr_inst_' . $kpais]));
                    }
                }
                break;
            case 'plan':
                //$id_found = array_search($datos['id'], array_column($new_datos->planes, 'id'));
                $data['pag'] = $datos['pag_plan'];

                $data['precios'] = ["venta" => [], "comodato" => []];

                foreach ($paises as $pais) {
                    $kpais = strtolower($pais);
                    $data['precios']['venta'][$pais] = (isset($datos['pr_' . $kpais]) && !empty($datos['pr_' . $kpais])) ? floatval(str_replace(',', '.', $datos['pr_' . $kpais])) : 0;
                    $data['precios']['comodato'][$pais] = (isset($datos['valor_comod_' . $kpais]) && !empty($datos['valor_comod_' . $kpais])) ?
                        floatval(str_replace(',', '.', $datos['valor_comod_' . $kpais])) : 0;
                }
                break;
            case 'accesorio':
                $data = ["precios" => [], "instala" => []];

                foreach ($paises as $pais) {
                    $kpais = strtolower($pais);
                    if (isset($datos['pr_' . $kpais]) && !empty($datos['pr_' . $kpais])) {
                        $data['precios'][$pais] = floatval(str_replace(',', '.', $datos['pr_' . $kpais]));
                        $data['instala'][$pais] = floatval(str_replace(',', '.', $datos['pr_inst_' . $kpais]));
                    }
                }
                break;
            default:
                break;
        endswitch;

        $data = json_encode($data);

        $sql = "INSERT INTO {$this->tabla} (id_opcion, nombre_opcion, tipo_opcion, paises, datos_opcion, date_created) VALUES ('{$id_campo}', '{$nombre_campo}', '{$tipo_campo}', '{$list_paises}', '{$data}', CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE nombre_opcion = VALUES(nombre_opcion), tipo_opcion =VALUES(tipo_opcion), paises = VALUES(paises), datos_opcion = VALUES(datos_opcion), date_updated = VALUES(date_updated);";

        $results = $wpdb->get_results($sql, OBJECT);

        return $results;
    }

    private function load_datos($search = "", $type_aray = false, $del = false)
    {
        global $wpdb;

        $planes_temp = [
            'pl_avanzado' => 1,
            'pl_base' => 2,
            'pl_corto' => 3,
            'pl_estandar' => 4,
            'pl_plus' => 5,
            'pl_pro' => 6,
            'pl_unidad_portatil' => 7,
            'pl_video_online' => 8,
            'pl_video_online_avanzado' => 9
        ];

        if ($del) {
            $sql = "SELECT * FROM {$this->tabla} WHERE  status = 0";
        } else {
            if ($this->id_pais !== 'all') {
                $id = strtoupper($this->id_pais);
                $sql = "SELECT * FROM {$this->tabla} WHERE paises LIKE '%{$id}%' AND status > 0";
            } elseif ($search !== "") {
                $sql = "SELECT * FROM {$this->tabla} WHERE status > 0 AND ( nombre_opcion Like '%{$search}%' OR tipo_opcion Like '%{$search}%')";
            } else {
                $sql = "SELECT * FROM {$this->tabla} WHERE status > 0";
            }
        }

        $results = ($type_aray) ? $wpdb->get_results($sql, ARRAY_A) : $wpdb->get_results($sql, OBJECT);

        if ($wpdb->last_error !== '') :
            $wpdb->print_error();
        endif;

        $this->datos = new stdClass();

        if ($this->id_pais !== 'all') {

            foreach ($results as $item) :
                $data = (array) json_decode($item->datos_opcion);

                if (!array_key_exists($this->id_pais, $data)) {

                    switch (strtolower($item->tipo_opcion)):
                        case "equipo":
                            $equipo = (object) [
                                "id" => $item->id_opcion,
                                "nombre" => $item->nombre_opcion,
                                "precio" => $data['precios']->{$this->id_pais},
                                "planes" => (!empty($data['planes']) || $data['planes'] != "" || $data['planes'] != " ") ? explode(",", str_replace(" ", "", $data['planes'])) : null,
                                "no_instala" => ($data['instala']->{$this->id_pais} === false) ? true : false
                            ];
                            $new_pan = array();
                            foreach ($equipo->planes as $plan) {
                                $new_pan[] = $planes_temp[$plan];
                            }
                            $equipo->planes = $new_pan;
                            unset($new_pan);

                            $this->datos->equipos[] = $equipo;
                            break;
                        case "accesorio":
                            $this->datos->acc[] = (object) [
                                "id" => $item->id_opcion,
                                "nombre" => $item->nombre_opcion,
                                "precio" => (isset($data['precios']->{$this->id_pais})) ? $data['precios']->{$this->id_pais} : 0,
                                "instala" => (isset($data['instala']->{$this->id_pais})) ? $data['instala']->{$this->id_pais} : 0
                            ];
                            break;
                        case "plan":
                            $this->datos->planes[] = (object) [
                                "id" => $item->id_opcion,
                                "nombre" => $item->nombre_opcion,
                                "precio" => $data['precios']->venta->{$this->id_pais},
                                "comodato" => (isset($data['precios']->comodato->{$this->id_pais})) ? $data['precios']->comodato->{$this->id_pais} : null,
                                "pag" => (int) $data['pag']
                            ];
                            break;
                        case "instalacion":
                            $this->datos->inst_equipos[$item->id_opcion] = (object) [
                                "id" => $item->id_opcion,
                                "nombre" => $item->nombre_opcion,
                                "precio" => $data['precios']->{$this->id_pais}
                            ];
                            break;
                        default:
                            break;
                    endswitch;
                } else {
                    switch (strtolower($item->tipo_opcion)):
                        case "equipo":
                            $this->datos->equipos[] = (object) [
                                "id" => $item->id_opcion,
                                "new" => true,
                                "nombre" => $item->nombre_opcion,
                                "precio" => $data[$this->id_pais]->valor,
                                "planes" => (!is_array($data['planes'])) ? explode(",", str_replace(" ", "", $data['planes'])) : $data['planes'],
                                "no_instala" => ($data[$this->id_pais]->instalacion === 0) ? true : false
                            ];
                            break;
                        case "accesorio":
                            $this->datos->acc[] = (object) [
                                "id" => $item->id_opcion,
                                "nombre" => $item->nombre_opcion,
                                "precio" => (isset($data[$this->id_pais]->valor)) ? $data[$this->id_pais]->valor : 0,
                                "instala" => (isset($data[$this->id_pais]->valor_inst)) ? $data[$this->id_pais]->valor_inst : 0
                            ];
                            break;
                        case "plan":
                            $this->datos->planes[] = (object) [
                                "id" => $item->id_opcion,
                                "nombre" => $item->nombre_opcion,
                                "precio" => $data[$this->id_pais]->valor,
                                "comodato" => (isset($data[$this->id_pais]->valor_comodato)) ? $data[$this->id_pais]->valor_comodato : null,
                                "pag" => (int) $data['pag']
                            ];
                            break;
                        case "instalacion":
                            $this->datos->inst_equipos[$item->id_opcion] = (object) [
                                "id" => $item->id_opcion,
                                "nombre" => $item->nombre_opcion,
                                "precio" => $data[$this->id_pais]->valor
                            ];
                            break;
                        default:
                            break;
                    endswitch;
                }
            endforeach;
        } else {
            $this->datos = $results;
        }
        return $this->datos;
    }

    private function load_datos_by_type($tipo, $type_aray = false)
    {
        global $wpdb;

        if ($this->id_pais !== 'all') {
            $id = strtoupper($this->id_pais);
            $sql = "SELECT * FROM {$this->tabla} WHERE paises LIKE '%{$id}%' AND tipo_opcion = '{$tipo}' AND status > 0";
        } else {
            $sql = "SELECT * FROM {$this->tabla} WHERE tipo_opcion = '{$tipo}' AND status > 0";
        }

        $results = ($type_aray) ? $wpdb->get_results($sql, ARRAY_A) : $wpdb->get_results($sql, OBJECT);

        if ($wpdb->last_error !== '') :
            $wpdb->print_error();
        endif;

        $this->datos = new stdClass();

        if ($this->id_pais !== 'all') {
            foreach ($results as $item) :
                $data = json_decode($item->datos_opcion);
                switch ($item->tipo_opcion):
                    case "Equipo":
                        $this->datos->equipos[] = (object) [
                            "id" => $item->id_opcion,
                            "nombre" => $item->nombre_opcion,
                            "precio" => $data->precios->{$this->id_pais},
                            "planes" => (!empty($data->planes) || $data->planes != "" || $data->planes != " ") ? explode(",", str_replace(" ", "", $data->planes)) : null,
                            "no_instala" => ($data->instala->{$this->id_pais} === false) ? true : false
                        ];
                        break;
                    case "Accesorio":
                        $this->datos->acc[] = (object) [
                            "id" => $item->id_opcion,
                            "nombre" => $item->nombre_opcion,
                            "precio" => (isset($data->precios->{$this->id_pais})) ? $data->precios->{$this->id_pais} : 0,
                            "instala" => (isset($data->instala->{$this->id_pais})) ? $data->instala->{$this->id_pais} : 0
                        ];
                        break;
                    case "Plan":
                        $this->datos->planes[] = (object) [
                            "id" => $item->id_opcion,
                            "nombre" => $item->nombre_opcion,
                            "precio" => $data->precios->venta->{$this->id_pais},
                            "comodato" => (isset($data->precios->comodato->{$this->id_pais})) ? $data->precios->comodato->{$this->id_pais} : null,
                            "pag" => (int) $data->pag
                        ];
                        break;
                    case "Valor instalacion equipo":
                        $this->datos->inst_equipos[$item->id_opcion] = (object) [
                            "id" => $item->id_opcion,
                            "nombre" => $item->nombre_opcion,
                            "precio" => $data->precios->{$this->id_pais}
                        ];
                        break;
                    default:
                        break;
                endswitch;

            endforeach;
        } else {
            $this->datos = $results;
        }

        return $this->datos;
    }

    public function get_list_all_planes()
    {
        global $wpdb;
        $sql = "SELECT id_opcion, nombre_opcion FROM {$this->tabla} WHERE tipo_opcion = 'plan'";

        $datos = $wpdb->get_results($sql, OBJECT);

        $new_datos = array();
        foreach ($datos as $key => $item) {
            $new_datos[$item->id_opcion] = $item->nombre_opcion;
        }
        return $new_datos;
    }

    public function get_opcion_by_id($id)
    {
        $planes_temp = [
            'pl_avanzado' => 1,
            'pl_base' => 2,
            'pl_corto' => 3,
            'pl_estandar' => 4,
            'pl_plus' => 5,
            'pl_pro' => 6,
            'pl_unidad_portatil' => 7,
            'pl_video_online' => 8,
            'pl_video_online_avanzado' => 9
        ];

        global $wpdb;
        $sql = "SELECT * FROM {$this->tabla} WHERE id_opcion = '{$id}'";

        $results = $wpdb->get_row($sql, OBJECT);

        if (empty($results)) {
            return false;
        } else {
            $results->paises = (!is_array($results->paises)) ? explode(",", str_replace(" ", "", $results->paises)) : $results->paises;
            $results->datos_opcion = (array) json_decode($results->datos_opcion);

            /*Cambio de formato para la entrega de datos*/
            if (is_array($results->datos_opcion)) {
                $new_datos = array();
                $old_llaves = array_keys((array) $results->datos_opcion);

                if (!in_array($results->paises[0], $old_llaves)) {
                    foreach ($results->paises as $llave) {
                        /* la llave es cada pais */
                        $new_datos[$llave] = new stdClass();
                        foreach ($results->datos_opcion as $key => $opt) :
                            if (isset($opt->{$llave})) {
                                $key = ($key === 'precios') ? 'valor' : $key;
                                $key = ($key === 'instala' && $results->tipo_opcion === 'equipo') ? 'instalacion' : $key;
                                $key = ($key === 'instala' && $results->tipo_opcion !== 'equipo') ? 'valor_inst' : $key;

                                //$key = ($key === 'instala') ? 'valor_inst' : $key;
                                $new_datos[$llave]->{$key} = ($results->tipo_opcion === 'equipo') ? (int) $opt->$llave : $opt->$llave;
                            } else {
                                $lleno = false;
                                if ($key === 'precios' && isset($opt->venta->{$llave})) {
                                    $new_datos[$llave]->valor = $opt->venta->{$llave};
                                    $lleno = true;
                                }

                                if ($key === 'precios' && isset($opt->comodato->{$llave})) {
                                    $new_datos[$llave]->valor_comodato = $opt->comodato->{$llave};
                                    $lleno = true;
                                }

                                if ($key === 'planes') {
                                    $pl = (!is_array($opt)) ? explode(",", str_replace(" ", "", $opt)) : $opt;

                                    if (isset($pl[0]) && !is_numeric($pl[0])) {
                                        $pl2 = array();
                                        foreach ($pl as $key => $value) {
                                            $pl2[$key] = $planes_temp[$value];
                                        }
                                        $pl = $pl2;
                                        unset($pl2);
                                    }

                                    $new_datos['planes'] = $pl;
                                    $lleno = true;
                                }

                                if ($key === 'pag') {
                                    $results->{$key} = $opt;
                                    $lleno = true;
                                }

                                if (!$lleno) {
                                    $new_datos[$llave]->{$key} = $opt;
                                }
                            }

                        // $llave es igual a COL. y $key al tipo de valor
                        endforeach;
                    }
                    $results->datos_opcion = $new_datos;
                } else {
                    $results->datos_opcion = $results->datos_opcion;
                }
            }

            return $results;
        }
    }

    public function set_status_opcion($id, $status)
    {
        global $wpdb;
        $fecha_actual = new DateTime('', new DateTimeZone('	America/Bogota'));
        $fecha_actual = $fecha_actual->format("Y-m-d H:i:s");

        $data = ['status' => $status, 'date_updated' => $fecha_actual];
        $where = ['id_opcion' => $id];

        $wpdb->update($this->tabla, $data, $where);
    }

    public function update_opcion($id, $data)
    {
        global $wpdb;
        $fecha_actual = new DateTime('', new DateTimeZone('	America/Bogota'));
        $fecha_actual = $fecha_actual->format("Y-m-d H:i:s");

        $data['date_updated'] =  $fecha_actual;
        $where = ['id_opcion' => $id];

        $wpdb->update($this->tabla, $data, $where);
    }

    public function insert_opcion($data)
    {
        global $wpdb;
        $fecha_actual = new DateTime('', new DateTimeZone('	America/Bogota'));
        $fecha_actual = $fecha_actual->format("Y-m-d H:i:s");

        $data['date_updated'] =  $fecha_actual;
        $data['status'] =  1;

        $wpdb->insert($this->tabla, $data, ['%s', '%s', '%s', '%s', '%s', '%d']);
    }

    public function delete_opcion($id)
    {
        global $wpdb;

        $where = ['id_opcion' => $id];

        $wpdb->delete($this->tabla, $where);
    }

    public function get_number_actions_tbl($tipo = 'all')
    {
        global $wpdb;
        if ($tipo === 'trash') {
            $num_rows = $wpdb->get_var("SELECT COUNT(*) FROM {$this->tabla} WHERE status=0");
        } elseif ($tipo === 'all') {
            $num_rows = $wpdb->get_var("SELECT COUNT(*) FROM {$this->tabla} WHERE status>0");
        } else {
            $num_rows = $wpdb->get_var("SELECT COUNT(*) FROM {$this->tabla} WHERE tipo_opcion = '{$tipo}' AND status>0");
        }
        return $num_rows;
    }
    /*private function get_all_cotizaciones($page, $admin, $user)
    {

        /*if(isset($_GET['buscar']) && !empty($_GET['buscar'])){
			$termino = $_GET['buscar'];
			$termino = str_replace("AT-", "", $termino);

			if($this->form_render->get_datos_by_parm('areas', $termino)){
				$termino = $this->form_render->get_datos_by_parm('areas', $termino);

				$sql = "SELECT * FROM {$wpdb->prefix}hojas_servicio WHERE status > 0 AND area = {$termino} LIMIT {$limite} OFFSET {$offset} ";

			} elseif($this->form_render->get_datos_by_parm('colaboradores',$termino)) {
				$termino = $this->form_render->get_datos_by_parm('colaboradores',$termino);

				$sql = "SELECT * FROM {$wpdb->prefix}hojas_servicio WHERE status > 0 AND colaborador = {$termino} LIMIT {$limite} OFFSET {$offset} ";
			} else {
				$sql = "SELECT * FROM {$wpdb->prefix}hojas_servicio WHERE status > 0 AND (id LIKE '%{$termino}%' OR nombre LIKE '%{$termino}%' OR descripcion LIKE '%{$termino}%' OR recursos LIKE '%{$termino}%' OR automatizaciones LIKE '%{$termino}%' OR alm_datos LIKE '%{$termino}%' OR observaciones LIKE '%{$termino}%') LIMIT {$limite} OFFSET {$offset} ";
			}
		} else {*//*
			$sql = "SELECT * FROM {$wpdb->prefix}hojas_servicio WHERE status > 0";
		//}
		
    }*/
}
