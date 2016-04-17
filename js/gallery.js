$( window ).load( function() {
   var $container = $('.isotope').isotope({
      itemSelector: '.item',
      masonry: {
        columnWidth: '.grid-sizer',
        gutter: '.gutter-sizer'
      }
    });
    $container.on( 'click', '.item', function() {
      // change size of item by toggling big-up class
      $( '.item' ).removeClass('big-up');
      $( this ).toggleClass('big-up');
      $container.isotope('layout');
    });
    $container.on( 'click', '.item.big-up', function() {
      $( this ).removeClass('big-up');
      $( this ).toggleClass('big-up');
      $container.isotope('layout');
    });
});