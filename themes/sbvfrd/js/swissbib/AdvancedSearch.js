'use strict';

swissbib.AdvancedSearch = {

  searchDetails: {},
  searchFields: {},
  searchJoins: {},
  searchLabels: {},

  groupCount: 0,
  fieldCount: [],

  catTreeAutoSend: false,


  /**
   * Initialize when in advanced search view
   */
  init: function () {
    if (this.isInAdvancedSearch()) {
      this.initJsTree();
      this.initChosenMultiSelect();

      $("#addGroupLink").removeClass("offscreen");
    }

    if (this.isInAdvancedClassificationSearch()) {
      this.initAdvancedClassificationTabs();
    }
  },

  initChosenMultiSelect: function() {
    $('.chosen-select').chosen({no_results_text: VuFind.translate('MultiSelectNothingFound')});
  },

  /**
   * @return void
   */
  initJsTree: function () {
    jQuery(".classification-tree").jstree().bind("select_node.jstree", this.onJsTreeSelectNode);
    jQuery(".classification-tree").bind("close_node.jstree", this.onJsTreeCloseNode);
  },

  /**
   * @param {Event} event
   * @param {Object} data
   *
   * @return void
   */
  onJsTreeCloseNode: function(event, data) {
    data.instance.deselect_node(data.node);
  },

  /**
   * @param {Event} event
   * @param {Object} data
   *
   * @return void
   */
  onJsTreeSelectNode: function (event, data) {
    var el = jQuery('#' + data.selected[0]);

    if (el.data('openchildren') === 'yes') {
      if (data.instance.is_open(data.node)) {
        data.instance.close_node(data.node);
      } else {
        data.instance.open_node(data.node);
      }
    } else {
      el.find("input").first().attr("name", "filter[]");

      if (swissbib.AdvancedSearch.catTreeAutoSend)  swissbib.AdvancedSearch.sendForm(el);
    }
  },

  /**
   * @param {Element} el
   *
   * @return void
   */
  sendForm: function (el) {
    jQuery(el).parents('form:first').submit();
  },


  /**
   * Check whether current view is advanced search
   *
   * @return    {Boolean}
   */
  isInAdvancedSearch: function () {
    return location.pathname.indexOf('/Advanced') >= 0;
  },


  /**
   * Check whether current view is the advanced classification view
   *
   * @return    {Boolean}
   */
  isInAdvancedClassificationSearch: function () {
    return location.pathname.indexOf('/AdvancedClassification') >= 0;
  },



  /**
   * Add a group
   *
   * @param    {String}    [firstTerm]
   * @param    {String}    [firstField]
   * @param    {String}    [join]
   * @return    {Number}
   */
  addGroup: function (firstTerm, firstField, join) {
    firstTerm = firstTerm || '';
    firstField = firstField || '';
    join = join || '';

    var groupIndex = this.groupCount;
    var groupHtml = this.buildGroup(groupIndex, join);
    // Set to 0 so adding searches knows which one is first.
    this.groupCount++;
    this.fieldCount[groupIndex] = 0;

    // Add the new group into the page
    $("#groupPlaceHolder").before(groupHtml);
    // Add the first search field
    this.addField(groupIndex, firstTerm, firstField);

    // Keep the page in order
    this.reSortGroups();

    // Pass back the number of this group
    return groupIndex;
  },


  /**
   * Add a new group with 3 empty fields
   *
   */
  addNewGroup: function () {
    var groupIndex = this.addGroup();

    this.addField(groupIndex);
    this.addField(groupIndex);
  },


  /**
   * Delete group
   *
   * @param    {Number}    groupIndex
   */
  deleteGroup: function (groupIndex) {
    $("#group" + groupIndex).remove();
    this.reSortGroups()
  },


  /**
   * Delete group where clicked link is in
   *
   * @param    {HTMLElement}    element
   */
  deleteThisGroup: function (element) {
    this.deleteGroup(element.id.split('_').pop());
  },


  /**
   * Add a new field to the group the link belongs to
   *
   * @param    {HTMLElement}    element
   */
  addFieldToThisGroup: function (element) {
    this.addField(element.id.split('_').pop());
  },


  /**
   * Resort the groups
   *
   */
  reSortGroups: function reSortGroups() {
    var groupIndex = 0,
        that = this;

    $("#advSearchForm .group").each(function (index, group) {
      // If the number of this group doesn't match our running count
      if ($(this).attr("id") != "group" + groupIndex) {
        // Re-number this group
        that.reNumGroup(this, groupIndex);
      }
      groupIndex++;
    });

    this.groupCount = groupIndex;

    // Hide some group-related controls if there is only one group:
    var action = this.groupCount > 1 ? 'show' : 'hide';
    $("#groupJoin")[action]();
    $("#delete_link_0")[action]();
  },


  /**
   * Update group and field numbers after modifications
   *
   * @param    {HTMLElement}    groupElement
   * @param    {Number}        newGroupIndex
   */
  reNumGroup: function (groupElement, newGroupIndex) {
    // Keep the old details for use
    var oldGroupIndex = $(groupElement).attr("id").substring(5),
        searchHolder = $("#group" + oldGroupIndex + "SearchHolder");

    // Update the delete link with the new ID
    $("#delete_link_" + oldGroupIndex).attr("id", "delete_link_" + newGroupIndex);

    // Update the bool[] parameter number
    $(groupElement).find("[name='bool" + oldGroupIndex + "[]']:first").attr("name", "bool" + newGroupIndex + "[]");

    // Update the add term link with the new ID
    $("#add_search_link_" + oldGroupIndex).attr("id", "add_search_link_" + newGroupIndex);

    // Now loop through and update all lookfor[] and type[] parameters
    searchHolder.find("[name='lookfor" + oldGroupIndex + "[]']").each(function () {
      $(this).attr("name", "lookfor" + newGroupIndex + "[]");
    });
    searchHolder.find("[name='type" + oldGroupIndex + "[]']").each(function () {
      $(this).attr("name", "type" + newGroupIndex + "[]");
    });

    // Update search holder ID
    searchHolder.attr("id", "group" + newGroupIndex + "SearchHolder");

    // Finally, re-number the group itself
    $(groupElement).attr("id", "group" + newGroupIndex);
  },


  /**
   * Build group
   *
   * @param    {Number}    groupIndex
   * @param    {String}    join
   * @return    {String}
   */
  buildGroup: function (groupIndex, join) {
    var html = $("#adv-search-group").html(),
        template = Handlebars.compile(html),
        data = {
          groupIndex: groupIndex,
          searchDetails: this.buildGroupSearchDetails(groupIndex, join),
          addLink: this.buildGroupAddFieldLink(groupIndex)
        };

    return template(data);
  },


  /**
   * Add a new field to the group
   *
   * @param    {Number}    groupIndex
   * @param    {String}    [searchWord]
   * @param    {String}    [matchField]
   * @return    {Number}    New field index
   */
  addField: function (groupIndex, searchWord, matchField) {
    searchWord = searchWord || '';
    matchField = matchField || '';

    var fieldIndex = this.fieldCount[groupIndex],
        fieldHtml = this.buildField(groupIndex, searchWord, matchField, fieldIndex);

    $("#group" + groupIndex + "SearchHolder").append(fieldHtml);

    return ++this.fieldCount[groupIndex];
  },


  /**
   * Build field
   *
   * @param    {Number}    groupIndex
   * @param    {String}    searchWord
   * @param    {String}    matchField
   * @param    {Number}    fieldIndex
   * @return    {String}
   */
  buildField: function (groupIndex, searchWord, matchField, fieldIndex) {
    var html = $("#adv-search-field").html(),
        template = Handlebars.compile(html),
        data = {
          groupIndex: groupIndex,
          fieldIndex: fieldIndex,
          label: this.buildFieldLabel(groupIndex, fieldIndex),
          term: this.buildFieldTermText(groupIndex, fieldIndex, searchWord),
          selector: this.buildFieldFieldSelector(groupIndex, fieldIndex, matchField)
        };

    return template(data);
  },


  removeField: function (groupIndex, fieldIndex) {
    //remove console object because of IE8 / IE9 incompatibility
    //http://stackoverflow.com/questions/690251/what-happened-to-console-log-in-ie8
    //console.log(groupIndex, fieldIndex);
    this.fieldCount[groupIndex]--;
    $('#search_field_row_' + groupIndex + '_' + fieldIndex).remove();
  },


  /**
   * Build field label
   *
   * @param    {Number}    groupIndex
   * @param    {Number}    fieldIndex
   * @return    {String}
   */
  buildFieldLabel: function (groupIndex, fieldIndex) {
    var html = $("#adv-search-field-label").html(),
        template = Handlebars.compile(html),
        data = {
          groupIndex: groupIndex,
          fieldIndex: fieldIndex,
          label: this.searchLabels.searchLabel,
          classes: 'sr-only'
        };

    return template(data);
  },


  /**
   * Build term field
   *
   * @param    {Number}    groupIndex
   * @param    {Number}    fieldIndex
   * @param    {String}    searchWord     *
   * @return    {String}
   */
  buildFieldTermText: function (groupIndex, fieldIndex, searchWord) {
    var html = $("#adv-search-field-term-text").html(),
        template = Handlebars.compile(html),
        data = {
          groupIndex: groupIndex,
          fieldIndex: fieldIndex,
          value: searchWord,
          size: 50
        };

    return template(data);
  },


  /**
   * Build selector for matching field
   *
   * @param    {Number}    groupIndex
   * @param    {Number}    fieldIndex
   * @param    {String}    matchField
   * @return    {String}
   */
  buildFieldFieldSelector: function (groupIndex, fieldIndex, matchField) {
    var html = $("#adv-search-field-field-selector").html(),
        template = Handlebars.compile(html),
        data = {
          groupIndex: groupIndex,
          fieldIndex: fieldIndex,
          label: this.searchLabels.searchFieldLabel,
          options: []
        };

    jQuery.each(this.searchFields, function (name, label) {
      data.options.push({
        name: name,
        label: label,
        selected: matchField == name
      })
    });

    return template(data);
  },


  /**
   * Build add field link for group
   *
   * @param    {Number}    groupIndex
   * @return    {String}
   */
  buildGroupAddFieldLink: function (groupIndex) {
    var html = $("#adv-search-group-addfield").html(),
        template = Handlebars.compile(html),
        data = {
          groupIndex: groupIndex,
          addLabel: this.searchLabels.addSearchString
        };

    return template(data);
  },


  /**
   * Build search details for group
   *
   * @param    {Number}    groupIndex
   * @param    {String}    selectedJoin
   * @return    {String}
   */
  buildGroupSearchDetails: function (groupIndex, selectedJoin) {
    var html = $("#adv-search-group-searchDetails").html(),
        template = Handlebars.compile(html),
        data = {
          label: this.buildFieldLabel(groupIndex, this.fieldCount[groupIndex]),
          groupIndex: groupIndex,
          matchLabel: this.searchLabels.searchMatch,
          deleteLabel: this.searchLabels.deleteSearchGroupString,
          joins: []
        };

    jQuery.each(this.searchJoins, function (name, label) {
      data.joins.push({
        name: name,
        label: label,
        selected: selectedJoin == name
      })
    });

    return template(data);
  },


  /**
   * Initializes classification tabs
   */
  initAdvancedClassificationTabs: function() {
    $('#tabbed-tree').find('.nav-tabs a').click(function (e) {
      e.preventDefault()
      $(this).tab('show')
    })
  }

};