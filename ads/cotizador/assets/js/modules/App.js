import Cliente from "./controllers/Cliente.js?v=1.0.5";
import Propuesta from "./controllers/Propuesta.js?v=1.0.7";
import Acciones from "./controllers/Acciones.js?v=1.0.5";

export default class App {
    constructor() {
        console.log("desde app");
        this.id_prop = 0;
        this.cliente = new Cliente();
        let prop_temp = new Propuesta(this.id_prop);
        this.propuesta = new Array();
        this.propuesta[0] = prop_temp;
        this.acciones = new Acciones();
        this.comenta =
            this.cabezales = [];

        this.alerta = document.createElement('div');
        this.alerta.classList.add('alert');
        this.alerta.classList.add('alert-dismissible');
        this.alerta.classList.add('fade');
        this.alerta.classList.add('hide');
        this.alerta.role = 'alert';
        this.alerta.innerHTML = `<span class='msj'>Texto</span><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;

        this.loader = document.createElement('div');
        this.loader.classList.add('container-loader');
        this.loader.innerHTML = `<div class="custom-loader"></div>`;


        this.acciones.btn_guardar.addEventListener('click', this.guardar.bind(this));
        this.acciones.btn_agregarProp.addEventListener('click', this.agregarProp.bind(this));
        //this.acciones.btn_descargar.addEventListener('click', this.guardar.bind(this));

        this.render();

        let inputs = document.querySelectorAll('.form-control');

        inputs.forEach((input) => {
            input.addEventListener('input', (event) => {
                let error = '';
                // Cada vez que el usuario escribe algo, verificamos si
                // los campos del formulario son válidos.

                this.validar(event.target);
            });
        });

        let opcionales = this.cliente.contenedor.querySelectorAll('.opcional');

        opcionales.forEach((it) => {
            it.addEventListener('keyup', (event) => {
                if (this.cliente.email.value !== "" && this.cliente.phone.value == "") {
                    this.cliente.phone.required = false;
                    this.cliente.phone.classList.remove('hasError');
                    this.cliente.phone.parentElement.querySelector('label').classList.remove('txt-req-ama');
                } else if (this.cliente.phone.value !== "" && this.cliente.email.value == '') {
                    this.cliente.email.required = false;
                    this.cliente.email.classList.remove('hasError');
                    this.cliente.email.parentElement.querySelector('label').classList.remove('txt-req-ama');
                } else {
                    this.cliente.phone.required = "required";
                    this.cliente.email.required = "required";
                    this.cliente.phone.parentElement.querySelector('label').classList.add('txt-req-ama');
                    this.cliente.email.parentElement.querySelector('label').classList.add('txt-req-ama');
                }

            });
        });


        this.cabezales = Array.prototype.slice.call(document.querySelectorAll('.box-header'));

        this.cabezales.forEach((item) => {

            item.addEventListener('click', (event) => {
                let cont = event.target;
                do
                    cont = cont.parentNode;
                while (!cont.classList.contains('section_cw'));

                cont.classList.toggle("seccion-hide");

                this.cabezales.forEach((dsd) => {
                    if (cont !== dsd.parentNode) {
                        dsd.parentNode.classList.add("seccion-hide");
                    }

                });
            });
        });

    }

    render() {
        document.querySelector('#contenedor_prop').append(this.cliente.contenedor);
        let caja = document.createElement('div');
        caja.classList.add('caja_prop');
        caja.id = 'caja_prop';
        this.propuesta.forEach((prop) => {
            prop.contenedor.classList.add('seccion-hide');
            caja.append(prop.contenedor);
        });

        document.querySelector('#contenedor_prop').append(caja);
        document.querySelector('#contenedor_prop').append(this.acciones.contenedor);

        this.propuesta[this.id_prop].editor();
    }

    guardar() {
        if (this.validar()) {
            this.animar_espera('show');
            let datos = new Array();

            this.propuesta.forEach((item_prop) => {
                let comenta = item_prop.getContent();
                comenta = comenta.replace(/\n/g, "<br />");
                let propu = {
                    id: item_prop.id,
                    total: item_prop.data.total,
                    subtotal: item_prop.data.subtotal,
                    imp: item_prop.data.imp,
                    tasa_imp: datos_cw.impuesto / 100,
                    vendedor: datos_cw.vendedor,
                    equipo: {
                        nombre: item_prop.disp.nombre,
                        valor_equipo: item_prop.disp.valor,
                        valor_inst_equipo: item_prop.disp.valor_inst,
                        cant_uni: item_prop.disp.cant_equipo.value,
                        desc: item_prop.disp.desc_equipo.valor,
                        desc_inst: item_prop.disp.desc_inst_equipo.valor
                    },
                    plan: {
                        id: item_prop.plan.plan.value,
                        nombre: item_prop.plan.name_plan,
                        valor_plan: item_prop.plan.valor,
                        permanencia: item_prop.perma.name_perma,
                        label_perma: item_prop.perma.label,
                        regla_mes: item_prop.perma.valor,
                        nun_mes: item_prop.perma.meses,
                        desc: item_prop.plan.desc_plan.valor,
                        imp: datos_cw.imp_mes
                    },
                    modalidad: item_prop.tipo_modalidad,
                    accesorios: [],
                    comenta: comenta
                }

                item_prop.acc.accesorios.forEach((item) => {
                    propu.accesorios.push({
                        accesorio: item.name_acc,
                        valor: item.valor,
                        valor_inst: item.valor_inst,
                        cantidad: item.cantidad,
                        desc: item.desc_acc.valor,
                        desc_inst: item.desc_inst_acc.valor
                    })
                });

                datos.push(propu);
            });
            console.log(datos);
            let formData = new FormData();
            if (this.acciones.id_cot > 0) {
                formData.append("id", this.acciones.id_cot);
            }
            formData.append("action", "save_ajax_cotizacion");
            formData.append("nonce", datos_cw.NONCE);
            formData.append("id_pais", datos_cw.pais);
            formData.append("nombre_cliente", this.cliente.nombre.value);
            formData.append("apellido_cliente", this.cliente.apellido.value);
            formData.append("empresa", this.cliente.empresa.value);
            formData.append("email", this.cliente.email.value);
            formData.append("ciudad", this.cliente.ciudad.value);
            formData.append("tipo_cliente", this.cliente.tipo_cliente.value);
            formData.append("phone", this.cliente.phone.value);
            //formData.append("datos", datos);
            formData.append("datos", JSON.stringify(datos));

            this.envio_ajax(formData);
        }
    }

    envio_ajax(data = null) {
        const http = new XMLHttpRequest();

        http.addEventListener('load', this.accion_cargado.bind(this), false);

        http.responseType = "json";
        http.open('POST', dcms_vars.ajaxurl);
        http.send(data);
    }

    validar(item_val = '') {
        let msj_error = '';
        let error = new Array();
        let estado_error = false;

        if (item_val === '') {
            let requeridos = document.querySelector('#contenedor_prop').querySelectorAll('*[required]');
            requeridos.forEach((item) => {
                if (item.validity.valid) {
                    item.classList.remove('hasError');
                } else {
                    // Si todavía hay un error, muestra el error exacto
                    estado_error = true;
                    error.push(item);

                    if (item.getAttribute('aria-describedby')) {
                        msj_error += `<p>${item.getAttribute('aria-describedby')}</p>`;
                    } else {
                        msj_error += `<p>${item.validationMessage}</p>`;
                    }
                    item.classList.add('hasError');
                }
            });

            if (error.length > 1) {
                msj_error = 'Varios campos son requeridos para guardar la información';
            }

            if (estado_error) {
                this.showError(msj_error);
                return false;
            } else {
                this.hideError();
                return true;
            }
        } else {
            if (item_val.validity.valid) {
                estado_error = false;
                item_val.classList.remove('hasError'); // Restablece el contenido del mensaje
            } else {
                // Si todavía hay un error, muestra el error exacto
                estado_error = false;
                if (item_val.getAttribute('aria-describedby')) {
                    msj_error += `<p>${item_val.getAttribute('aria-describedby')}</p>`;
                } else {
                    msj_error += `<p>${item_val.validationMessage}</p>`;
                }

                item_val.classList.add('hasError');
            }
        }
    }


    accion_cargado = function (event) {
        this.animar_espera('hide');
        if (event.target.readyState == 4 && event.target.status == 200) {
            console.log(event.target.response);
            //let data = JSON.parse(event.target.response);
            //this.acciones.id_cot = data.id;
            if (event.target.status == 200) {
                if (event.target.response.status == 200) {
                    console.log(event.target.response);
                    this.acciones.btn_guardar.classList.remove('btn-primary');
                    this.acciones.btn_guardar.classList.add('btn-info');
                    this.acciones.btn_guardar.innerHTML = '<i class="far fa-save"></i> Actualizar';
                    this.acciones.id_cot = event.target.response.id;

                    this.acciones.btn_descargar.classList.remove('disabled');

                    this.acciones.btn_descargar.href = window.location.origin + window.location.pathname + "?action=pdf_cotizacion&id_cot=" + event.target.response.id + "&file=cotizacion.pdf";
                    //this.acciones.btn_enviar_mail.disabled = false;
                    this.showError('Guardado satisfactoriamente', 'info');
                    setTimeout(() => { this.hideError(); }, 4000);
                } else {
                    this.showError('Error al guardar');
                    setTimeout(() => { this.hideError(); }, 4000);
                }

            } else {
                this.showError('problemas con la conexion')
            }
        }
    }

    agregarProp(event) {
        this.id_prop++;

        let propu = new Propuesta(this.id_prop);
        let btn_delete = document.createElement('button');
        btn_delete.classList.add('btn');
        btn_delete.classList.add('btn-danger');
        btn_delete.classList.add('btn-sm');
        btn_delete.classList.add('btn-del-prop');
        btn_delete.innerHTML = '<i class="fas fa-times"></i>';
        btn_delete.id_prop = this.id_prop;

        btn_delete.addEventListener('click', this.quitar_prop.bind(this));

        propu.contenedor.querySelector('.box-header').appendChild(btn_delete);

        this.propuesta.push(propu);

        document.querySelector('#contenedor_prop #caja_prop').append(propu.contenedor);

        this.cabezales.push(propu.contenedor.querySelector('.box-header'));

        propu.contenedor.querySelector('.box-header').addEventListener('click', (event) => {
            let cont = event.target;
            do
                cont = cont.parentNode;
            while (!cont.classList.contains('section_cw'));

            cont.classList.toggle("seccion-hide");

            this.cabezales.forEach((dsd) => {
                if (cont !== dsd.parentNode) {
                    dsd.parentNode.classList.add("seccion-hide");
                }

            });
        });

        propu.editor();
    }

    quitar_prop(event) {
        let propuesta = event.target;
        do
            propuesta = propuesta.parentNode;
        while (!propuesta.classList.contains('section_prop'));

        let boton = event.target;

        while (!boton.classList.contains('btn-del-prop')) {
            boton = boton.parentNode;
        }

        let id_del = boton.id_prop;

        this.propuesta.forEach((elem, key) => {
            if (id_del === elem.id) {
                delete this.propuesta[key];
            }
        });


        document.querySelector('#contenedor_prop #caja_prop').removeChild(propuesta);

        this.cant_prop = this.propuesta.length;
    }

    showError(msj, tipo = 'warning') {
        this.alerta.classList.remove('hide');
        this.alerta.classList.add('show');
        this.alerta.classList.add('alert-' + tipo);
        this.alerta.tipo = tipo;
        this.alerta.querySelector('.msj').innerHTML = msj;
        document.querySelector('#contenedor_prop').appendChild(this.alerta);
    }

    hideError() {
        this.alerta.classList.remove('alert-' + this.alerta.tipo);
        this.alerta.tipo = '';
        this.alerta.classList.remove('show');
        this.alerta.classList.add('hide');
        this.alerta.querySelector('.msj').innerHTML = "";
        if (document.querySelector('#contenedor_prop').querySelector('.alert')) {
            document.querySelector('#contenedor_prop').removeChild(this.alerta);
        }

    }

    animar_espera(accion) {
        if (accion == 'show') {
            document.querySelector('body').appendChild(this.loader);
            this.loader.classList.add('active');
            document.querySelector('body').classList.add('body-loader-on');
        } else if (accion == 'hide') {
            this.loader.classList.remove('active');
            document.querySelector('body').removeChild(this.loader);
            document.querySelector('body').classList.remove('body-loader-on');
        }
    }
}