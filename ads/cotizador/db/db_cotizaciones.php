<?php
if (!defined('ABSPATH')) die;
class DB_Cotizaciones
{
    public function __construct()
    {
    }
    public function getAllCotizacionesActivas($user,  $pais, $estado = 1)
    {
        global $wpdb;
        $pais = strtolower($pais);

        if ($user->is_admin) {
            $sql = "SELECT * FROM {$wpdb->prefix}cotizaciones_satrack WHERE status = {$estado}  AND id_pais='{$pais}'";
        } else {
            $sql = "SELECT * FROM {$wpdb->prefix}cotizaciones_satrack WHERE status = {$estado} AND user_wp = {$user->ID} AND id_pais='{$pais}'";
        }

        $results = $wpdb->get_results($sql, OBJECT);

        if ($wpdb->last_error !== '') :
            $wpdb->print_error();
        endif;

        return $results;
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
