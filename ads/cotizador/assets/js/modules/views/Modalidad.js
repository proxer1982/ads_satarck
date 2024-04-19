export default class Modalidad {
    constructor(id) {
        this.valor = 'venta';

        this.objeto = document.createElement('div');
        this.objeto.classList.add('col-md-auto');
        this.objeto.classList.add('flex-md-fill');
        this.objeto.innerHTML = '<label class="form-label mb-1 d-block">Modalidad</label><input type="radio" class="btn-check" name="modalidad_' + id + '" id="moda1_' + id + '" autocomplete="off" checked="" value="venta"><label class="btn btn-primary btn-sm" for="moda1_' + id + '">Venta</label><input type="radio" class="btn-check" name="modalidad_' + id + '" id="moda2_' + id + '" autocomplete="off" value="comodato"><label class="btn btn-primary btn-sm" for="moda2_' + id + '">Comodato</label>';

        this.totalizar = function (event) {
            this.valor = event.target.value;

            this.objeto.dispatchEvent(new CustomEvent('cambio_modal', {
                bubbles: true,
                composed: true,
                detail: this.valor
            }));
        }

        for (const child of this.objeto.children) {
            if (child.className == 'btn-check') child.addEventListener('change', this.totalizar.bind(this), false);
        }
    }


}