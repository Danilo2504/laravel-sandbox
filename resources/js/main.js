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
   event.preventDefault();
   $('.css-editor').toggleClass('active');
});

// const draggable = document.getElementById('.css-editor.active');
(function($){
   // Inicializa el arrastre para cada .css-editor
   function initEditorDrag($editor){
      if (!$editor.length) return;

      // Asegurarse de que el editor pueda posicionarse
      if ($editor.css('position') === 'static') {
         $editor.css('position', 'absolute');
      }

      let dragging = false;
      let startX = 0, startY = 0;
      let origLeft = 0, origTop = 0;

      // Solo iniciar arrastre cuando se presiona el handle .css-editor-draggable
      $editor.on('mousedown touchstart', '.css-editor-draggable', function(e){
         e.preventDefault();
         const ev = e.originalEvent && e.originalEvent.touches ? e.originalEvent.touches[0] : e;
         dragging = true;
         startX = ev.clientX;
         startY = ev.clientY;
         // obtener valores actuales (fallback a posicion si left/top no están definidos)
         origLeft = parseInt($editor.css('left'), 10);
         origTop  = parseInt($editor.css('top'), 10);
         if (isNaN(origLeft)) origLeft = $editor.position().left;
         if (isNaN(origTop))  origTop  = $editor.position().top;

         // Bind globalmente mientras arrastra
         $(document).on('mousemove.editorDrag touchmove.editorDrag', onMove);
         $(document).on('mouseup.editorDrag touchend.editorDrag touchcancel.editorDrag', stopMove);
      });

      function onMove(e){
         if (!dragging) return;
         const ev = e.originalEvent && e.originalEvent.touches ? e.originalEvent.touches[0] : e;
         const dx = ev.clientX - startX;
         const dy = ev.clientY - startY;
         $editor.css({
            left: (origLeft + dx) + 'px',
            top:  (origTop  + dy) + 'px'
         });
      }

      function stopMove(){
         dragging = false;
         $(document).off('.editorDrag');
      }
   }

   // Inicializar todos los .css-editor (o usa '.css-editor.active' si solo quieres los activos)
   $('.css-editor').each(function(){
      initEditorDrag($(this));
   });
})(jQuery);