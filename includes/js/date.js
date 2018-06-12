jQuery(function( $ ) {

  $('.seen').closest('tr').hide();
    $(document).ready(function() {
      $("#custom_select").change(function() {
         var selected_option = $('#custom_select').val();
         if (selected_option == 'video') {
             $('.seen').closest('tr').show();
         }
         if (selected_option != 'video') {
             $('.seen').closest('tr').hide();
         }
      })
      $( ".datepicker" ).datepicker({
          dateFormat : "dd-mm-yy"
      });
    })

});
