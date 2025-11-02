import StardustDescriptor from "./Descriptor"
import StardustEventEmitter from "./EventEmitter"

/**
 * Main orchestrator class for the Stardust library.
 * Manages plugin lifecycle, adapters, and provides a centralized event system for plugin management.
 */
export class StardustOrchestrator{
   /**
    * Create a new Stardust orchestrator instance.
    * @param {boolean} [debugMode=true] - Enable debug mode with console logging
    */
   constructor(debugMode = true) {
      this.plugins = new Map()  // id → PluginDescriptor
      this.adapters = new Map() // name → Adapter
      this.events = new StardustEventEmitter()
      this.debugMode = debugMode;
      
      this.debugMode ? this.enableDebug() : this.disableDebug();
   }

   /**
    * Register an adapter for a specific plugin type.
    * Adapters define the initialization, destruction, and reload behavior for plugins.
    * @param {string} name - The adapter name (must match plugin name)
    * @param {Object} adapter - The adapter object with init, destroy, and reload methods
    */
   registerAdapter(name, adapter) {
      this.adapters.set(name, adapter)
   }
   
   /**
    * Register a new plugin with its configuration.
    * Creates a descriptor and adds it to the managed plugins collection.
    * @param {Object} config - Plugin configuration object
    * @param {string} config.name - Plugin name
    * @param {string} config.element - Element selector
    * @param {Object} [config.options] - Plugin options
    * @param {Function} [config.onInit] - Initialization handler
    * @param {Function} [config.onDestroy] - Destruction handler
    * @param {Function} [config.onReload] - Reload handler
    * @returns {StardustDescriptor} The created descriptor
    */
   register(config) {
      const descriptor = new StardustDescriptor(config)

      if (!this.adapters.has(descriptor.name)) {
         console.warn(`No adapter found for ${descriptor.name}`)
         // Aún así lo registras, solo que no se podrá init/destroy
      }
      
      this.plugins.set(descriptor.id, descriptor)
      this.events.emit('plugin:registered', { descriptor, id: descriptor.id })
      return descriptor
   }

   /**
    * Initialize a registered plugin by its ID.
    * Uses the registered adapter or falls back to the onInit handler.
    * @param {number} id - The plugin descriptor ID
    * @returns {boolean} True if initialization succeeded, false otherwise
    */
   init(id) {
      const descriptor = this.plugins.get(id)
      if (!descriptor) throw new Error(`Plugin ${id} not found`)
      
      const adapter = this.adapters.get(descriptor.name)
      
      if (!adapter) {
         if(descriptor.handlers.onInit){
            descriptor.markAsInitialized('unkown');
            descriptor.setAdapterType('inline');
            descriptor.handlers.onInit(descriptor.element, descriptor.options);
            this.events.emit('plugin:initialized', { descriptor, id: descriptor.id })
         } else {
            descriptor.markAsFailed('No adapter found')
            this.events.emit('plugin:failed', { descriptor, id: descriptor.id, error: 'No adapter found' })
         }

         console.warn(`Cannot initialize ${id}: no adapter for ${descriptor.name}. Using method onInit`)
         return false;
      }
      
      try {
         adapter.init(descriptor);
         descriptor.setAdapterType('adapter');
         this.events.emit('plugin:initialized', { descriptor, id: descriptor.id })
         return true
      } catch (error) {
         console.error(`Failed to initialize ${id}:`, error)
         descriptor.markAsFailed(error.message)
         this.events.emit('plugin:failed', { descriptor, id: descriptor.id, error })
         return false
      }
   }

   /**
    * Destroy an initialized plugin by its ID.
    * Cleans up resources and removes event listeners.
    * @param {number} id - The plugin descriptor ID
    * @returns {boolean} True if destruction succeeded, false otherwise
    */
   destroy(id) {
      const descriptor = this.plugins.get(id)
      if (!descriptor) return false
      
      const adapter = this.adapters.get(descriptor.name)
      if (adapter && descriptor.isActive()) {
         try {
            adapter.destroy(descriptor)
         } catch (error) {
            console.error(`Failed to destroy ${id}:`, error)
            this.events.emit('plugin:failed', { descriptor, id: descriptor.id, error, action: 'destroy' })
         }
      }
      
      // Siempre lo marcas como destruido, incluso si falló
      descriptor.markAsDestroyed()
      this.events.emit('plugin:destroyed', { descriptor, id: descriptor.id })
      return true
   }

   /**
    * Reload a plugin with new context or options.
    * Destroys and reinitializes the plugin.
    * @param {number} id - The plugin descriptor ID
    * @param {jQuery} [newContext] - New jQuery context
    * @param {Object} [newOptions] - New plugin options
    * @returns {boolean} True if reload succeeded, false otherwise
    */
   reload(id, newContext = null, newOptions = null) {
      const descriptor = this.plugins.get(id)
      if (!descriptor) return false
      
      const adapter = this.adapters.get(descriptor.name)
      if (!adapter) return false
      
      this.events.emit('plugin:initialized', { descriptor, id: descriptor.id });
      adapter.reload(descriptor, newContext, newOptions)
      return true
   }

   /**
    * Initialize all registered plugins, optionally filtered.
    * @param {Function} [filter] - Optional filter predicate function
    * @returns {boolean[]} Array of initialization results
    */
   initAll(filter = null) {
      const plugins = filter 
         ? this.filterPlugins(filter)
         : Array.from(this.plugins.values())
      
      return plugins.map(p => this.init(p.id))
   }
   
   /**
    * Destroy all initialized plugins, optionally filtered.
    * @param {Function} [filter] - Optional filter predicate function
    * @returns {boolean[]} Array of destruction results
    */
   destroyAll(filter = null) {
      const plugins = filter 
         ? this.filterPlugins(filter)
         : Array.from(this.plugins.values())
      
      return plugins.map(p => this.destroy(p.id))
   }
   
   /**
    * Reload all plugins with new context or options, optionally filtered.
    * @param {jQuery} [newContext] - New jQuery context
    * @param {Object} [newOptions] - New plugin options
    * @param {Function} [filter] - Optional filter predicate function
    * @returns {boolean[]} Array of reload results
    */
   reloadAll(newContext = null, newOptions = null, filter = null) {
      const plugins = filter 
         ? this.filterPlugins(filter)
         : Array.from(this.plugins.values())
      
      return plugins.map(p => this.reload(p.id, newContext, newOptions))
   }

   /**
    * Get a plugin descriptor by its ID.
    * @param {number} id - The plugin descriptor ID
    * @returns {StardustDescriptor|undefined} The descriptor if found
    */
   get(id) {
      return this.plugins.get(id)
   }

   /**
    * Get all registered plugin descriptors.
    * @returns {StardustDescriptor[]} Array of all descriptors
    */
   getAll() {
      return Array.from(this.plugins.values());
   }
   
   /**
    * Get all plugins with a specific name.
    * @param {string} name - The plugin name to search for
    * @returns {StardustDescriptor[]} Array of matching descriptors
    */
   getByName(name) {
      return this.filterPlugins(p => p.name === name);
   }
   
   /**
    * Get all active (initialized) plugins.
    * @returns {StardustDescriptor[]} Array of active descriptors
    */
   getActive() {
      return this.filterPlugins(p => p.state === p.isActive());
   }

   /**
    * Get all plugins whose elements still exist in the DOM.
    * @returns {StardustDescriptor[]} Array of alive descriptors
    */
   getAlive() {
      return this.filterPlugins(p => p.state === p.isAlive());
   }

   /**
    * Get all plugins in a specific state.
    * @param {string} state - The state to filter by ('registered', 'initialized', 'destroyed', 'failed')
    * @returns {StardustDescriptor[]} Array of descriptors in the specified state
    */
   getByState(state){
      return this.filterPlugins(p => p.state === state);
   }

   /**
    * Filter plugins by a custom predicate function.
    * @param {Function} predicate - Filter function that receives (plugin, key)
    * @returns {StardustDescriptor[]} Array of filtered descriptors
    */
   filterPlugins(predicate) {
      return Array.from(this.plugins.values()).filter((plugin, key) => predicate(plugin, key));
   }

   /**
    * Serialize the orchestrator state to JSON.
    * Excludes non-serializable properties like instances and handlers.
    * @returns {Object[]} Array of serialized plugin data
    */
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

   /**
    * Count the number of plugins with a specific name.
    * @param {string} name - The plugin name to count
    * @returns {number} The count of plugins with this name
    */
   count(name){
      return this.getByName(name).length;
   }

   /**
    * Inspect detailed information about a plugin.
    * @param {number} id - The plugin descriptor ID
    * @returns {Object} Detailed inspection data about the plugin
    */
   inspect(id){
      const desc = this.plugins.get(id)
      return {
         id: desc.id,
         name: desc.name,
         state: desc.state,
         hasAdapter: this.adapters.has(desc.name),
         hasInstance: desc.instance !== null,
         hasRealInstance: desc.hasRealInstance(),
         isAlive: desc.isAlive(),
         elementExists: document.querySelector(desc.element) !== null
      }
   }

   /**
    * Enable debug mode with console logging for all plugin lifecycle events.
    */
   enableDebug() {
      this.debugMode = true;
      this.#setupInspector();
      console.log('🔍 Stardust Inspector: Debug mode enabled')
   }

   /**
    * Disable debug mode and clear all plugin event listeners.
    */
   disableDebug() {
      this.debugMode = false;
      this.events.clear('plugin');
      console.log('🔍 Stardust Inspector: Debug mode disabled')
   }

   /**
    * Set up debug event listeners for plugin lifecycle monitoring.
    * @private
    */
   #setupInspector() {
      this.events.on('plugin:registered', ({ descriptor, id }) => {
         console.log(`✅ [REGISTERED] ${descriptor.name} (ID: ${id})`, descriptor)
      })

      this.events.on('plugin:initialized', ({ descriptor, id }) => {
         console.log(`🚀 [INITIALIZED] ${descriptor.name} (ID: ${id})`, {
            element: descriptor.element,
            options: descriptor.options,
            instance: descriptor.instance
         })
      })

      this.events.on('plugin:destroyed', ({ descriptor, id }) => {
         console.log(`💀 [DESTROYED] ${descriptor.name} (ID: ${id})`, descriptor)
      })

      this.events.on('plugin:failed', ({ descriptor, id, error }) => {
         console.error(`❌ [FAILED] ${descriptor.name} (ID: ${id})`, {
            error,
            descriptor
         })
      })
   }
};