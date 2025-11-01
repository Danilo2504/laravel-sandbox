import { CommonAdapter, OnlyNumbers, PasswordToggler } from "./Stardust/Adapters";
import { StardustOrchestrator } from "./Stardust/Orchestrator";
import "./plugins.js";

window.Stardust = new StardustOrchestrator();

Stardust.registerAdapter('onlyNumbers', OnlyNumbers);
Stardust.registerAdapter('passwordToggler', PasswordToggler);
Stardust.registerAdapter('commonAdapter', CommonAdapter);

Stardust.register({
   name: 'passwordToggler',  // ← Hace referencia al adapter
   element: '[data-toggle]'
});

Stardust.register({
   name: 'onlyNumbers',  // ← Hace referencia al adapter
   element: '[data-onlynumbers]'
});

Stardust.register({
   name: 'editorHtml',
   element: '[data-editor-html]',
   onInit: function(el, opts){
      const options = $(el).data('editor-html');
      $(el).editorHtml(options);
   }
});

Stardust.register({
   name: 'commonAdapter',
   element: '[data-onlynumbers]',
   onInit: function(el, options){
      console.log('Element inline', el);
   }
});

$(function(){
   Stardust.initAll();
   console.log(Stardust.getAll());
})