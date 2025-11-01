export const OnlyNumbers = {
   init(descriptor){
      const $el = descriptor.getElement();
      
      $el.onlyNumbers(descriptor.options)
      
      // No hay "instancia" que devolver, pero registras que se aplicó
      descriptor.markAsInitialized(true)
      descriptor.setAdapterType('adapter');
      
      // Si tiene handler personalizado, lo ejecutas DESPUÉS
      descriptor.handlers.onInit?.($el, descriptor.options)
   },
   destroy(descriptor){
      const $el = descriptor.getElement();
      
      // onlyNumbers probablemente usa eventos
      $el.off('.onlyNumbers')  // Namespace jQuery
      
      descriptor.handlers.onDestroy?.($el)
      descriptor.markAsDestroyed();
   },
   reload(descriptor, newContext, newOptions) {
      this.destroy(descriptor);
      if (newContext) descriptor.context = newContext;
      if (newOptions) descriptor.options = newOptions;
      this.init(descriptor);
   }
};

export const PasswordToggler = {
   init(descriptor){
      const $el = descriptor.getElement();
      
      $el.passwordToggler(descriptor.options)
      
      // No hay "instancia" que devolver, pero registras que se aplicó
      descriptor.markAsInitialized(true)
      descriptor.setAdapterType('adapter');
      
      // Si tiene handler personalizado, lo ejecutas DESPUÉS
      descriptor.handlers.onInit?.($el, descriptor.options)
   },
   destroy(descriptor){
      const $el = descriptor.getElement();
      
      // onlyNumbers probablemente usa eventos
      $el.off('.passwordToggler')  // Namespace jQuery
      
      descriptor.handlers.onDestroy?.($el)
      descriptor.markAsDestroyed();
   },
   reload(descriptor, newContext, newOptions) {
      this.destroy(descriptor);
      if (newContext) descriptor.context = newContext;
      if (newOptions) descriptor.options = newOptions;
      this.init(descriptor);
   }
};

export const CommonAdapter = {
   init(descriptor) {
      try {
         descriptor.handlers.onInit?.(descriptor.element, descriptor.options)
         descriptor.markAsInitialized('unkown')
         descriptor.setAdapterType('common');
      } catch (e) {
         console.error('Manual init failed:', e)
      }
   },
   destroy(descriptor) {
      // Intentas cleanup genérico
      const $el = descriptor.getElement()
      $el.off()  // Remueve todos los eventos
      
      descriptor.handlers.onDestroy?.(descriptor.element)
      descriptor.markAsDestroyed()
   },
   reload(descriptor, newContext) {
      this.destroy(descriptor)
      if (newContext) descriptor.context = newContext
      this.init(descriptor)
   }
}