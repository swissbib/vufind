/**
 *
 * @type {{sidebar: null, button: null, body: null, init: init, initOffCanvas: initOffCanvas, toggleOffCanvas: toggleOffCanvas, enableTransition: enableTransition}}
 */
var OffCanvas = {
  isActive: true,
  sidebar: null,
  button: null,
  icon: null,
  body: null,

  /**
   * Initialize Off-Canvas handling
   */
  init: function () {
    'use strict';
    if (OffCanvas.isActive) OffCanvas.initOffCanvas();
  },

  /**
   *
   */
  initOffCanvas: function () {
    'use strict';
    OffCanvas.sidebar = $(".sidebar");
    OffCanvas.button = $("button#sidebar-offcanvas-trigger");
    OffCanvas.icon = $("button#sidebar-offcanvas-trigger i");

    if (OffCanvas.sidebar.length > 0) {
      OffCanvas.body = $("body");
      OffCanvas.footer = $("footer");
      OffCanvas.main = $(".main");
      OffCanvas.header = $("header");

      OffCanvas.button.removeClass('hidden');
      OffCanvas.button.click(OffCanvas.toggleOffCanvas);
    }
  },

  /**
   * Toggle Off Canvas
   */
  toggleOffCanvas: function () {
    'use strict';
    OffCanvas.enableTransition();

    if (OffCanvas.body.hasClass("offcanvas-active")) {
      OffCanvas.body.removeClass("offcanvas-active");
      OffCanvas.button.removeClass("offcanvas-active");
      OffCanvas.sidebar.removeClass("offcanvas-active");
      OffCanvas.sidebar.css('height', '');
      OffCanvas.sidebar.css('overflow-y', '');
      OffCanvas.body.css('height', '');
      OffCanvas.body.css('overflow-y', '');
      OffCanvas.icon.removeClass("fa fa-caret-right");
      OffCanvas.icon.addClass("fa fa-caret-left");
    } else {
      OffCanvas.body.addClass("offcanvas-active");
      OffCanvas.button.addClass("offcanvas-active");
      OffCanvas.sidebar.addClass("offcanvas-active");
      OffCanvas.sidebar.css('height', window.innerHeight);
      OffCanvas.sidebar.css('overflow-y', 'scroll');
      OffCanvas.body.css('height', window.innerHeight);
      OffCanvas.body.css('overflow-y', 'hidden');
      OffCanvas.icon.removeClass("fa fa-caret-left");
      OffCanvas.icon.addClass("fa fa-caret-right");
    }
  },

  /**
   * Workaround to prevent transition on orientation change
   */
  enableTransition: function() {
    'use strict';
    OffCanvas.sidebar.addClass('transition');
    setTimeout(function() {
      $('.sidebar').removeClass('transition');
    }, 400);
  }
};

$(OffCanvas.init);
