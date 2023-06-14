/// <reference path="Persona.ts" />
namespace Entidades {
  export class Usuario extends Persona {
      constructor(public id: number, public id_perfil: number, nombre: string, correo: string, clave: string, public perfil: string = "") {
          super(nombre, correo, clave);
      }

      ToJSON(): string {
          return JSON.stringify(this);
      }
  }
}