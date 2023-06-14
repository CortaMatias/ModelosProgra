/// <reference path="NeumaticoBD.ts" />
/// <reference path="IParte2.ts" />
/// <reference path="IParte3.ts" />
/// <reference path="IParte4.ts" />

window.addEventListener("load", (): void => {
  PrimerParcial.Manejadora.MostrarNeumaticosBD();  

  let foto: HTMLInputElement = document.getElementById("foto") as HTMLInputElement;
  let previsualizacion: HTMLImageElement = document.getElementById("imgSpinner") as HTMLImageElement;

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

namespace PrimerParcial {
  export class Manejadora implements Entidades.IParte2 , Entidades.IParte3, Entidades.IParte4{

    static AJAX: Ajax = new Ajax();
    static URL_API: string = "./";

    public static AgregarNeumaticoJSON(){
      (new Manejadora()).AgregarNeumaticoJSON();
    }
    AgregarNeumaticoJSON(){
      let marca = (<HTMLInputElement>document.getElementById("marca")).value;
      let medidas = (<HTMLInputElement>document.getElementById("medidas")).value;
      let precio = (<HTMLInputElement>document.getElementById("precio")).value;
      let formData: FormData = new FormData();
      formData.append("marca", marca);
      formData.append("medidas", medidas);
      formData.append("precio", precio);
      Manejadora.AJAX.Post(Manejadora.URL_API + "backend/altaNeumaticoJSON.php", Manejadora.AltaNuematicoJSONSuccess, formData, Manejadora.Fail);
    }
    public static AltaNuematicoJSONSuccess(respuesta: string) {
      console.log(respuesta);
      let obj = JSON.parse(respuesta);
      console.log(obj);
      alert(obj.mensaje);
      if(obj.exito)Manejadora.MostrarNuematicosJSON();
    }
    public static MostrarNuematicosJSON(){
      (new Manejadora()).MostrarNuematicosJSON();
    }
    MostrarNuematicosJSON() {
      Manejadora.AJAX.Get(Manejadora.URL_API + "backend/listadoNeumaticosJSON.php", Manejadora.listadoNuematicosJSONSucess, "", Manejadora.Fail);
    }
    public static listadoNuematicosJSONSucess(respuesta: string) {
      console.log(respuesta);
      let neumaticos = JSON.parse(respuesta);
      let tablaHTML = "<table style='border-collapse: collapse; width: 100%; padding: 5px; margin: 5px;'>";
      tablaHTML += "<thead style='border: 1px solid black;'><tr><th style='border: 1px solid black;'>Marca</th><th style='border: 1px solid black;'>Medidas</th><th style='border: 1px solid black;'>Precio</th></tr></thead>";
      tablaHTML += "<tbody>";

      for (const neumatico of neumaticos) {
        tablaHTML += `<tr><td style='border: 1px solid black; text-align: center;'>${neumatico.marca}</td><td style='border: 1px solid black; text-align: center;'>${neumatico.medidas}</td><td style='border: 1px solid black; text-align: center;'>${neumatico.precio}</td></tr>`;
      }

      tablaHTML += "</tbody></table>";
      let div = <HTMLDivElement>document.getElementById("divTabla");
      div.innerHTML = tablaHTML;
    }

    public static VerificarNeumaticoJSON(){
      (new Manejadora()).VerificarNeumaticoJSON();
    }
    VerificarNeumaticoJSON() {
      let marca = (<HTMLInputElement>document.getElementById("marca")).value;
      let medidas = (<HTMLInputElement>document.getElementById("medidas")).value;
      let formData: FormData = new FormData();
      formData.append("marca", marca);
      formData.append("medidas", medidas);
      Manejadora.AJAX.Post(Manejadora.URL_API + "backend/verificarNeumaticoJSON.php", Manejadora.VerificarNeumaticoJSONSuccess, formData, Manejadora.Fail);
    }
    public static VerificarNeumaticoJSONSuccess(respuesta: string) {
      console.log(respuesta);
      let obj = JSON.parse(respuesta);
      console.log(obj);
      alert(obj.mensaje);
    }

    public static AgregarNeumaticoSinFoto(){
      (new Manejadora()).AgregarNeumaticoSinFoto();
    }
     AgregarNeumaticoSinFoto() {
      let marca = (<HTMLInputElement>document.getElementById("marca")).value;
      let medidas = (<HTMLInputElement>document.getElementById("medidas")).value;
      let precio = (<HTMLInputElement>document.getElementById("precio")).value;
      let foto: any = (<HTMLInputElement>document.getElementById("foto"));
      let formData: FormData = new FormData();
      let neumatico: Entidades.Neumatico = new Entidades.Neumatico(marca, medidas, parseInt(precio));
      let data: any = {
        marca: marca,
        precio: precio,
        medidas: medidas
      }
      formData.append("marca", marca);
      formData.append("medidas", medidas);
      formData.append("precio", precio);
      formData.append("neumatico_json", JSON.stringify(neumatico));
      Manejadora.AJAX.Post(Manejadora.URL_API + "backend/agregarNeumaticoSinFoto.php", Manejadora.AltaNuematicoBDSuccess, formData, Manejadora.Fail);

    }
    public static AltaNuematicoBDSuccess(respuesta: string) {
      console.log(respuesta);
      let obj = JSON.parse(respuesta);
      console.log(obj);
      alert(obj.mensaje);
      if(obj.exito)Manejadora.MostrarNeumaticosBD();
    }


    public static MostrarNeumaticosBD() {
      (new Manejadora()).MostrarNeumaticosBD();
    }
     MostrarNeumaticosBD() {
      Manejadora.AJAX.Get(Manejadora.URL_API + "backend/listadoNeumaticosBD.php", Manejadora.ListadoNeumaticosBDSucess, "tabla=mostrar", Manejadora.Fail);
    }
    public static ListadoNeumaticosBDSucess(respuesta: string) {
      let div = <HTMLDivElement>document.getElementById("divTabla");
      div.innerHTML = respuesta;
      console.log(respuesta);
      let foto: any = (<HTMLInputElement>document.getElementById("foto"));
      Manejadora.EliminarNeumatico();
      Manejadora.CargarInfo();
    }

    public static CargarInfo(){
      document.getElementsByName("btnModificar").forEach((boton) => {
        boton.addEventListener("click", () => {
          const jsonData = boton.getAttribute("data-json");
          const data = JSON.parse(jsonData ?? "");
          (<HTMLInputElement>document.getElementById("idNeumatico")).value = data.id;
          (<HTMLInputElement>document.getElementById("medidas")).value = data.medidas;
          (<HTMLInputElement>document.getElementById("precio")).value = data.precio;
          (<HTMLInputElement>document.getElementById("marca")).value = data.marca;
        });
      });
    }

    public static EliminarNeumatico(){
      (new Manejadora()).EliminarNeumatico();
    }
    EliminarNeumatico() {
      document.getElementsByName("btnEliminar").forEach((boton) => {
        boton.addEventListener("click", () => {
          const jsonData = boton.getAttribute("data-json");
          const data = JSON.parse(jsonData ?? "");          
          if (confirm(`¿Seguro de eliminar el neumatico con id ${data.id}?`)) {
            let form: FormData = new FormData();
            console.log(data.pathFoto);
            if(data.pathFoto.trim()=="sin foto"){
              form.append("neumatico_json", JSON.stringify(data));
              Manejadora.AJAX.Post(Manejadora.URL_API + "backend/eliminarNeumaticoBD.php",
                Manejadora.EliminarNeumaticoBDSucess,
                form,
                Manejadora.Fail);
            }else {
              Manejadora.BorrarNeumaticoFoto(data);
            }
            
           
          }
        });
      });
    }
    public static EliminarNeumaticoBDSucess(respuesta: string) {
      console.log(respuesta);
      let retorno = JSON.parse(respuesta);
      console.log(retorno.mensaje);
      alert(retorno.mensaje);
      if (retorno.exito) Manejadora.MostrarNeumaticosBD();
    }

    public static ModificarNeumatico(){
      (new Manejadora()).ModificarNeumatico();
    }
    ModificarNeumatico() {
      let marca = (<HTMLInputElement>document.getElementById("marca")).value;
      let medidas = (<HTMLInputElement>document.getElementById("medidas")).value;
      let precio = (<HTMLInputElement>document.getElementById("precio")).value;
      let id = (<HTMLInputElement>document.getElementById("idNeumatico")).value;
      let foto: any = (<HTMLInputElement>document.getElementById("foto"));
      let formData: FormData = new FormData();
      let data: any = {
        marca: marca,
        precio: precio,
        medidas: medidas,
        id: id
      }
      formData.append("neumatico_json", JSON.stringify(data));
        Manejadora.AJAX.Post(Manejadora.URL_API + "backend/modificarNeumaticoBD.php",
          Manejadora.ModificarNeumaticosBDSucess,
          formData,
          Manejadora.Fail);      
    }
    public static ModificarNeumaticosBDSucess(respuesta: string) {
      console.log(respuesta);
      let retorno = JSON.parse(respuesta);
      console.log(retorno.mensaje);
      alert(retorno.mensaje);
      if (retorno.exito) Manejadora.MostrarNeumaticosBD();
    }

    public static VerificarNeumaticoBD(){
      (new Manejadora()).VerificarNeumaticoBD();
    }
    VerificarNeumaticoBD(){
      let marca = (<HTMLInputElement>document.getElementById("marca")).value;
      let medidas = (<HTMLInputElement>document.getElementById("medidas")).value;
      let formData: FormData = new FormData();
      let data = {
        marca: marca,
        medidas: medidas
      };
      formData.append("obj_neumatico", JSON.stringify(data));
      Manejadora.AJAX.Post(Manejadora.URL_API + "backend/verificarNeumaticoBD.php",
        Manejadora.VerificarNeumaticoBDSucess,
        formData,
        Manejadora.Fail);
    }
    public static VerificarNeumaticoBDSucess(retorno: string) {
      console.log(retorno);
      if (retorno.trim() == "{}{}") { 
        alert("No se encontro el neumatico en la base de datos");
        console.log("No se encontro el neumatico en la base de datos");
      } else {
        alert("Neumatico encontrado ");
        console.log("Neumatico encontrado ");
      }
    }

    public static AgregarNeumaticoFoto(){
      (new Manejadora()).AgregarNeumaticoFoto();
    }
    AgregarNeumaticoFoto(){
      let marca = (<HTMLInputElement>document.getElementById("marca")).value;
      let medidas = (<HTMLInputElement>document.getElementById("medidas")).value;
      let precio = (<HTMLInputElement>document.getElementById("precio")).value;
      let foto: any = (<HTMLInputElement>document.getElementById("foto"));
      let formData: FormData = new FormData();
      let data: any = {
        marca: marca,
        precio: precio,
        medidas: medidas
      }
      formData.append("marca",marca );
      formData.append("medidas",medidas );
      formData.append("precio",precio);
      formData.append("foto", foto.files[0]);
      Manejadora.AJAX.Post(Manejadora.URL_API + "backend/agregarNeumaticoBD.php", Manejadora.AltaNuematicoBDSuccess, formData, Manejadora.Fail);
    }

    public static BorrarNeumaticoFoto(data : any) {
      (new Manejadora()).BorrarNeumaticoFoto(data);
    }
    BorrarNeumaticoFoto(data : any){
      let form: FormData = new FormData();
      form.append("neumatico_json", JSON.stringify(data));
      Manejadora.AJAX.Post(Manejadora.URL_API + "backend/eliminarNeumaticoBDFoto.php",
        Manejadora.EliminarNeumaticoBDSucess,
        form,
        Manejadora.Fail);
    }

    public static ModificarNeumaticoBDFoto(){
      (new Manejadora()).ModificarNeumaticoBDFoto();
    }
    ModificarNeumaticoBDFoto(){
      let marca = (<HTMLInputElement>document.getElementById("marca")).value;
      let medidas = (<HTMLInputElement>document.getElementById("medidas")).value;
      let precio = (<HTMLInputElement>document.getElementById("precio")).value;
      let id = (<HTMLInputElement>document.getElementById("idNeumatico")).value;
      let foto: any = (<HTMLInputElement>document.getElementById("foto"));
      let formData: FormData = new FormData();
      let data: any = {
        marca: marca,
        precio: precio,
        medidas: medidas,
        id: id
      }
      formData.append("neumatico_json", JSON.stringify(data));
      if(foto.files && foto.files[0]){             
        formData.append("foto", foto.files[0]);
        Manejadora.AJAX.Post(Manejadora.URL_API + "backend/modificarNeumaticoBDFoto.php",
          Manejadora.ModificarNeumaticosBDSucess,
          formData,
          Manejadora.Fail);
      }else{
        Manejadora.AJAX.Post(Manejadora.URL_API + "backend/modificarNeumaticoBD.php",
          Manejadora.ModificarNeumaticosBDSucess,
          formData,
          Manejadora.Fail);
      }   
    }


    public static MostrarBorradosJSON(){
      (new Manejadora()).MostrarBorradosJSON();
    }
    MostrarBorradosJSON(){
      Manejadora.AJAX.Get(Manejadora.URL_API + "backend/mostrarBorradosJSON.php", Manejadora.MostrarBorradosJSONSucess,"",Manejadora.Fail);
    }
    public static MostrarBorradosJSONSucess(retorno : string){
      console.log(retorno);
      let div = <HTMLDivElement>document.getElementById("divTabla");
      div.innerHTML = retorno;      
    }

    public static MostrarModificados(){
      (new Manejadora()).MostrarModificados();
    }
    MostrarModificados(){
      Manejadora.AJAX.Get(Manejadora.URL_API + "backend/mostrarFotosDeModificados.php", Manejadora.MostrarModificadosSucess,"",Manejadora.Fail);
    }
    public static MostrarModificadosSucess(retorno : string){
      console.log(retorno);
      let div = <HTMLDivElement>document.getElementById("divTabla");
      div.innerHTML = retorno;      
    }




    public static Fail(retorno: string): void {
      console.error(retorno);
      alert("Ha ocurrido un ERROR!!!");
    }
  }
}