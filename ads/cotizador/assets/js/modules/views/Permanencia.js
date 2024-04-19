export default class Permanencia {
    constructor(id) {
        console.log("desde permanencia");
        this.valor = '';
        this.name_perma = '';
        this.meses = 0;
        this.label = '';

        this.objeto = document.createElement('div');
        this.objeto.classList.add('col-md-auto');
        this.objeto.classList.add('flex-md-fill');
        this.objeto.innerHTML = `<label class="form-label mb-1 d-block">Periocidad</label>
        <input type="radio" class="btn-check" id="perma_01_${id}" autocomplete="off" checked="" value="0" name="permanencia_${id}" data-nombre="Ninguna"><label class="btn btn-primary btn-sm" for="perma_01_${id}">Ninguna</label>`;
        console.log(datos_cw.lista_reglas);
        Object.keys(datos_cw.lista_reglas).map((item, key) => {
            this.objeto.innerHTML += `<input type="radio" class="btn-check" id="perma_0${key + 2}_${id}" autocomplete="off" value="${datos_cw.lista_reglas[item].regla}" name="permanencia_${id}" data-nombre="${datos_cw.lista_reglas[item].nombre}" data-label="${datos_cw.lista_reglas[item].texto}" data-meses="${datos_cw.lista_reglas[item].meses}"><label class="btn btn-primary btn-sm" for="perma_0${key + 2}_${id}">${datos_cw.lista_reglas[item].nombre}</label>`;
        });

        this.objeto.innerHTML += '</div>';

        this.totalizar = function (event) {
            this.valor = event.target.value;
            this.name_perma = event.target.dataset.nombre;
            this.meses = event.target.dataset.meses;
            this.label = event.target.dataset.label;

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

    render() {

    }
}