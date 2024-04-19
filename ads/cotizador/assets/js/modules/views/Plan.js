import formatNumber from "../utils/utils.js";
import Descuento from "./Descuento.js";

export default class Plan {
    constructor(id, obj_cant, obj_modal) {
        this.valor = 0;
        this.valor_mes = 0;
        this.obj_cant = obj_cant;
        this.obj_modal = obj_modal;
        this.oculto = false;
        this.name_plan = '';


        this.objeto = document.createElement('div');
        this.objeto.classList.add('row');

        //Se crea el selector del dispositivo
        this.plan = document.createElement('select');
        this.plan.innerHTML = '<option value="" data-venta="0" data-comodato="0">Selecciona un plan</option>';
        this.plan.classList.add('select_plan');
        this.plan.classList.add('form-select');
        //this.plan.required = 'required';
        this.plan.setAttribute('aria-describedby', 'Seleccione un plan');

        datos_cw.lista_planes.forEach(elemt => {
            let option = document.createElement('option');
            option.value = elemt.id;

            if (elemt.comodato == 0) {
                option.setAttribute('hide-comodato', true);
            }

            option.text = elemt.nombre;
            option.dataset.venta = elemt.precio;
            option.dataset.comodato = elemt.comodato;
            this.plan.appendChild(option);
        });

        this.plan.addEventListener('change', (event) => {
            let opcion = event.target.options[event.target.selectedIndex];
            this.valor_mes = opcion.dataset[this.obj_modal.valor];
            this.valor_plan.value = formatNumber(this.valor_mes);
            this.valor_plan.valor_uni = this.valor_mes;
            this.name_plan = opcion.text;

            this.totalizar();
        })


        //Se crea el input de solo lectura del valor del plan
        this.valor_plan = document.createElement('input');
        this.valor_plan.classList.add('text-end');
        this.valor_plan.classList.add('form-control');
        this.valor_plan.name = 'val_mes_plan_' + id;
        this.valor_plan.min = 0;
        this.valor_plan.value = '0.00';
        this.valor_plan.setAttribute('readonly', true);
        this.valor_plan.valor_uni = 0;


        //Se crea el input y slect del descuento para el dispositivo
        this.desc_plan = new Descuento(id, 'mb-3', this.valor_plan, 0, '0.01');
        this.desc_plan.objeto.addEventListener('cambio_desc', () => {
            this.totalizar();
        })

        //Se crea el input de solo lectura del valor TOTAL de instalacion de todos los dispositivos
        this.total_plan = document.createElement('input');
        this.total_plan.classList.add('text-end');
        this.total_plan.classList.add('form-control');
        this.total_plan.name = 'val_total_disp_' + id;
        this.total_plan.min = 0;
        this.total_plan.value = '0.00';
        this.total_plan.setAttribute('readonly', true);

        this.render();
    }

    hide(tipo_mod) {
        let elementos = this.plan.querySelectorAll("option[hide-comodato='true']");

        if (tipo_mod === 'comodato') {
            elementos.forEach((item) => {
                item.classList.add('hide-comodato');
            });

        } else if (tipo_mod === 'venta') {
            elementos.forEach((item) => {
                item.classList.remove('hide-comodato');
            });
        }
        if (this.plan.options[this.plan.selectedIndex].classList.contains('hide-comodato')) {
            this.plan.value = '';
        }

        this.plan.dispatchEvent(new Event('change'));
    }

    hide_disp(lista) {
        let elementos = this.objeto.querySelectorAll('option');
        this.oculto = true;

        elementos.forEach((item, index) => {
            if (index > 0) {
                if (lista.indexOf(item.value) === -1) {
                    item.classList.add('hide-disp');
                } else {
                    item.classList.remove('hide-disp');
                }
            }
        });

        if (this.plan.options[this.plan.selectedIndex].classList.contains('hide-disp')) {
            this.plan.value = '';
            this.plan.dispatchEvent(new Event('change'));
        }
    }

    show() {
        let elementos = this.objeto.querySelectorAll(`option.hide-disp`);

        elementos.forEach((item) => {
            item.classList.remove('hide-disp');
        });

        this.oculto = false;
    }

    render() {
        //se crea los campos de unidad
        this.objeto.innerHTML = '<div class="section-title col-12"><h3><i class="fas fa-file-invoice-dollar icono"></i> Plan</h3></div>';
        this.objeto.innerHTML += '<div class="flex-row justify-content-between mb-2 d-md-flex row1"></div>';

        let col = document.createElement('div');
        col.classList.add('pe-1');
        col.classList.add('flex-grow-1');
        col.style = 'min-width:180px;';

        col.innerHTML = '<label for="dispositivo" class="form-label mb-1 txt-req">Nombre del plan</label>';
        col.appendChild(this.plan);

        this.objeto.querySelector('.row1').appendChild(col);

        col = document.createElement('div');
        col.classList.add('px-1');

        col.innerHTML = `<label for="dispositivo" class="form-label mb-1">Valor mensual x plan</label>
        <div class="input-group"><span class="input-group-text">$</span></div`;

        col.querySelector('.input-group').appendChild(this.valor_plan);

        this.objeto.querySelector('.row1').appendChild(col);



        col = document.createElement('div');
        col.classList.add('px-1');
        col.style = 'max-width:180px;';

        col.innerHTML = '<label class="form-label mb-1">Descuento (x Plan)</label>';
        col.appendChild(this.desc_plan.objeto);
        this.objeto.querySelector('.row1').appendChild(col);

        col = document.createElement('div');
        col.classList.add('ps-1');
        col.style = 'min-width:210px;max-width:210px; margin-left:auto;';

        col.innerHTML = '<label for="total_disp" class="form-label mb-1">Total mensualidad</label>';
        col.innerHTML += '<div class="input-group"><span class="input-group-text">$</span></div>';
        col.querySelector('.input-group').appendChild(this.total_plan);

        this.objeto.querySelector('.row1').appendChild(col);
    }

    totalizar() {
        //this.desc_plan.totalizar();
        this.valor = (this.valor_mes - this.desc_plan.valor) * this.obj_cant.cantidad;

        this.total_plan.value = formatNumber(this.valor);

        this.objeto.dispatchEvent(new CustomEvent('cambio_valor', {
            bubbles: true,
            composed: true,
            detail: this
        }));
    }
}