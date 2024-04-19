<?php
if (!defined('ABSPATH')) die;

//include plugin_dir_path(__FILE__) . 'cotizador/data_temp.php';

class Data
{
    public $id_pais;
    private $datos, $url_json, $options, $options_gr, $list_pais_user, $distri;
    public function __construct()
    {
        //global $id_pais;
        //var_dump($id_pais);
        //exit;
        //$this->id_pais = strtoupper($id_pais);
        $this->insert_hs_table_into_db();

        $this->distri = [
            [
                "id" => "satrack",
                "nombre" => "Satrack"
            ],
            [
                "id" => "lys",
                "nombre" => "L&S, Monitoreo GPS"
            ],
            [
                "id" => "segtec",
                "nombre" => "Segtec GPS"
            ],
            [
                "id" => "rastreamos",
                "nombre" => "Rastreamos GPS"
            ],
            [
                "id" => "omm",
                "nombre" => "OMM Monitoreo Satelital"
            ],
            [
                "id" => "ns",
                "nombre" => "NS Satelital"
            ],
            [
                "id" => "logistica",
                "nombre" => "Logística y GPS"
            ],
            [
                "id" => "jfl",
                "nombre" => "JFL Soluciones"
            ],
            [
                "id" => "sya",
                "nombre" => "S&A Comunicaciones Ltda."
            ],
            [
                "id" => "indycar",
                "nombre" => "Inv. Indycar Ltda."
            ],
            [
                "id" => "idcom",
                "nombre" => "IDCOM Monitoreo Satelital"
            ],
            [
                "id" => "destino_seguro",
                "nombre" => "Destino Seguro"
            ],
            [
                "id" => "comseguridad",
                "nombre" => "Comseguridad"
            ],
            [
                "id" => "tyn",
                "nombre" => "T&N Rastreo y Gestión Vehícular"
            ],
            [
                "id" => "syco",
                "nombre" => "SYCO Ltda."
            ],
            [
                "id" => "cmo",
                "nombre" => "CMO Distribuciones"
            ],
            [
                "id" => "avl",
                "nombre" => "AVL Soluciones"
            ]
        ];
    }

    public function paises_to_string()
    {
        $texto = array();
        if (isset($this->options_gr->list_paises) && count($this->options_gr->list_paises) > 0) {
            foreach ($this->options_gr->list_paises as $list) {
                $texto[] = implode("=", $list);
            }
            $texto = implode("\n", $texto);
            return $texto;
        } else {
            return false;
        }
    }

    public function get_cotizacion_id($id)
    {
        global $wpdb;
        $datos = [];
        $sql = "SELECT * FROM {$wpdb->prefix}cotizaciones_satrack WHERE id = " . $id;

        $results = $wpdb->get_row($sql, OBJECT);
        $datos = json_decode($results->datos);

        if (!is_array($datos) || sizeof($datos) == 0) {
            $results->datos = stripslashes($results->datos);
            $datos = json_decode($results->datos);
        }

        $results->datos = $datos;
        return $results;
    }

    /**
     * Summary of get_pais
     * @return string
     */
    public function get_pais()
    {
        if (empty($this->id_pais)) {
            $this->load_pais();
        }
        return $this->id_pais;
    }

    public function get_rules()
    {
        return $this->datos->reglas;
    }

    public function get_ecep_reg()
    {
        return $this->datos->ecep_reg;
    }

    public function get_list_paises()
    {
        if (empty($this->options_gr)) {
            $this->load_options_gr();
        }
        if (isset($this->options_gr->list_paises) && !empty($this->options_gr->list_paises)) {
            return $this->options_gr->list_paises;
        } else {
            return false;
        }
    }

    public function get_list_paises_user()
    {
        if (empty($this->list_pais_user)  || !$this->list_pais_user) {
            $this->load_paises_user();
        }

        return $this->list_pais_user;
    }



    public function get_active_distri()
    {
        if (isset($this->options->distri) && !empty($this->options->distri)) {
            return $this->options->distri;
        } else {
            return false;
        }
    }

    public function get_list_distri()
    {
        return $this->distri;
    }

    public function get_data_distri()
    {
        if (empty($this->options_gr)) {
            $this->load_options_gr();
        }

        return $this->options_gr->distri;
    }



    /**
     * Retrieves the options for the current instance.
     *
     * @throws Some_Exception_Class description of exception
     * @return mixed The options for the current instance.
     */
    public function get_options()
    {
        if ((isset($this->options->inst_equipos) && empty($this->options->inst_equipos)) || $this->options == null) {
            $this->load_options_for_pais();
        }
        return $this->options;
    }

    public function get_options_gr()
    {
        if (empty($this->options_gr)) {
            $this->load_options_gr();
        }

        return $this->options_gr;
    }

    /**
     * Summary of insert_hs_table_into_db
     * Verifica la exitencia de la tabla en mysql y sino la crea para la adminstracion de las cotizaciones
     * @return bool
     */
    private function insert_hs_table_into_db()
    {
        global $wpdb;
        // establecer el juego de caracteres predeterminado y la intercalación para la tabla
        $charset_collate = $wpdb->get_charset_collate();
        // Compruebe que la tabla no exista antes de continuar
        $sql = "CREATE TABLE if not exists `" . $wpdb->prefix . "cotizaciones_satrack` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nombre_cliente` varchar(60) COLLATE utf8mb4_unicode_520_ci NOT NULL,
            `apellido_cliente` varchar(60) COLLATE utf8mb4_unicode_520_ci NOT NULL,
            `empresa` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
            `email` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
            `phone` varchar(30) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
            `ciudad` varchar(40) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
            `tipo_cliente` varchar(30) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
            `datos` text COLLATE utf8mb4_unicode_520_ci,
            `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `date_updated` datetime DEFAULT CURRENT_TIMESTAMP,
            `status` tinyint(3) DEFAULT '1',
            `user_wp` int(11) DEFAULT NULL,
            `id_pais` varchar(10) NOT NULL,
            PRIMARY KEY (`id`)
        ) $charset_collate;";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
        $is_error = empty($wpdb->last_error);
        return $is_error;
    }

    /**
     * Summary of guardar_options
     * @return never
     */
    public function guardar_options()
    {
        if (isset($_POST['cw_pais']) && !empty($_POST['cw_pais'])) {

            $reglas = array();

            if (isset($_POST['regla-name']) && !empty($_POST['regla-name'])) {
                foreach ($_POST['regla-name'] as $key => $regla) {
                    $id = eliminar_acentos($regla);
                    $reglas[$id] = [
                        "nombre" => $regla,
                        "regla" => $_POST['regla'][$key],
                        "texto" => $_POST['regla-text'][$key],
                        "meses" => $_POST['regla-meses'][$key]
                    ];
                }
            }

            ($_POST['imp-mes']) ? $imp_mes = 'true' : $imp_mes = 'false';

            $pais = array(
                'decimales' => $_POST['decima'],
                "imp" => $_POST['imp-name'],
                "imp_mes" => $imp_mes,
                "valor_imp" => (int) $_POST['valor-imp'],
                "dir_company" => $_POST['dir_company'],
                "phone_company" => $_POST['phone_company'],
                "reglas" => $reglas,
                "ecep_reg" => []
            );

            update_option('options_' . strtolower($_POST['cw_pais']) . '_cotiza_satrack', $pais);
        }

        $opciones = array();
        if (isset($_POST['distri']) && !empty($_POST['distri'])) {


            $opciones['distri'] = array();

            $list_distr = $this->get_list_distri();


            foreach ($_POST['distri'] as $dist) {
                $indice = array_search($dist, array_column($list_distr, 'id'));

                $opciones['distri'][$dist] = [
                    "nombre" => $list_distr[$indice]['nombre']
                ];
            }
        }

        if (isset($_POST['list_paises']) && !empty($_POST['list_paises'])) {

            $list_paises = explode("\n", $_POST['list_paises']);

            if (is_array($list_paises)) {
                foreach ($list_paises as $pais) {
                    list($value, $key) = explode('=', $pais);
                    $key = str_replace(['\n', "\r"], '', $key);

                    $opciones['list_paises'][$key] = ["name" => $value, "ind" => strtoupper($key)];
                }
            }
        }

        if (count($opciones) > 0) {
            update_option('options_cotiza_satrack', $opciones);
        }


        echo "<script>location.href ='" . admin_url('admin.php') . "?page=cotizador_web';</script>";
        die;
    }


    public function guardar_cotizacion()
    {

        $nonce = $_POST['nonce'];

        if (!check_ajax_referer('cotizador_web_satrack', 'nonce')) {
            echo "No tiene permisos para realizar esta acción";
            wp_die();
        }

        global $wpdb;

        $fecha_actual = new DateTime('', new DateTimeZone('America/Bogota'));

        $fecha_actual = $fecha_actual->format("Y-m-d H:i:s");

        $datos_pro = stripslashes($_POST['datos']);

        $wpdb->show_errors();
        $datos = array();
        $datos['nombre_cliente'] = sanitize_text_field($_POST['nombre_cliente']);
        $datos['id_pais'] = sanitize_text_field($_POST['id_pais']);
        $datos['apellido_cliente'] = sanitize_text_field($_POST['apellido_cliente']);
        $datos['empresa'] = sanitize_text_field($_POST['empresa']);
        $datos['email'] =  sanitize_email($_POST['email']);
        $datos['phone'] =  $_POST['phone'];
        $datos['ciudad'] =  sanitize_text_field($_POST['ciudad']);
        $datos['tipo_cliente'] = sanitize_text_field($_POST['tipo_cliente']);
        //$datos['datos'] =  $datos_pro;
        $datos['datos'] = $datos_pro;

        $datos['user_wp'] = get_current_user_id();
        $datos['date_updated'] = $fecha_actual;

        $str_inserts = ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d'];

        if ($wpdb->insert(
            "{$wpdb->prefix}cotizaciones_satrack",
            $datos,
            $str_inserts
        )) {
            $resp = array(
                'status' => 200,
                'id' => $wpdb->insert_id,
                'data' => $datos
            );
        } else {
            $resp = array(
                'status' => 401,
                'error' => 'Sucedio algun error con la config de la conexion'
            );
        }
        //$sql = "INSERT INTO  {$str_inserts} VALUES {$str_vals}";

        //$wpdb->query($sql);

        echo json_encode($resp);

        wp_die();
    }

    public function actualizar_cotizacion($id)
    {

        $nonce = $_POST['nonce'];

        if (!check_ajax_referer('cotizador_web_satrack', 'nonce')) {
            echo "No tiene permisos para realizar esta acción";
            wp_die();
        }

        global $wpdb;

        $fecha_actual = new DateTime('', new DateTimeZone('America/Bogota'));

        $fecha_actual = $fecha_actual->format("Y-m-d H:i:s");

        $datos = [];
        $where['id'] = $id;
        foreach ($_POST as $key => $valor) {
            if ($key !== 'id' && $key !== 'nonce' && $key !== 'action') {
                if ($key == 'datos') {
                    $datos[$key] = stripslashes($valor);
                } else {
                    $datos[$key] = $valor;
                }
            }
        }
        $datos['date_updated'] = $fecha_actual;

        $updated = $wpdb->update("{$wpdb->prefix}cotizaciones_satrack", $datos,  $where);

        if (!$updated) {
            $resp = array(
                'status' => 400,
                'id' => $id,
                'msj' => 'NO se actualizao correctamente'
            );
        } else {
            $resp = array(
                'status' => 200,
                'id' => $id,
                'msj' => 'SI se actualizó correctamente'
            );
        }

        echo json_encode($resp);
    }

    public function actualizar_estado_cotizacion($id, $estado)
    {

        $nonce = $_POST['nonce'];

        if (!check_ajax_referer('cotizador_web_satrack', 'nonce')) {
            echo "No tiene permisos para realizar esta acción";
            wp_die();
        }

        global $wpdb;

        $fecha_actual = new DateTime('', new DateTimeZone('America/Bogota'));

        $fecha_actual = $fecha_actual->format("Y-m-d H:i:s");

        $datos = ['estado' => $estado];
        $where['id'] = $id;

        $datos['date_updated'] = $fecha_actual;

        $updated = $wpdb->update("{$wpdb->prefix}cotizaciones_satrack", $datos,  $where);

        if (!$updated) {
            $resp = array(
                'status' => 400,
                'id' => $id,
                'msj' => 'NO se actualizao correctamente'
            );
        } else {
            $resp = array(
                'status' => 200,
                'id' => $id,
                'msj' => 'SI se actualizó correctamente'
            );
        }

        echo json_encode($resp);
    }


    public function load_paises_user()
    {
        global $ads_user;

        $this->list_pais_user = $ads_user->get_list_paises();

        if (!is_array($this->list_pais_user)) {
            $this->list_pais_user = [];
        }

        return $this->list_pais_user;
    }

    private function load_options_gr()
    {
        $this->options_gr = get_option('options_cotiza_satrack');

        if ($this->options_gr === false ||  $this->options_gr === "") {
            $this->options_gr = (object) array(
                'list_paises' => [
                    'COL' => [
                        'nombre' => "Colombia",
                        'ind' => "COL"
                    ]
                ],
                "distri" => [
                    "satrack" => ["nombre" => "Satrack"]
                ]
            );

            add_option('options_cotiza_satrack', $this->options_gr);
        }

        $this->options_gr = (object) $this->options_gr;
    }

    private function load_pais()
    {
        global $wp_query;
        $id = "";
        if (isset($wp_query->query_vars['pais_coti']) && !empty($wp_query->query_vars['pais_coti']) && !is_admin()) {
            $id = strtoupper($wp_query->query_vars['pais_coti']);
            $lista_paises = $this->get_list_paises_user();

            if (sizeof($lista_paises) > 0 && in_array($id, $lista_paises)) {
                $this->id_pais = $id;
            }
            return true;
        } elseif (is_admin() && isset($_GET['tab']) && !empty($_GET['tab'])) {
            $id = strtoupper($_GET['tab']);

            if (isset($this->options_gr->list_paises[$id]) && !empty($this->options_gr->list_paises[$id])) {
                $this->id_pais = $_GET['tab'];
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Summary of load_options_for_pais
     * @return void
     */
    private function load_options_for_pais()
    {
        if (empty($this->id_pais)) {
            $this->load_pais();
        }
        $this->options =  get_option('options_' . strtolower($this->id_pais) . '_cotiza_satrack');

        if ($this->options === false ||  $this->options === "") {
            $this->options = [
                'decimales' => 0,
                "imp" => 'XXX',
                "imp_mes" => 'false',
                "valor_imp" => 19,
                "dir_company" => "Carrera 35A No. 15B - 35<br>Edificio Prisma - Oficina 9808",
                "phone_company" => 'Línea Ventas: 604 604 5454 Opc. VENTAS',
                "reglas" => [],
                "ecep_reg" => []
            ];

            add_option('options_' . strtolower($this->id_pais) . '_cotiza_satrack', $this->options);
        }

        $this->options = (object) $this->options;
    }
}
