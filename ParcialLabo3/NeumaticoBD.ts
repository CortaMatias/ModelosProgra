/// <reference path="Neumatico.ts" />

namespace Entidades {
class NeumaticoBD extends Neumatico {
  id: number;
  pathFoto: string;

  constructor(marca: string, medidas: string, precio: number, id?: number, pathFoto?: string) {
    super(marca, medidas, precio);
    this.id = id || 0;
    this.pathFoto = pathFoto || '';
  }

  ToJSON(): any {
    const neumaticoJSON = super.ToJSON();
    return {
      ...neumaticoJSON,
      id: this.id,
      pathFoto: this.pathFoto,
    };
  }
  }
}