// compatability safe
(function($) {

  /**
   * Add focusFirstVisible to list of jQuery functions
   * Highlights the first field with an error otherwise, the first field on the
   * selected element.
   */
  $.fn.focusFirstVisible = function() {
    var element = $(this);

    // If called without context, assume the entire page is the context.
    if (typeof element['context'] === 'undefined') {
      element = $('body');
    }

    var visibleErrorFields = element.find("input.error, .checkbox-inline.error input, select.error, textarea.error").filter(':visible:enabled');
    var errorTabs = element.find(".nav-tabs li.error");

    // Look for errors in this section
    if (visibleErrorFields.length) {
      // Highlight the first visible one
      visibleErrorFields.filter(":first").focus().select();
    }
    // Look for error on other tabs
    else if (errorTabs.length) {
      errorTabs.find('a').targetFocusFirstVisible();
    }
    // Otherwise, just highlight first field you come to
    else {
      // Highlight the first field
      element.find('input, select, textarea').not(':button').filter(':visible:enabled:first').focus().select();
    }
  };

  /**
   * Highlight the first element on the element that this element is linking to.
   */
  $.fn.targetFocusFirstVisible = function() {
    // click to follow the link (for tabs and modals)
    // if this leaves the page everything else will stop
    $(this).click();

    // Get the Id of the associated link target (with the #)
    var target = $(this).attr('href');
    var target_id = target.substring(target.indexOf('#'));

    // Use focusFirstVisible for consistency but with the new context
    if (typeof target_id !== 'undefined') {
      $(target_id).focusFirstVisible();
    }
  };

})(jQuery);