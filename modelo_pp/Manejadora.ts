/// <reference path="Ajax.ts" />
/// <reference path="Empleado.ts" />

window.addEventListener("load", (): void => {
  ModeloParcial.Manejadora.MostrarEmpleados();  

  let foto: HTMLInputElement = document.getElementById("foto") as HTMLInputElement;
  let previsualizacion: HTMLImageElement = document.getElementById("imgFoto") as HTMLImageElement;

  foto.addEventListener("change", () => {
    if (foto.files && foto.files[0]) {
      let reader: FileReader = new FileReader();
      reader.onload = () => {
        previsualizacion.src = reader.result as string;
      };
      reader.readAsDataURL(foto.files[0]);
    }
  });
});

namespace ModeloParcial {
  export class Manejadora {

    static AJAX: Ajax = new Ajax();
    static URL_API: string = "./";


    public static AgregarEmpleado() {
      let foto: any = (<HTMLInputElement>document.getElementById("foto"));
      let nombre = (<HTMLInputElement>document.getElementById("nombre")).value;
      let correo = (<HTMLInputElement>document.getElementById("correo")).value;
      let clave = (<HTMLInputElement>document.getElementById("clave")).value;
      let id_perfil = (<HTMLSelectElement>document.getElementById("cboPerfiles")).value;
      let sueldo = (<HTMLSelectElement>document.getElementById("sueldo")).value;
      let form: FormData = new FormData();
      form.append("nombre", nombre);
      form.append("correo", correo);
      form.append("clave", clave);
      form.append("id_perfil", id_perfil);
      form.append("sueldo", sueldo);
      form.append("foto", foto.files[0]);
      form.append("accion", "agregarbd");

      this.AJAX.Post(this.URL_API + "backend/AltaEmpleado.php", this.AgregarEmpleadoSucess, form, this.Fail);
    }
    public static AgregarEmpleadoSucess(retorno: string) {
      console.log(retorno);
      let respuesta = JSON.parse(retorno);
      if (respuesta.exito) {
        Manejadora.MostrarEmpleados(); console.log(respuesta.mensaje); alert(respuesta.mensaje);
      }
      else { console.log(respuesta.mensaje); alert(respuesta.mensaje); }
    }

    public static MostrarEmpleados() {
      this.AJAX.Get(this.URL_API + "backend/ListadoEmpleados.php", this.MostrarEmpleadosSucess, "tabla=mostrar", this.Fail);
    }
    public static MostrarEmpleadosSucess(retorno: string) {
      let div = <HTMLDivElement>document.getElementById("divTablaEmpleados");
      div.innerHTML = retorno;
      document.getElementsByName("btnEliminar").forEach((boton)=>{
        boton.addEventListener("click", ()=>{ 
            let id : any = boton.getAttribute("data-empleado");          
            if(confirm(`¿Seguro de eliminar alumno con id ${id}?`)){                  
                let form : FormData = new FormData()
                form.append('id', id);             
                form.append('accion', "eliminarbd");             
                Manejadora.AJAX.Post(Manejadora.URL_API + "backend/EliminarEmpleado.php", 
                Manejadora.DeleteSuccessEmpleado, 
                            form, 
                            Manejadora.Fail);
            }                
        });
    }); 
      document.getElementsByName("btnModificar").forEach((boton)=>{
        boton.addEventListener("click", ()=>{ 
            let obj : any = boton.getAttribute("data-empleado");
            let obj_dato = JSON.parse(obj);
            (<HTMLInputElement>document.getElementById("id")).value = obj_dato.id;
            (<HTMLInputElement>document.getElementById("nombre")).value = obj_dato.nombre;
            (<HTMLInputElement>document.getElementById("correo")).value = obj_dato.correo;   
            (<HTMLInputElement>document.getElementById("clave")).value = obj_dato.clave; 
            (<HTMLInputElement>document.getElementById("cboPerfiles")).value = obj_dato.id_perfil; 
            (<HTMLInputElement>document.getElementById("sueldo")).value = obj_dato.sueldo;  
            let previsualizacion: HTMLImageElement = document.getElementById("imgFoto") as HTMLImageElement;
            previsualizacion.src =  obj_dato.foto;              
        });
    });
    }
    public static ModificarEmpleado(){
      let foto: any = (<HTMLInputElement>document.getElementById("foto"));
      let nombre = (<HTMLInputElement>document.getElementById("nombre")).value;
      let correo = (<HTMLInputElement>document.getElementById("correo")).value;
      let clave = (<HTMLInputElement>document.getElementById("clave")).value;
      let id_perfil = (<HTMLSelectElement>document.getElementById("cboPerfiles")).value;
      let sueldo = (<HTMLSelectElement>document.getElementById("sueldo")).value;
      let id = (<HTMLSelectElement>document.getElementById("id")).value;
      let data = {
        id : id,
        correo : correo,
        nombre : nombre,
        sueldo : sueldo,
        clave : clave,
        id_perfil : id_perfil
      }
      let objJson = JSON.stringify(data);
      let form : FormData = new FormData();
      form.append("empleado_json", objJson);
      form.append("accion", "modificarbd");
      form.append("foto", foto.files[0]);
      this.AJAX.Post(this.URL_API + "backend/ModificarEmpleado.php", this.ModificarEmpleadoSucess,form,this.Fail);
    }
    public static ModificarEmpleadoSucess(retorno : string ){
      let respuesta = JSON.parse(retorno);
      if(respuesta.exito) Manejadora.MostrarEmpleados();
      console.log(respuesta.mensaje);
      alert(respuesta.mensaje);
    }

    public static DeleteSuccessEmpleado(retorno:string)
    {             
        let respuesta = JSON.parse(retorno);
        console.log("Eliminar: ", respuesta.mensaje);        
        Manejadora.MostrarEmpleados();
        alert("Eliminar:"+respuesta.mensaje);
    }

    public static ModificarUsuario() {
      let nombre = (<HTMLInputElement>document.getElementById("nombre")).value;
      let id = (<HTMLInputElement>document.getElementById("id")).value;
      let correo = (<HTMLInputElement>document.getElementById("correo")).value;
      let clave = (<HTMLInputElement>document.getElementById("clave")).value;
      let id_perfil = (<HTMLSelectElement>document.getElementById("cboPerfiles")).value;
      let formData: FormData = new FormData();
      let usuario: Entidades.Usuario = new Entidades.Usuario(parseInt(id), parseInt(id_perfil), nombre, correo, clave);
      let obj = JSON.stringify(usuario);
      formData.append("usuario_json", obj);
      this.AJAX.Post(this.URL_API + "backend/ModificarUsuario.php", this.ModificarSucess, formData, this.Fail);
    }
    public static ModificarSucess(retorno: string) {
      let respuesta = JSON.parse(retorno);
      if (respuesta.exito == true) Manejadora.MostrarUsuarios();
      else {
        console.log("Modificar: " + respuesta.mensaje);
        alert("Modificar: " + respuesta.mensaje);
      }

    }

    public static MostrarUsuarios() {
      this.AJAX.Get(this.URL_API + "backend/ListadoUsuarios.php", this.MostrarUsuariosSucess, "tabla=mostrar", this.Fail);
    }
    public static MostrarUsuariosSucess(retorno: string) {
      let usuarios: Entidades.Usuario[] = JSON.parse(retorno);
      console.log(usuarios);
      let div = <HTMLDivElement>document.getElementById("divTabla");
      let tabla = `<table class="table table-hover">
      <tr>
          <th>ID</th><th>ID Perfil</th><th>NOMBRE</th><th>CORREO</th><th>CLAVE</th><th>Acciones</th>
      </tr>`;

      if (usuarios.length < 1) {
        tabla += `<tr><td colspan="6">No hay usuarios</td></tr>`;
      } else {
        usuarios.forEach((usuario) => {
          tabla += `<tr data-id="${usuario.id}" data-idperfil="${usuario.id_perfil}" data-nombre="${usuario.nombre}" data-correo="${usuario.correo}" data-clave="${usuario.clave}">
            <td>${usuario.id}</td>
            <td>${usuario.id_perfil}</td>
            <td>${usuario.nombre}</td>
            <td>${usuario.correo}</td>
            <td>${usuario.clave}</td>
            <td><button id="btnEliminar" data-id="${usuario.id}">Eliminar</button></td>
            </tr>`;
        });
      }

      tabla += `</table>`;
      div.innerHTML = tabla;

      // Evento click en las filas de la tabla
      const filas = div.getElementsByTagName("tr");
      for (let i = 1; i < filas.length; i++) { //INICIO EN 1 PARA EVITAR EL ENCABEZADO
        filas[i].addEventListener("click", (event) => {
          const idInput = document.getElementById("id") as HTMLInputElement;
          idInput.readOnly = true;
          const fila = event.currentTarget as HTMLTableRowElement;
          const id = fila.getAttribute("data-id") ?? "";
          const idPerfil = fila.getAttribute("data-idperfil") ?? "";
          const nombre = fila.getAttribute("data-nombre") ?? "";
          const correo = fila.getAttribute("data-correo") ?? "";
          const clave = fila.getAttribute("data-clave") ?? "";
          (<HTMLInputElement>document.getElementById("nombre")).value = nombre;
          (<HTMLInputElement>document.getElementById("id")).value = id;
          (<HTMLInputElement>document.getElementById("correo")).value = correo;
          (<HTMLInputElement>document.getElementById("clave")).value = clave;
          (<HTMLSelectElement>document.getElementById("cboPerfiles")).value = idPerfil;
        });
        //Evento click boton eliminar tabla
        const botonesEliminar = div.querySelectorAll("[id^='btnEliminar']");
        botonesEliminar.forEach((boton) => {
          boton.addEventListener("click", (event) => {
            const idUsuario = event.currentTarget as HTMLButtonElement;
            let id = idUsuario.getAttribute("data-id") ?? "";
            let form: FormData = new FormData();
            form.append("accion", "asd");
            form.append("id", id.trim());
            Manejadora.AJAX.Post(Manejadora.URL_API + "backend/EliminarUsuario.php", Manejadora.EliminarSucces, form, Manejadora.Fail);
          });
        });
      }
    }
    public static EliminarSucces(retorno: string) {
      let obj = JSON.parse(retorno);
      Manejadora.MostrarUsuarios();
      console.log("Retorno: " + obj.mensaje);
      alert("Retorno: " + obj.mensaje);
    }


    public static MostrarUsuariosJSON() {
      Manejadora.AJAX.Get(Manejadora.URL_API + "backend/ListadoUsuariosJSON.php", this.MostrarUsuariosJSONSucess, "", this.Fail);
    }
    public static MostrarUsuariosJSONSucess(retorno: string) {
      let obj: any[] = JSON.parse(retorno);
      console.log(obj);
      let div = <HTMLDivElement>document.getElementById("divTabla");
      let tabla = `<table class="table table-hover">
                        <tr>
                            <th>NOMBRE</th><th>CORREO</th><th>CLAVE</th>
                        </tr>`;
      if (obj.length < 1) {
        tabla += `<tr><td>---</td><td>---</td><td>---</td><td>---</td>
        <td>---</td></tr>`;
      } else {
        obj.forEach((dato) => {
          tabla += `<tr><td>${dato.nombre}</td><td>${dato.correo}</td><td>${dato.clave}</td></tr>`;
        })
      }
      tabla += `</table>`;
      div.innerHTML = tabla;
    }

    public static AgregarUsuario() {
      let nombre = (<HTMLInputElement>document.getElementById("nombre")).value;
      let correo = (<HTMLInputElement>document.getElementById("correo")).value;
      let clave = (<HTMLInputElement>document.getElementById("clave")).value;
      let id_perfil = (<HTMLSelectElement>document.getElementById("cboPerfiles")).value;
      console.log(id_perfil);
      let formData: FormData = new FormData();
      formData.append("nombre", nombre);
      formData.append("correo", correo);
      formData.append("clave", clave);
      formData.append("id_perfil", id_perfil);
      this.AJAX.Post(this.URL_API + "backend/AltaUsuario.php", this.AgregarSucess, formData, this.Fail);
    }
    public static AgregarSucess(retorno: string) {
      console.log("Agregar: " + retorno);
      alert("Agregar:" + retorno);
    }

    public static VerificarUsuarioJSON() {
      let correoInput = (<HTMLInputElement>document.getElementById("correo")).value;
      let claveInput = (<HTMLInputElement>document.getElementById("clave")).value;
      let usuario = {
        correo: correoInput,
        clave: claveInput
      };
      let form: FormData = new FormData();
      form.append("usuario_json", JSON.stringify(usuario));
      Manejadora.AJAX.Post(Manejadora.URL_API + "backend/VerificarUsuarioJSON.php", this.VerificarJSONsucess, form, this.Fail);
    }
    public static VerificarJSONsucess(retorno: string) {
      console.log("Retorno: " + retorno);
      alert("Retorno: " + retorno);
    }

    public static AgregarUsuarioJSON() {
      let nombre = (<HTMLInputElement>document.getElementById("nombre")).value;
      let correo = (<HTMLInputElement>document.getElementById("correo")).value;
      let clave = (<HTMLInputElement>document.getElementById("clave")).value;
      let formData = new FormData();
      formData.append("nombre", nombre);
      formData.append("correo", correo);
      formData.append("clave", clave);
      this.AJAX.Post(this.URL_API + "backend/AltaUsuarioJSON.php", this.AgregarJSONSucess, formData, this.Fail);
    }
    public static AgregarJSONSucess(retorno: string) {
      console.log("Agregar: " + retorno);
      alert("Agregar:" + retorno);
    }



    public static Fail(retorno: string): void {
      console.error(retorno);
      alert("Ha ocurrido un ERROR!!!");
    }


  }
}