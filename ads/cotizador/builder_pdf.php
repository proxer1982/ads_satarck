<?php
if (!defined('ABSPATH')) die;

require_once plugin_dir_path(__FILE__) . '../lib/vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

define('CW_PATH_BASE', get_home_path() . "wp-content/uploads/cw_satrack/");




class BuilderPDF
{
    private $html2pdf, $datos, $list_dristri, $opt_pais, $dir_company, $phone_company, $name_file;
    public function __construct($datos)
    {
        global $ads_data;

        $this->html2pdf = new Html2Pdf('L', 'letter', 'es', true, 'UTF-8', 0);
        $this->datos = $datos;
        $this->name_file = $this->getNameFile();
        $this->list_dristri = $ads_data->get_data_distri();

        $ads_data->id_pais = $datos->id_pais;
        $this->opt_pais = $ads_data->get_options();

        $this->dir_company = (isset($this->datos->vendedor->address) && !empty($this->datos->vendedor->address)) ? $this->datos->vendedor->address : $this->opt_pais->dir_company;

        $this->phone_company = (isset($this->datos->vendedor->phone) && !empty($this->datos->vendedor->phone)) ? $this->datos->vendedor->phone : $this->opt_pais->phone_company;
    }

    public function generar_pdf()
    {
        $html = $this->pdf_cotizacion_front();


        $this->html2pdf->pdf->SetDisplayMode('fullpage');
        $this->html2pdf->writeHTML($html);
        $this->html2pdf->output($this->name_file . '.pdf');
    }

    public function pdf_cotizacion_front()
    {
        //$this->datos = array();
        $meses = array("enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre");
        $temp_pag = [
            3 => 2,
            2 => 2,
            4 => 3,
            5 => 3,
            6 => 3,
            1 => 3,
            9 => 3,
            7 => 2,
            8 => 2,
            51 => 2
        ];

        $new_url = [
            'pl_corto' => 3,
            'pl_base' => 2,
            'pl_estandar' => 4,
            'pl_plus' => 5,
            'pl_pro' => 6,
            'pl_avanzado' => 1,
            'pl_video_online_avanzado' => 9,
            'pl_unidad_portatil' => 7,
            'pl_video_online' => 8,
        ];
        $fecha = '';

        //$usuario = get_userdata( 1 );
        //$user_stockist = esc_attr( get_the_author_meta( 'user_stockist', $user->ID ) );
        //$user_phone = esc_attr( get_the_author_meta( 'user_phone', $user->ID ) );
        function sumar($v)
        {
            return $v->desc + $v->desc_inst;
        }

        $id = $_GET['id_cot'];
        //$this->datos = $this->get_cotizacion_id($id);+

        $utc = strtotime($this->datos->date_updated);
        $fecha = date('j', $utc) . ' de ' . $meses[date('n', $utc) - 1] . ' de ' . date('Y', $utc);

        //$id_plan = $this->datos->datos[0]->plan->id;

        $urlbaseplan = CW_PATH_BASE . "planes/";
        //$num_page_plan = $this->pag_plan[];
        $temp = [];

        foreach ($this->datos->datos as $porpu) {
            $id_plan = (is_numeric($porpu->plan->id)) ? (int) $porpu->plan->id : $new_url[$porpu->plan->id];
            $num_pag = (isset($porpu->plan->num_pag)) ? $porpu->plan->num_pag : $temp_pag[$id_plan];


            $temp[] = ['id' => $id_plan, "pag" => $num_pag];
        }

        $temp[] = ['id' => 'pl_corto', "pag" => 0];
        $planes = unique_multidim_array($temp, "id");
        //print_r($temp);
        //exit;
        //$url_img_plan = $urlbaseplan . "pl_estandar/";

        $distri = $this->datos->datos[0]->vendedor->dist;
        $url_footer = CW_PATH_BASE . 'footer/footer_' . $distri . '.png';
        $url_firma = CW_PATH_BASE . 'firmas/firma_' . $distri . '.jpg';
        $url_portada = CW_PATH_BASE . 'portadas/logo_' . $distri . '.jpg';


        /*if(array_key_exists($id_plan, $this->pag_plan)){     <img class='imagen' src='<?= $url_footer ; ?>'>
				$num_page_plan = $this->pag_plan[$id_plan];
				$url_img_plan = $urlbaseplan . $id_plan . "/";
			}
			*//*backimg="<?= $urlbaseplan; ?>page_01.jpg"*/

        ob_start();
?>
        <style>
            * {
                box-sizing: border-box;
            }

            .header-general {
                background-color: #dee5f5;
                padding: 4mm 0;
                margin-bottom: 10mm;
            }

            .header-general h1 {
                font-size: 30px;
                text-align: center;
                font-weight: bold;
                color: #1D3080;
                text-transform: uppercase;
            }

            .header-general h1 .txt-amarillo {
                color: #F1C541;
            }

            .tabla0 {
                width: 70mm;
                margin: 185mm 0mm 0mm 130mm;
                padding: 1mm;
                text-align: right;
                vertical-align: middle;
                font-size: 17px;
                color: #FFFFFF;
                font-weight: 200;
            }

            .tabla1 {
                width: 100%;
                margin: 0mm 0mm 0mm;
                padding: 1mm;
                text-align: left;
                vertical-align: middle;
                font-size: 17px;
                color: #FFFFFF;
                font-weight: 200;
            }

            .tabla1 .fecha {
                padding: 2mm 1mm 1mm;
                font-size: 12px;
            }

            .titulo_prop {
                margin: 0mm;
            }

            .titulo_prop2 {
                margin: 6mm 0 0;
            }

            .titulo_prop h1 {
                font-size: 16px;
                font-weight: bold;
                color: #0d3582;
                line-height: 14px;
                margin: 2mm 0 3mm;
            }

            .titulo_prop2 h1 {
                font-size: 16px;
                font-weight: bold;
                color: #0d3582;
                line-height: 18px;
                margin: 2mm 0 5mm;
            }

            .titulo_disp {
                font-size: 14px;
                font-weight: bold;
                color: #989898;
                line-height: 14px;
                margin: 2mm 0 2mm;
            }

            .titulo_prop p {
                font-size: 12px;
                line-height: 9px;
                margin: 0 0 2mm;
            }

            .titulo_prop2 p {
                font-size: 12px;
                line-height: 9px;
                margin: 0 0 2mm;
                color: #646464;
            }

            .tabla2 {
                margin: 0;
                width: 100%;
                font-size: 11px;
                border-collapse: collapse;
                vertical-align: middle;
            }

            .tabla2 th {
                background-color: #dbe0f0;
                font-weight: 600;
                font-size: 11px;
                padding: 1mm 4mm;
                vertical-align: middle;
            }

            .tabla2 .fila-items td {
                font-weight: 200;
                padding: 2mm 5mm;
                font-size: 11px;
                background-color: #FFFFFF;
            }

            .tabla2 .fila-valores1 td {
                font-weight: 600;
                padding: 3mm 5mm 1mm;
                font-size: 11px;
                color: #646464;
            }

            .tabla2 .fila-valores2 td {
                font-weight: 600;
                padding: 1mm 5mm;
                font-size: 11px;
                color: #646464;
            }

            .condiciones {
                width: 160mm;
                color: #868686;
                margin: 10mm 0 0;
            }

            .condiciones li {
                padding: 1.2mm 0;
                display: block;
                font-size: 12px;
                line-height: 15px;
            }

            .condiciones li b {
                color: #0d3582;
            }

            .tabla3 {
                width: 100%;
                margin: 20mm 0mm 0mm;
                padding: 1mm;
                text-align: left;
            }

            .tabla3 td {
                color: #0d3582;
                padding: 1mm 4mm;
                font-weight: 200;
                font-size: 14px;
            }

            .tabla3 .nombre_asesor {
                font-size: 20px;
                color: #0d3582;

            }

            .tabla3 .nombre_asesor i {
                font-size: 17px;

            }

            .imagen_firma_02 {
                width: 100%;
            }

            .imagen {
                width: 100%;
            }

            .imagen_footer {
                width: 200mm;
                margin: 0 0 0 16mm;
            }

            .text-end {
                text-align: right;
            }

            .text-center {
                text-align: center;
            }
        </style>
        <page backimg="<?= $urlbaseplan . $this->datos->id_pais . '/'; ?>page_01.jpg" backimgw="100%" backleft="9mm" backright="10mm" backtop="10mm" backbottom="0mm" orientation="portrait" format="LETTER">
            <table class="tabla0">

                <tr>
                    <td><img class="imagen" src="<?= $url_portada ?>"></td>
                </tr>
            </table>
            <table class="tabla1">

                <tr>
                    <td>Señor(a)</td>
                </tr>
                <tr>
                    <td><b><?= $this->datos->nombre_cliente . ' ' . $this->datos->apellido_cliente; ?></b></td>
                </tr>
                <tr>
                    <td><?= $this->datos->empresa . ' - ' . $this->datos->ciudad; ?></td>
                </tr>
                <tr>
                    <td> </td>
                </tr>
                <tr>
                    <td class="fecha"><?= $fecha; ?></td>
                </tr>
            </table>
        </page>
        <page backimg="<?= $urlbaseplan . $this->datos->id_pais . '/'; ?>page_02.jpg" backimgw="100%" backleft="10mm" backright="10mm" backtop="10mm" backbottom="10mm" backimgy="0mm" orientation="portrait" format="LETTER">
        </page>
        <page backimg="<?= $urlbaseplan . $this->datos->id_pais . '/'; ?>page_03.jpg" backimgw="100%" backleft="10mm" backright="10mm" backtop="10mm" backbottom="10mm" backimgy="0mm" orientation="portrait" format="LETTER">
        </page>
        <?php foreach ($planes as $plan) {
            for ($i = 1; $i <= $plan['pag']; $i++) { ?>
                <page backimg="<?= $urlbaseplan . $this->datos->id_pais . '/' . $plan['id']; ?>/page_0<?= $i ?>.jpg" backimgw="100%" backleft="10mm" backright="10mm" backtop="10mm" backbottom="10mm" backimgy="0mm" orientation="portrait" format="LETTER">
                </page>
            <?php }
        }

        foreach ($this->datos->datos as $key => $propuesta) {
            ?>
            <page backimgw="100%" backleft="10mm" backright="10mm" backtop="34mm" backbottom="10mm" backimgy="0mm" orientation="portrait" format="LETTER">
                <page_header>
                    <div class='header-general'>
                        <h1>PROPUESTA <?= $key + 1 ?> - <span class='txt-amarillo'>MODALIDAD <?= $propuesta->modalidad ?></span></h1>
                    </div>
                </page_header>
                <div class="titulo_prop">
                    <?php if (empty($propuesta->plan->permanencia) || $propuesta->plan->permanencia == "Ninguna") { //si no tienen permanencia 
                    ?>
                        <h1 class="text-center"><b>Pago inicial</b></h1>
                </div>
                <h2 class='titulo_disp'>Unidad GPS</h2>
                <?php (!empty($propuesta->equipo->desc) || !empty($propuesta->equipo->desc_inst)) ? $ancho = 310 : $ancho = 410; ?>
                <table class="tabla2">
                    <tr>
                        <th><b>Cantidad</b></th>
                        <th width="<?= $ancho ?>"><b>Descripción</b></th>
                        <th><b>Valor unidad</b></th>
                        <?php if (!empty($propuesta->equipo->desc) || !empty($propuesta->equipo->desc_inst)) : ?>
                            <th><b>Descuento</b></th>
                        <?php endif; ?>
                        <th><b>Valor total</b></th>
                    </tr>
                    <tr class="fila-items">
                        <td class="text-center"><?= $propuesta->equipo->cant_uni; ?></td>
                        <td><?= $propuesta->equipo->nombre ?></td>
                        <td class="text-end">$ <?= number_format(($propuesta->equipo->valor_equipo / $propuesta->equipo->cant_uni) + $propuesta->equipo->desc, $this->opt_pais->decimales); ?></td>
                        <?php if (!empty($propuesta->equipo->desc) || !empty($propuesta->equipo->desc_inst)) : ?>
                            <td class="text-end"><?php if (!empty($propuesta->equipo->desc)) {
                                                        echo "$ " . number_format($propuesta->equipo->desc, $this->opt_pais->decimales);
                                                    } ?></td>
                        <?php endif; ?>
                        <td class="text-end">$ <?= number_format($propuesta->equipo->valor_equipo, $this->opt_pais->decimales); ?></td>
                    </tr>
                    <tr class="fila-items">
                        <td class="text-center"><?= $propuesta->equipo->cant_uni; ?></td>
                        <td>Instalación de unidad</td>
                        <td class="text-end">$ <?= number_format(($propuesta->equipo->valor_inst_equipo / $propuesta->equipo->cant_uni) + $propuesta->equipo->desc_inst, $this->opt_pais->decimales); ?></td>
                        <?php if (!empty($propuesta->equipo->desc) || !empty($propuesta->equipo->desc_inst)) : ?>
                            <td class="text-end"><?php if (!empty($propuesta->equipo->desc_inst)) {
                                                        echo "$ " . number_format($propuesta->equipo->desc_inst, $this->opt_pais->decimales);
                                                    } ?></td>
                        <?php endif; ?>
                        <td class="text-end">$ <?= number_format($propuesta->equipo->valor_inst_equipo, $this->opt_pais->decimales); ?></td>
                    </tr>
                    <tr class="fila-valores1">
                        <td></td>
                        <td></td>
                        <?php if (!empty($propuesta->equipo->desc) || !empty($propuesta->equipo->desc_inst)) : ?>
                            <td></td>
                        <?php endif; ?>
                        <td class="text-end"><b>Subtotal</b></td>
                        <td class="text-end">$ <?= number_format($propuesta->equipo->valor_equipo + $propuesta->equipo->valor_inst_equipo, $this->opt_pais->decimales); ?></td>
                    </tr>
                    <tr class="fila-valores2">
                        <td></td>
                        <td></td>
                        <?php if (!empty($propuesta->equipo->desc) || !empty($propuesta->equipo->desc_inst)) : ?>
                            <td></td>
                        <?php endif; ?>
                        <td class="text-end"><b><?= $this->opt_pais->imp; ?></b></td>
                        <td class="text-end">$ <?= number_format(($propuesta->equipo->valor_equipo + $propuesta->equipo->valor_inst_equipo) * $propuesta->tasa_imp, $this->opt_pais->decimales); ?></td>
                    </tr>
                    <tr class="fila-valores2">
                        <td></td>
                        <td></td>
                        <?php if (!empty($propuesta->equipo->desc) || !empty($propuesta->equipo->desc_inst)) : ?>
                            <td></td>
                        <?php endif; ?>
                        <td class="text-end"><b>Valor total</b></td>
                        <td class="text-end">$ <?= number_format((($propuesta->equipo->valor_equipo + $propuesta->equipo->valor_inst_equipo) * $propuesta->tasa_imp) + ($propuesta->equipo->valor_equipo + $propuesta->equipo->valor_inst_equipo), $this->opt_pais->decimales); ?></td>
                    </tr>
                </table>
                <?php if (sizeof($propuesta->accesorios) > 0) : ?>
                    <h2 class='titulo_disp'>Acccesorios</h2>
                    <?php
                            $val_des = 0;
                            $texto = '';



                            $val_des = array_sum(array_map('sumar', $propuesta->accesorios));
                            $subtotal = 0;
                            foreach ($propuesta->accesorios as $ac) {
                                $subtotal += $ac->valor + $ac->valor_inst;
                                $texto .= "<tr class='fila-items'>
			<td class='text-center'>{$ac->cantidad}</td>
			<td>{$ac->accesorio}</td>
			<td class='text-end'>$ " . number_format(($ac->valor / $ac->cantidad) + $ac->desc, $this->opt_pais->decimales) . "</td>";
                                if ($val_des > 0) {
                                    $texto .= "<td class='text-end'>";
                                    (!empty($ac->desc)) ? $texto .= '$ ' . number_format($ac->desc, $this->opt_pais->decimales) : $texto .= "";
                                    $texto .= "</td>";
                                }
                                $texto .= "<td class='text-end'>$ " . number_format($ac->valor, $this->opt_pais->decimales) . "</td>
			</tr>
			<tr class='fila-items'>
			<td class='text-center'>{$ac->cantidad}</td>
			<td>Instalación de {$ac->accesorio}</td>
			<td class='text-end'>$ " . number_format(($ac->valor_inst / $ac->cantidad) + $ac->desc_inst, $this->opt_pais->decimales) . "</td>";
                                if ($val_des > 0) {
                                    $texto .= "<td class='text-end'>";
                                    (!empty($ac->desc_inst)) ? $texto .= '$ ' . number_format($ac->desc_inst, $this->opt_pais->decimales) : $texto .= "";
                                    $texto .= "</td>";
                                }
                                $texto .= "<td class='text-end'>$ " . number_format($ac->valor_inst, $this->opt_pais->decimales) . "</td>
			</tr>";
                            }


                            ($val_des > 0) ? $ancho = 310 : $ancho = 410; ?>
                    <table class="tabla2">
                        <tr>
                            <th><b>Cantidad</b></th>
                            <th width="<?= $ancho ?>"><b>Nombre del plan</b></th>
                            <th><b>Valor unidad</b></th>
                            <?php if ($val_des > 0) : ?>
                                <th><b>Descuento</b></th>
                            <?php endif; ?>
                            <th><b>Valor total</b></th>
                        </tr>
                        <?= $texto; ?>
                        <tr class="fila-valores1">
                            <td></td>
                            <td></td>
                            <?php if ($val_des > 0) : ?>
                                <td></td>
                            <?php endif; ?>
                            <td class="text-end"><b>Subtotal</b></td>
                            <td class="text-end">$ <?= number_format($subtotal, $this->opt_pais->decimales); ?></td>
                        </tr>
                        <tr class="fila-valores2">
                            <td></td>
                            <td></td>
                            <?php if ($val_des > 0) : ?>
                                <td></td>
                            <?php endif; ?>
                            <td class="text-end"><b><?= $this->opt_pais->imp; ?></b></td>
                            <td class="text-end">$ <?= number_format($subtotal * $propuesta->tasa_imp, $this->opt_pais->decimales); ?></td>
                        </tr>
                        <tr class="fila-valores2">
                            <td></td>
                            <td></td>
                            <?php if ($val_des > 0) : ?>
                                <td></td>
                            <?php endif; ?>
                            <td class="text-end"><b>Valor total</b></td>
                            <td class="text-end">$ <?= number_format(($subtotal * $propuesta->tasa_imp) + $subtotal, $this->opt_pais->decimales); ?></td>
                        </tr>
                    </table>
                <?php endif;

                        ($propuesta->plan->desc > 0) ? $ancho = 310 : $ancho = 410; ?>

                <div class="titulo_prop2">
                    <h1 class="text-center"><b>Pago mensual</b></h1>
                </div>
                <table class="tabla2">
                    <tr>
                        <th><b>Cantidad</b></th>
                        <th width="<?= $ancho ?>"><b>Descripción</b></th>
                        <th><b>Valor unidad</b></th>
                        <?php if ($propuesta->plan->desc > 0) : ?><th><b>Descuento</b></th><?php endif; ?>
                        <th><b>Valor total</b></th>
                    </tr>
                    <tr class="fila-items">
                        <td class="text-center"><?= $propuesta->equipo->cant_uni; ?></td>
                        <td><?= $propuesta->plan->nombre; ?></td>
                        <td class="text-end">$ <?= number_format(($propuesta->plan->valor_plan / $propuesta->equipo->cant_uni) + $propuesta->plan->desc, $this->opt_pais->decimales); ?></td>
                        <?php if ($propuesta->plan->desc > 0) : ?><td class="text-end">$ <?= number_format($propuesta->plan->desc, $this->opt_pais->decimales); ?></td><?php endif; ?>
                        <td class="text-end">$ <?= number_format($propuesta->plan->valor_plan, $this->opt_pais->decimales); ?></td>
                    </tr>

                    <tr class="fila-valores1">
                        <td></td>
                        <td></td>
                        <?php if ($propuesta->plan->desc > 0) : ?><td></td><?php endif; ?>
                        <td class="text-end"><b>Subtotal</b></td>
                        <td class="text-end">$ <?= number_format($propuesta->plan->valor_plan, $this->opt_pais->decimales); ?></td>
                    </tr>
                    <tr class="fila-valores2">
                        <td></td>
                        <td></td>
                        <?php if ($propuesta->plan->desc > 0) : ?><td></td><?php endif; ?>
                        <td class="text-end"><b><?= $this->opt_pais->imp; ?></b></td>
                        <td class="text-end"><?php if ($propuesta->plan->imp == 'false') {
                                                    echo "$ 0";
                                                } else {
                                                    echo "$" . number_format($propuesta->plan->valor_plan * $propuesta->tasa_imp, $this->opt_pais->decimales);
                                                } ?></td>
                    </tr>
                    <tr class="fila-valores2">
                        <td></td>
                        <td></td>
                        <?php if ($propuesta->plan->desc > 0) : ?><td></td><?php endif; ?>
                        <td class="text-end"><b>Valor total</b></td>
                        <?php if ($propuesta->plan->imp == 'false') : ?>
                            <td class="text-end">$ <?= number_format($propuesta->plan->valor_plan, $this->opt_pais->decimales) ?></td>
                        <?php else : ?>
                            <td class="text-end">$ <?= number_format(($propuesta->plan->valor_plan * $propuesta->tasa_imp) + $propuesta->plan->valor_plan, $this->opt_pais->decimales) ?></td>
                        <?php endif; ?>
                    </tr>
                </table>

            <?php
                    } else { //Comienzo de los parametros para permaniencia
                        $val_des = 0;
                        $texto = '';

                        $val_des = array_sum(array_map('sumar', $propuesta->accesorios));
                        $val_des += $propuesta->equipo->desc + $propuesta->equipo->desc_inst + $propuesta->plan->desc;
            ?>
                <p><b>Periodicidad de pago:</b> <?php
                                                if (isset($propuesta->plan->label_perma)) {
                                                    echo $propuesta->plan->label_perma;
                                                } else {
                                                    echo $propuesta->plan->permanencia;
                                                } ?></p><br>
                </div>


                <?php $ancho = 310; ?>
                <table class="tabla2">
                    <tr>
                        <th><b>Cantidad</b></th>
                        <th width="<?= $ancho ?>"><b>Descripción</b></th>
                        <th><b>Valor unidad</b></th>
                        <th><b>Descuento</b></th>
                        <th><b>Valor total</b></th>
                    </tr>
                    <tr class="fila-items">
                        <td class="text-center"><?= $propuesta->equipo->cant_uni; ?></td>
                        <td><?= $propuesta->equipo->nombre ?></td>
                        <td class="text-end">$ <?= number_format(($propuesta->equipo->valor_equipo / $propuesta->equipo->cant_uni) + $propuesta->equipo->desc, $this->opt_pais->decimales); ?></td>
                        <td class="text-end"><?php if (!empty($propuesta->equipo->desc)) {
                                                    echo "$ " . number_format($propuesta->equipo->desc, $this->opt_pais->decimales);
                                                } ?></td>
                        <td class="text-end">$ <?= number_format($propuesta->equipo->valor_equipo, $this->opt_pais->decimales); ?></td>
                    </tr>
                    <tr class="fila-items">
                        <td class="text-center"><?= $propuesta->equipo->cant_uni; ?></td>
                        <td>Instalación de unidad</td>
                        <td class="text-end">$ <?= number_format(($propuesta->equipo->valor_inst_equipo / $propuesta->equipo->cant_uni) + $propuesta->equipo->desc_inst, $this->opt_pais->decimales); ?></td>
                        <td class="text-end"><?php if (!empty($propuesta->equipo->desc_inst)) {
                                                    echo "$ " . number_format($propuesta->equipo->desc_inst, $this->opt_pais->decimales);
                                                } ?></td>
                        <td class="text-end">$ <?= number_format($propuesta->equipo->valor_inst_equipo, $this->opt_pais->decimales); ?></td>
                    </tr>

                    <?php
                        $subtotal = 0;
                        foreach ($propuesta->accesorios as $ac) {
                            $subtotal += $ac->valor + $ac->valor_inst;
                            $texto .= "<tr class='fila-items'>
			<td class='text-center'>{$ac->cantidad}</td>
			<td>{$ac->accesorio}</td>
			<td class='text-end'>$ " . number_format(($ac->valor / $ac->cantidad) + $ac->desc, $this->opt_pais->decimales) . "</td>";
                            $texto .= "<td class='text-end'>";
                            (!empty($ac->desc)) ? $texto .= '$ ' . number_format($ac->desc, $this->opt_pais->decimales) : $texto .= "";
                            $texto .= "</td>";
                            $texto .= "<td class='text-end'>$ " . number_format($ac->valor, $this->opt_pais->decimales) . "</td>
			</tr>
			<tr class='fila-items'>
			<td class='text-center'>{$ac->cantidad}</td>
			<td>Instalación de {$ac->accesorio}</td>
			<td class='text-end'>$ " . number_format(($ac->valor_inst / $ac->cantidad) + $ac->desc_inst, $this->opt_pais->decimales) . "</td>";


                            $texto .= "<td class='text-end'>";
                            (!empty($ac->desc_inst)) ? $texto .= '$ ' . number_format($ac->desc_inst, $this->opt_pais->decimales) : $texto .= "";
                            $texto .= "</td>";
                            $texto .= "<td class='text-end'>$ " . number_format($ac->valor_inst, $this->opt_pais->decimales) . "</td>
			</tr>";
                        } ?>

                    <?= $texto ?>

                    <tr class="fila-items">
                        <?php $tt = 0;
                        $meses = ['Semestre' => 6, 'Anualidad' => 12];

                        $num_mes = (isset($propuesta->plan->nun_mes)) ? $propuesta->plan->nun_mes : $meses[$propuesta->plan->permanencia];
                        eval('$tt = (' . $propuesta->plan->valor_plan . $propuesta->plan->regla_mes . ');'); ?>
                        <td class="text-center"><?= $propuesta->equipo->cant_uni; ?></td>
                        <td><?= $propuesta->plan->nombre; ?></td>
                        <td class="text-end">$ <?= number_format(($propuesta->plan->valor_plan / $propuesta->equipo->cant_uni) + $propuesta->plan->desc, $this->opt_pais->decimales); ?></td>
                        <td class="text-end">
                            <?php $meses = str_replace("meses", "", $propuesta->plan->regla_mes);


                            $descuento = ($propuesta->plan->valor_plan * $num_mes) - $tt;
                            if ($propuesta->plan->desc > 0) {
                                $descuento += $propuesta->plan->des;
                            }
                            echo "$" . number_format($descuento, $this->opt_pais->decimales);
                            ?>
                        </td>
                        <td class="text-end">$ <?= number_format($tt, $this->opt_pais->decimales); ?></td>
                    </tr>

                    <tr class="fila-valores1">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-end"><b>Subtotal</b></td>
                        <td class="text-end">$ <?= number_format($propuesta->subtotal, $this->opt_pais->decimales); ?></td>
                    </tr>
                    <tr class="fila-valores2">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-end"><b><?= $this->opt_pais->imp; ?></b></td>
                        <td class="text-end">$ <?= number_format($propuesta->imp, $this->opt_pais->decimales); ?></td>
                    </tr>
                    <tr class="fila-valores2">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-end"><b>Valor total</b></td>
                        <td class="text-end">$ <?= number_format($propuesta->total, $this->opt_pais->decimales); ?></td>
                    </tr>
                </table>

            <?php } //final de las condiciones de permanencia



                    if (!empty($propuesta->comenta)) : ?>
                <div class="titulo_prop2">
                    <p><b>Comentarios:</b></p>
                    <p><?= $propuesta->comenta ?></p>
                </div>
            <?php endif; ?>

            <page_footer>
                <img class='imagen_footer' src='<?= $url_footer; ?>'>
            </page_footer>
            </page>
        <?php } // final del bucle de las propuestas 
        ?>
        <page backimg="<?= $urlbaseplan . $this->datos->id_pais . '/'; ?>/page_04.jpg" backimgw="100%" backleft="10mm" backright="10mm" backtop="10mm" backbottom="10mm" backimgy="0mm" orientation="portrait" format="LETTER">
        </page>
        <page backimg="<?= $urlbaseplan . $this->datos->id_pais . '/'; ?>last_page.jpg" backimgw="100%" backleft="10mm" backright="10mm" backtop="10mm" backbottom="10mm" backimgy="0mm" orientation="portrait" format="LETTER">

            <table class="tabla3">
                <tr>
                    <?php if (isset($this->datos->datos[0]->vendedor->cargo) && !empty($this->datos->datos[0]->vendedor->cargo)) {
                        $cargo = $this->datos->datos[0]->vendedor->cargo;
                    } else {
                        $cargo = "Consultor comercial";
                    } ?>
                    <td rowspan="6" width='220'><img class='imagen' src='<?= $url_firma; ?>'></td>
                    <td class="nombre_asesor"><b><?= $this->datos->datos[0]->vendedor->nombre ?></b><br><i><?= $cargo ?></i></td>
                </tr>
                <tr>
                    <td><b><?= (isset($this->datos->datos[0]->vendedor->mobile)) ? $this->datos->datos[0]->vendedor->mobile : $this->phone_company; ?></b><br>
                        <?= (isset($this->datos->datos[0]->vendedor->phone)) ? $this->datos->datos[0]->vendedor->phone : ""; ?></td>
                </tr>
                <tr>
                    <td><b><?= $this->datos->datos[0]->vendedor->mail ?></b></td>
                </tr>
                <tr>
                    <td><?= (isset($this->datos->datos[0]->vendedor->address)) ? $this->datos->datos[0]->vendedor->address : $this->dir_company; ?></td>
                </tr>
                <?php if (isset($this->datos->datos[0]->vendedor->calendly) && !empty($this->datos->datos[0]->vendedor->calendly)) : ?>
                    <tr>
                        <td><a target="_blank" href="<?= $this->datos->datos[0]->vendedor->calendly ?>"> Agendar cita aquí </a></td>
                    </tr>
                <?php endif; ?>
                <tr>

                    <?php if (isset($this->datos->datos[0]->vendedor->user_url) && !empty($this->datos->datos[0]->vendedor->user_url)) {
                        $url = $this->datos->datos[0]->vendedor->user_url;
                    } else {
                        $url = get_site_url();
                    } ?>
                    <td><b><a target="_blank" href="<?= $url ?>"><?= str_replace("http://", "", str_replace("https://", "", $url)) ?></a></b></td>
                </tr>

            </table>
        </page>
<?php
        $html = ob_get_clean();
        //echo $html;
        //exit;
        return $html;
    }

    public function getNameFile()
    {
        $name = "cotizacion_satrack_" . str_pad($this->datos->id, 5, "0", STR_PAD_LEFT) . "-" . date("d-m-y", strtotime($this->datos->date_created));

        return $name;
    }
}
