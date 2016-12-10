/**
 * These scripts will be run on every page.
 */

/*
 * This snippet of code will ensure that end users do not get errors in Internet Exploder due to an undefined console or console methods.
 */
(function() {
    //These are all the methods defined by chrome console as of Oct 29 2015
    var methods = [
       'memory', 'debug', 'error', 'info', 'log', 'warn', 'dir', 'dirxml', 'table', 'trace', 'assert',
       'count', 'markTimeline', 'profile', 'profileEnd', 'time', 'timeEnd', 'timeStamp', 'timeline',
       'timelineEnd', 'group', 'groupCollapsed', 'groupEnd', 'clear'
    ];
    var idx = methods.length;
    var console = (window.console = window.console || {});

    while (idx--) {
        var method = methods[idx];
        if (!console[method]) {
            console[method] = function () {};
        }
    }
}());


var ISAjax = function($) {
  /**
   * @data parameter passed to the ajax error callback
   * Requires bootbox
   *
   * @defaultMessage the message to display if there isn't a message in the data parameter
   * @displayMessagElement if this is defined, display the message in this element.  This is
   * a reference to the actual element, not an id or name of the element.
   * @append if this is to be displayed inside the specified element, should we append or replace
   * the contents of that element? if false or undefined, replace the contents.
   */
  function handleAjaxError(data, defaultMessage, displayMessageElement, append) {
    $.unblockUI();
    defaultMessage = typeof defaultMessage !== 'undefined' ? defaultMessage
        : "Unable to complete the requested action";
    append = typeof append !== 'undefined' ?  append : false;

    if (typeof data !== 'undefined') {
      if (typeof data.responseJSON !== 'undefined') {

        var jData = data.responseJSON;

        if (typeof jData.message !== 'undefined') {
          displayMessage(jData.message, displayMessageElement, append);
          if (typeof jData.fieldValidationErrors !== 'undefined') {
            handleFieldValidationErrors(jData.fieldValidationErrors);
          }
        }
        if (typeof jData.devMessage !== 'undefined') {
          console.log(jData.devMessage);
        }

      }
      else if (typeof data.responseText !== 'undefined') {
        displayMessage(data.responseText, displayMessageElement, append);
      }
      else {
        displayMessage(defaultMessage, displayMessageElement, append);
      }
    }
    else {
      displayMessage(defaultMessage, displayMessageElement, append);
    }
  };


  function displayMessage(message, displayMessageElement, append) {

    if (typeof displayMessageElement !== "undefined") {
      $(displayMessageElement).show();
      if (append) {
        $(displayMessageElement).append(message);
      }
      else {
        $(displayMessageElement).html(message);
      }
    }
    else {
      bootbox.alert(message, function() { });
    }
  };


  /**
   * For each validation error, highlight the field with the error and add
   * any message for that field
   */
  function handleFieldValidationErrors(fieldValidationErrors) {
    for (var fieldIndex = 0; fieldIndex < fieldValidationErrors.length; fieldIndex++) {
      $('#'+fieldValidationErrors[fieldIndex].fieldName).addClass('error');
      if (fieldValidationErrors[fieldIndex].message) {
        hightlightError($('#'+fieldValidationErrors[fieldIndex].fieldName), fieldValidationErrors[fieldIndex].message);
      }
    }
  };


  var errorToolTipTemplate = '<div class="tooltip error"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>';

  /**
   * Maybe this should be dependent on having validator.js included, because this method is the
   * same as one in that file.  Highlights an error in an individual form field
   */
  function hightlightError(element, errorMessage) {
    var keydownHandler = function() {
      $(element).tooltip('hide');
      $(element).tooltip('destroy');
      $(element).removeAttr('title').removeClass("error");
      $(element).next('.tooltip').remove();
      $(element).unbind('keydown', keydownHandler);
    };
    $(element).attr('title', errorMessage).addClass('error').tooltip({
      template: errorToolTipTemplate
    }).bind('keydown', keydownHandler);
    $(element).blur(function() {
      // for some reason, sometimes the error tooltip doesn't get removed
      // and it blocks the elements above this element and keeps you
      // from clicking in them.  This block of code should help
      // remove the error tooltip when it isn't needed anymore.
      if (!$(this).hasClass('error')) {
        $(this).tooltip('hide');
        $(this).tooltip('destroy');
        $(this).removeAttr('title').removeClass("error");
        $(this).next('.tooltip').remove();
        $(this).unbind('keydown', keydownHandler);
      }
    });
  }


  return {
    handleAjaxError : handleAjaxError
  };

} (jQuery)


var ISHandlebars = function($) {

  var REPLACE = 1;
  var APPEND = 2;
  var PREPEND = 3;

  var acceptedStrategies = [REPLACE,APPEND,PREPEND]

  /**
   * Set of handlebars templates that have already been read from the file
   * system into memory and compiled.
   */
  var handlebarsTemplates = {};

  /**
   * Applies the data to the Handlebars template and renders it into the
   * specified location
   * @param compiledTemplate
   * @param data
   * @param targetEntity - if an id, include the '#'; if a class, include the '.'
   * @param strategy - (optional, defaults to REPLACE) whether to replace the contents of the targetEntity,
   *   or to append or prepend the contents
   */
  function renderHandlebarsTemplate(compiledTemplate, data, targetEntity, strategy) {
    if (typeof strategy === "undefined" || strategy === null
        || acceptedStrategies.indexOf(strategy) == -1) {
      strategy = REPLACE;
    }
    if (compiledTemplate !== undefined) {
      var html = compiledTemplate(data);

      switch(strategy) {
      case APPEND:
        $(targetEntity).append(html);
        break;
      case PREPEND:
        $(targetEntity).prepend(html);
        break;
      default:
        $(targetEntity).html(html);
      }

    }
  }


  /**
   * Locates the Handlebars template at that path provided, and applies
   * the data provided to that template.  Then renders the results as the html
   * body of the targetEntity.
   *
   * @param templatePath
   * @param templateData
   * @param targetEntity
   * @param callback method to call when the ajax call returns successfully
   * @param strategy - (optional, defaults to REPLACE) whether to replace the contents of the targetEntity,
   *   or to append or prepend the contents.
   *
   */
  function displayHandlebarsTemplate(templatePath, templateData, targetEntity, callback, strategy) {
    // If we've already used this template once while on this page, don't bother
    // reading in that file again.  Just pull it from memory.
    if (handlebarsTemplates[templatePath] === undefined) {
      $.ajax({
        type: 'GET',
        url: templatePath,
        datatype: 'text',
        success: function (templateContents, status, response) {
          handlebarsTemplates[templatePath] = Handlebars.compile(templateContents);
          renderHandlebarsTemplate(handlebarsTemplates[templatePath], templateData, targetEntity, strategy);
          if (callback != null) {
            callback();
          }
        },
        error: function (data, response, error) {
          bootbox.alert("An error occurred while rendering this screen.  Please try again, and if the problem continues contact your system administrator.", function() {});
        }
      });
    }
    else {
      renderHandlebarsTemplate(handlebarsTemplates[templatePath], templateData, targetEntity, strategy);
      if (callback != null) {
        callback();
      }
    }
  }


  return {
    displayHandlebarsTemplate: displayHandlebarsTemplate,
    REPLACE: REPLACE,
    APPEND: APPEND,
    PREPEND: PREPEND
  }

}(jQuery)