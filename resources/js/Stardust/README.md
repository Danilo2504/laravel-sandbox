# Stardust ✨

**Stardust** is a lightweight JavaScript library for managing plugin lifecycles in jQuery-based applications. It provides a centralized orchestrator that handles plugin initialization, destruction, and reloading with a flexible adapter system.

## Features

- 🎯 **Centralized Plugin Management** - Register and manage multiple plugin instances from a single orchestrator
- 🔄 **Lifecycle Control** - Initialize, destroy, and reload plugins programmatically
- 🔌 **Adapter System** - Define reusable adapters for common plugin patterns
- 📡 **Event System** - Built-in event emitter for tracking plugin lifecycle events
- 🐛 **Debug Mode** - Comprehensive console logging for development
- 🎨 **jQuery Integration** - Seamless integration with jQuery plugins

## Installation

Simply import the orchestrator and any adapters you need:

```javascript
import { StardustOrchestrator } from './Stardust/Orchestrator'
import { NumbersOnly, PasswordToggler, EditorHTML } from './Stardust/Adapters'
```

## Quick Start

```javascript
// 1. Create the orchestrator instance
const stardust = new StardustOrchestrator(true) // true = debug mode

// 2. Register adapters
stardust.registerAdapter('NumbersOnly', NumbersOnly)
stardust.registerAdapter('PasswordToggler', PasswordToggler)
stardust.registerAdapter('EditorHTML', EditorHTML)

// 3. Register plugins
const phoneInput = stardust.register({
   name: 'NumbersOnly',
   element: '#phone-input',
   options: {}
})

// 4. Initialize the plugin
stardust.init(phoneInput.id)
```

## Core Concepts

### Orchestrator

The `StardustOrchestrator` is the main class that manages all plugins. It maintains a registry of plugins and their associated adapters.

```javascript
const stardust = new StardustOrchestrator(debugMode)
```

### Descriptors

Each registered plugin gets a `StardustDescriptor` that tracks:
- Plugin metadata (id, name, element selector)
- Lifecycle state (registered, initialized, destroyed, failed)
- Configuration (options, handlers)
- Instance reference

### Adapters

Adapters define how plugins are initialized, destroyed, and reloaded. Each adapter must implement:
- `init(descriptor)` - Initialize the plugin
- `destroy(descriptor)` - Clean up the plugin
- `reload(descriptor, newContext, newOptions)` - Reload with new configuration

## Built-in Adapters

### NumbersOnly

Restricts input fields to only accept numeric values.

```javascript
stardust.registerAdapter('NumbersOnly', NumbersOnly)

stardust.register({
   name: 'NumbersOnly',
   element: '#phone-number',
   options: {}
})
```

**Use case**: Phone numbers, zip codes, quantity inputs

### PasswordToggler

Adds show/hide password functionality to password fields.

```javascript
stardust.registerAdapter('PasswordToggler', PasswordToggler)

stardust.register({
   name: 'PasswordToggler',
   element: '.toggle-password',
   options: {}
})
```

**HTML Structure**:
```html
<input type="password" id="password" />
<i class="fa fa-eye toggle-password" data-toggle="#password"></i>
```

### EditorHTML

Wraps Summernote WYSIWYG editor initialization.

```javascript
stardust.registerAdapter('EditorHTML', EditorHTML)

stardust.register({
   name: 'EditorHTML',
   element: '.wysiwyg-editor',
   options: {
      height: 300,
      toolbar: [
         ['style', ['bold', 'italic', 'underline']],
         ['para', ['ul', 'ol', 'paragraph']]
      ]
   }
})
```

**Use case**: Rich text editors, blog post content, email composers

## Common Use Cases

### 1. Basic Plugin Registration and Initialization

```javascript
const stardust = new StardustOrchestrator()

// Register adapter
stardust.registerAdapter('NumbersOnly', NumbersOnly)

// Register plugin
const descriptor = stardust.register({
   name: 'NumbersOnly',
   element: '#credit-card',
   options: {}
})

// Initialize
stardust.init(descriptor.id)
```

### 2. Batch Initialization

Initialize all registered plugins at once:

```javascript
// Register multiple plugins
stardust.register({ name: 'NumbersOnly', element: '#phone' })
stardust.register({ name: 'NumbersOnly', element: '#zip-code' })
stardust.register({ name: 'EditorHTML', element: '.editor' })

// Initialize all
stardust.initAll()
```

### 3. Custom Handlers

Add custom logic during lifecycle events:

```javascript
stardust.register({
   name: 'EditorHTML',
   element: '#content-editor',
   options: {
      height: 400
   },
   onInit: ($el, options) => {
      console.log('Editor initialized!', $el)
      // Custom initialization logic
   },
   onDestroy: ($el) => {
      console.log('Editor destroyed!', $el)
      // Custom cleanup logic
   }
})
```

### 4. Dynamic Content (AJAX/SPA)

Handle dynamically loaded content:

```javascript
// Initial page load
const editors = stardust.register({
   name: 'EditorHTML',
   element: '.dynamic-editor',
   context: $('#content-area')
})
stardust.init(editors.id)

// After AJAX content load
function onContentLoaded() {
   // Reload plugin with new context
   stardust.reload(editors.id, $('#content-area'))
}
```

### 5. Filtering Plugins

Work with specific subsets of plugins:

```javascript
// Initialize only NumbersOnly plugins
stardust.initAll((plugin) => plugin.name === 'NumbersOnly')

// Destroy all plugins in a specific container
stardust.destroyAll((plugin) => {
   return plugin.element.startsWith('#modal')
})
```

### 6. Custom Adapter

Create your own adapter for custom functionality:

```javascript
const CustomDatePicker = {
   EVENT_NAMESPACE: '.customDatePicker',
   
   init(descriptor) {
      const $el = descriptor.getElement()
      
      // Initialize your plugin
      $el.datepicker(descriptor.options)
      
      descriptor.markAsInitialized($el)
      descriptor.handlers.onInit?.($el, descriptor.options)
   },
   
   destroy(descriptor) {
      const $el = descriptor.getElement()
      
      // Cleanup
      $el.datepicker('destroy')
      $el.off(this.EVENT_NAMESPACE)
      
      descriptor.handlers.onDestroy?.($el)
      descriptor.markAsDestroyed()
   },
   
   reload(descriptor, newContext, newOptions) {
      this.destroy(descriptor)
      if (newContext) descriptor.context = newContext
      if (newOptions) descriptor.options = newOptions
      this.init(descriptor)
   }
}

// Register and use
stardust.registerAdapter('CustomDatePicker', CustomDatePicker)
```

### 7. Inline Plugins (No Adapter)

For simple one-off functionality without creating an adapter:

```javascript
stardust.register({
   name: 'CustomBehavior', // No adapter registered with this name
   element: '.custom-element',
   onInit: ($el, options) => {
      // Your custom initialization logic
      $el.on('click', function() {
         console.log('Clicked!')
      })
   },
   onDestroy: ($el) => {
      // Cleanup
      $el.off('click')
   }
})
```

### 8. Event Listening

Listen to plugin lifecycle events:

```javascript
const stardust = new StardustOrchestrator(true)

// Listen to initialization events
stardust.events.on('plugin:initialized', ({ descriptor, id }) => {
   console.log(`Plugin ${descriptor.name} initialized with ID: ${id}`)
   // Update UI, send analytics, etc.
})

// Listen to errors
stardust.events.on('plugin:failed', ({ descriptor, error }) => {
   console.error(`Plugin ${descriptor.name} failed:`, error)
   // Log to error tracking service
})
```

### 9. State Queries

Get information about registered plugins:

```javascript
// Get all plugins with a specific name
const editors = stardust.getByName('EditorHTML')

// Get all active plugins
const active = stardust.getActive()

// Count plugins by name
const editorCount = stardust.count('EditorHTML')

// Check if element still exists in DOM
const isAlive = descriptor.isAlive()

// Inspect plugin details
const info = stardust.inspect(descriptor.id)
console.log(info)
// {
//   id: 12345,
//   name: 'EditorHTML',
//   state: 'initialized',
//   hasAdapter: true,
//   hasInstance: true,
//   isAlive: true,
//   elementExists: true
// }
```

### 10. Serialization

Export plugin state (useful for debugging or state persistence):

```javascript
const state = stardust.toJSON()
console.log(JSON.stringify(state, null, 2))
// [
//   {
//     "id": 12345,
//     "name": "EditorHTML",
//     "state": "initialized",
//     "element": "#content-editor"
//   }
// ]
```

## API Reference

### StardustOrchestrator

| Method | Description |
|--------|-------------|
| `registerAdapter(name, adapter)` | Register an adapter for a plugin type |
| `register(config)` | Register a new plugin |
| `init(id)` | Initialize a plugin by ID |
| `destroy(id)` | Destroy a plugin by ID |
| `reload(id, context, options)` | Reload a plugin with new configuration |
| `initAll(filter?)` | Initialize all (or filtered) plugins |
| `destroyAll(filter?)` | Destroy all (or filtered) plugins |
| `reloadAll(context?, options?, filter?)` | Reload all (or filtered) plugins |
| `get(id)` | Get descriptor by ID |
| `getAll()` | Get all descriptors |
| `getByName(name)` | Get descriptors by plugin name |
| `getByState(state)` | Get descriptors by state |
| `getActive()` | Get all active plugins |
| `getAlive()` | Get plugins with existing DOM elements |
| `count(name)` | Count plugins by name |
| `inspect(id)` | Get detailed plugin information |
| `enableDebug()` | Enable debug mode |
| `disableDebug()` | Disable debug mode |

### StardustDescriptor

| Property | Description |
|----------|-------------|
| `id` | Unique descriptor ID |
| `name` | Plugin name |
| `element` | Element selector |
| `state` | Current state (registered/initialized/destroyed/failed) |
| `type` | Adapter type (adapter/inline) |
| `options` | Plugin options |
| `context` | jQuery context |
| `instance` | Plugin instance reference |

| Method | Description |
|--------|-------------|
| `isActive()` | Check if plugin is initialized |
| `isAlive()` | Check if element exists in DOM |
| `getElement()` | Get jQuery element |

## Best Practices

1. **Always register adapters before plugins** - Register all adapters during app initialization
2. **Use descriptors for tracking** - Store descriptor references for plugins you need to manipulate later
3. **Enable debug mode during development** - It provides valuable insights into plugin lifecycle
4. **Clean up on navigation** - Destroy plugins when navigating away or removing content
5. **Use contexts for scoped plugins** - Pass jQuery contexts to limit plugin scope to specific containers
6. **Handle dynamic content properly** - Use `reload()` for content updated via AJAX
7. **Implement error handlers** - Listen to `plugin:failed` events for error tracking

## Debug Mode

Enable debug mode to see detailed console logs:

```javascript
const stardust = new StardustOrchestrator(true)

// Or toggle later
stardust.enableDebug()
stardust.disableDebug()
```

**Debug Output**:
- ✅ Plugin registered
- 🚀 Plugin initialized
- 💀 Plugin destroyed
- ❌ Plugin failed

<!-- ## License

MIT -->

## Contributing

Contributions are welcome! Please ensure all code is properly documented with JSDoc comments.
