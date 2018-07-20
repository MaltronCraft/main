jQuery(function($) {

  // Define Theme specific functions
  var Theme = {
    searchInit: function() {
      var $search = $('.site-search'),
          $input = $('.wsite-search-input'),
          $form = $('#wsite-header-search-form'),
          $submit = $('.wsite-search-button');


      $search.on('mouseenter', function(event){
        $input.focus();
      });

      $input.on('focus', function(event){
        $search.addClass('site-search-focused');

        $(document).one('touchstart', function(){
          $search.removeClass('site-search-focused');
        });

        // Close nav items
        $('.wsite-menu-item-wrap').removeClass('site-menu-hover');

        // Close menu
        $(document.body).removeClass('site-navigation-mobile-on');
      });

      $input.on('blur', function(event){
        $search.removeClass('site-search-focused');
      });

      $submit.click(function(){
        if ($input.val().length < 1) {
          return false;
        }
      });

      if ($('body').hasClass('wsite-editor')) {
        setTimeout(function(){
          var svg = $(".site-header .site-search svg").detach();
          $('.site-search .wsite-search').append(svg);
        }, 1000);
      }
    },

    headerSetup: function() {
      var $desktopNav = $('.site-navigation .wsite-menu-default');
      if (typeof DISABLE_NAV_MORE == 'undefined' || !DISABLE_NAV_MORE) {
        $desktopNav.pxuMenu({
          moreLinkHtml: 'More...',
        });
        setTimeout(function(){
          $desktopNav.data('pxuMenu').update();
          Theme.navInit();
        }, 1200);
      }
      else {
        Theme.navInit();
      }
    },

    navInit: function() {
      var $toggle = $('.hamburger');
      var $nav = $('.site-navigation');
      var hoverTimer;

      $toggle.on('click', function(event){
        $(document.body).toggleClass('site-navigation-mobile-on');
      });

      $('.wsite-menu-item-wrap').on('mouseenter', function(event){
        $('.wsite-menu-item-wrap').removeClass('site-menu-hover');
      });

      $('.site-menu-parent').on('mouseenter', function(event){
        var $item = $(this);
        clearTimeout(hoverTimer);

        $item.addClass('site-menu-hover');

        $item.one('mouseleave', function(event){
          hoverTimer = setTimeout(function(){
            $item.removeClass('site-menu-hover');
          }, 1000);
        });

        $('.site-submenu-parent').on('mouseenter', function(event){
          var $item = $(this);
          var $parent = $(this).closest('.site-menu-parent');

          clearTimeout(hoverTimer);

          $item.addClass('site-menu-hover');

          $parent.one('mouseleave', function(event){
            hoverTimer = setTimeout(function(){
              $item.removeClass('site-menu-hover');
            }, 1000);
          });

          // Close search bar
          $('.wsite-search-input').trigger('blur');
        });

        // Dropdown menus in mobile
        $(".site-menu-parent a, .site-submenu-parent a ").click(function(e) {
            if (('ontouchstart' in window) && !$(this).hasClass("submenu-open")) {
                e.preventDefault();
            }
            $(this).addClass("submenu-open");
        })

        // Close search bar
        $('.wsite-search-input').trigger('blur');
      });
    },

    minicartInit: function() {
      var $body = $(document.body);

      $body.on('click', '.wsite-cart-bottom', function(event){
        var href = $('#wsite-com-minicart-checkout-button').attr('href');
        window.location = href;
      });

      $('#wsite-nav-cart-a').on('mouseenter', function(event){
        // Close search bar
        $('.wsite-search-input').trigger('blur');

        // Close mobile menu
        $(document.body).removeClass('site-navigation-mobile-on');
      });
    },

    productInit: function() {
      // Show loading indicator when adding product to cart
      $('#wsite-com-product-add-to-cart').on('click', function(){
        var $button = $(this);
        $button.addClass('is-loading');

        setTimeout(function(){
          $button.addClass('is-animating');
        }, 100);

        setTimeout(function(){
          $button.removeClass('is-loading is-animating');
        }, 2000);
      });
    },

    // Swiping mobile galleries wwith Hammer.js
    swipeGallery: function() {
      setTimeout(function() {
        var touchGallery = document.getElementsByClassName("fancybox-wrap")[0];
        var mc = new Hammer(touchGallery);
        mc.on("panleft panright", function(ev) {
          if (ev.type == "panleft") {
            $("a.fancybox-next").trigger("click");
          } else if (ev.type == "panright") {
            $("a.fancybox-prev").trigger("click");
          }
          Theme.swipeGallery();
        });
      }, 500);
    },
    swipeInit: function() {
      if ('ontouchstart' in window) {
        $("body").on("click", "a.w-fancybox", function() {
          Theme.swipeGallery();
        });
      }
      // Add fullwidth class to gallery thumbs if less than 6
      $('.imageGallery').each(function(){
        if ($(this).children('div').length <= 6) {
          $(this).children('div').addClass('fullwidth-mobile');
        }
      });
    },
    toggleClick: function(click, target, classname){
      $(click).click(function(){
        $(target).toggleClass(classname);
      });
    },
    // Interval function to execute post-post-load events
    interval: function(condition, action, duration, limit) {
      var counter = 0;
      var looper = setInterval(function(){
        if (counter >= limit || Theme.checkElement(condition)) {
          clearInterval(looper);
        } else {
          action();
          counter++;
        }
      }, duration);
    },
    checkElement: function(selector) {
      return $(selector).length;
    },
    copyLogin: function() {
      var login = $("#member-login").clone(true);
      $("#navmobile .wsite-menu-default, .site-navigation .wsite-menu-default").append(login);
    }
  }

  $(document).ready(function() {
    $("body").addClass("postload");
    Theme.copyLogin();
    Theme.swipeInit();
    Theme.searchInit();
    Theme.headerSetup();
    Theme.minicartInit();
    Theme.productInit();
    Theme.toggleClick(".wsite-com-sidebar", ".wsite-com-sidebar", "sidebar-expanded");
  });
});
