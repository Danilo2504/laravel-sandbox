/**
 * Event emitter class for managing custom events and listeners.
 * Provides a simple pub/sub pattern implementation for the Stardust library.
 */
export default class StardustEventEmitter {
  constructor() {
    this.listeners = new Map() // eventName → Set of callbacks
  }

  /**
   * Register an event listener for a specific event.
   * @param {string} eventName - The name of the event to listen to
   * @param {Function} callback - The function to call when the event is emitted
   * @returns {Function} Unsubscribe function to remove this listener
   */
  on(eventName, callback) {
    if (!this.listeners.has(eventName)) {
      this.listeners.set(eventName, new Set())
    }
    this.listeners.get(eventName).add(callback)
    
    // Return unsubscribe function
    return () => this.off(eventName, callback)
  }

  /**
   * Remove an event listener for a specific event.
   * @param {string} eventName - The name of the event
   * @param {Function} callback - The callback function to remove
   */
  off(eventName, callback) {
    if (!this.listeners.has(eventName)) return
    this.listeners.get(eventName).delete(callback)
  }

  /**
   * Emit an event to all registered listeners.
   * Catches and logs any errors thrown by listeners.
   * @param {string} eventName - The name of the event to emit
   * @param {*} data - Data to pass to the event listeners
   */
  emit(eventName, data) {
    if (!this.listeners.has(eventName)) return
    this.listeners.get(eventName).forEach(callback => {
      try {
        callback(data)
      } catch (error) {
        console.error(`Error in event listener for ${eventName}:`, error)
      }
    })
  }

  /**
   * Register an event listener that will only be called once.
   * The listener is automatically removed after being called.
   * @param {string} eventName - The name of the event to listen to
   * @param {Function} callback - The function to call when the event is emitted
   * @returns {Function} Unsubscribe function to remove this listener
   */
  once(eventName, callback) {
    const onceWrapper = (data) => {
      callback(data)
      this.off(eventName, onceWrapper)
    }
    return this.on(eventName, onceWrapper)
  }

  /**
   * Clear all listeners for a specific event, or all events if no name is provided.
   * @param {string} [eventName] - The name of the event to clear (optional)
   */
  clear(eventName) {
    if (eventName) {
      this.listeners.delete(eventName)
    } else {
      this.listeners.clear()
    }
  }
}
