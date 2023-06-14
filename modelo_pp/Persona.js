"use strict";
var Entidades;
(function (Entidades) {
    class Persona {
        constructor(nombre, correo, clave) {
            this.nombre = nombre;
            this.correo = correo;
            this.clave = clave;
        }
        ToString() {
            return `Nombre: ${this.nombre}, Correo: ${this.correo}, Clave: ${this.clave}`;
        }
        ToJSON() {
            return JSON.stringify(this);
        }
    }
    Entidades.Persona = Persona;
})(Entidades || (Entidades = {}));
//# sourceMappingURL=Persona.js.map