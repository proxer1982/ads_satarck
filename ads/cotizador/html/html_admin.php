<?php
if (!defined('ABSPATH')) die;
class Html_Admin
{
    private $user;
    public function __construct()
    {
        global $ads_user;
        $ads_user->load_user();

        $this->user = $ads_user->get_user();
        //si es backend
        add_action('admin_enqueue_scripts', array($this, 'satrack_scripts_admin'));
    }

    public function satrack_scripts_admin()
    {
        wp_register_style('cw_admin_css', URL_PLUGIN_CW . 'assets/css/style_admin_cw.css', false, '1.0.1.3');
        wp_enqueue_style('cw_admin_css');

        wp_register_script('cw_admin-js', URL_PLUGIN_CW . 'assets/js/cw_admin_js.js', array('jquery'), '1.2.0.11');
        wp_enqueue_script('cw_admin-js');
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-widget');
        wp_enqueue_script('jquery-ui-mouse');
        wp_enqueue_script('jquery-ui-accordion');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('jquery-ui-slider');
    }

    public function create_form_options()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        global $ads_data;

        $opciones = $ads_data->get_options_gr();

        $list_distri = $ads_data->get_list_distri();
        $opciones_pais = array();

        $default_tab = 'gen';
        $tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;

        if ($tab != 'gen') {
            $opciones_pais = (object) $ads_data->get_options();
        }


        $pagina_datos = "admin.php?page=cotizador_web&tab=datos";


        if ($tab === 'datos' && (!isset($_GET['action']) || $_GET['action'] === 'quitar_opcion' || $_GET['action'] === 'delete' || $_GET['action'] === 'rest')) {
            require_once('my-list-table.php');
            $table_para = new CW_My_Table();
        } elseif ($tab === 'datos' && isset($_GET['action']) && $_GET['action'] === 'editar_opcion') {
            $db_config = new DB_Config();
            $datos = $db_config->get_opcion_by_id($_GET['id_opcion']);

            $lista_planes = $db_config->get_list_all_planes();
        } elseif ($tab === 'datos' && isset($_GET['action']) && $_GET['action'] === 'new_opcion') {
            $db_config = new DB_Config();
            $lista_planes = $db_config->get_list_all_planes();
        }

?>
        <div class='wrap caja-datos'>
            <svg xmlns="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" style="display: none;">
                <symbol id="icon_items" viewBox="0 0 16 16">
                    <title>icon_items</title>
                    <path d="M1.5 0A1.5 1.5 0 0 0 0 1.5v2A1.5 1.5 0 0 0 1.5 5h13A1.5 1.5 0 0 0 16 3.5v-2A1.5 1.5 0 0 0 14.5 0h-13zm1 2h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1 0-1zm9.927.427A.25.25 0 0 1 12.604 2h.792a.25.25 0 0 1 .177.427l-.396.396a.25.25 0 0 1-.354 0l-.396-.396zM0 8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V8zm1 3v2a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2H1zm14-1V8a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v2h14zM2 8.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0 4a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5z"></path>
                </symbol>
                <symbol id="icon_gear" viewBox="0 0 16 16">
                    <title>icon_gear</title>
                    <path d="M7.293 1.5a1 1 0 0 1 1.414 0L11 3.793V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v3.293l2.354 2.353a.5.5 0 0 1-.708.708L8 2.207 1.354 8.854a.5.5 0 1 1-.708-.708L7.293 1.5Z" />
                    <path d="M11.07 9.047a1.5 1.5 0 0 0-1.742.26l-.02.021a1.5 1.5 0 0 0-.261 1.742 1.5 1.5 0 0 0 0 2.86 1.504 1.504 0 0 0-.12 1.07H3.5A1.5 1.5 0 0 1 2 13.5V9.293l6-6 4.724 4.724a1.5 1.5 0 0 0-1.654 1.03Z" />
                    <path d="m13.158 9.608-.043-.148c-.181-.613-1.049-.613-1.23 0l-.043.148a.64.64 0 0 1-.921.382l-.136-.074c-.561-.306-1.175.308-.87.869l.075.136a.64.64 0 0 1-.382.92l-.148.045c-.613.18-.613 1.048 0 1.229l.148.043a.64.64 0 0 1 .382.921l-.074.136c-.306.561.308 1.175.869.87l.136-.075a.64.64 0 0 1 .92.382l.045.149c.18.612 1.048.612 1.229 0l.043-.15a.64.64 0 0 1 .921-.38l.136.074c.561.305 1.175-.309.87-.87l-.075-.136a.64.64 0 0 1 .382-.92l.149-.044c.612-.181.612-1.049 0-1.23l-.15-.043a.64.64 0 0 1-.38-.921l.074-.136c.305-.561-.309-1.175-.87-.87l-.136.075a.64.64 0 0 1-.92-.382ZM12.5 14a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3Z" />
                </symbol>
                <symbol id="icon_flag" viewBox="0 0 16 16">
                    <title>icon_flag</title>
                    <path d="m10.495 6.92 1.278-.619a.483.483 0 0 0 .126-.782c-.252-.244-.682-.139-.932.107-.23.226-.513.373-.816.53l-.102.054c-.338.178-.264.626.1.736a.476.476 0 0 0 .346-.027ZM7.741 9.808V9.78a.413.413 0 1 1 .783.183l-.22.443a.602.602 0 0 1-.12.167l-.193.185a.36.36 0 1 1-.5-.516l.112-.108a.453.453 0 0 0 .138-.326ZM5.672 12.5l.482.233A.386.386 0 1 0 6.32 12h-.416a.702.702 0 0 1-.419-.139l-.277-.206a.302.302 0 1 0-.298.52l.761.325Z" />
                    <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0ZM1.612 10.867l.756-1.288a1 1 0 0 1 1.545-.225l1.074 1.005a.986.986 0 0 0 1.36-.011l.038-.037a.882.882 0 0 0 .26-.755c-.075-.548.37-1.033.92-1.099.728-.086 1.587-.324 1.728-.957.086-.386-.114-.83-.361-1.2-.207-.312 0-.8.374-.8.123 0 .24-.055.318-.15l.393-.474c.196-.237.491-.368.797-.403.554-.064 1.407-.277 1.583-.973.098-.391-.192-.634-.484-.88-.254-.212-.51-.426-.515-.741a6.998 6.998 0 0 1 3.425 7.692 1.015 1.015 0 0 0-.087-.063l-.316-.204a1 1 0 0 0-.977-.06l-.169.082a1 1 0 0 1-.741.051l-1.021-.329A1 1 0 0 0 11.205 9h-.165a1 1 0 0 0-.945.674l-.172.499a1 1 0 0 1-.404.514l-.802.518a1 1 0 0 0-.458.84v.455a1 1 0 0 0 1 1h.257a1 1 0 0 1 .542.16l.762.49a.998.998 0 0 0 .283.126 7.001 7.001 0 0 1-9.49-3.409Z"></path>
                </symbol>
            </svg>
            <div class="cabezal_Cotizador">
                <div class="icono"><svg xmlns="http://www.w3.org/2000/svg" id="Capa_1" data-name="Capa 1" viewBox="0 0 512 512">
                        <path d="M377.16,340.9H127.39A30.22,30.22,0,0,1,97.2,310.71V157.76a30.23,30.23,0,0,1,30.19-30.19H376.93a30.24,30.24,0,0,1,30.2,30.19v9.43a5,5,0,0,1-10,0v-9.43a20.22,20.22,0,0,0-20.2-20.19H127.39a20.22,20.22,0,0,0-20.19,20.19v153a20.22,20.22,0,0,0,20.19,20.19H377.16a20,20,0,0,0,20-20V208.78a5,5,0,1,1,10,0V310.94A30,30,0,0,1,377.16,340.9Z"></path>
                        <path d="M402.13,312.88H102.2a5,5,0,1,1,0-10H402.13a5,5,0,0,1,0,10Z"></path>
                        <path d="M322.43,384.43H182.63a5,5,0,1,1,0-10h139.8a5,5,0,0,1,0,10Z"></path>
                        <path d="M252.53,384.43a5,5,0,0,1-5-5V335.9a5,5,0,1,1,10,0v43.53A5,5,0,0,1,252.53,384.43Z"></path>
                        <path d="M160.76,182.46h-24.5a14.15,14.15,0,1,1,0-28.3h24.5a14.15,14.15,0,1,1,0,28.3Zm-24.5-18.3a4.15,4.15,0,1,0,0,8.3h24.5a4.15,4.15,0,1,0,0-8.3Z"></path>
                        <path d="M160.76,258h-24.5a14.16,14.16,0,0,1,0-28.31h24.5a14.16,14.16,0,1,1,0,28.31Zm-24.5-18.31a4.16,4.16,0,0,0,0,8.31h24.5a4.16,4.16,0,1,0,0-8.31Z"></path>
                        <path d="M160.76,295.81h-24.5a14.16,14.16,0,0,1,0-28.31h24.5a14.16,14.16,0,1,1,0,28.31Zm-24.5-18.31a4.16,4.16,0,0,0,0,8.31h24.5a4.16,4.16,0,1,0,0-8.31Z"></path>
                        <path d="M199,220.25h-24.5a14.16,14.16,0,0,1,0-28.31H199a14.16,14.16,0,1,1,0,28.31Zm-24.5-18.31a4.16,4.16,0,0,0,0,8.31H199a4.16,4.16,0,1,0,0-8.31Z"></path>
                        <path d="M212.18,173.31H169.92a5,5,0,0,1,0-10h42.26a5,5,0,0,1,0,10Z"></path>
                        <path d="M165.34,211.09H123.07a5,5,0,1,1,0-10h42.27a5,5,0,0,1,0,10Z"></path>
                        <path d="M212.18,248.87H169.92a5,5,0,1,1,0-10h42.26a5,5,0,0,1,0,10Z"></path>
                        <path d="M212.18,286.66H169.92a5,5,0,0,1,0-10h42.26a5,5,0,0,1,0,10Z"></path>
                        <path d="M259,252.68a18.87,18.87,0,1,1,18.87-18.86A18.88,18.88,0,0,1,259,252.68ZM259,225a8.87,8.87,0,1,0,8.87,8.87A8.88,8.88,0,0,0,259,225Z"></path>
                        <path d="M317.64,288.73a18.87,18.87,0,1,1,18.86-18.87A18.89,18.89,0,0,1,317.64,288.73Zm0-27.73a8.87,8.87,0,1,0,8.86,8.86A8.88,8.88,0,0,0,317.64,261Z"></path>
                        <path d="M376.23,189.71a18.87,18.87,0,1,1,18.86-18.87A18.9,18.9,0,0,1,376.23,189.71Zm0-27.73a8.87,8.87,0,1,0,8.86,8.86A8.87,8.87,0,0,0,376.23,162Z"></path>
                        <path d="M434.82,230.87A18.86,18.86,0,1,1,453.68,212,18.88,18.88,0,0,1,434.82,230.87Zm0-27.72a8.86,8.86,0,1,0,8.86,8.86A8.86,8.86,0,0,0,434.82,203.15Z"></path>
                        <path d="M305.74,267.73a5,5,0,0,1-2.62-.75L268.2,245.37a5,5,0,0,1,5.26-8.51l34.92,21.62a5,5,0,0,1-2.64,9.25Z"></path>
                        <path d="M324.69,262.93a4.92,4.92,0,0,1-2.54-.7,5,5,0,0,1-1.76-6.84l44.47-75.16a5,5,0,0,1,8.61,5.09L329,260.48A5,5,0,0,1,324.69,262.93Z"></path>
                        <path d="M423.47,209a5,5,0,0,1-2.87-.91L384.7,182.9a5,5,0,1,1,5.75-8.18L426.35,200a5,5,0,0,1-2.88,9.09Z"></path>
                    </svg></div>
                <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            </div>
            <div class="caja-nav">
                <nav class="nav-tab-cotizador">
                    <a href="?page=cotizador_web&tab=gen" class="nav-tab <?php if ($tab === 'gen') : ?>nav-tab-active<?php endif; ?>">
                        <svg class='' fill='currentColor' width='1.2em' height='1.2em'>
                            <use xlink:href='#icon_gear'></use>
                        </svg>
                        Generales
                    </a>
                    <a href="?page=cotizador_web&tab=datos" class="nav-tab <?php if ($tab === 'datos') : ?>nav-tab-active<?php endif; ?>">
                        <svg class='' fill='currentColor' width='1.2em' height='1.2em'>
                            <use xlink:href='#icon_items'></use>
                        </svg>
                        Datos
                    </a>
                    <?php if (isset($opciones->list_paises) && count($opciones->list_paises) > 0) {
                        foreach ($opciones->list_paises as $pais) : ?>
                            <a href="?page=cotizador_web&tab=<?= strtolower($pais['ind']) ?>" class="nav-tab <?php if ($tab === strtolower($pais['ind'])) : ?>nav-tab-active<?php endif; ?>">
                                <svg class='' fill='currentColor' width='1.2em' height='1.2em'>
                                    <use xlink:href='#icon_flag'></use>
                                </svg>
                                <?= $pais['name'] ?>
                            </a>
                    <?php endforeach;
                    } ?>

                </nav>
            </div>
            <div class="contenido_cotizador">
                <?php

                if (array_key_exists(strtoupper($tab), $opciones->list_paises)) :
                    $pais = $opciones->list_paises[strtoupper($tab)]; ?>

                    <div class="titulo_page">
                        <div class="texto">
                            <h2>Datos <?= $pais['name'] ?></h2>
                            <p>Aca encuentras los ajustes para cada país determinado.</p>
                        </div>
                        <div class="acciones">
                            <a href='<?= $pagina_datos ?>&action=new_opcion&type=equipo' class='page-title-action boton-accion'>Nuevo Equipo</a>
                            <a href='<?= $pagina_datos ?>&action=new_opcion&type=accesorio' class='page-title-action boton-accion'>Nuevo Accesorio</a>
                            <a href='<?= $pagina_datos ?>&action=new_opcion&type=plan' class='page-title-action boton-accion'>Nuevo Plan</a>
                            <a href='<?= $pagina_datos ?>&action=new_opcion&type=instalacion' class='page-title-action boton-accion'>Nuevo Plan de instalación</a>
                        </div>
                    </div>
                    <div class="contenido-page">
                        <form method="post">
                            <?php wp_nonce_field('admin.php?page=cotizador_web&action=options', 'options'); ?>
                            <input type="hidden" name="cw_pais" value="<?= $pais['ind'] ?>">
                            <table class='form-table form-opcion-gr'>
                                <tbody>

                                    <tr>
                                        <th class='row'><label for='imp-name'>Dirección compañía</label></th>
                                        <td><input name='dir_company' type='text' class='regular-text' required value="<?= $opciones_pais->dir_company ?>"></td>
                                    </tr>
                                    <tr>
                                        <th class='row'><label for='imp-name'>Teléfono compañía</label></th>
                                        <td><input name='phone_company' type='text' class='regular-text' required value="<?= $opciones_pais->phone_company ?>"></td>
                                    </tr>
                                    <tr>
                                        <th class='row'><label for='imp-name'>Nombre del impuesto</label></th>
                                        <td><input name='imp-name' type='text' class='regular-text' required value="<?= $opciones_pais->imp ?>"></td>
                                    </tr>
                                    <tr>
                                        <th class='row'><label for='decima'>Decimales</label></th>
                                        <td><label style='display:flex; align-items:center;'><input name='decima' class="form-range" id="decima" type='range' min="0" max='2' step="1" required value="<?= $opciones_pais->decimales ?>"> <b><span class="value-range" style='margin-left:15px;'><?= $opciones_pais->decimales ?></span></b></label></td>
                                    </tr>
                                    <tr>
                                        <th class='row'><label for='imp-mes'>¿Aplica impuesto a la mensualidad?</label></th>
                                        <td>
                                            <div class="input-group mb-3">
                                                <input name='imp-mes' id="imp-mes-si" type='radio' class='regular-text btn-check' required value="1" <?= ($opciones_pais->imp_mes == 'true') ? "checked" : ""; ?> autocomplete="off">
                                                <label class='btn btn-checker_si' for='imp-mes-si'>SI</label>
                                                <input name='imp-mes' id="imp-mes-no" type='radio' class='regular-text btn-check' required value="0" <?= ($opciones_pais->imp_mes == 'false') ? "checked" : ""; ?> autocomplete="off">
                                                <label class="btn btn-checker_no" for='imp-mes-no'>NO</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class='row'><label for='valor-imp'>Porcentaje del impuesto</label></th>
                                        <td>
                                            <div class="input-group mb-3">
                                                <input name='valor-imp' type='text' min="0" max="100" class='form-control input-number' required value="<?= $opciones_pais->valor_imp ?>">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </td>
                                    </tr>
                            </table>
                            <hr>
                            <h3>Reglas de periodicidad</h3>
                            <div class='col-sm-4 d-flex justify-content-end'><input type='button' name='agregar_regla' id='agregar_regla' class='btn btn-info button boton-accion' value='Agregar regla'></div>
                            <div id='tbl_reglas' data-items='<?= sizeof($opciones_pais->reglas)  ?>' class='sortable'>
                                <div class="row-items item-head">
                                    <div></div>
                                    <div><label for='regla-name'>Nombre</label></div>
                                    <div><label for='regla'>Regla</label></div>
                                    <div><label for='regla-text'>Label</label></div>
                                    <div><label for='regla-text'>Meses</label></div>
                                    <div><label for='regla-text'>Eliminar</label></div>
                                </div>
                                <?php $i = 1;
                                foreach ($opciones_pais->reglas as $key => $regla) : ?>
                                    <div class="row-items row_regla_<?= $i ?>">
                                        <div class="d-flex align-items-center"><span class="dashicons dashicons-ellipsis handle"></span></div>
                                        <div>
                                            <input name='regla-name[]' type='text' class='regular-text' required value="<?= $regla["nombre"] ?>">
                                        </div>
                                        <div>
                                            <input name='regla[]' type='text' class='regular-text' required value="<?= $regla["regla"] ?>">
                                        </div>
                                        <div>
                                            <input name='regla-text[]' type='text' class='regular-text' required value="<?= $regla["texto"] ?>" />
                                        </div>
                                        <div>
                                            <input name='regla-meses[]' type='text' class='input-number' required value='<?= (isset($regla["meses"])) ? $regla["meses"] : null; ?>' />
                                        </div>
                                        <div class='del_campo text-center'>
                                            <a class='text-danger button btn-default btn-eliminar-regla mx-auto' style='display:inline-flex; align-items:center;' data-item='.row_regla_<?= $i ?>'><span class='dashicons dashicons-no'></span></a>
                                        </div>
                                    </div>
                                <?php $i++;
                                endforeach; ?>
                            </div>
                            <input type='submit' value='Guardar' class='button button-primary'>
                        </form>
                    </div>
                <?php elseif ($tab === 'datos' && (!isset($_GET['action']) || $_GET['action'] == 'quitar_opcion' || $_GET['action'] === 'delete' || $_GET['action'] === 'rest')) : ?>

                    <div class="titulo_page">
                        <div class="texto">
                            <h2>Parametros Cotizador</h2>
                            <p>Aca encuentras los ajustes generales de distribuidores y los paises activos en el cotizador</p>
                        </div>
                        <div class="acciones">
                            <a href='<?= $pagina_datos ?>&action=new_opcion&type=equipo' class='page-title-action boton-accion'>Nuevo Equipo</a>
                            <a href='<?= $pagina_datos ?>&action=new_opcion&type=accesorio' class='page-title-action boton-accion'>Nuevo Accesorio</a>
                            <a href='<?= $pagina_datos ?>&action=new_opcion&type=plan' class='page-title-action boton-accion'>Nuevo Plan</a>
                            <a href='<?= $pagina_datos ?>&action=new_opcion&type=instalacion' class='page-title-action boton-accion'>Nuevo Plan de instalación</a>
                        </div>
                    </div>
                    <div class="contenido-page">

                        <form method="post">
                            <?= $table_para->views(); ?>
                            <?php (isset($_GET['estado']) && $_GET['estado'] === "delete") ? $table_para->prepare_items(true) : $table_para->prepare_items(); ?>
                            <?php $table_para->search_box('buscar', 'search_id'); ?>

                            <?php $table_para->display(); ?>
                        </form>
                    </div>
                <?php elseif ($tab === 'datos' && isset($_GET['action']) && $_GET['action'] === 'editar_opcion') : ?>
                    <?php if (!$datos) {
                        echo "<script>window.location.href = '" . get_admin_url(null, "admin.php?page=cotizador_web") . "';</script>";
                    } ?>

                    <div class="titulo_page">
                        <div class="texto">
                            <h2>Editar <?= $datos->tipo_opcion ?></h2>
                            <p>Ajustes para cada uno de los items dentro del cotizador</p>
                        </div>
                        <div class="acciones">
                            <a href='<?= $pagina_datos ?>&tipo=<?= $datos->tipo_opcion ?>' class='page-title-action boton-alert'>Regresar</a>
                            <?php if ($datos->tipo_opcion === 'equipo') : ?><a href='<?= $pagina_datos ?>&action=new_opcion&type=equipo' class='page-title-action boton-accion'>Nuevo Equipo</a><?php endif; ?>
                            <?php if ($datos->tipo_opcion === 'accesorio') : ?><a href='<?= $pagina_datos ?>&action=new_opcion&type=accesorio' class='page-title-action boton-accion'>Nuevo Accesorio</a><?php endif; ?>
                            <?php if ($datos->tipo_opcion === 'plan') : ?><a href='<?= $pagina_datos ?>&action=new_opcion&type=plan' class='page-title-action boton-accion'>Nuevo Plan</a><?php endif; ?>
                            <?php if ($datos->tipo_opcion === 'instalacion') : ?><a href='<?= $pagina_datos ?>&action=new_opcion&type=instalacion' class='page-title-action boton-accion'>Nuevo Plan de instalación</a><?php endif; ?>
                        </div>
                    </div>
                    <div class="contenido-page">
                        <form method="post">
                            <?php wp_nonce_field('admin.php?page=cotizador_web&action=options', 'actualizar_opcion'); ?>
                            <input type="hidden" name="tipo_opcion" value="<?= $datos->tipo_opcion ?>">
                            <table class='form-table form-opcion-gr'>
                                <tbody>
                                    <tr>
                                        <th class="row">
                                            Nombre
                                        </th>
                                        <td>
                                            <input type="text" name="nombre_opcion" class="form-control regular-text" value="<?= $datos->nombre_opcion ?>" required>
                                        </td>
                                    </tr>
                                    <?php if ($datos->tipo_opcion === 'plan') : ?>
                                        <tr>
                                            <th class="row">
                                                Paginas plan pdf
                                            </th>
                                            <td>
                                                <input type="text" class="form-control input-number" name="pag" value="<?= (isset($datos->pag)) ? $datos->pag : $datos->datos_opcion['pag']; ?>">
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            <br>
                            <table class='form_datos' role='presentation'>
                                <thead>
                                    <tr>
                                        <th>Pais</th>
                                        <th>Valor</th>
                                        <?php if ($datos->tipo_opcion === 'equipo' || $datos->tipo_opcion === 'accesorio') : ?><th>Valor Instalación</th><?php endif; ?>
                                        <?php if ($datos->tipo_opcion === 'plan') : ?><th>Valor Comodato</th><?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($opciones->list_paises as $pais) : ?>
                                        <tr id="row_<?= $pais['ind'] ?>">
                                            <th class="text-start">
                                                <div class="form-check">
                                                    <input type="checkbox" name="paises[]" class="select_pais form-check-input" id="pais_<?= $pais['ind'] ?>" value="<?= $pais['ind'] ?>" <?= (in_array($pais['ind'], $datos->paises) ? "checked" : "") ?>> <label class="form-check-label" for="pais_<?= $pais['ind'] ?>"><?= $pais['name'] ?> </label>
                                                </div>
                                            </th>
                                            <td>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text">$</span>
                                                    <input type="text" step="any" class="form-control input-number" data-valor="" name="datos_<?= $pais['ind'] ?>[valor]" value="<?= (isset($datos->datos_opcion[$pais['ind']]->valor)) ? $datos->datos_opcion[$pais['ind']]->valor  : ""; ?>" <?= (!in_array($pais['ind'], $datos->paises) ? "disabled" : "") ?>>
                                                </div>
                                            </td>
                                            <?php if ($datos->tipo_opcion === 'equipo' || $datos->tipo_opcion === 'accesorio') { ?>
                                                <td class="text-center">
                                                    <?php if ($datos->tipo_opcion === 'equipo') : ?>
                                                        <div class="form-check form-switch">
                                                            <input type="checkbox" class="form-check-input" data-valor="" name="datos_<?= $pais['ind'] ?>[instalacion]" id="instalacion_<?= $pais['ind'] ?>" value="true" <?= (isset($datos->datos_opcion[$pais['ind']]->instalacion) && $datos->datos_opcion[$pais['ind']]->instalacion === 1) ? "checked" : ""; ?> <?= (!in_array($pais['ind'], $datos->paises) ? "disabled" : "") ?>><label class="form-check-label" for="instalacion_<?= $pais['ind'] ?>">Aplica</label>
                                                        </div>
                                                    <?php else : ?>
                                                        <div class="input-group mb-3">
                                                            <span class="input-group-text">$</span>
                                                            <input type="text" step="any" class="form-control input-number" data-valor="" name="datos_<?= $pais['ind'] ?>[valor_inst]" value="<?= (isset($datos->datos_opcion[$pais['ind']]->valor_inst)) ? $datos->datos_opcion[$pais['ind']]->valor_inst : ""; ?>" <?= (!in_array($pais['ind'], $datos->paises) ? "disabled" : "") ?>>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                            <?php } ?>
                                            <?php if ($datos->tipo_opcion === 'plan') { ?>
                                                <td>
                                                    <div class="input-group mb-3">
                                                        <span class="input-group-text">$</span>
                                                        <input type="text" class="form-control input-number" step="any" data-valor="" name="datos_<?= $pais['ind'] ?>[valor_comodato]" value="<?= (isset($datos->datos_opcion[$pais['ind']]->valor_comodato)) ? $datos->datos_opcion[$pais['ind']]->valor_comodato : ""; ?>" <?= (!in_array($pais['ind'], $datos->paises) ? "disabled" : "") ?>>
                                                    </div>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php if (isset($lista_planes) && $datos->tipo_opcion === 'equipo') : ?>
                                <table class='form-table form-opcion-gr'>
                                    <tbody>

                                        <tr>
                                            <th class="row">
                                                Planes activos
                                            </th>
                                            <td>
                                                <?php foreach ($lista_planes as $id_plan => $plan) : ?>
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" name="planes[]" id="plan_<?= $id_plan; ?>" value="<?= $id_plan ?>" <?= (in_array($id_plan, $datos->datos_opcion['planes'])) ? "checked" : ""; ?>>
                                                        <label class="form-check-label" for="plan_<?= $id_plan; ?>"><?= $plan ?></label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            <?php endif; ?>
                            <br><br>
                            <input type='submit' value='Guardar' class='button button-primary'>
                        </form>
                    </div>
                <?php elseif ($tab === 'datos' && isset($_GET['action']) && $_GET['action'] === 'new_opcion') : ?>
                    <?php $tipo_op = (isset($_GET['type']) && !empty($_GET['type'])) ? $_GET['type'] : 'equipo'; ?>
                    <div class="titulo_page">
                        <div class="texto">
                            <h2>Nuevo <?= $tipo_op ?></h2>
                            <p>Parametros para la creación de un nuevo <?= $tipo_op ?> para el cotizador.</p>
                        </div>
                        <div class="acciones">
                            <a href='<?= $pagina_datos ?>&tipo=<?= $tipo_op ?>' class='page-title-action boton-alert'>Regresar</a>
                            <?php if ($tipo_op !== 'equipo') : ?><a href='<?= $pagina_datos ?>&action=new_opcion&type=equipo' class='page-title-action boton-accion'>Nuevo Equipo</a><?php endif; ?>
                            <?php if ($tipo_op !== 'accesorio') : ?><a href='<?= $pagina_datos ?>&action=new_opcion&type=accesorio' class='page-title-action boton-accion'>Nuevo Accesorio</a><?php endif; ?>
                            <?php if ($tipo_op !== 'plan') : ?><a href='<?= $pagina_datos ?>&action=new_opcion&type=plan' class='page-title-action boton-accion'>Nuevo Plan</a><?php endif; ?>
                            <?php if ($tipo_op !== 'instalacion') : ?><a href='<?= $pagina_datos ?>&action=new_opcion&type=instalacion' class='page-title-action boton-accion'>Nuevo Plan de instalación</a><?php endif; ?>
                        </div>
                    </div>
                    <div class="contenido-page">
                        <form method="post">
                            <?php wp_nonce_field('admin.php?page=cotizador_web&action=options', 'new_opcion'); ?>
                            <input type="hidden" name="tipo_opcion" value="<?= $tipo_op ?>">
                            <table class='form-table form-opcion-gr'>
                                <tbody>
                                    <tr>
                                        <th class="row">
                                            Nombre
                                        </th>
                                        <td>
                                            <input type="text" name="nombre_opcion" class="form-control regular-text" value="" required>
                                        </td>
                                    </tr>
                                    <?php if ($tipo_op === 'plan') : ?>
                                        <tr>
                                            <th class="row">
                                                Paginas plan pdf
                                            </th>
                                            <td>
                                                <input type="text" class="form-control input-number" name="pag" value="">
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            <br>
                            <table class='form_datos' role='presentation'>
                                <thead>
                                    <tr>
                                        <th>Pais</th>
                                        <th>Valor</th>
                                        <?php if ($tipo_op === 'equipo' || $tipo_op === 'accesorio') : ?><th>Valor Instalación</th><?php endif; ?>
                                        <?php if ($tipo_op === 'plan') : ?><th>Valor Comodato</th><?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($opciones->list_paises as $pais) : ?>
                                        <tr id="row_<?= $pais['ind'] ?>">
                                            <th class="text-start">
                                                <div class="form-check">
                                                    <input type="checkbox" name="paises[]" class="select_pais form-check-input" id="pais_<?= $pais['ind'] ?>" value="<?= $pais['ind'] ?>"> <label class="form-check-label" for="pais_<?= $pais['ind'] ?>"><?= $pais['name'] ?> </label>
                                                </div>
                                            </th>
                                            <td>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text">$</span>
                                                    <input type="text" step="any" class="form-control input-number" data-valor="" name="datos_<?= $pais['ind'] ?>[valor]" value="" disabled>
                                                </div>
                                            </td>
                                            <?php if ($tipo_op === 'equipo' || $tipo_op === 'accesorio') { ?>
                                                <td class="text-center">
                                                    <?php if ($tipo_op === 'equipo') : ?>
                                                        <div class="form-check form-switch">
                                                            <input type="checkbox" class="form-check-input" data-valor="" name="datos_<?= $pais['ind'] ?>[instalacion]" id="instalacion_<?= $pais['ind'] ?>" value="true" disabled><label class="form-check-label" for="instalacion_<?= $pais['ind'] ?>">Aplica</label>
                                                        </div>
                                                    <?php else : ?>
                                                        <div class="input-group mb-3">
                                                            <span class="input-group-text">$</span>
                                                            <input type="text" step="any" class="form-control input-number" data-valor="" name="datos_<?= $pais['ind'] ?>[valor_inst]" value="" disabled>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                            <?php } ?>
                                            <?php if ($tipo_op === 'plan') { ?>
                                                <td>
                                                    <div class="input-group mb-3">
                                                        <span class="input-group-text">$</span>
                                                        <input type="text" class="form-control input-number" step="any" data-valor="" name="datos_<?= $pais['ind'] ?>[valor_comodato]" value="" disabled>
                                                    </div>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php if (isset($lista_planes) && $tipo_op === 'equipo') : ?>
                                <table class='form-table form-opcion-gr'>
                                    <tbody>

                                        <tr>
                                            <th class="row">
                                                Planes activos
                                            </th>
                                            <td>
                                                <?php foreach ($lista_planes as $id_plan => $plan) : ?>
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" name="planes[]" id="plan_<?= $id_plan; ?>" value="<?= $id_plan ?>">
                                                        <label class="form-check-label" for="plan_<?= $id_plan; ?>"><?= $plan ?></label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            <?php endif; ?>
                            <br><br>
                            <input type='submit' value='Guardar' class='button button-primary'>
                        </form>
                    </div>
                <?php /* Si no hay tab o tab es gen */ else : ?>

                    <?php $list_paises = $ads_data->paises_to_string(); ?>
                    <div class="titulo_page">
                        <div class="texto">
                            <h2>Ajustes generales</h2>
                            <p>Ajustes generales de los distribuidores y paises activos para el cotizador.</p>
                        </div>
                        <div class="acciones">
                            <a href='<?= $pagina_datos ?>&action=new_opcion&type=equipo' class='page-title-action boton-accion'>Nuevo Equipo</a>
                            <a href='<?= $pagina_datos ?>&action=new_opcion&type=accesorio' class='page-title-action boton-accion'>Nuevo Accesorio</a>
                            <a href='<?= $pagina_datos ?>&action=new_opcion&type=plan' class='page-title-action boton-accion'>Nuevo Plan</a>
                            <a href='<?= $pagina_datos ?>&action=new_opcion&type=instalacion' class='page-title-action boton-accion'>Nuevo Plan de instalación</a>
                        </div>
                    </div>
                    <div class="contenido-page">
                        <form method="post">
                            <?php wp_nonce_field('admin.php?page=cotizador_web&action=options', 'options'); ?>
                            <h3>Paises</h3>
                            <table class='form-table form-opcion-gr'>
                                <tbody>
                                    <tr>
                                        <td>
                                            <small>Separar pais por linea de texto, y nombre y indice con "="</small>
                                            <textarea rows="4" name='list_paises' class='regular-text form-control'><?= $list_paises ?></textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <h3>Distribuidores</h3>
                            <div class="grilla-distri">
                                <?php foreach ($list_distri as $id => $dist) : ?>
                                    <li>
                                        <div class="form-check form-switch">
                                            <input name='distri[]' class="form-check-input" id="distri_<?= $id; ?>" type="checkbox" value="<?= $dist['id'] ?>" <?= (isset($opciones->distri) && array_key_exists($dist['id'], $opciones->distri)) ? "checked" : ""; ?>>
                                            <label class="form-check-label" for="distri_<?= $id; ?>"><?= $dist['nombre'] ?></label>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </div>

                            <input type='submit' value='Guardar' class='button button-primary'>
                        </form>
                    </div>
                <?php endif; ?>
            </div> <!-- End .contenido_cotizador -->
        </div> <!-- End .wrap caja-datos -->
<?php
    }
}
