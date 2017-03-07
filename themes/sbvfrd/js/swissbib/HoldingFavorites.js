'use strict';

/**
 * Manage favorite actions on record holding tab
 *
 */
swissbib.HoldingFavorites = {

  /**
   * @var {String}
   */
  baseUrl: '/MyResearch/Favorites',


  /**
   * Initialize record
   *
   */
  initRecord: function () {
    this.baseUrl = window.path + this.baseUrl;

    this.setupClickHandlers();
  },


  /**
   * Setup click handlers for add and remove of favorite institutions
   *
   */
  setupClickHandlers: function () {
    var that = this, allMiniActions, notFavorised,
        favoriteInstitutionCodes = this.getFavoriteInstitutionCodes();

    $.each(favoriteInstitutionCodes, function (index, institutionCode) {
      $('.miniactions-' + institutionCode).find('.institutionFavorite')
          .addClass('miniaction_favorite_remove')
          .click($.proxy(that.onRemoveFavoriteIconClick, that))
          .data('favorised', true);
    });

    allMiniActions = $('#holdings-tab').find('.miniactions');

    notFavorised = $.grep(allMiniActions, function (node, index) {
      return $(node).find('.institutionFavorite').data('favorised') !== true;
    });

    $(notFavorised).find('.institutionFavorite')
        .addClass('miniaction_favorite_add')
        .click($.proxy(this.onAddFavoriteIconClick, this));
  },


  /**
   * Handle remove click
   *
   * @param  {Object}  event
   */
  onRemoveFavoriteIconClick: function (event) {
    var institutionBox = $(event.target).parents('.institutionBox').get(0),
        institutionCode = institutionBox.id.split('-')[3];

    this.updateFavorite(institutionCode, 'delete');
  },


  /**
   * Handle add click
   *
   * @param  {Object}  event
   */
  onAddFavoriteIconClick: function (event) {
    var institutionBox = $(event.target).parents('.institutionBox').get(0),
        institutionCode = institutionBox.id.split('-')[3];

    this.updateFavorite(institutionCode, 'add');
  },


  /**
   * Send favorite update request (add or delete) for institution
   *
   * @param  {String}  institutionCode
   * @param  {String}  action
   * @param  {Function}  [callback]
   */
  updateFavorite: function (institutionCode, action, callback) {
    var url = this.baseUrl + '/' + action,
        data = {
          institution: institutionCode
        },
        success = function (response) {
          if (callback) {
            callback(institutionCode, action, response);
          } else {
            $('body').mask(VuFind.translate('favoriteReload'));
            location.reload();
          }
        };

    $.post(url, data, success);
  },


  /**
   * Get codes of favorite institutions (in favorite box)
   *
   * @returns {String[]}
   */
  getFavoriteInstitutionCodes: function () {
    var favoriteTogglers = $('#holdings-favorite').find('.institutionToggler');

    return $.map(favoriteTogglers, function (node, index) {
      return $(node).attr('id').split('-').pop();
    });
  }

};