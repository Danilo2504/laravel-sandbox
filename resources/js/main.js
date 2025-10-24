// Restricts input for the set of matched elements to the given inputFilter function.
(function($) {
   $.fn.onlyNumbers = function(){
      return this.on("input keydown keyup mousedown mouseup select contextmenu drop focusout", function(e) {
         const hasNumbers = /^\d*$/.test(this.value);

         if (hasNumbers) {
            this.oldValue = this.value;
         } else if(this.hasOwnProperty("oldValue")){
            this.value = this.oldValue;
         } else {
            this.value = "";
         }
      });
   }
}(jQuery));

function initTelephoneInput(){
   $('input[type="tel"].input-component').onlyNumbers();
}

function initPasswordToggler(){
   $('.toggle-password').on('mousedown touchstart', function(){
      const $this = $(this);
      const $inputTarget = $($this.data('toggle'));
      $inputTarget.attr('type', 'text');
      $this.removeClass('fa-eye').addClass('fa-eye-slash');
   });
   $('.toggle-password').on('mouseup touchend', function(){
      const $this = $(this);
      const $inputTarget = $($this.data('toggle'));
      $inputTarget.attr('type', 'password');
      $this.removeClass('fa-eye-slash').addClass('fa-eye');
   });
}

$(function(){
   initPasswordToggler();
   initTelephoneInput();
});