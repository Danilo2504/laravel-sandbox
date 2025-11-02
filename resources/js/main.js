import "./plugins.js";
import Stardust from "./setup";

window.Stardust = Stardust;

Stardust.register({
   name: 'passwordToggler',  // ← Hace referencia al adapter
   element: '[data-toggle]',
});

Stardust.register({
   name: 'numbersOnly',  // ← Hace referencia al adapter
   element: '[data-numbersonly]'
});

Stardust.register({
   name: 'editorHtml',
   element: '[data-editor-html]',
   options: {
      minHeight: 300,
      maxHeight: 800,
      toolbar: [
         ['style', ['bold', 'italic', 'underline', 'clear']],
         ['font', ['strikethrough', 'superscript', 'subscript']],
         ['fontname', ['fontname']],
         ['fontsize', ['fontsize']],
         ['color', ['color']],
         ['para', ['ul', 'ol', 'paragraph']],
         ['insert', ['picture', 'link', 'video', 'table']],
         ['misc', ['codeview', 'fullscreen', 'undo', 'redo']]
      ]
   }
});

$(function(){
   Stardust.initAll();
})