/**
 * Javascript handling to override actions in common.js
 *
 * @type {{initExportHandling: Function, exportFormHandler: Function, onExportFormHandlerSuccess: Function}}
 */
//swissbib.common = {

  /**
   * initializes form handling for export form
   */
/*
  initExportHandling: function() {
    Lightbox.addFormHandler(
      'exportForm',
      swissbib.common.exportFormHandler
    );
  },
*/

  /**
   * adds the ajax handling for export form
   *
   * @param evt
   */
  /*
  exportFormHandler: function(evt) {
    var data = Lightbox.getFormData($(evt.target));
    if (swissbib.common.exportNeedsRedirect(data)) {
      var myWindow = window.open('', '_blank');

      $(myWindow.document.body).append($('#format-redirect-text').html());
    }

    $.ajax({
      url: path + '/AJAX/JSON?' + $.param({method:'exportFavorites'}),
      type:'POST',
      dataType:'json',
      data:data,
      target: myWindow,
      success: swissbib.common.onExportFormHandlerSuccess,
      error:function(d,e) {
        if (this.target) {
          this.target.close();
        }
      }
    });

    return false;
  },
  */

  /**
   * adds success callback for export handling
   *
   * @param data
   */
  /*
  onExportFormHandlerSuccess: function(data) {
    if(data.data.needs_redirect && this.target) {
      this.target.location.href = data.data.result_url;
      Lightbox.close();
    } else {
      Lightbox.changeContent(data.data.result_additional);
    }
  },
  */

  /**
   * check if opening a new window is necessary
   *
   * @param data
   */
  /*
  exportNeedsRedirect: function(data) {
    var format = data.format;
    var redirect = false;

    $('#format-redirect').find('option').each(function() {
      if (this.value == format) {
        redirect = true;
      }
    });

    return redirect
  }
};
*/

/*
$(document).ready(
    function() {
      swissbib.common.initExportHandling();
    }
);
*/