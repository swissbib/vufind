'use strict';

swissbib.Holdings = {


  /**
   * Initialize for record
   *
   * @param  {Number}  idRecord
   */
  initRecord: function (idRecord) {
    this.getInstitutionHeaders().click($.proxy(this.onInstitutionClick, this, idRecord));
  },


  /**
   * Get all institution headers
   *
   * @returns  {*|jQuery|HTMLElement}
   */
  getInstitutionHeaders: function () {
    return $('.institutionToggler');
  },


  /**
   * Handle institution heading click
   * Click will toggle the container, update content with ajax
   * Existing ajax spinner will be replaced
   *
   * @param  {Number}  idRecord
   * @param  {Event}    event
   */
  onInstitutionClick: function (idRecord, event) {
    var isLoaded = !!$.data(event.target, 'loaded'),
        dataParts, groupCode, institutionCode;

    if (!isLoaded) {
      dataParts = $(event.target).attr('id').split('-');
      groupCode = dataParts[2];
      institutionCode = dataParts[3];

      // Start ajax spinner
      this.startSpinner(institutionCode, groupCode);
      // Load table
      this.loadHoldingTable(idRecord, groupCode, institutionCode);
      // Mark institution as loaded
      $.data(event.target, 'loaded', true)
    }
  },


  /**
   * Load holdings table for institution
   *
   * @param  {Number}  idRecord
   * @param  {String}  groupCode
   * @param  {String}  institutionCode
   */
  loadHoldingTable: function (idRecord, groupCode, institutionCode) {
    var url = window.path + '/Holdings/' + idRecord + '/' + institutionCode,
        callback = $.proxy(this.onHoldingTableLoaded, this, idRecord, groupCode, institutionCode),
        container = $('.holding-institution-' + groupCode + '-' + institutionCode);

    container.load(url, '', callback);
  },


  /**
   * Handle container loaded
   *
   * @param  {Number}  idRecord
   * @param  {String}  groupCode
   * @param  {String}  institutionCode
   * @param  {String}  responseText
   * @param  {String}  status
   * @param  {Object}  response
   */
  onHoldingTableLoaded: function (idRecord, groupCode, institutionCode, responseText, status, response) {
    if (status === 'error') {
      $('#holdings-institution-' + groupCode + '-' + institutionCode).html('Request failed. Information is currently not available');
    }
  },


  /**
   * Start ajax spinner
   *
   * @param  {String}  institutionCode
   */
  startSpinner: function (institutionCode, groupCode) {
    var loaderBox = $('.holding-ajax-spinner-' + groupCode + '-' + institutionCode);
    loaderBox.css({
      display: 'inline-block'
    });
  },


  /**
   * Open EOD link in popup
   *
   * @param  {String}  url
   * @param  {Number}  width
   * @param  {Number}  height
   */
  openEODPopup: function (url, width, height) {
    width = width || 650;
    height = height || 760;

    window.open(url, 'eodpopup', 'scrollbars,height=' + height + ',width=' + width).focus();
  },


  /**
   * Show map popup
   * Allow window size overrides
   *
   * @param  {String}  url
   * @param  {Number}  width
   * @param  {Number}  height
   */
  showMap: function (url, width, height) {
    width = width || 650;
    height = height || 760;

    window.open(url, 'mappopup', 'height=' + height + ',width=' + width).focus();
  },


  /**
   * Open popup with details about holding items
   *
   * @param  {String}  contentUrl    URL to load popup content from
   * @param  {String}  dialogTitle    Title of dialog
   */
  openHoldingItemsPopup: function (contentUrl, dialogTitle) {
    var that = this,
        popup = $('#holdings-items-popup');

    // Clear content
    popup.html('');

    popup.html('Loading... :)');
    VuFind.lightbox.ajax({ url: contentUrl });
  },


  /**
   *
   * @param {Object} dialog
   */
  centerPopup: function (dialog) {
    dialog.dialog("option", "position", { my: "center", at: "center", of: window });
  },


  /**
   * Enable special features in popup
   * Observe filter changes and paging links
   *
   * @param  {Object}  dialog
   */
  setupItemsPopup: function (dialog) {
    var that = this,
        popup = $('#holdings-items-popup'),
        paging = $('#holding-items-popup-paging'),
        form = popup.find('form');

    popup.unmask();

    paging.find('a').click(function (event) {
      event.preventDefault();
      that.updateHoldingsPopup(event.target.href, dialog);
    });

    popup.find('select').change(function (event) {
      popup.mask("Loading...");
      $.ajax({
        url: form.attr('action'),
        data: form.serialize(),
        success: function (response) {
          popup.html(response);
          that.setupItemsPopup(dialog);
        }
      });
    });
    that.centerPopup(dialog);
  },


  /**
   * Load new content from URL and install handlers again
   *
   * @param  {String}  url
   */
  updateHoldingsPopup: function (url, dialog) {
    var that = this,
        popup = $('#holdings-items-popup');

    popup.mask("Loading...");

    popup.load(url, function () {
      that.setupItemsPopup(dialog);
    });
  },


  /**
   * Show QR code window
   *
   * @param  {String}    winKey
   * @param  {String}    url
   * @param  {String}    text
   */
  showQrCode: function (winKey, url, text) {
    var win = $('#qrcode-' + winKey);

    $('img', win).attr('src', url);
    $('.datatext', win).html(text);

    win.dialog({
      modal: false,
      resizable: false
    });
  }

};
