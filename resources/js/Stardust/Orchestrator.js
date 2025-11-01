import StardustDescriptor from "./Descriptor"

export class StardustOrchestrator{
   constructor() {
      this.plugins = new Map()  // id → PluginDescriptor
      this.adapters = new Map() // name → Adapter
   }

   // Registras adaptadores una vez
   registerAdapter(name, adapter) {
      this.adapters.set(name, adapter)
   }
   
   // Registras un plugin con su descriptor
   register(config) {
      const descriptor = new StardustDescriptor(config)

      if (!this.adapters.has(descriptor.name)) {
         console.warn(`No adapter found for ${descriptor.name}`)
         // Aún así lo registras, solo que no se podrá init/destroy
      }
      
      this.plugins.set(descriptor.id, descriptor)
      return descriptor
   }

    // Inicialización explícita
   init(id) {
      const descriptor = this.plugins.get(id)
      if (!descriptor) throw new Error(`Plugin ${id} not found`)
      
      const adapter = this.adapters.get(descriptor.name)
      
      if (!adapter) {
         if(descriptor.handlers.onInit){
            descriptor.markAsInitialized('unkown');
            descriptor.setAdapterType('inline');
            descriptor.handlers.onInit(descriptor.element, descriptor.options);
         }

         console.warn(`Cannot initialize ${id}: no adapter for ${descriptor.name}. Using method onInit`)
         return false;
      }
      
      try {
         adapter.init(descriptor);
         return true
      } catch (error) {
         console.error(`Failed to initialize ${id}:`, error)
         return false
      }
   }

   destroy(id) {
      const descriptor = this.plugins.get(id)
      if (!descriptor) return false
      
      const adapter = this.adapters.get(descriptor.name)
      if (adapter && descriptor.isActive()) {
         try {
            adapter.destroy(descriptor)
         } catch (error) {
            console.error(`Failed to destroy ${id}:`, error)
         }
      }
      
      // Siempre lo marcas como destruido, incluso si falló
      descriptor.markAsDestroyed()
      return true
   }

   reload(id, newContext = null, newOptions = null) {
      const descriptor = this.plugins.get(id)
      if (!descriptor) return false
      
      const adapter = this.adapters.get(descriptor.name)
      if (!adapter) return false
      
      adapter.reload(descriptor, newContext, newOptions)
      return true
   }

   initAll(filter = null) {
      const plugins = filter 
         ? Array.from(this.plugins.values()).filter(filter)
         : Array.from(this.plugins.values())
      
      return plugins.map(p => this.init(p.id))
   }
   
   destroyAll(filter = null) {
      const plugins = filter 
         ? Array.from(this.plugins.values()).filter(filter)
         : Array.from(this.plugins.values())
      
      return plugins.map(p => this.destroy(p.id))
   }
   
   reloadAll(newContext = null, newOptions = null, filter = null) {
      const plugins = filter 
         ? Array.from(this.plugins.values()).filter(filter)
         : Array.from(this.plugins.values())
      
      return plugins.map(p => this.reload(p.id, newContext, newOptions))
   }

   // Consultas
   get(id) {
      return this.plugins.get(id)
   }

   getAll() {
      return Array.from(this.plugins.values());
   }
   
   getByName(name) {
      return this.filter(p => p.state === p.name === name);
   }
   
   getActive() {
      return this.filter(p => p.state === p.isActive());
   }

   getAlive() {
      return this.filter(p => p.state === p.isAlive());
   }

   getByState(state){
      return this.filter(p => p.state === state);
   }

   filter(predicate) {
      return Array.from(this.plugins.values()).filter(([key, plugin]) => predicate(plugin, key));
   }

   toJSON() {
      // Serializa estado actual sin funciones ni instancias
      return Array.from(this.plugins.values()).map(desc => ({
         id: desc.id,
         name: desc.name,
         state: desc.state,
         element: desc.element  // selector string
         // NO incluyas instance ni handlers (no serializables)
      }))
   }

   count(name){
      return this.getByName(name).length;
   }
};