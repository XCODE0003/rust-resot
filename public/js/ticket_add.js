  $(document).ready(function () {

      $('#select-type li').on('click', function () {
          console.log($(this).children().data('title'));
          $('#title').val($(this).children().data('title'));
          $('#type').val($(this).children().data('type'));

          if ($(this).children().data('type') == '4' || $(this).children().data('type') == '5') {
              $('.checkbox-player-not').show();
          } else {
              $('.checkbox-player-not').hide();
              $('.server-content-players-search').show();
              $('.select-char').show();
          }

          $('.server-content-ticket').removeClass('active');
          $('.select-char li').removeClass('checked');
          $('.player_not').prop('checked',false)
          //$('.not-found').show();
      });

      $('#select-server li').on('click', function () {
          console.log($(this).children().data('server'));
          $('#server_id').val($(this).children().data('server'));
          $('.server-content-ticket').removeClass('active');
          $('.select-char li').removeClass('checked');
          //$('.not-found').show();
      });

      $(document).on('click', '.select-char li', function(event){
          console.log($(this).data('char'));
          $('.select-char li').removeClass('checked');
          $(this).addClass('checked');
          $('#char_id').val($(this).data('char'));
          $('.server-content-ticket').addClass('active');
      });

      $(document).on('change', '.player_not', function(event){
          if ($(this).prop('checked')) {
              $(this).parent().parent().parent().find('.server-content-players-search').hide();
              $(this).parent().parent().parent().find('.select-char').hide();
              $(this).parent().parent().parent().find('.not-found').hide();
              $('.server-content-ticket').addClass('active');
              $('#char_id').val('0');
          } else {
              $(this).parent().parent().parent().find('.server-content-players-search').show();
              $(this).parent().parent().parent().find('.select-char').show();
              $(this).parent().parent().parent().find('.not-found').show();
              $('.server-content-ticket').removeClass('active');
          }
      });

  });