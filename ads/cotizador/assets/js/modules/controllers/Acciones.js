export default class Acciones {
    constructor() {
        console.log("desde class acciones");
        this.contenedor = document.createElement('section');
        this.contenedor.classList.add('section_cw');
        this.contenedor.classList.add('section_acciones');

        this.btn_agregarProp = document.createElement('button');
        this.btn_agregarProp.classList.add('btn');
        this.btn_agregarProp.classList.add('btn-primary');
        this.btn_agregarProp.innerHTML = '<i class="fas fa-plus"></i> Agregar propuesta';

        this.btn_guardar = document.createElement('button');
        this.btn_guardar.classList.add('btn');
        this.btn_guardar.classList.add('btn-primary');
        this.btn_guardar.innerHTML = '<i class="far fa-save"></i> Guardar';

        this.btn_descargar = document.createElement('a');
        this.btn_descargar.classList.add('btn');
        this.btn_descargar.classList.add('btn-primary');
        this.btn_descargar.classList.add('disabled');
        this.btn_descargar.href = "https://pruebas.satrack.com.pa/cotizador-web/?action=pdf_cotizacion&id_cot=1&file=file.pdf";
        this.btn_descargar.target = "_blank";
        this.btn_descargar.disabled = 'disabled';
        this.btn_descargar.innerHTML = '<i class="far fa-file-pdf"></i> Descargar PDF';

        this.btn_enviar_mail = document.createElement('button');
        this.btn_enviar_mail.classList.add('btn');
        this.btn_enviar_mail.classList.add('btn-primary');
        this.btn_enviar_mail.disabled = 'disabled';
        this.btn_enviar_mail.innerHTML = '<i class="far fa-paper-plane"></i> Enviar por mail';

        this.render();
    }

    render() {
        this.contenedor.innerHTML = `<div class='row-flex d-flex justify-content-end mb-2'></div>`;

        let section = document.createElement('div');
        section.classList.add('me-auto');

        section.appendChild(this.btn_agregarProp);

        this.contenedor.querySelector('.row-flex').appendChild(section);


        section = document.createElement('div');
        //section.classList.add('col');

        section.appendChild(this.btn_guardar);

        this.contenedor.querySelector('.row-flex').appendChild(section);


        section = document.createElement('div');
        //section.classList.add('col');

        section.appendChild(this.btn_descargar);

        this.contenedor.querySelector('.row-flex').appendChild(section);

        //section = document.createElement('div');
        //section.classList.add('col');

        //section.appendChild(this.btn_enviar_mail);

        //this.contenedor.querySelector('.row-flex').appendChild(section);
    }
}