namespace Entidades {
  export class Neumatico {
    marca: string;
    medidas: string;
    precio: number;

    constructor(marca: string, medidas: string, precio: number) {
      this.marca = marca;
      this.medidas = medidas;
      this.precio = precio;
    }

    ToString(): string {
      return JSON.stringify(this.ToJSON());
    }

    ToJSON(): any {
      return {
        marca: this.marca,
        medidas: this.medidas,
        precio: this.precio,
      };
    }
  }
}