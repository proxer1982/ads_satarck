export default class Impuesto {
    constructor(id) {
        this.valor = datos_cw.imp_mes;
        let check = "";
        console.log("probando desde Impuesto 2");

        if (this.valor == 'true') {
            check = "checked";
        }

        this.objeto = document.createElement('div');
        this.objeto.classList.add('col-md-auto');
        this.objeto.innerHTML = `<label class="form-label mb-1 d-block">Impuesto mensualidad</label>
        <div class="form-check form-switch d-flex"><label class="form-check-label" for="impuesto_${id}">NO</label><input name="impuesto_${id}" id="impuesto_${id}" class="form-check-input mx-2" type="checkbox" id="flexSwitchCheckDefault" ${check}><label class="form-check-label" for="impuesto_${id}"> SI</label></div>`;

        this.totalizar = function (event) {
            this.valor = event.target.checked;

            this.objeto.dispatchEvent(new CustomEvent('cambio_modal', {
                bubbles: true,
                composed: true,
                detail: this.valor
            }));
        }

        let input = this.objeto.querySelector('#impuesto_' + id);
        input.addEventListener('change', this.totalizar.bind(this), false);
    }
}