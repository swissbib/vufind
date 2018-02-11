'use strict';

/**
 * swissbib VuFind Javascript
 */
(function closure(s) {
  /**
   * Initialize on ready.
   */
  s.initOnReady = function () {
    this.initBackgrounds();
    this.initFocus();
    this.initRemoveSearchText();
    this.initUserVoiceFeedback();
    this.initBulkExport();
    this.AdvancedSearch.init();
    //this.initHierarchyTree();
    //this.initNationaLicensesFlow();
  };

  /**
   * Initialize focus either
   * - on search box at the end of text
   * - or on login-field, if present
   * - or on favorites-library-field, if present
   */
  s.initFocus = function() {
    var favoritesLibraryField = window.location.pathname.match(/Favorites$/) !== null ? document.getElementById('query') : null;
    var loginField = document.getElementById('login_username');
    var searchField = document.getElementById('searchForm_lookfor');

    if (favoritesLibraryField !== null) {
      favoritesLibraryField.focus();
    }
    else if (loginField !== null) {
      loginField.focus();
    }
    else if (searchField !== null) {
      var textLength = searchField.value.length;
      // For IE Only
      if (document.selection) {
        searchField.focus();
        var oSel = document.selection.createRange();
        // Reset position to 0 & then set at end
        oSel.moveStart('character', -textLength);
        oSel.moveStart('character', textLength);
        oSel.moveEnd('character', 0);
        oSel.select();
      }
      else if (searchField.selectionStart || searchField.selectionStart == '0') {
        // Firefox/Chrome
        searchField.selectionStart = textLength;
        searchField.selectionEnd = textLength;
        searchField.focus();
      }
    }
  };

  /**
   * Initializes remove search text icon on main search field
   */
  s.initRemoveSearchText = function () {
    var $searchInputField = $('#searchForm_lookfor');
    var $removeSearchTextIcon = $('#remove-search-text');

    if ($searchInputField.val() !== '') {
      $removeSearchTextIcon.show();
    }

    $removeSearchTextIcon.click(function () {
      $searchInputField.val('');
      $searchInputField.focus();
      $removeSearchTextIcon.hide();
    });

    $searchInputField.on('input', function () {
      if ($searchInputField.val() === '') {
        $removeSearchTextIcon.hide();
      } else {
        $removeSearchTextIcon.show();
      }
    });
  };

  /**
   *
   */
  s.initBulkExport = function () {
    var hasResults = $('form[name="bulkActionForm"]').find('a.singleLinkForBulk').length > 0;

    if (hasResults) {
      $('.dropdown-menu[role="export-menu"] li').click($.proxy(this.onBulkExportFormatClick, this));
    }
  };

  /**
   * Enables scroll to selected node, mostly copied from VuFind bootstrap3 hierarchyTree.js
   */
  /*
  s.initHierarchyTree = function () {
    var htmlID = swissbib.getParameterByName('htmlID');

    if (htmlID !== '') {
      var $hierarchyTree = $("#hierarchyTree");

      $hierarchyTree.bind("ready.jstree", function (event, data) {
        var jstree = $hierarchyTree.jstree(true);

        jstree.select_node(htmlID);
        jstree._open_to(htmlID);
      });
    }
  },
  */

  /**
   * Handle click on bulk export
   * Append list of record ids to existing link
   *
   * @param    {Object}    event
   */
  s.onBulkExportFormatClick = function (event) {
    var driver = $('div.search-tabs-box:has(ul)').length ? $('div.search-tabs-box li.active').attr('data-searchClass') : 'VuFind';
    var baseUrl = event.target.href,
        idArgs = [],
        fullUrl,
        ids = $('a.singleLinkForBulk').map(function () {
          return driver + '|' + this.href.split('/').pop()
        }).get();

    event.preventDefault();

    $.each(ids, function (index, id) {
      idArgs.push('i[]=' + id);
    });

    fullUrl = baseUrl + '&' + idArgs.join('&');

    window.open(fullUrl);
  };

  /**
   * function for the UserVoice feedback widget in swissbib green
   */
  s.initUserVoiceFeedback = function () {
    window.UserVoice = window.UserVoice || [];
    (function () {
      var uv = document.createElement('script');
      uv.type = 'text/javascript';
      uv.async = true;
      uv.src = '//widget.uservoice.com/JtF9LB73G7r3zwkipwE1LA.js';
      var s = document.getElementsByTagName('script')[0];
      s.parentNode.insertBefore(uv, s)
    })();
    UserVoice.push(['set', {
      accent_color: '#6aba2e',
      trigger_color: 'white',
      trigger_background_color: 'rgba(46, 49, 51, 0.6)'
    }]);
    if (document.getElementById('feedback') != null) {
      UserVoice.push(['addTrigger', '#feedback', {
        mode: 'contact'
      }]);
    }
    UserVoice.push(['autoprompt', {}]);
  };

  /**
   * Placeholder function for VuFind hook
   */
  s.updatePageForLoginParent = function () {},

  /**
   *
   */
  s.initBackgrounds = function () {
    var sidebarHeight = 0,
        elementHeight = 0,
        parentElement = $('.dirty-hack-column > .row').first(),
        sidebarFound = false,
        hasChildren = false;

    parentElement.children().each(function(index, element) {
      if($(element).hasClass('sidebar')) {
        sidebarHeight = $(element).outerHeight(true);
        sidebarFound = true;
        hasChildren = $(element).children().length > 0;
      } else {
        var tempHeight = $(element).outerHeight(true);

        if (tempHeight > elementHeight) {
          elementHeight = tempHeight;
        }
      }
    });

    if (elementHeight > sidebarHeight && sidebarFound && hasChildren) {
      parentElement.removeClass('bg-white').addClass('bg-grey');
      parentElement.children('div:first-of-type').removeClass('bg-grey').addClass('bg-white');
    } else {
      parentElement.removeClass('bg-grey').addClass('bg-white');

      if (sidebarFound && !hasChildren) {
        parentElement.children('div.sidebar').addClass('invisible');
      }
    }
  };

  /**
   * init backgrounds during transition to prevent flickering
   */
  s.initBackgroundsRecursive = function (count) {
    swissbib.initBackgrounds();
    swissbib.currentTimeout = setTimeout(
        function() {
          swissbib.initBackgroundsRecursive();
        },
        1
    );
  };

  /**
   * clear the init background initiation
   */
  s.destructBackgroundsRecursive = function () {
    swissbib.initBackgrounds();
    clearTimeout(swissbib.currentTimeout);
  };

  s.getParameterByName = function (name) {
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);

    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
  };

  s.initNationaLicensesFlow = (function() {

    $('.nlItem').on("click", function (event) {
      event.preventDefault();
      event.stopPropagation();
      var publisherURL = $(this).parent().next().text();
      //use a callback function to analyze the response
      //JSON as response object
      /*
      {
      status: ['publisherRequest'|'initializelogin']
      idpURL: 'url of the swisseduidIDP' - shouldn't be a fix value as part of the java script code
      }
       */
      $.ajax({
        "url": '/NationalLicences/signPost?publisher=' + encodeURIComponent(publisherURL)
      });
    });

    //delete it when we have implemented a correct response
    return false;

  });


  var Carousel = function () {
    var infos = {};

    /**
     * Adds information for a Bootstrap carousel component rendered on the current page.
     *
     * @param id
     * The unique identifier of the carousel. Each carousel component's root element has an id-attribute value of the
     * format results-carousel-<id> where <id> is the value passed in for this parameter.
     *
     * @param templates
     * An object that provides URL templates as strings:
     * ajax: The AJAX URL template to use for fetching new data for the carousel.
     * page: The page URL template is used by the carousel's column label to link to the detail page if the rendered
     *       data entry.
     * info: The info URL template is used to refer to an inline info box (e.g. knowledge-card) that renders most
     *       relevant information that belongs to the rendered data entry.
     *
     * @param pagination
     * A generic object that contains responsive pagination page size values. It uses the Bootstrap layout size prefixes
     * 'xs', 'sm', 'md' and 'lg'.
     *
     * @param thumbnail
     * The path to an image to be used as fallback image when a data entry in the carousel does not provide one.
     *
     * @param {number} total
     * The total amount of data entries to show in the carousel.
     *
     * @return {Object}
     * A configuration object with the given data as it is stored internally.
     */
    this.add = function (id, templates, pagination, thumbnail, total) {
      infos[id] = { id: id, templates: templates, pagination: pagination, thumbnail: thumbnail, total: total };
      return this.get(id);
    };

    /**
     * Accessor for previously registered carousel configuration.
     *
     * @param id
     * The unique identifier of the carousel to retrieve the configuration for.
     *
     * @returns {Object|null}
     * The configuration item entry in case the identifier exists or null otherwise.
     * The entry has the properties 'id', 'template' and 'pagination' which map 1 on 1 on the parameters passed
     * in to the addResultsCarouselInfo() method.
     */
    this.get = function(id) {
      var info = infos[id] || null;
      return info ? JSON.parse(JSON.stringify(info)) : null;
    };

    /**
     * Provides all available results carousel info identifiers.
     *
     * @return {Array}
     */
    this.identifiers = function () {
      return Object.keys(infos);
    };

    /**
     * Indicates whether there are carousel infos are registered.
     *
     * @returns {boolean}
     */
    this.available = function () {
      return this.getIdentifiers().length > 0;
    };
  };

  s.carousel = new Carousel();

})(window.swissbib = window.swissbib || {});


/**
 * Init Swissbib on ready & load
 */
$(document).ready(function () {
  swissbib.initOnReady();
});

$(document).ajaxComplete(swissbib.initBackgrounds);
$(document).on('show.bs.collapse', swissbib.initBackgroundsRecursive);
$(document).on('hide.bs.collapse', swissbib.initBackgroundsRecursive);
$(document).on('shown.bs.collapse', swissbib.destructBackgroundsRecursive);
$(document).on('hidden.bs.collapse', swissbib.destructBackgroundsRecursive);
