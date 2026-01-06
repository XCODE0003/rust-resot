  // Server select and active ticket content

  $(function () {
    $('.list li').on('click', function() {
      $('.checked').not(this).removeClass('checked')
      $(this).closest("li").toggleClass('checked');
      $('.server-content-ticket').toggleClass('active')
    });
  });


  $('.list li').click( function () {
    if( $(this).hasClass('checked') ) {
      $('.server-content-ticket').addClass('active');
         
    } else {
      $('.server-content-ticket').removeClass('active'); 
    }
  });



  // Faq

  $(function () {
    $('.faq-list-title').on('click', function() {
      $(this).closest(".faq-list-li").toggleClass('faq-on');
    });
  });


  // Tabs


  $('.tab-nav span').on('click', function() {
    $([$(this).parent()[0], $($(this).data('href'))[0]]).addClass('active').siblings('.active').removeClass('active');
  });