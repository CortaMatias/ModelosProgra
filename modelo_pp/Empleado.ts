/// <reference path="Usuario.ts" />
namespace Entidades {
  export class Empleado extends Usuario {
      constructor(public id: number, public sueldo: number, public foto: string, id_perfil: number, perfil: string, nombre: string, correo: string, clave: string) {
          super(id, id_perfil, perfil, nombre, correo, clave);
      }
  }
}