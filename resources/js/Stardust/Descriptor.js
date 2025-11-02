/**
 * Descriptor class that represents a plugin instance configuration and lifecycle state.
 * Manages plugin metadata, state tracking, and element references.
 */
export default class StardustDescriptor {
  /**
   * Create a new descriptor for a plugin.
   * @param {Object} config - Configuration object
   * @param {string} config.name - Plugin name
   * @param {string} config.element - Element selector
   * @param {Function} [config.onInit] - Initialization handler
   * @param {Function} [config.onDestroy] - Destruction handler
   * @param {Function} [config.onReload] - Reload handler
   * @param {Object} [config.options] - Plugin options
   * @param {jQuery} [config.context] - jQuery context for element lookup
   */
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

  /**
   * Mark the descriptor as initialized with its instance.
   * @param {*} instance - The initialized plugin instance
   */
  markAsInitialized(instance) {
    this.state = 'initialized'
    this.instance = instance
  }
  
  /**
   * Mark the descriptor as destroyed and clear the instance reference.
   */
  markAsDestroyed() {
    this.state = 'destroyed'
    this.instance = null
  }

  /**
   * Mark the descriptor as failed and store the error.
   * @param {string|Error} error - The error that caused the failure
   */
  markAsFailed(error) {
    this.state = 'failed'
    this.error = error
  }

  /**
   * Set the adapter type used for this plugin.
   * @param {string} type - The adapter type ('adapter' or 'inline')
   */
  setAdapterType(type) {
    this.type = type;
  }
  
  /**
   * Check if the plugin is currently active (initialized with an instance).
   * @returns {boolean} True if the plugin is active
   */
  isActive() {
    return this.state === 'initialized' && this.instance !== null
  }

  /**
   * Check if the plugin's element still exists in the DOM.
   * @returns {boolean} True if the element exists in the DOM
   */
  isAlive() {
    return this.context ? (this.context.find(this.element).length > 0) : ($(this.element).length > 0);
  }

  /**
   * Get the jQuery element for this descriptor.
   * @returns {jQuery|null} The jQuery element if it exists, null otherwise
   */
  getElement(){
    if(this.isAlive()){
      return this.context ? this.context.find(this.element) : $(this.element);
    }

    return null;
  }

  /**
   * Generate a unique ID for the descriptor based on name and timestamp.
   * @private
   * @param {string} name - The plugin name to hash
   * @returns {number} A unique identifier
   */
  #generateId(name){
    let hash = 0;
    for (const char of name) {
      hash = (hash << 5) - hash + char.charCodeAt(0);
      hash |= 0; // Constrain to 32bit integer
    }
    return new Date().getUTCMilliseconds() + hash;
  }
}