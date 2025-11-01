(function ($) {
   // === Plugin: OnlyNumbers ===
   $.fn.onlyNumbers = function () {
      return this.each(function () {
         const $el = $(this);

         $el.on("input keydown keyup mousedown mouseup select contextmenu drop focusout", function () {
            const hasNumbers = /^\d*$/.test(this.value);
            if (hasNumbers) {
               this.oldValue = this.value;
            } else if (this.hasOwnProperty("oldValue")) {
               this.value = this.oldValue;
            } else {
               this.value = "";
            }
         });
      });
   };

   // === Plugin: EditorHTML (ej. Summernote) ===
   $.fn.editorHtml = function (options = {}) {
      const defaults = {
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
      };

      const config = $.extend(true, {}, defaults, options);

      return this.each(function () {
         const $el = $(this);

         $el.summernote(config);
      });
   };

   // === Plugin: PasswordToggler ===
   $.fn.passwordToggler = function () {
      return this.each(function () {
         const $toggle = $(this);
         const $inputTarget = $($toggle.data('toggle'));

         $toggle.on('mousedown touchstart', function () {
            $inputTarget.attr('type', 'text');
            $toggle.removeClass('fa-eye').addClass('fa-eye-slash');
         });

         $toggle.on('mouseup touchend', function () {
            $inputTarget.attr('type', 'password');
            $toggle.removeClass('fa-eye-slash').addClass('fa-eye');
         });
      });
   };
})(jQuery);