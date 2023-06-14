"use strict";
var Entidades;
(function (Entidades) {
    class Neumatico {
        constructor(marca, medidas, precio) {
            this.marca = marca;
            this.medidas = medidas;
            this.precio = precio;
        }
        ToString() {
            return JSON.stringify(this.ToJSON());
        }
        ToJSON() {
            return {
                marca: this.marca,
                medidas: this.medidas,
                precio: this.precio,
            };
        }
    }
    Entidades.Neumatico = Neumatico;
})(Entidades || (Entidades = {}));
//# sourceMappingURL=Neumatico.js.map