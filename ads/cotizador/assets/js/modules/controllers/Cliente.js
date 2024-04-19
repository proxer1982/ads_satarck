export default class Cliente {
    constructor() {
        this.contenedor = document.createElement('section');
        this.contenedor.classList.add('section_cw');
        this.contenedor.classList.add('section_datos');

        this.nombre = document.createElement('input');
        this.nombre.classList.add('form-control');
        this.nombre.id = 'first_name';
        this.nombre.required = 'required';
        this.nombre.name = this.nombre.id;
        this.nombre.setAttribute('aria-describedby', 'Ingrese el nombre del cliente');

        this.apellido = document.createElement('input');
        this.apellido.classList.add('form-control');
        this.apellido.id = 'last_name';
        this.apellido.required = 'required';
        this.apellido.name = this.apellido.id;
        this.apellido.setAttribute('aria-describedby', 'Ingrese el apellido del cliente');

        this.empresa = document.createElement('input');
        this.empresa.classList.add('form-control');
        this.empresa.id = 'company';
        this.empresa.name = this.empresa.id;

        this.tipo_cliente = document.createElement('select');
        this.tipo_cliente.innerHTML = `
          <option value="" disabled selected >Seleccionar</option>
          <option value="Alguna vez coticé sus servicio">Alguna vez cotizó</option>
          <option value="Primera vez que tengo contacto">Primera vez que cotiza</option>
          <option value="Soy excliente">Excliente</option>
          <option value="Soy cliente actual">Cliente actual</option>`;
        this.tipo_cliente.classList.add('form-select');
        this.tipo_cliente.required = 'required';
        this.tipo_cliente.id = 'type_customer';
        this.tipo_cliente.name = this.tipo_cliente.id;
        this.tipo_cliente.setAttribute('aria-describedby', 'Seleccione un tipo de cliente');

        this.ciudad = document.createElement('input');
        this.ciudad.classList.add('form-control');
        this.ciudad.id = 'last_name';
        this.ciudad.name = this.ciudad.id;

        this.email = document.createElement('input');
        this.email.classList.add('form-control');
        this.email.classList.add('opcional');
        this.email.id = 'mail_customer';
        this.email.type = 'email';
        this.email.required = 'required';
        this.email.name = this.email.id;

        this.phone = document.createElement('input');
        this.phone.classList.add('form-control');
        this.phone.classList.add('opcional');
        this.phone.id = 'phone';
        this.phone.type = 'tel';
        this.phone.placeholder = '000 000 0000';
        this.phone.pattern = "[0-9]{10}";
        this.phone.required = 'required';
        this.phone.name = this.phone.id;
        this.tipo_cliente.setAttribute('aria-describedby', 'Ingrese un número de teléfono válido');

        this.render();
    }

    render() {
        this.contenedor.innerHTML = `<div class='box-header header-prop'>
        <h2><i class="far fa-user icono"></i> Información del cliente</h2>
        </div>
        <div class='row row1 mb-2'></div>
        <div class='row row2 mb-2'></div>
        <div class='row row3'></div>`;

        let section = document.createElement('div');
        section.classList.add('col-6');
        section.innerHTML = `<label for="${this.nombre.id}" class="form-label txt-req">Nombres</label>`;

        section.appendChild(this.nombre);

        this.contenedor.querySelector('.row1').appendChild(section);


        section = document.createElement('div');
        section.classList.add('col-6');
        section.innerHTML = `<label for="${this.apellido.id}" class="form-label txt-req">Apellidos</label>`;

        section.appendChild(this.apellido);

        this.contenedor.querySelector('.row1').appendChild(section);

        section = document.createElement('div');
        section.classList.add('col-sm-9');
        section.innerHTML = `<label for="${this.empresa.id}" class="form-label">Empresa</label>`;

        section.appendChild(this.empresa);

        this.contenedor.querySelector('.row2').appendChild(section);

        section = document.createElement('div');
        section.classList.add('col-sm-3');
        section.innerHTML = `<label for="${this.tipo_cliente.id}" class="form-label txt-req">Tipo cliente</label>`;

        section.appendChild(this.tipo_cliente);

        this.contenedor.querySelector('.row2').appendChild(section);

        section = document.createElement('div');
        section.classList.add('col-sm-4');
        section.innerHTML = `<label for="${this.ciudad.id}" class="form-label">Ciudad</label>`;

        section.appendChild(this.ciudad);

        this.contenedor.querySelector('.row3').appendChild(section);

        section = document.createElement('div');
        section.classList.add('col-sm-4');
        section.innerHTML = `<label for="${this.email.id}" class="form-label txt-req-ama">Email</label>`;

        section.appendChild(this.email);

        this.contenedor.querySelector('.row3').appendChild(section);

        section = document.createElement('div');
        section.classList.add('col-sm-4');
        section.innerHTML = `<label for="${this.phone.id}" class="form-label txt-req-ama">Teléfono</label>`;

        section.appendChild(this.phone);

        this.contenedor.querySelector('.row3').appendChild(section);
    }
}