import Descuento from './Descuento.js?v=1.0.1';
import formatNumber from "../utils/utils.js";

export default class Acc {
    constructor(id, id_acc) {
        this.valor = 0;
        this.id = id_acc;
        this.valor_inst = 0;
        this.cantidad = 1;
        this.name_acc = '';

        //Se crea el contenedor
        this.objeto = document.createElement('div');
        this.objeto.classList.add('caja_acc');
        this.objeto.classList.add('border');
        this.objeto.classList.add('border-info');
        this.objeto.classList.add('my-4');
        this.objeto.dataset.num = id_acc;

        //Se crea el selector del dispositivo
        this.accesorio = document.createElement('select');
        this.accesorio.innerHTML = '<option value="">Selecciona uno</option>';
        this.accesorio.classList.add('select_acc');
        this.accesorio.classList.add('form-select');

        datos_cw.lista_acc.forEach(elemt => {
            let option = document.createElement('option');
            option.value = elemt.id;
            option.text = elemt.nombre;
            option.dataset.precio = elemt.precio;
            option.dataset.inst = elemt.instala;
            this.accesorio.appendChild(option);
        });

        this.accesorio.addEventListener('change', (event) => {
            let opcion = event.target.options[event.target.selectedIndex];
            this.valor_acc.valor_uni = opcion.dataset.precio;
            this.valor_acc.value = formatNumber(this.valor_acc.valor_uni);

            this.valor_inst_acc.valor_uni = opcion.dataset.inst;
            this.valor_inst_acc.value = formatNumber(this.valor_inst_acc.valor_uni);

            this.name_acc = opcion.text;

            this.totalizar();
        })


        //Se crea el input de cantidad de dispositivos
        this.cant_acc = document.createElement('input');
        this.cant_acc.classList.add('form-control');
        this.cant_acc.name = 'cant_acc_' + id;
        this.cant_acc.type = 'number';
        this.cant_acc.min = 0;
        this.cant_acc.value = 1;
        this.cant_acc.style = 'width:90px;';

        this.cant_acc.addEventListener('change', (event) => {
            this.cantidad = event.target.value;
            this.totalizar();
        })
        this.cant_acc.addEventListener('keyup', (event) => {
            this.cantidad = event.target.value;
            this.totalizar();
        })


        //Se crea el input de solo lectura del valor de los dispositivos
        this.valor_acc = document.createElement('input');
        this.valor_acc.classList.add('text-end');
        this.valor_acc.classList.add('form-control');
        this.valor_acc.name = 'val_uni_acc_' + id;
        this.valor_acc.min = 0;
        this.valor_acc.step = '0.01';
        this.valor_acc.value = '0.00';
        this.valor_acc.setAttribute('readonly', true);
        this.valor_acc.valor_uni = 0;

        //Se crea el input y slect del descuento para el dispositivo
        this.desc_acc = new Descuento(id, 'mb-3', this.valor_acc, 0, '0.01');
        this.desc_acc.objeto.addEventListener('cambio_desc', () => {
            this.totalizar();
        })


        this.total_acc = document.createElement('input');
        this.total_acc.classList.add('text-end');
        this.total_acc.classList.add('form-control');
        this.total_acc.name = 'val_total_acc_' + id;
        this.total_acc.min = 0;
        this.total_acc.step = '0.01';
        this.total_acc.value = '0.00';
        this.total_acc.setAttribute('readonly', true);


        //Se crea el input de solo lectura del valor de instalacion por dispositivo
        this.valor_inst_acc = document.createElement('input');
        this.valor_inst_acc.classList.add('text-end');
        this.valor_inst_acc.classList.add('form-control');
        this.valor_inst_acc.name = 'val_uni_inst_acc_' + id;
        this.valor_inst_acc.min = 0;
        this.valor_inst_acc.value = formatNumber(0);
        this.valor_inst_acc.setAttribute('readonly', true);
        this.valor_inst_acc.valor_uni = 0;

        //Se crea el input y slect del descuento para la instalacion del dispositivo
        this.desc_inst_acc = new Descuento(id, 'px-1', this.valor_inst_acc, 0, '0.01');
        this.desc_inst_acc.objeto.addEventListener('cambio_desc', () => {
            this.totalizar();
        })


        //Se crea el input de solo lectura del valor TOTAL de instalacion de todos los dispositivos
        this.total_inst_acc = document.createElement('input');
        this.total_inst_acc.classList.add('text-end');
        this.total_inst_acc.classList.add('form-control');
        this.total_inst_acc.name = 'val_total_acc_' + id;
        this.total_inst_acc.min = 0;
        this.total_inst_acc.step = '0.01';
        this.total_inst_acc.value = '0.00';
        this.total_inst_acc.setAttribute('readonly', true);



        this.render();
    }

    render() {
        this.objeto.innerHTML = `<div class="section-title col-12"><h4><i class="fas fa-microchip icono"></i> Accesorio ${this.id + 1}</h4></div>`;
        this.objeto.innerHTML += '<div class="flex-row justify-content-between mb-2 d-md-flex row1"></div>';
        this.objeto.innerHTML += `<div class="section-subtitle col-12"><h5>Instalación del accesorio</h5></div>
            <div class="flex-row justify-content-between mb-2 d-md-flex row2"></div>`;

        //se crea los campos de unidad
        let col = document.createElement('div');
        col.classList.add('pe-1');
        col.classList.add('flex-grow-1');
        col.style = 'min-width:180px;';

        col.innerHTML = '<label for="dispositivo" class="form-label mb-1">Accesorio</label>';
        col.appendChild(this.accesorio);
        this.objeto.querySelector('.row1').appendChild(col);

        col = document.createElement('div');
        col.classList.add('px-1');
        col.classList.add('flex-shrink-1');

        col.innerHTML = '<label for="cant_disp" class="form-label mb-1">Cantidad</label>';
        col.appendChild(this.cant_acc);
        col.style = 'max-width:150px;';

        this.objeto.querySelector('.row1').appendChild(col);

        col = document.createElement('div');
        col.classList.add('px-1');

        col.innerHTML = `<label class="form-label mb-1">Precio uni.</label>
        <div class="input-group"><span class="input-group-text">$</span></div`;

        col.querySelector('.input-group').appendChild(this.valor_acc);
        this.objeto.querySelector('.row1').appendChild(col);

        col = document.createElement('div');
        col.classList.add('px-1');
        col.setAttribute('hide-comodato', true);
        col.style = 'max-width:180px;';

        col.innerHTML = '<label class="form-label mb-1">Dto.(x Acc.)</label>';
        col.appendChild(this.desc_acc.objeto);
        this.objeto.querySelector('.row1').appendChild(col);

        col = document.createElement('div');
        col.classList.add('ps-1');

        col.innerHTML = '<label for="total_disp" class="form-label mb-1">Total</label>';
        col.innerHTML += '<div class="input-group"><span class="input-group-text">$</span></div>';
        col.querySelector('.input-group').appendChild(this.total_acc);
        col.style = 'min-width:210px;max-width:210px;';

        this.objeto.querySelector('.row1').appendChild(col);

        //se crea los campos de instalacion unidad
        col = document.createElement('div');
        col.classList.add('pe-1');

        col.innerHTML = `<label class="form-label mb-1">Valor por accesorio</label>
        <div class="input-group"><span class="input-group-text">$</span></div`;

        col.querySelector('.input-group').appendChild(this.valor_inst_acc);
        this.objeto.querySelector('.row2').appendChild(col);


        col = document.createElement('div');
        col.classList.add('px-1');
        col.style = 'max-width:180px;';

        col.innerHTML = '<label class="form-label mb-1">Descuento (x Inst.)</label>';
        col.appendChild(this.desc_inst_acc.objeto);
        this.objeto.querySelector('.row2').appendChild(col);


        col = document.createElement('div');
        col.classList.add('ps-1');
        col.style = 'min-width:210px;max-width:210px; margin-left:auto;';

        col.innerHTML = '<label for="total_disp" class="form-label mb-1">Total</label>';
        col.innerHTML += '<div class="input-group"><span class="input-group-text">$</span></div>';
        col.querySelector('.input-group').appendChild(this.total_inst_acc);

        this.objeto.querySelector('.row2').appendChild(col);

    }

    totalizar() {
        //this.desc_acc.totalizar();
        //this.desc_inst_acc.totalizar();

        this.valor = (this.valor_acc.valor_uni - this.desc_acc.valor) * this.cantidad;
        this.valor_inst = (this.valor_inst_acc.valor_uni - this.desc_inst_acc.valor) * this.cantidad;

        this.total_acc.value = formatNumber(this.valor);
        this.total_inst_acc.value = formatNumber(this.valor_inst);

        this.objeto.dispatchEvent(new CustomEvent('cambio_valor', {
            bubbles: true,
            composed: true,
            detail: this
        }));
    }
}