// Best practice Drupal uses behaviours. These aren't that difficult once you get the hang of it.
// Source -  https://www.drupal.org/node/2269515
//
// --------------------------------- Example behaviour ---------------------------------
// Drupal.behaviors.myBehavior = {
//   attach: function (context, settings) {
//     // Using once() to apply the myCustomBehaviour effect when you want to do just run one function.
//     $('input.myCustomBehavior', context).once('myCustomBehavior').addClass('processed');
//
//     // Using once() with more complexity.
//     $('input.myCustom', context).once('mySecondBehavior').each(function () {
//       if ($(this).visible()) {
//         $(this).css('background', 'green');
//       }
//       else {
//         $(this).css('background', 'yellow').show();
//       }
//     });
//   }
// };
//
// }(jQuery));

(function ($, viewport) {
  'use strict';

    // ---- Mobile menu ----
    Drupal.behaviors.mobileMenu = {
      attach: function (context, settings) {
        $('header.header', context).each(function () {

          $('.region-mobilenav').addClass('closed');

          var navButton = $('svg.menu-button');

          $(navButton).click(function () {
            if ($('.region-mobilenav').hasClass('closed')) {
              $('.region-mobilenav').slideDown().removeClass('closed');
              $(this).addClass('open');
              $('.layout-container').addClass('menu-open');
            }
            else {
              $('.region-mobilenav').slideUp().addClass('closed');
              $(this).removeClass('open');
              $('.layout-container').removeClass('menu-open');
            }
          });

        });

      }
    };

    // ---- Search box  ----
    Drupal.behaviors.search = {
      attach: function (context, settings) {
        $('.layout-container.bones-search', context).each(function () {
          // $(this).prepend('<a class="search-show" title="Search">Search</a>');
          // $('.search-show').load('/themes/contrib/bones_theme/images/search.svg');
          $('.search-block-form .search-show').on('click', function () {
            $(this).addClass('hide');
            $('#search-block-form').addClass('show');
            $('.search-block-form').addClass('show');
            $(this).parent('div').find('.form-search').focus();
            $(this).parents('.region, .layout-container').addClass('search-open');
          });
          $(window).on('click', function () {
            var searchOpen = $('.search-block-form input.form-search').is(':focus');
            if (searchOpen === false) {
              $('#search-block-form').removeClass('show');
              $('.search-block-form').removeClass('show');
              $('.search-block-form .search-show').removeClass('hide');
              $('.region-navigation, .layout-container').removeClass('search-open');
            }
          });
        });
      }
    };

    // ---- Sticky header ----
    Drupal.behaviors.stickyHeader = {
      attach: function (context, settings) {
        $('.layout-container.bones-fixed-header', context).each(function () {

          function fixedHeader() {
            var windowHeight = $(window).height();
            var pageHeight = $('body').height();
            var $window = $(window);
            if (($window.scrollTop() >= 200) && (pageHeight > (windowHeight + 150))) {
              $('.layout-container').addClass('scrolled');
            }
            else {
              $('.layout-container').removeClass('scrolled');
            }
          }

          $(window).scroll(fixedHeader);

          var headerHeight = $('header.header').height();
          $('main').css('padding-top', headerHeight);

        });

      }
    };

    // ---- Hide Empty Regions ----
    // ---- Known bug: https://www.drupal.org/project/drupal/issues/953034?page=1
    Drupal.behaviors.emptyRegions = {
      attach: function (context, settings) {
        $('.region-container', context).each(function () {
          if ($(this).children().length === 0) {
            $(this).parent().addClass('empty');
          }
        });
      }
    };


}(jQuery));
