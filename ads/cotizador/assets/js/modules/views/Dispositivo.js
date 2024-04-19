import Descuento from './Descuento.js';
import formatNumber from '../utils/utils.js';

export default class Dispositivo {
    constructor(id, tipo_plan = 'venta') {
        this.valor = 0;
        this.valor_inst = 0;
        this.costo_inst = 0;
        this.cantidad = 1;
        this.tipo_plan = tipo_plan;
        this.planes = new Array();
        this.nombre = '';

        //Se crea el contenedor
        this.objeto = document.createElement('div');
        this.objeto.classList.add('row');


        //Se crea el selector del dispositivo
        this.equipo = document.createElement('select');
        this.equipo.innerHTML = '<option value="" disabled="" selected="" data-plan="" data-precio="0">Selecciona unidad</option>';
        this.equipo.classList.add('select_disp');
        this.equipo.classList.add('form-select');
        //this.equipo.required = 'required';
        this.equipo.setAttribute('aria-describedby', 'Seleccione una unidad');


        datos_cw.lista_equipos.forEach(elemt => {
            let option = document.createElement('option');
            option.value = elemt.id;
            option.text = elemt.nombre;
            option.dataset.precio = elemt.precio;

            if (elemt.hasOwnProperty('planes')) {
                option.dataset.plan = elemt.planes;
            } else {
                option.dataset.plan = '';
            }

            if (elemt.hasOwnProperty('no_instala')) {
                option.dataset.no_instala = elemt.no_instala;
            }

            this.equipo.appendChild(option);
        });

        this.equipo_comd = document.createElement('option');
        this.equipo_comd.value = 'Equipo en comodato';
        this.equipo_comd.text = "Equipo en comodato";
        this.equipo_comd.dataset.plan = '';
        this.equipo_comd.dataset.precio = 0;

        this.equipo.addEventListener('change', (event) => {
            let sel_equipo = event.target.options[event.target.selectedIndex];
            if (this.tipo_plan === 'venta') {
                this.valor_equipo.valor_uni = sel_equipo.dataset.precio;
                this.valor_equipo.value = formatNumber(this.valor_equipo.valor_uni);
            } else {
                this.valor_equipo.valor_uni = 0;
                this.valor_equipo.value = formatNumber(0);
            }

            if (sel_equipo.dataset.plan.length > 0) {
                this.planes = event.target.options[event.target.selectedIndex].dataset.plan.split(",");
            } else {
                this.planes = [];
            }

            if (sel_equipo.dataset.no_instala == "true") {
                this.valor_inst = 0;
                this.costo_inst = 0;
                this.hide_inst();

            } else {

                this.tipo_inst.dispatchEvent(new CustomEvent('change', {
                    bubbles: true,
                    composed: true,
                    detail: this
                }))
                //this.valor_inst = sel_equipo.dataset.precio;
                //this.costo_inst = sel_equipo.dataset.precio;
                this.show_inst();
            }

            this.nombre = sel_equipo.text;
            this.totalizar();
        })

        //Se crea el input de cantidad de dispositivos
        this.cant_equipo = document.createElement('input');
        this.cant_equipo.classList.add('form-control');
        this.cant_equipo.name = 'cant_disp_' + id;
        this.cant_equipo.type = 'number';
        this.cant_equipo.min = 0;
        this.cant_equipo.value = 1;
        this.cant_equipo.style = 'width:90px;';

        this.cant_equipo.addEventListener('change', (event) => {
            this.cantidad = event.target.value;
            this.totalizar();

            this.objeto.dispatchEvent(new CustomEvent('cambio_valor', {
                bubbles: true,
                composed: true,
                detail: this
            }));
        })
        this.cant_equipo.addEventListener('keyup', (event) => {
            this.cantidad = event.target.value;
            this.totalizar();
        })

        //Se crea el input de solo lectura del valor de los dispositivos
        this.valor_equipo = document.createElement('input');
        this.valor_equipo.classList.add('text-end');
        this.valor_equipo.classList.add('form-control');
        this.valor_equipo.name = 'val_uni_disp_' + id;
        this.valor_equipo.min = 0;
        this.valor_equipo.step = '0.01';
        this.valor_equipo.value = '0.00';
        this.valor_equipo.setAttribute('readonly', true);
        this.valor_equipo.valor_uni = 0;

        //Se crea el input y slect del descuento para el dispositivo
        this.desc_equipo = new Descuento(id, 'mb-3', this.valor_equipo, 0, '0.01');
        this.desc_equipo.objeto.addEventListener('cambio_desc', () => {
            this.totalizar();
        })

        this.total_equipo = document.createElement('input');
        this.total_equipo.classList.add('text-end');
        this.total_equipo.classList.add('form-control');
        this.total_equipo.name = 'val_total_disp_' + id;
        this.total_equipo.min = 0;
        this.total_equipo.step = '0.01';
        this.total_equipo.value = '0.00';
        this.total_equipo.setAttribute('readonly', true);


        //Se crea el selector del dispositivo
        this.tipo_inst = document.createElement('select');
        this.tipo_inst.classList.add('select_tipo_disp');
        this.tipo_inst.classList.add('form-select');


        Object.entries(datos_cw.lista_tipoInst).forEach(([key, elemt]) => {
            let option = document.createElement('option');
            option.value = key;
            option.text = elemt.nombre;
            option.dataset.precio = elemt.precio;

            this.tipo_inst.appendChild(option);
        });

        this.costo_inst = this.tipo_inst.firstChild.dataset.precio;

        this.tipo_inst.addEventListener('change', (event) => {
            this.costo_inst = event.target.options[event.target.selectedIndex].dataset.precio;

            this.valor_inst_equipo.value = formatNumber(this.costo_inst);
            this.valor_inst_equipo.valor_uni = this.costo_inst;

            this.totalizar();
        });

        //Se crea el input de solo lectura del valor de instalacion por dispositivo
        this.valor_inst_equipo = document.createElement('input');
        this.valor_inst_equipo.classList.add('text-end');
        this.valor_inst_equipo.classList.add('form-control');
        this.valor_inst_equipo.name = 'val_uni_inst_disp_' + id;
        this.valor_inst_equipo.min = 0;
        this.valor_inst_equipo.value = formatNumber(this.costo_inst);
        this.valor_inst_equipo.setAttribute('readonly', true);
        this.valor_inst_equipo.valor_uni = this.costo_inst;

        //Se crea el input y slect del descuento para la instalacion del dispositivo
        this.desc_inst_equipo = new Descuento(id, 'px-1', this.valor_inst_equipo, 0, '0.01');
        this.desc_inst_equipo.objeto.addEventListener('cambio_desc', () => {
            this.totalizar();
        })

        //Se crea el input de solo lectura del valor TOTAL de instalacion de todos los dispositivos
        this.total_inst_equipo = document.createElement('input');
        this.total_inst_equipo.classList.add('text-end');
        this.total_inst_equipo.classList.add('form-control');
        this.total_inst_equipo.name = 'val_total_disp_' + id;
        this.total_inst_equipo.min = 0;
        this.total_inst_equipo.step = '0.01';
        this.total_inst_equipo.value = '0.00';
        this.total_inst_equipo.setAttribute('readonly', true);



        this.render();
    }


    /**
     * Representa el contenido HTML para la función dada.
      * Esta función crea los campos para la unidad y la instalación de la unidad.
      *
      * @return {void} La función no devuelve un valor.
     */
    render() {
        //se crea los campos de unidad
        this.objeto.innerHTML = '<div class="section-title col-12"><h3><i class="fas fa-microchip icono"></i> Unidad</h3></div>';
        this.objeto.innerHTML += '<div class="flex-row justify-content-between mb-2 d-md-flex row1"></div>';
        this.objeto.innerHTML += `<div class="section-title col-12"><h5>Instalación de la unidad</h5></div>
            <div class="flex-row justify-content-between mb-2 d-md-flex row2"></div>`;

        let col = document.createElement('div');
        col.classList.add('pe-1');
        col.classList.add('flex-grow-1');
        col.style = 'min-width:180px;';

        col.innerHTML = '<label for="dispositivo" class="form-label mb-1 txt-req">Nombre de unidad</label>';
        col.appendChild(this.equipo);

        this.objeto.querySelector('.row1').appendChild(col);

        col = document.createElement('div');
        col.classList.add('px-1');
        col.classList.add('flex-shrink-1');

        col.innerHTML = '<label for="cant_disp" class="form-label mb-1">Cantidad</label>';
        col.appendChild(this.cant_equipo);
        col.style = 'max-width:150px;';

        this.objeto.querySelector('.row1').appendChild(col);

        col = document.createElement('div');
        col.classList.add('px-1');
        col.setAttribute('hide-comodato', true);

        col.innerHTML = `<label class="form-label mb-1">Precio unitario</label>
        <div class="input-group"><span class="input-group-text">$</span></div>`;

        col.querySelector('.input-group').appendChild(this.valor_equipo);
        this.objeto.querySelector('.row1').appendChild(col);

        col = document.createElement('div');
        col.classList.add('px-1');
        col.setAttribute('hide-comodato', true);
        col.style = 'max-width:180px;';

        col.innerHTML = '<label class="form-label mb-1">Descuento (x Und.)</label>';
        col.appendChild(this.desc_equipo.objeto);
        this.objeto.querySelector('.row1').appendChild(col);

        col = document.createElement('div');
        col.classList.add('ps-1');
        col.setAttribute('hide-comodato', true);

        col.innerHTML = '<label for="total_disp" class="form-label mb-1">Total</label>';
        col.innerHTML += '<div class="input-group"><span class="input-group-text">$</span></div>';
        col.querySelector('.input-group').appendChild(this.total_equipo);
        col.setAttribute('hide-comodato', true);
        col.style = 'min-width:210px;max-width:210px;';

        this.objeto.querySelector('.row1').appendChild(col);

        //se crea los campos de instalacion unidad
        col = document.createElement('div');
        col.classList.add('pe-1');
        col.setAttribute('hide-instala', true);

        col.innerHTML = `<label for="dispositivo" class="form-label mb-1">Tipo</label>`;

        col.appendChild(this.tipo_inst);
        this.objeto.querySelector('.row2').appendChild(col);


        col = document.createElement('div');
        col.classList.add('px-1');
        col.setAttribute('hide-instala', true);

        col.innerHTML = `<label class="form-label mb-1">Valor por unidad</label>
        <div class="input-group"><span class="input-group-text">$</span></div>`;

        col.querySelector('.input-group').appendChild(this.valor_inst_equipo);
        this.objeto.querySelector('.row2').appendChild(col);


        col = document.createElement('div');
        col.classList.add('px-1');
        col.style = 'max-width:180px;';

        col.innerHTML = '<label class="form-label mb-1">Descuento (x Inst.)</label>';
        col.appendChild(this.desc_inst_equipo.objeto);
        this.objeto.querySelector('.row2').appendChild(col);


        col = document.createElement('div');
        col.classList.add('ps-1');
        col.style = 'min-width:210px;max-width:210px; margin-left:auto;';

        col.innerHTML = '<label for="total_disp" class="form-label mb-1">Total</label>';
        col.innerHTML += '<div class="input-group"><span class="input-group-text">$</span></div>';
        col.querySelector('.input-group').appendChild(this.total_inst_equipo);

        this.objeto.querySelector('.row2').appendChild(col);
    }

    /**
     * Hides elements based on the given tipo_mod parameter.
     *
     * @param {string} tipo_mod - The type of modification.
     */
    hide(tipo_mod) {

        let elementos = this.objeto.querySelectorAll("div[hide-comodato='true']");

        if (tipo_mod === 'comodato') {
            let options = this.equipo.querySelectorAll('option');
            this.equipo.appendChild(this.equipo_comd);
            this.equipo_comd.selected = true;
            elementos.forEach((item) => {
                item.classList.add('hide-comodato');
            });

            options.forEach((item, key) => {
                if (key !== 0) {
                    item.classList.add('hide-comodato');
                }
            });


        } else if (tipo_mod === 'venta') {
            let options = this.equipo.querySelectorAll('option');
            elementos.forEach((item) => {
                item.classList.remove('hide-comodato');
            });
            options.forEach((item, key) => {
                if (key !== 0) {
                    item.classList.remove('hide-comodato');
                }
            });

            if (this.valor_equipo.valor_uni == 0) {
                this.equipo.firstChild.selected = true;
            }
            this.equipo.removeChild(this.equipo_comd);

        }
        this.equipo.dispatchEvent(new Event('change'));
        this.totalizar();
    }

    hide_inst = function () {
        let elementos = this.objeto.querySelectorAll("div[hide-instala='true']");

        elementos.forEach((item) => {
            item.classList.add('d-none');
        });
    }

    show_inst = function () {
        let elementos = this.objeto.querySelectorAll("div[hide-instala='true']");

        elementos.forEach((item) => {
            item.classList.remove('d-none');
        });
    }

    totalizar = function () {
        //this.desc_equipo.totalizar();

        this.valor = (this.valor_equipo.valor_uni - this.desc_equipo.valor) * this.cantidad;
        this.valor_inst = (this.costo_inst - this.desc_inst_equipo.valor) * this.cantidad;

        this.total_equipo.value = formatNumber(this.valor);
        this.total_inst_equipo.value = formatNumber(this.valor_inst);

        this.objeto.dispatchEvent(new CustomEvent('cambio_valor', {
            bubbles: true,
            composed: true,
            detail: this
        }));
    }
}