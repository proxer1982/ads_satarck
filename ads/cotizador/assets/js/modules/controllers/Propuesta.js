import Modalidad from "../views/Modalidad.js?v=1.0.2";
import Dispositivo from "../views/Dispositivo.js?v=1.0.2";
import Permanencia from "../views/Permanencia.js?v=1.0.2";
import Plan from "../views/Plan.js?v=1.0.2";
import Accesorios from "../views/Accesorios.js?v=1.0.2";
import Impuesto from "../views/Impuesto.js?v=1.0.2";
import formatNumber from "../utils/utils.js?v=1.0.2";

export default class Propuesta {
    constructor(id) {
        console.log("desde propuesta");
        this.data = new Object();
        this.data.total = 0;
        this.data.subtotal = 0;
        this.data.imp = 0;

        this.id = id;
        this.tipo_modalidad = 'venta';
        this.tipo_perma = '';
        this.cant_equipos = 1;

        this.contenedor = document.createElement('section');
        this.contenedor.classList.add('section_cw');
        this.contenedor.classList.add('section_prop');
        this.contenedor.classList.add('propuesta_0' + (id + 1));

        this.modalidad = new Modalidad(id);
        this.perma = new Permanencia(id);
        this.disp = new Dispositivo(id);
        this.plan = new Plan(id, this.disp, this.modalidad);
        this.acc = new Accesorios(id);

        this.imp = new Impuesto(id);


        this.comenta = document.createElement('textarea');
        this.comenta.classList.add('form-control');
        this.comenta.classList.add('area-comenta');
        this.comenta.id = 'area_comenta_' + id;

        this.subtotal = document.createElement('input');
        this.subtotal.classList.add('form-control');
        this.subtotal.classList.add('text-end');
        this.subtotal.setAttribute('readonly', true);
        this.subtotal.value = formatNumber(0);

        this.impuesto = document.createElement('input');
        this.impuesto.classList.add('form-control');
        this.impuesto.classList.add('text-end');
        this.impuesto.setAttribute('readonly', true);
        this.impuesto.value = formatNumber(0);

        this.total = document.createElement('input');
        this.total.classList.add('form-control');
        this.total.classList.add('text-end');
        this.total.setAttribute('readonly', true);
        this.total.value = formatNumber(0);

        this.modalidad.objeto.addEventListener('cambio_modal', (event) => {
            this.tipo_modalidad = event.detail;
            this.disp.hide(this.tipo_modalidad);
            this.plan.hide(this.tipo_modalidad);

            this.totalizar();
        })

        this.perma.objeto.addEventListener('cambio_modal', (event) => {
            this.tipo_perma = event.detail;
            this.totalizar();
        })

        this.imp.objeto.addEventListener('cambio_modal', (event) => {
            //this.tipo_modalidad = event.detail;
            //this.disp.hide(this.tipo_modalidad);
            //this.plan.hide(this.tipo_modalidad);
            console.log('cambio el tipo de impuesto para la mensualidad');
            this.totalizar();
        })

        // Accion a relizar cuando sucede un cambio en el selector de los dispositivos
        this.disp.objeto.addEventListener('cambio_valor', () => {
            if (this.disp.planes.length > 0) {
                this.plan.hide_disp(this.disp.planes);
            } else {
                this.plan.show();
            }

            this.plan.totalizar();
            this.totalizar();
        });
        this.acc.objeto.addEventListener('cambio_valor', (event) => { this.totalizar(); });

        this.plan.objeto.addEventListener('cambio_valor', (event) => { this.totalizar(); });


        this.render();
    }

    render() {
        this.contenedor.innerHTML = `<div class='box-header header-prop'>
        <h2><i class='fas fa-tasks icono'></i> Propuesta ${this.id + 1}</h2>
        </div>`;

        let section = document.createElement('section');
        section.classList.add('row');
        section.classList.add('parte_01');

        let div = document.createElement('hr');
        div.classList.add('my-3');

        section.appendChild(this.modalidad.objeto);
        section.appendChild(this.perma.objeto);
        section.appendChild(this.imp.objeto);

        this.contenedor.appendChild(section);
        this.contenedor.appendChild(div);


        div = document.createElement('hr');
        div.classList.add('my-3');

        section = document.createElement('section');
        section.classList.add('parte_02');


        section.appendChild(this.disp.objeto);

        this.contenedor.appendChild(section);
        this.contenedor.appendChild(div);

        div = document.createElement('hr');
        div.classList.add('my-3');

        section = document.createElement('section');
        section.classList.add('parte_03');

        section.appendChild(this.plan.objeto);

        this.contenedor.appendChild(section);
        this.contenedor.appendChild(div);

        div = document.createElement('hr');
        div.classList.add('my-3');

        section = document.createElement('section');
        section.classList.add('parte_04');


        section.appendChild(this.acc.objeto);

        this.contenedor.appendChild(section);
        this.contenedor.appendChild(div);

        div = document.createElement('hr');
        div.classList.add('my-3');

        section = document.createElement('section');
        section.classList.add('row');
        section.classList.add('my-3');
        section.classList.add('parte_05');
        section.innerHTML = `<div class="col">
        <label>Escribe observaciones o comentarios
        </label></div>`;

        section.querySelector('.col').appendChild(this.comenta);

        this.contenedor.appendChild(section);
        this.contenedor.appendChild(div);

        section = document.createElement('section');
        section.classList.add('row');
        section.classList.add('mt-3');
        section.classList.add('parte_06');
        section.innerHTML = `
        <div class='row-flex justify-content-end mb-2 d-md-flex'>
            <div class="col-3 ps-1 align-items-center d-flex">
                <label class="w-100 text-end me-3"><strong>Sub-total</strong></label>
            </div>
            <div class="col-4 ps-1">
                <div class="input-group ms-auto group-subtotal"><span class="input-group-text">$</span></div>
            </div>
        </div>
        <div class='row-flex justify-content-end mb-2 d-md-flex'>
            <div class="col-3 ps-1 align-items-center d-flex">
                <label class="w-100 text-end me-3"><strong>Total ${datos_cw.name_impuesto}</strong></label>
            </div>
            <div class="col-4 ps-1">
                <div class="input-group ms-auto group-imp"><span class="input-group-text">$</span></div>
            </div>
        </div>
        <div class='row-flex justify-content-end mb-2 d-md-flex'>
            <div class="col-3 ps-1 align-items-center d-flex">
                <label class="w-100 text-end me-3"><strong>Total</strong></label>
            </div>
            <div class="col-4 ps-1">
                <div class="input-group ms-auto group-total"><span class="input-group-text">$</span></div>
            </div>
        </div>`;

        section.querySelector('.group-subtotal').appendChild(this.subtotal);
        section.querySelector('.group-imp').appendChild(this.impuesto);
        section.querySelector('.group-total').appendChild(this.total);

        this.contenedor.appendChild(section);
    }

    editor() {
        wp.editor.initialize('area_comenta_' + this.id, {
            tinymce: {
                wpautop: true,
                plugins: 'charmap colorpicker compat3x directionality fullscreen hr image lists media paste tabfocus textcolor wordpress wpautoresize wpdialogs wpeditimage wpemoji wpgallery wplink wptextpattern wpview',
                toolbar1: 'formatselect bold italic | bullist numlist | blockquote | alignleft aligncenter alignright | link unlink | wp_more | spellchecker'
            },
            quicktags: false
        });
    }

    getContent() {
        return wp.editor.getContent('area_comenta_' + this.id);
    }

    totalizar() {
        this.data.subtotal = 0;
        if (this.tipo_modalidad === 'comodato') {
            this.data.subtotal = this.disp.valor_inst;
        } else {
            this.data.subtotal = this.disp.valor + this.disp.valor_inst;
        }

        let valor_plan = 0;
        if (this.tipo_perma == '0') {
            valor_plan = this.plan.valor;
        } else {
            valor_plan = eval(this.plan.valor + this.perma.valor);
        }
        this.data.subtotal += valor_plan;

        this.data.subtotal += this.acc.valor;
        this.subtotal.value = formatNumber(this.data.subtotal);

        if (this.imp.valor === 'false' || this.imp.valor === false) {
            this.data.imp = ((this.data.subtotal - valor_plan) * datos_cw.impuesto) / 100;
        } else {
            this.data.imp = (this.data.subtotal * datos_cw.impuesto) / 100;
        }


        this.impuesto.value = formatNumber(this.data.imp);

        this.data.total = this.data.subtotal + this.data.imp;
        this.total.value = formatNumber(this.data.total);
    }
}