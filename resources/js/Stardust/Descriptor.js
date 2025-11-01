// id = uatogenerado (UUID)
// name = nombre de la libreria (Case sensitive)
// elemento = string o DOM Element (si es string se convierte en un DOM Element)
// instance = gaurda la instancia del objeto

// Estados posibles:
// - 'registered': Existe en el sistema pero no ha sido inicializado
// - 'initialized': Completamente funcional
// - 'destroyed': Limpiado pero aún en memoria para consultas
// - 'failed': Algo salió mal

// Usamos tanto handlers que el usuario define al momento, como Adapters que nosotros definimos para cada instancia. No se pueden usar ambas al mismo tiempo

export default class StardustDescriptor {
  constructor(config) {
    this.id = this.#generateId(config.name);
    this.name = config.name;
    this.element = config.element;
    this.state = 'registered';
    this.type = null;

    this.handlers = {
      onInit: config.onInit || null,
      onDestroy: config.onDestroy || null,
      onReload: config.onReload || null
    }

    this.options = config.options;
    this.context = config.context || null  // jQuery context
    this.instance = null
  }

  markAsInitialized(instance) {
    this.state = 'initialized'
    this.instance = instance
  }
  
  markAsDestroyed() {
    this.state = 'destroyed'
    this.instance = null
  }

  setAdapterType(type) {
    this.type = type;
  }
  
  isActive() {
    return this.state === 'initialized' && this.instance !== null
  }

  isAlive() {
    return this.context ? (this.context.find(this.element).length > 0) : ($(this.element).length > 0);
  }

  getElement(){
    if(this.isAlive()){
      return this.context ? this.context.find(this.element) : $(this.element);
    }

    return null;
  }

  #generateId(name){
    let hash = 0;
    for (const char of name) {
      hash = (hash << 5) - hash + char.charCodeAt(0);
      hash |= 0; // Constrain to 32bit integer
    }
    return new Date().getUTCMilliseconds() + hash;
  }
}