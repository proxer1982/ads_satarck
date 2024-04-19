import Acc from './Acc.js';

export default class Accesorios {
    constructor(id) {
        this.valor = 0;
        this.id = id;
        this.cant_acc = 0;
        this.accesorios = [];
        this.id_acc = 0;

        //Se crea el contenedor
        this.objeto = document.createElement('div');
        this.objeto.classList.add('row');

        this.btn_agregar = document.createElement('button');
        this.btn_agregar.classList.add('btn');
        this.btn_agregar.classList.add('btn-info');
        this.btn_agregar.classList.add('btn-sm');
        this.btn_agregar.innerHTML = '<i class="fas fa-plus"></i> Añadir Accesorio';


        this.btn_agregar.addEventListener('click', this.agregar_acc.bind(this));

        this.render();
    }

    agregar_acc(event) {
        let accesorio = new Acc(this.id, this.id_acc);
        let btn_delete = document.createElement('button');
        btn_delete.classList.add('btn');
        btn_delete.classList.add('btn-danger');
        btn_delete.classList.add('btn-sm');
        btn_delete.classList.add('btn-del-acc');
        btn_delete.innerHTML = '<i class="fas fa-times"></i>';
        btn_delete.id_acc = this.id_acc;

        btn_delete.addEventListener('click', this.quitar_acc.bind(this));

        this.accesorios[this.id_acc] = accesorio;
        this.id_acc++;
        this.cant_acc = this.accesorios.length;

        accesorio.objeto.appendChild(btn_delete);
        accesorio.objeto.addEventListener('cambio_valor', (event) => { this.totalizar(); });

        this.objeto.querySelector('.cont_acc').appendChild(accesorio.objeto);
    }

    quitar_acc(event) {
        let accesorio = event.target;
        do
            accesorio = accesorio.parentNode;
        while (!accesorio.classList.contains('caja_acc'));

        this.accesorios.splice(event.target.id_acc, 1);
        accesorio.parentNode.removeChild(accesorio);

        this.cant_acc = this.accesorios.length;
        this.totalizar();
    }

    render() {
        //se crea los campos de unidad
        this.objeto.innerHTML = '<div class="section-title col-12 d-flex justify-content-between mb-3 align-items-center"><h3><i class="fab fa-whmcs icono"></i> Accesorios</h3></div>';
        this.objeto.innerHTML += '<div class="col-12 cont_acc"></div>';

        this.objeto.querySelector('.section-title').appendChild(this.btn_agregar);
    }

    totalizar() {
        this.valor = 0;
        this.cant_acc = 0;
        this.accesorios.forEach((item) => {
            this.valor += item.valor + item.valor_inst;
            this.cant_acc += item.cantidad;
        })

        this.objeto.dispatchEvent(new CustomEvent('cambio_valor', {
            bubbles: true,
            composed: true,
            detail: this
        }));
    }
}