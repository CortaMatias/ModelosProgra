"use strict";
/// <reference path="Persona.ts" />
var Entidades;
(function (Entidades) {
    class Usuario extends Entidades.Persona {
        constructor(id, id_perfil, nombre, correo, clave, perfil = "") {
            super(nombre, correo, clave);
            this.id = id;
            this.id_perfil = id_perfil;
            this.perfil = perfil;
        }
        ToJSON() {
            return JSON.stringify(this);
        }
    }
    Entidades.Usuario = Usuario;
})(Entidades || (Entidades = {}));
//# sourceMappingURL=Usuario.js.map