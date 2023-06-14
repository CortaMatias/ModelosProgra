namespace Entidades {
  export class Persona {
      constructor(public nombre: string, public correo: string, public clave: string) {}

      ToString(): string {
          return `Nombre: ${this.nombre}, Correo: ${this.correo}, Clave: ${this.clave}`;
      }

      ToJSON(): string {
          return JSON.stringify(this);
      }
  }
}