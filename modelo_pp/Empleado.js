"use strict";
/// <reference path="Usuario.ts" />
var Entidades;
(function (Entidades) {
    class Empleado extends Entidades.Usuario {
        constructor(id, sueldo, foto, id_perfil, perfil, nombre, correo, clave) {
            super(id, id_perfil, perfil, nombre, correo, clave);
            this.id = id;
            this.sueldo = sueldo;
            this.foto = foto;
        }
    }
    Entidades.Empleado = Empleado;
})(Entidades || (Entidades = {}));
//# sourceMappingURL=Empleado.js.map