swissbib.Settings = {

  init: function () {
    'use strict';
    this.observeFormChange();
  },

  observeFormChange: function () {
    'use strict';
    $('#settings-form').find('select').change(this.onFormChange);
  },

  onFormChange: function (event) {
    'use strict';
    $(this).parents('form').submit();
  }
};