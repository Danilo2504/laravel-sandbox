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
});