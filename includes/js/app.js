console.log('coucou');
jQuery(function( $ ) {

  $(document).ready(function(){

    $(".carousel").slick({
    	dots: true,
      autoplay: true,
      autoplaySpeed: 5000,
      slidesToShow: 1,
      slidesToScroll: 1,

    });
  });

});
