"use strict";
/// <reference path="NeumaticoBD.ts" />
/// <reference path="IParte2.ts" />
/// <reference path="IParte3.ts" />
/// <reference path="IParte4.ts" />
window.addEventListener("load", () => {
    PrimerParcial.Manejadora.MostrarNeumaticosBD();
    let foto = document.getElementById("foto");
    let previsualizacion = document.getElementById("imgSpinner");
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
var PrimerParcial;
(function (PrimerParcial) {
    class Manejadora {
        static AgregarNeumaticoJSON() {
            (new Manejadora()).AgregarNeumaticoJSON();
        }
        AgregarNeumaticoJSON() {
            let marca = document.getElementById("marca").value;
            let medidas = document.getElementById("medidas").value;
            let precio = document.getElementById("precio").value;
            let formData = new FormData();
            formData.append("marca", marca);
            formData.append("medidas", medidas);
            formData.append("precio", precio);
            Manejadora.AJAX.Post(Manejadora.URL_API + "backend/altaNeumaticoJSON.php", Manejadora.AltaNuematicoJSONSuccess, formData, Manejadora.Fail);
        }
        static AltaNuematicoJSONSuccess(respuesta) {
            console.log(respuesta);
            let obj = JSON.parse(respuesta);
            console.log(obj);
            alert(obj.mensaje);
            if (obj.exito)
                Manejadora.MostrarNuematicosJSON();
        }
        static MostrarNuematicosJSON() {
            (new Manejadora()).MostrarNuematicosJSON();
        }
        MostrarNuematicosJSON() {
            Manejadora.AJAX.Get(Manejadora.URL_API + "backend/listadoNeumaticosJSON.php", Manejadora.listadoNuematicosJSONSucess, "", Manejadora.Fail);
        }
        static listadoNuematicosJSONSucess(respuesta) {
            console.log(respuesta);
            let neumaticos = JSON.parse(respuesta);
            let tablaHTML = "<table style='border-collapse: collapse; width: 100%; padding: 5px; margin: 5px;'>";
            tablaHTML += "<thead style='border: 1px solid black;'><tr><th style='border: 1px solid black;'>Marca</th><th style='border: 1px solid black;'>Medidas</th><th style='border: 1px solid black;'>Precio</th></tr></thead>";
            tablaHTML += "<tbody>";
            for (const neumatico of neumaticos) {
                tablaHTML += `<tr><td style='border: 1px solid black; text-align: center;'>${neumatico.marca}</td><td style='border: 1px solid black; text-align: center;'>${neumatico.medidas}</td><td style='border: 1px solid black; text-align: center;'>${neumatico.precio}</td></tr>`;
            }
            tablaHTML += "</tbody></table>";
            let div = document.getElementById("divTabla");
            div.innerHTML = tablaHTML;
        }
        static VerificarNeumatiJSON() {
            (new Manejadora()).VerificarNeumaticoJSON();
        }
        VerificarNeumaticoJSON() {
            let marca = document.getElementById("marca").value;
            let medidas = document.getElementById("medidas").value;
            let formData = new FormData();
            formData.append("marca", marca);
            formData.append("medidas", medidas);
            Manejadora.AJAX.Post(Manejadora.URL_API + "backend/verificarNeumaticoJSON.php", Manejadora.VerificarNeumaticoJSONSuccess, formData, Manejadora.Fail);
        }
        static VerificarNeumaticoJSONSuccess(respuesta) {
            console.log(respuesta);
            let obj = JSON.parse(respuesta);
            console.log(obj);
            alert(obj.mensaje);
        }
        static AgregarNeumaticoSinFoto() {
            (new Manejadora()).AgregarNeumaticoSinFoto();
        }
        AgregarNeumaticoSinFoto() {
            let marca = document.getElementById("marca").value;
            let medidas = document.getElementById("medidas").value;
            let precio = document.getElementById("precio").value;
            let foto = document.getElementById("foto");
            let formData = new FormData();
            let neumatico = new Entidades.Neumatico(marca, medidas, parseInt(precio));
            let data = {
                marca: marca,
                precio: precio,
                medidas: medidas
            };
            formData.append("marca", marca);
            formData.append("medidas", medidas);
            formData.append("precio", precio);
            formData.append("neumatico_json", JSON.stringify(neumatico));
            Manejadora.AJAX.Post(Manejadora.URL_API + "backend/agregarNeumaticoSinFoto.php", Manejadora.AltaNuematicoBDSuccess, formData, Manejadora.Fail);
        }
        static AltaNuematicoBDSuccess(respuesta) {
            console.log(respuesta);
            let obj = JSON.parse(respuesta);
            console.log(obj);
            alert(obj.mensaje);
            if (obj.exito)
                Manejadora.MostrarNeumaticosBD();
        }
        static MostrarNeumaticosBD() {
            (new Manejadora()).MostrarNeumaticosBD();
        }
        MostrarNeumaticosBD() {
            Manejadora.AJAX.Get(Manejadora.URL_API + "backend/listadoNeumaticosBD.php", Manejadora.ListadoNeumaticosBDSucess, "tabla=mostrar", Manejadora.Fail);
        }
        static ListadoNeumaticosBDSucess(respuesta) {
            let div = document.getElementById("divTabla");
            div.innerHTML = respuesta;
            console.log(respuesta);
            let foto = document.getElementById("foto");
            Manejadora.EliminarNeumatico();
            Manejadora.CargarInfo();
        }
        static CargarInfo() {
            document.getElementsByName("btnModificar").forEach((boton) => {
                boton.addEventListener("click", () => {
                    const jsonData = boton.getAttribute("data-json");
                    const data = JSON.parse(jsonData !== null && jsonData !== void 0 ? jsonData : "");
                    document.getElementById("idNeumatico").value = data.id;
                    document.getElementById("medidas").value = data.medidas;
                    document.getElementById("precio").value = data.precio;
                    document.getElementById("marca").value = data.marca;
                });
            });
        }
        static EliminarNeumatico() {
            (new Manejadora()).EliminarNeumatico();
        }
        EliminarNeumatico() {
            document.getElementsByName("btnEliminar").forEach((boton) => {
                boton.addEventListener("click", () => {
                    const jsonData = boton.getAttribute("data-json");
                    const data = JSON.parse(jsonData !== null && jsonData !== void 0 ? jsonData : "");
                    if (confirm(`¿Seguro de eliminar el neumatico con id ${data.id}?`)) {
                        let form = new FormData();
                        console.log(data.pathFoto);
                        if (data.pathFoto.trim() == "sin foto") {
                            form.append("neumatico_json", JSON.stringify(data));
                            Manejadora.AJAX.Post(Manejadora.URL_API + "backend/eliminarNeumaticoBD.php", Manejadora.EliminarNeumaticoBDSucess, form, Manejadora.Fail);
                        }
                        else {
                            Manejadora.BorrarNeumaticoFoto(data);
                        }
                    }
                });
            });
        }
        static EliminarNeumaticoBDSucess(respuesta) {
            console.log(respuesta);
            let retorno = JSON.parse(respuesta);
            console.log(retorno.mensaje);
            alert(retorno.mensaje);
            if (retorno.exito)
                Manejadora.MostrarNeumaticosBD();
        }
        static ModificarNeumatico() {
            (new Manejadora()).ModificarNeumatico();
        }
        ModificarNeumatico() {
            let marca = document.getElementById("marca").value;
            let medidas = document.getElementById("medidas").value;
            let precio = document.getElementById("precio").value;
            let id = document.getElementById("idNeumatico").value;
            let foto = document.getElementById("foto");
            let formData = new FormData();
            let data = {
                marca: marca,
                precio: precio,
                medidas: medidas,
                id: id
            };
            formData.append("neumatico_json", JSON.stringify(data));
            Manejadora.AJAX.Post(Manejadora.URL_API + "backend/modificarNeumaticoBD.php", Manejadora.ModificarNeumaticosBDSucess, formData, Manejadora.Fail);
        }
        static ModificarNeumaticosBDSucess(respuesta) {
            console.log(respuesta);
            let retorno = JSON.parse(respuesta);
            console.log(retorno.mensaje);
            alert(retorno.mensaje);
            if (retorno.exito)
                Manejadora.MostrarNeumaticosBD();
        }
        static VerificarNeumaticoBD() {
            (new Manejadora()).VerificarNeumaticoBD();
        }
        VerificarNeumaticoBD() {
            let marca = document.getElementById("marca").value;
            let medidas = document.getElementById("medidas").value;
            let formData = new FormData();
            let data = {
                marca: marca,
                medidas: medidas
            };
            formData.append("obj_neumatico", JSON.stringify(data));
            Manejadora.AJAX.Post(Manejadora.URL_API + "backend/verificarNeumaticoBD.php", Manejadora.VerificarNeumaticoBDSucess, formData, Manejadora.Fail);
        }
        static VerificarNeumaticoBDSucess(retorno) {
            console.log(retorno);
            if (retorno.trim() == "{}{}") {
                alert("No se encontro el neumatico en la base de datos");
                console.log("No se encontro el neumatico en la base de datos");
            }
            else {
                alert("Neumatico encontrado ");
                console.log("Neumatico encontrado ");
            }
        }
        static AgregarNeumaticoFoto() {
            (new Manejadora()).AgregarNeumaticoFoto();
        }
        AgregarNeumaticoFoto() {
            let marca = document.getElementById("marca").value;
            let medidas = document.getElementById("medidas").value;
            let precio = document.getElementById("precio").value;
            let foto = document.getElementById("foto");
            let formData = new FormData();
            let data = {
                marca: marca,
                precio: precio,
                medidas: medidas
            };
            formData.append("marca", marca);
            formData.append("medidas", medidas);
            formData.append("precio", precio);
            formData.append("foto", foto.files[0]);
            Manejadora.AJAX.Post(Manejadora.URL_API + "backend/agregarNeumaticoBD.php", Manejadora.AltaNuematicoBDSuccess, formData, Manejadora.Fail);
        }
        static BorrarNeumaticoFoto(data) {
            (new Manejadora()).BorrarNeumaticoFoto(data);
        }
        BorrarNeumaticoFoto(data) {
            let form = new FormData();
            form.append("neumatico_json", JSON.stringify(data));
            Manejadora.AJAX.Post(Manejadora.URL_API + "backend/eliminarNeumaticoBDFoto.php", Manejadora.EliminarNeumaticoBDSucess, form, Manejadora.Fail);
        }
        static ModificarNeumaticoBDFoto() {
            (new Manejadora()).ModificarNeumaticoBDFoto();
        }
        ModificarNeumaticoBDFoto() {
            let marca = document.getElementById("marca").value;
            let medidas = document.getElementById("medidas").value;
            let precio = document.getElementById("precio").value;
            let id = document.getElementById("idNeumatico").value;
            let foto = document.getElementById("foto");
            let formData = new FormData();
            let data = {
                marca: marca,
                precio: precio,
                medidas: medidas,
                id: id
            };
            formData.append("neumatico_json", JSON.stringify(data));
            if (foto.files && foto.files[0]) {
                formData.append("foto", foto.files[0]);
                Manejadora.AJAX.Post(Manejadora.URL_API + "backend/modificarNeumaticoBDFoto.php", Manejadora.ModificarNeumaticosBDSucess, formData, Manejadora.Fail);
            }
            else {
                Manejadora.AJAX.Post(Manejadora.URL_API + "backend/modificarNeumaticoBD.php", Manejadora.ModificarNeumaticosBDSucess, formData, Manejadora.Fail);
            }
        }
        static MostrarBorradosJSON() {
            (new Manejadora()).MostrarBorradosJSON();
        }
        MostrarBorradosJSON() {
            Manejadora.AJAX.Get(Manejadora.URL_API + "backend/mostrarBorradosJSON.php", Manejadora.MostrarBorradosJSONSucess, "", Manejadora.Fail);
        }
        static MostrarBorradosJSONSucess(retorno) {
            console.log(retorno);
            let div = document.getElementById("divTabla");
            div.innerHTML = retorno;
        }
        static MostrarModificados() {
            (new Manejadora()).MostrarModificados();
        }
        MostrarModificados() {
            Manejadora.AJAX.Get(Manejadora.URL_API + "backend/mostrarFotosDeModificados.php", Manejadora.MostrarModificadosSucess, "", Manejadora.Fail);
        }
        static MostrarModificadosSucess(retorno) {
            console.log(retorno);
            let div = document.getElementById("divTabla");
            div.innerHTML = retorno;
        }
        static Fail(retorno) {
            console.error(retorno);
            alert("Ha ocurrido un ERROR!!!");
        }
    }
    Manejadora.AJAX = new PrimerParcial.Ajax();
    Manejadora.URL_API = "./";
    PrimerParcial.Manejadora = Manejadora;
})(PrimerParcial || (PrimerParcial = {}));
//# sourceMappingURL=Manejadora.js.map