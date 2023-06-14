"use strict";
/// <reference path="Ajax.ts" />
/// <reference path="Empleado.ts" />
window.addEventListener("load", () => {
    ModeloParcial.Manejadora.MostrarEmpleados();
    let foto = document.getElementById("foto");
    let previsualizacion = document.getElementById("imgFoto");
    foto.addEventListener("change", () => {
        if (foto.files && foto.files[0]) {
            let reader = new FileReader();
            reader.onload = () => {
                previsualizacion.src = reader.result;
            };
            reader.readAsDataURL(foto.files[0]);
        }
    });
});
var ModeloParcial;
(function (ModeloParcial) {
    class Manejadora {
        static AgregarEmpleado() {
            let foto = document.getElementById("foto");
            let nombre = document.getElementById("nombre").value;
            let correo = document.getElementById("correo").value;
            let clave = document.getElementById("clave").value;
            let id_perfil = document.getElementById("cboPerfiles").value;
            let sueldo = document.getElementById("sueldo").value;
            let form = new FormData();
            form.append("nombre", nombre);
            form.append("correo", correo);
            form.append("clave", clave);
            form.append("id_perfil", id_perfil);
            form.append("sueldo", sueldo);
            form.append("foto", foto.files[0]);
            form.append("accion", "agregarbd");
            this.AJAX.Post(this.URL_API + "backend/AltaEmpleado.php", this.AgregarEmpleadoSucess, form, this.Fail);
        }
        static AgregarEmpleadoSucess(retorno) {
            console.log(retorno);
            let respuesta = JSON.parse(retorno);
            if (respuesta.exito) {
                Manejadora.MostrarEmpleados();
                console.log(respuesta.mensaje);
                alert(respuesta.mensaje);
            }
            else {
                console.log(respuesta.mensaje);
                alert(respuesta.mensaje);
            }
        }
        static MostrarEmpleados() {
            this.AJAX.Get(this.URL_API + "backend/ListadoEmpleados.php", this.MostrarEmpleadosSucess, "tabla=mostrar", this.Fail);
        }
        static MostrarEmpleadosSucess(retorno) {
            let div = document.getElementById("divTablaEmpleados");
            div.innerHTML = retorno;
            document.getElementsByName("btnEliminar").forEach((boton) => {
                boton.addEventListener("click", () => {
                    let id = boton.getAttribute("data-empleado");
                    if (confirm(`¿Seguro de eliminar alumno con id ${id}?`)) {
                        let form = new FormData();
                        form.append('id', id);
                        form.append('accion', "eliminarbd");
                        Manejadora.AJAX.Post(Manejadora.URL_API + "backend/EliminarEmpleado.php", Manejadora.DeleteSuccessEmpleado, form, Manejadora.Fail);
                    }
                });
            });
            document.getElementsByName("btnModificar").forEach((boton) => {
                boton.addEventListener("click", () => {
                    let obj = boton.getAttribute("data-empleado");
                    let obj_dato = JSON.parse(obj);
                    document.getElementById("id").value = obj_dato.id;
                    document.getElementById("nombre").value = obj_dato.nombre;
                    document.getElementById("correo").value = obj_dato.correo;
                    document.getElementById("clave").value = obj_dato.clave;
                    document.getElementById("cboPerfiles").value = obj_dato.id_perfil;
                    document.getElementById("sueldo").value = obj_dato.sueldo;
                    let previsualizacion = document.getElementById("imgFoto");
                    previsualizacion.src = obj_dato.foto;
                });
            });
        }
        static ModificarEmpleado() {
            let foto = document.getElementById("foto");
            let nombre = document.getElementById("nombre").value;
            let correo = document.getElementById("correo").value;
            let clave = document.getElementById("clave").value;
            let id_perfil = document.getElementById("cboPerfiles").value;
            let sueldo = document.getElementById("sueldo").value;
            let id = document.getElementById("id").value;
            let data = {
                id: id,
                correo: correo,
                nombre: nombre,
                sueldo: sueldo,
                clave: clave,
                id_perfil: id_perfil
            };
            let objJson = JSON.stringify(data);
            let form = new FormData();
            form.append("empleado_json", objJson);
            form.append("accion", "modificarbd");
            form.append("foto", foto.files[0]);
            this.AJAX.Post(this.URL_API + "backend/ModificarEmpleado.php", this.ModificarEmpleadoSucess, form, this.Fail);
        }
        static ModificarEmpleadoSucess(retorno) {
            let respuesta = JSON.parse(retorno);
            if (respuesta.exito)
                Manejadora.MostrarEmpleados();
            console.log(respuesta.mensaje);
            alert(respuesta.mensaje);
        }
        static DeleteSuccessEmpleado(retorno) {
            let respuesta = JSON.parse(retorno);
            console.log("Eliminar: ", respuesta.mensaje);
            Manejadora.MostrarEmpleados();
            alert("Eliminar:" + respuesta.mensaje);
        }
        static ModificarUsuario() {
            let nombre = document.getElementById("nombre").value;
            let id = document.getElementById("id").value;
            let correo = document.getElementById("correo").value;
            let clave = document.getElementById("clave").value;
            let id_perfil = document.getElementById("cboPerfiles").value;
            let formData = new FormData();
            let usuario = new Entidades.Usuario(parseInt(id), parseInt(id_perfil), nombre, correo, clave);
            let obj = JSON.stringify(usuario);
            formData.append("usuario_json", obj);
            this.AJAX.Post(this.URL_API + "backend/ModificarUsuario.php", this.ModificarSucess, formData, this.Fail);
        }
        static ModificarSucess(retorno) {
            let respuesta = JSON.parse(retorno);
            if (respuesta.exito == true)
                Manejadora.MostrarUsuarios();
            else {
                console.log("Modificar: " + respuesta.mensaje);
                alert("Modificar: " + respuesta.mensaje);
            }
        }
        static MostrarUsuarios() {
            this.AJAX.Get(this.URL_API + "backend/ListadoUsuarios.php", this.MostrarUsuariosSucess, "tabla=mostrar", this.Fail);
        }
        static MostrarUsuariosSucess(retorno) {
            let usuarios = JSON.parse(retorno);
            console.log(usuarios);
            let div = document.getElementById("divTabla");
            let tabla = `<table class="table table-hover">
      <tr>
          <th>ID</th><th>ID Perfil</th><th>NOMBRE</th><th>CORREO</th><th>CLAVE</th><th>Acciones</th>
      </tr>`;
            if (usuarios.length < 1) {
                tabla += `<tr><td colspan="6">No hay usuarios</td></tr>`;
            }
            else {
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
                    var _a, _b, _c, _d, _e;
                    const idInput = document.getElementById("id");
                    idInput.readOnly = true;
                    const fila = event.currentTarget;
                    const id = (_a = fila.getAttribute("data-id")) !== null && _a !== void 0 ? _a : "";
                    const idPerfil = (_b = fila.getAttribute("data-idperfil")) !== null && _b !== void 0 ? _b : "";
                    const nombre = (_c = fila.getAttribute("data-nombre")) !== null && _c !== void 0 ? _c : "";
                    const correo = (_d = fila.getAttribute("data-correo")) !== null && _d !== void 0 ? _d : "";
                    const clave = (_e = fila.getAttribute("data-clave")) !== null && _e !== void 0 ? _e : "";
                    document.getElementById("nombre").value = nombre;
                    document.getElementById("id").value = id;
                    document.getElementById("correo").value = correo;
                    document.getElementById("clave").value = clave;
                    document.getElementById("cboPerfiles").value = idPerfil;
                });
                //Evento click boton eliminar tabla
                const botonesEliminar = div.querySelectorAll("[id^='btnEliminar']");
                botonesEliminar.forEach((boton) => {
                    boton.addEventListener("click", (event) => {
                        var _a;
                        const idUsuario = event.currentTarget;
                        let id = (_a = idUsuario.getAttribute("data-id")) !== null && _a !== void 0 ? _a : "";
                        let form = new FormData();
                        form.append("accion", "asd");
                        form.append("id", id.trim());
                        Manejadora.AJAX.Post(Manejadora.URL_API + "backend/EliminarUsuario.php", Manejadora.EliminarSucces, form, Manejadora.Fail);
                    });
                });
            }
        }
        static EliminarSucces(retorno) {
            let obj = JSON.parse(retorno);
            Manejadora.MostrarUsuarios();
            console.log("Retorno: " + obj.mensaje);
            alert("Retorno: " + obj.mensaje);
        }
        static MostrarUsuariosJSON() {
            Manejadora.AJAX.Get(Manejadora.URL_API + "backend/ListadoUsuariosJSON.php", this.MostrarUsuariosJSONSucess, "", this.Fail);
        }
        static MostrarUsuariosJSONSucess(retorno) {
            let obj = JSON.parse(retorno);
            console.log(obj);
            let div = document.getElementById("divTabla");
            let tabla = `<table class="table table-hover">
                        <tr>
                            <th>NOMBRE</th><th>CORREO</th><th>CLAVE</th>
                        </tr>`;
            if (obj.length < 1) {
                tabla += `<tr><td>---</td><td>---</td><td>---</td><td>---</td>
        <td>---</td></tr>`;
            }
            else {
                obj.forEach((dato) => {
                    tabla += `<tr><td>${dato.nombre}</td><td>${dato.correo}</td><td>${dato.clave}</td></tr>`;
                });
            }
            tabla += `</table>`;
            div.innerHTML = tabla;
        }
        static AgregarUsuario() {
            let nombre = document.getElementById("nombre").value;
            let correo = document.getElementById("correo").value;
            let clave = document.getElementById("clave").value;
            let id_perfil = document.getElementById("cboPerfiles").value;
            console.log(id_perfil);
            let formData = new FormData();
            formData.append("nombre", nombre);
            formData.append("correo", correo);
            formData.append("clave", clave);
            formData.append("id_perfil", id_perfil);
            this.AJAX.Post(this.URL_API + "backend/AltaUsuario.php", this.AgregarSucess, formData, this.Fail);
        }
        static AgregarSucess(retorno) {
            console.log("Agregar: " + retorno);
            alert("Agregar:" + retorno);
        }
        static VerificarUsuarioJSON() {
            let correoInput = document.getElementById("correo").value;
            let claveInput = document.getElementById("clave").value;
            let usuario = {
                correo: correoInput,
                clave: claveInput
            };
            let form = new FormData();
            form.append("usuario_json", JSON.stringify(usuario));
            Manejadora.AJAX.Post(Manejadora.URL_API + "backend/VerificarUsuarioJSON.php", this.VerificarJSONsucess, form, this.Fail);
        }
        static VerificarJSONsucess(retorno) {
            console.log("Retorno: " + retorno);
            alert("Retorno: " + retorno);
        }
        static AgregarUsuarioJSON() {
            let nombre = document.getElementById("nombre").value;
            let correo = document.getElementById("correo").value;
            let clave = document.getElementById("clave").value;
            let formData = new FormData();
            formData.append("nombre", nombre);
            formData.append("correo", correo);
            formData.append("clave", clave);
            this.AJAX.Post(this.URL_API + "backend/AltaUsuarioJSON.php", this.AgregarJSONSucess, formData, this.Fail);
        }
        static AgregarJSONSucess(retorno) {
            console.log("Agregar: " + retorno);
            alert("Agregar:" + retorno);
        }
        static Fail(retorno) {
            console.error(retorno);
            alert("Ha ocurrido un ERROR!!!");
        }
    }
    Manejadora.AJAX = new ModeloParcial.Ajax();
    Manejadora.URL_API = "./";
    ModeloParcial.Manejadora = Manejadora;
})(ModeloParcial || (ModeloParcial = {}));
//# sourceMappingURL=Manejadora.js.map