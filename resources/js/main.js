import "./plugins.js";
import Stardust from "./setup";

window.Stardust = Stardust;

const password = Stardust.register({
   name: 'passwordToggler',  // ← Hace referencia al adapter
   element: '[data-toggle]',
});

const numbers = Stardust.register({
   name: 'numbersOnly',  // ← Hace referencia al adapter
   element: '[data-numbersonly]'
});

const editor = Stardust.register({
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

$('#toggle-editor').on('click', function(event){
   const $me = $(this);
   event.preventDefault();

   if(!$me.hasClass('active')){
      $('.css-editor-overlay').css('display', 'flex').hide().fadeIn(100, function(){
         $me.addClass('active');
      });
   } else{
      $('.css-editor-overlay').fadeOut(100, function(){
         $me.removeClass('active');
      });
   }
});

$('[data-css-editor]').cssEditor();