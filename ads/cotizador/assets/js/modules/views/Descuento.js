import formatNumber from '../utils/utils.js';

export default class Descuento {
    constructor(id, clases = [], input_valor, valor = 0, pasos = 0) {
        this.valor = 0;
        this.tipo = 'val';
        this.input_valor = input_valor;

        this.objeto = document.createElement('div');
        this.objeto.classList.add('input-group');

        this.sel_tipo = document.createElement('select');
        this.sel_tipo.innerHTML = '<option value="val" selected="">$</option><option value="por">%</option>';
        this.sel_tipo.classList.add('input-group-text');
        this.sel_tipo.classList.add('sel-des');

        this.input = document.createElement('input');
        this.input.classList.add('form-control');
        this.input.classList.add('desc_disp');
        this.input.classList.add('text-end');

        this.input.name = 'desc_disp' + id;
        this.input.type = 'number';
        this.input.step = pasos;

        this.input.dataset.pasos = pasos;
        this.input.min = 0;
        this.input.value = formatNumber(valor);

        if (clases.length > 0 && Array.isArray(clases)) {
            clases.forEach(clase => this.objeto.classList.add(clase));
        } else if (clases.length > 0 && typeof clases === 'string') {
            this.objeto.classList.add(clases);
        }

        this.objeto.appendChild(this.sel_tipo);
        this.objeto.appendChild(this.input);

        this.sel_tipo.addEventListener('change', (event) => {
            switch (event.target.value) {
                case 'val':
                    this.input.value = formatNumber(valor);
                    this.input.setAttribute('step', pasos);
                    this.tipo = "val";
                    break;
                case 'por':
                    this.input.value = 0;
                    this.input.setAttribute('step', 1);
                    this.tipo = "por";
                    break;
                default: break;
            }
            //$estado = '';

            this.totalizar();
        });

        this.input.addEventListener('change', event => {
            this.totalizar();
        });
        this.input.addEventListener('keyup', event => {
            this.totalizar();
        });
    }

    totalizar() {
        if (this.tipo === 'val' && this.input.value > 0) {
            this.valor = this.input.value;
        } else if (this.tipo === 'por' && this.input.value > 0) {
            this.valor = (this.input.value * this.input_valor.valor_uni) / 100;
        } else {
            this.valor = 0;
        }

        this.objeto.dispatchEvent(new CustomEvent('cambio_desc', {
            /*bubbles: true,
            composed: true,*/
            detail: this
        }));
    }
}