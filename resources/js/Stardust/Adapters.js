/**
 * Adapter for input fields that should only accept numeric values.
 * Validates input in real-time and prevents non-numeric characters.
 */
export const NumbersOnly = {
   EVENT_NAMESPACE: '.numbersOnly',
   /**
    * Initialize the numbers-only input validation.
    * @param {StardustDescriptor} descriptor - The plugin descriptor
    */
   init(descriptor){
      const $el = descriptor.getElement();
      const namespacedEvenets = $.fn.ns(
         this.EVENT_NAMESPACE,
         "input keydown keyup mousedown mouseup select contextmenu drop focusout"
      );

      $el.on(namespacedEvenets, function () {
         const hasNumbers = /^\d*$/.test(this.value);

         if (hasNumbers) {
            this.oldValue = this.value;
         } else if (this.hasOwnProperty("oldValue")) {
            this.value = this.oldValue;
         } else {
            this.value = "";
         }
      });
      
      // No hay "instancia" que devolver, pero registras que se aplicó
      descriptor.markAsInitialized($el)
      
      // Si tiene handler personalizado, lo ejecutas DESPUÉS
      descriptor.handlers.onInit?.($el, descriptor.options)
   },
   /**
    * Destroy the numbers-only validation by removing event listeners.
    * @param {StardustDescriptor} descriptor - The plugin descriptor
    */
   destroy(descriptor){
      const $el = descriptor.getElement();
      
      // onlyNumbers probablemente usa eventos
      $el.off(this.EVENT_NAMESPACE)  // Namespace jQuery
      
      descriptor.handlers.onDestroy?.($el)
      descriptor.markAsDestroyed();
   },
   /**
    * Reload the numbers-only validation with new context or options.
    * @param {StardustDescriptor} descriptor - The plugin descriptor
    * @param {jQuery} [newContext] - New jQuery context
    * @param {Object} [newOptions] - New options
    */
   reload(descriptor, newContext, newOptions) {
      this.destroy(descriptor);
      if (newContext) descriptor.context = newContext;
      if (newOptions) descriptor.options = newOptions;
      this.init(descriptor);
   }
};

/**
 * Adapter for password visibility toggle functionality.
 * Allows users to temporarily show/hide password text on mouse/touch events.
 */
export const PasswordToggler = {
   EVENT_NAMESPACE: '.passwordToggler',
   /**
    * Initialize the password toggle functionality.
    * @param {StardustDescriptor} descriptor - The plugin descriptor
    */
   init(descriptor){
      const $el = descriptor.getElement();
      const $inputTarget = $($el.data('toggle'));
      

      const namespacedEvenetsStart = $.fn.ns(
         this.EVENT_NAMESPACE,
         "mousedown touchstart"
      );
      const namespacedEvenetsEnd = $.fn.ns(
         this.EVENT_NAMESPACE,
         "mouseup touchend"
      );

      $el
         .on(namespacedEvenetsStart, function () {
            $inputTarget.attr('type', 'text');
            $el.removeClass('fa-eye').addClass('fa-eye-slash');
         })
         .on(namespacedEvenetsEnd, function () {
            $inputTarget.attr('type', 'password');
            $el.removeClass('fa-eye-slash').addClass('fa-eye');
         });
      
      descriptor.markAsInitialized($el)
      
      // Si tiene handler personalizado, lo ejecutas DESPUÉS
      descriptor.handlers.onInit?.($el, descriptor.options)
   },
   /**
    * Destroy the password toggle by removing event listeners.
    * @param {StardustDescriptor} descriptor - The plugin descriptor
    */
   destroy(descriptor){
      const $el = descriptor.getElement();
      
      $el.off(this.EVENT_NAMESPACE)  // Namespace jQuery
      
      descriptor.handlers.onDestroy?.($el)
      descriptor.markAsDestroyed();
   },
   /**
    * Reload the password toggle with new context or options.
    * @param {StardustDescriptor} descriptor - The plugin descriptor
    * @param {jQuery} [newContext] - New jQuery context
    * @param {Object} [newOptions] - New options
    */
   reload(descriptor, newContext, newOptions) {
      this.destroy(descriptor);
      if (newContext) descriptor.context = newContext;
      if (newOptions) descriptor.options = newOptions;
      this.init(descriptor);
   }
};

/**
 * Adapter for Summernote HTML editor initialization and management.
 * Wraps Summernote WYSIWYG editor functionality.
 */
export const EditorHTML = {
   EVENT_NAMESPACE: '.editorHtml',
   /**
    * Initialize the Summernote HTML editor.
    * @param {StardustDescriptor} descriptor - The plugin descriptor
    */
   init(descriptor){
      const $el = descriptor.getElement();
      const instance = $el.summernote(descriptor.options);
      
      descriptor.markAsInitialized(instance)
      
      // Si tiene handler personalizado, lo ejecutas DESPUÉS
      descriptor.handlers.onInit?.($el, descriptor.options)
   },
   /**
    * Destroy the Summernote editor instance.
    * @param {StardustDescriptor} descriptor - The plugin descriptor
    */
   destroy(descriptor){
      const $el = descriptor.getElement();

      $el.summernote('destroy');

      descriptor.handlers.onDestroy?.($el)
      descriptor.markAsDestroyed();
   },
   /**
    * Reload the HTML editor with new context or options.
    * @param {StardustDescriptor} descriptor - The plugin descriptor
    * @param {jQuery} [newContext] - New jQuery context
    * @param {Object} [newOptions] - New Summernote options
    */
   reload(descriptor, newContext, newOptions) {
      this.destroy(descriptor);
      if (newContext) descriptor.context = newContext;
      if (newOptions) descriptor.options = newOptions;
      
      this.init(descriptor);
   }
}

/**
 * Generic adapter for custom plugins without specific adapter implementations.
 * Provides basic lifecycle management using descriptor handlers.
 */
export const CommonAdapter = {
   /**
    * Initialize a custom plugin using its onInit handler.
    * @param {StardustDescriptor} descriptor - The plugin descriptor
    */
   init(descriptor) {
      try {
         descriptor.handlers.onInit?.(descriptor.element, descriptor.options)
         descriptor.markAsInitialized('unkown')
      } catch (e) {
         console.error('Manual init failed:', e)
      }
   },
   /**
    * Destroy a custom plugin by removing all events and calling onDestroy handler.
    * @param {StardustDescriptor} descriptor - The plugin descriptor
    */
   destroy(descriptor) {
      // Intentas cleanup genérico
      const $el = descriptor.getElement()
      $el.off()  // Remueve todos los eventos
      
      descriptor.handlers.onDestroy?.($el)
      descriptor.markAsDestroyed()
   },
   /**
    * Reload a custom plugin with a new context.
    * @param {StardustDescriptor} descriptor - The plugin descriptor
    * @param {jQuery} [newContext] - New jQuery context
    */
   reload(descriptor, newContext) {
      this.destroy(descriptor)
      if (newContext) descriptor.context = newContext
      this.init(descriptor)
   }
}
