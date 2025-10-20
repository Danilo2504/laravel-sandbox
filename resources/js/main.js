// Restricts input for the set of matched elements to the given inputFilter function.
(function($) {
   $.fn.inputFilter = function(callback, errMsg) {
      return this.on("input keydown keyup mousedown mouseup select contextmenu drop focusout", function(e) {
         if (callback(this.value)) {
            // Accepted value
            if (["keydown","mousedown","focusout"].indexOf(e.type) >= 0){
               $(this).removeClass("input-error");
               this.setCustomValidity("");
            }

            this.oldValue = this.value;
            this.oldSelectionStart = this.selectionStart;
            this.oldSelectionEnd = this.selectionEnd;
         } else if (this.hasOwnProperty("oldValue")) {
            // Rejected value - restore the previous one
            $(this).addClass("input-error");
            this.setCustomValidity(errMsg);
            this.reportValidity();
            this.value = this.oldValue;
            this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
         } else {
            // Rejected value - nothing to restore
            this.value = "";
         }
      });
   };

   $.fn.onlyNumbers = function(errMsg = "Only digits allowed"){
      return this.inputFilter(function(value) {
         return /^\d*$/.test(value);
      }, errMsg);
   }
}(jQuery));

function initTelephoneInput(){
   $('input[type="tel"].input-component').onlyNumbers("Only digits allowed in phone number");
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