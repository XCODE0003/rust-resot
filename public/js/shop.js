  // Gift

  $(function () {
    $('.shop-item-buy-gift-text').on('click', function() {
      $(this).closest('.shop-item-buy-gift').toggleClass('active');
    });
  });



  // Days and buy

  $(function () {
    $('.shop-item-buy-info li').on('click', function() {
      $(this).parent().find('li').removeClass('on');

        $(this).parent().find('.price').text($(this).attr('data-varprice')+'.00');

      $(this).closest('li').toggleClass('on');

      if( $(this).hasClass('on') ) {
        $(this).closest('.shop-item-buy-info').addClass('checked');
           
      } else {
        $(this).closest('.shop-item-buy-info').removeClass('checked'); 
      }
    });
  });


  // Payment
  $(function () {
    $('.buying-item-payment-option li').on('click', function() {
      $('.on-pay').not(this).removeClass('on-pay')
      $(this).closest('li').toggleClass('on-pay');

      if( $(this).hasClass('on-pay') ) {
        $(this).closest('.buying-item-payment-option').addClass('checked');
           
      } else {
        $(this).closest('.buying-item-payment-option').removeClass('checked'); 
      }
    });
  });




// Payment Modal

  
$(document).ready(function(){
	$(".buy").click(function(){
		$(".buying-item-modal").show();
		$("body, html").addClass("hidden");
		$(".modal-content").addClass("scale");
	}); 
    
	$(".modal-close, .buying-item-close").click(function(){
		$(".buying-item-modal").hide();
		$(".modal-content").removeClass("scale");
		$("body, html").removeClass("hidden");
	}); 
}); 









