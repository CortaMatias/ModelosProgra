"use strict";
/// <reference path="Neumatico.ts" />
var Entidades;
(function (Entidades) {
    class NeumaticoBD extends Entidades.Neumatico {
        constructor(marca, medidas, precio, id, pathFoto) {
            super(marca, medidas, precio);
            this.id = id || 0;
            this.pathFoto = pathFoto || '';
        }
        ToJSON() {
            const neumaticoJSON = super.ToJSON();
            return Object.assign(Object.assign({}, neumaticoJSON), { id: this.id, pathFoto: this.pathFoto });
        }
    }
})(Entidades || (Entidades = {}));
//# sourceMappingURL=NeumaticoBD.js.map