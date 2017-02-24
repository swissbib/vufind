/**
 * Manage accordion auto expand/collapse actions on record holding tab
 *
 */
swissbib.Accordion = {

  /**
   * @var {String}
   */
  cookieName: 'expandedGroups',

  /**
   * @var {Number}
   */
  idRecord: null,

  /**
   * Initialize for record
   *
   * @param  {Number}  idRecord
   */
  initRecord: function(idRecord) {
      this.idRecord = idRecord;
  },

  /**
   * Saves state of expanded groups
   *
   */
  saveExpandedGroups: function() {
      var expandedGroupIds = [];

      $("#accordion").find(".in").each(function (index) {
          var currentItem = $(this);

          //only saves state of first hierachie level
          if (currentItem.attr('level') == 1) {
              expandedGroupIds.push(currentItem.attr('id'));
          }
      });

      if(expandedGroupIds.length > 0) {
          var cookieData = {};
          cookieData[this.idRecord] = expandedGroupIds;
          $.cookie(swissbib.Accordion.cookieName, JSON.stringify(cookieData));
      } else {
          $.cookie(swissbib.Accordion.cookieName, null);
      }
  },

  /**
   * Get QueryString Parameter
   *
   * param {String} name
   *
   * return {String|null}
   */
  getParameterByName: function(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results === null ? null : decodeURIComponent(results[1].replace(/\+/g, " "));
  }

};

/**
* Remains state of expanded accordion group
*/
$(document).ready(function () {
    var accordionContainer = $("#accordion");

    //when a group is shown, save state of expanded groups
    accordionContainer.on('shown.bs.collapse', function (e) {
        var target = $(e.target);
        //level 1 means group
        if(target.attr('level') == 1) {
            swissbib.Accordion.saveExpandedGroups();
        }
    });

    //when a group is closed, save state of expanded groups
    accordionContainer.on('hidden.bs.collapse', function (e) {
        var target = $(e.target);
        //level 1 means group
        if(target.attr('level') == 1) {
            //collapse childern
            target.find('.in').collapse('hide');
            swissbib.Accordion.saveExpandedGroups();
        }
    });

    //on (re)load - open "collapseXX" divs
    var expanddiv = swissbib.Accordion.getParameterByName('expanddiv');
    if (expanddiv != null) {
        accordionContainer.find("#" + expanddiv).collapse('show');
        accordionContainer.find("a[href='#" + expanddiv + "']").click();
        location.hash = expanddiv;
    }

    //on (re)load - open direct link library
    var expandlib = swissbib.Accordion.getParameterByName('expandlib');
    if (expandlib != null) {
        var favoriteId = "favorite";
        var groupId =  expandlib.split('-')[0];
        var institutionId =  expandlib.split('-')[1];

        //open library in favorites to
        accordionContainer.find("#collapse-" + favoriteId).collapse('show');
        accordionContainer.find("a[href='#collapse-" + favoriteId + "-" + institutionId + "']").click();

        accordionContainer.find("#collapse-" + groupId).collapse('show');
        accordionContainer.find("a[href='#collapse-" + groupId + "-" + institutionId + "']").click();
    }

    //on (re)load - open previously expanded groups. if none, open favorites as default an clear cookie as user opened a new record
    var expandedGroupIds = JSON.parse($.cookie(swissbib.Accordion.cookieName));
    if (expandedGroupIds != null && expandedGroupIds[swissbib.Accordion.idRecord] != null) {
        $.each((expandedGroupIds[swissbib.Accordion.idRecord]), function (index, value){
            $("#" + value).collapse('show');
        });
    } else {
        accordionContainer.find("#collapse-favorite").collapse('show');
        $.cookie(swissbib.Accordion.cookieName, null);
    }

    window.onload = function() {
        if(location.hash) {
            var elId = location.hash.replace('#','');
            var scrollToEl = document.getElementById(elId);
            scrollToEl.scrollIntoView(true);
            console.log(elId);
        }
    }
});
