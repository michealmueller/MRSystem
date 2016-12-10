var ko = ko || {};
var tazworks = tazworks || {};

/**
 * Adds the restriction and format handlers to knockout js if it is included in the scripts.
 *
 * Restriction will prevent invalid characters from being entered into a text field.
 * Format will restrict and change the structure of the text being input.
 *
 * Example:
 * data-bind="value: object.field, format: 'Phone'
 * data-bind="value: object.field, format: 'Date'
 * data-bind="value: object.field, restriction: 'AlphaNumeric'
 * data-bind="value: object.field, restriction: 'Numeric'
 * data-bind="value: object.field, restriction: 'PhoneExtension'
 */
ko.bindingHandlers['restriction'] = {
  init: function(element, valueAccessor) {
    var value = ko.utils.unwrapObservable(valueAccessor()).toLowerCase();
    var $element = jQuery(element);

    var restrictions = {
      alphanumeric: "^[a-zA-Z0-9 ]$",
      numeric: "^[0-9]$",
      phoneextension: "^[0-9]$",
      username: "^[a-zA-Z0-9!$%&'()*,-./;@_ ]$",
      name: "^[a-zA-Z0-9 /#$()\\-,.'\":;%_@&+]$",
      jobtitle: "^[a-zA-Z0-9 '!\"#%&'()+,./:;@\\_-]$",
      inquiryname: "^[a-zA-Z0-9_\\- ]$"
    };

    // Don't attempt to enfore a restriction that isn't recognized.
    if (Object.keys(restrictions).indexOf(value) === -1) {
      return;
    }

    $element.keypress(function (event) {
      // Don't enfore restictions on control codes (enter, tab, etc)
      if (!tazworks.validation.isControlCode(event.keyCode)) {
        var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);

        // Test string against allowed characters
        var expression = new RegExp(restrictions[value]);
        if (!expression.test(key)) {
          event.preventDefault();
          return false;
        }
      }
    });
  }
};

ko.bindingHandlers['format'] = {
  init: function(element, valueAccessor, allBindings) {
    var value = ko.utils.unwrapObservable(valueAccessor());
    var $element = jQuery(element);

    if (Object.keys(tazworks.validation.formatters).indexOf(value) === -1) {
      return;
    }

    $element.keyup(function(evt){
      if (!tazworks.validation.isControlCode(evt.keyCode)) {
        var newValue = tazworks.validation.formatters[value]($element.val());

        if (typeof(allBindings.get('value')) === 'function') {
          allBindings.get('value')(newValue);
        } else {
          allBindings().value = newValue;
        }
        $element.val(newValue);
      }
    });
  }
};

ko.bindingHandlers['requireif'] = {
  update: function (element, valueAccessor, allBindingsAccessor) {
    var required = ko.utils.unwrapObservable(valueAccessor());
    if (required) {
      // Add the attribute of required
      ko.bindingHandlers.attr.update(element, function () {
        return {required: "required"};
      }, allBindingsAccessor);
    } else {
      jQuery(element).removeAttr("required");
    }
  }
};

// Makes elements show/hide using jQuery's slideDown()/slideUp() methods
ko.bindingHandlers['slideVisible'] = {
  init: function(element, valueAccessor) {
    // Initially set the element to be instantly visible/hidden depending on the value
    var value = valueAccessor();
    jQuery(element).toggle(ko.unwrap(value)); // Use "unwrapObservable" so we can handle values that may or may not be observable
  },
  update: function(element, valueAccessor) {
    // Whenever the value subsequently changes, slowly slide the section open or closed
    var value = valueAccessor();
    ko.unwrap(value) ? jQuery(element).slideDown(500) : jQuery(element).slideUp(500);
  }
};

ko.bindingHandlers['hidden'] = {
  update: function(element, valueAccessor) {
    value = ko.utils.unwrapObservable(valueAccessor());
    isCurrentlyVisible = (element.style.display !== "none");

    if (value && isCurrentlyVisible)
      element.style.display = "none";
    else if (!value && !isCurrentlyVisible)
      element.style.display = "";
  }
};

ko.bindingHandlers['foreachprop'] = {
  transformObject: function (obj) {
    var properties = [];
    ko.utils.objectForEach(obj, function (key, value) {
      properties.push({ key: key, value: value });
    });
    return properties;
  },
  init: function(element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
    var properties = ko.pureComputed(function () {
      var obj = ko.utils.unwrapObservable(valueAccessor());
      return ko.bindingHandlers.foreachprop.transformObject(obj);
    });
    ko.applyBindingsToNode(element, { foreach: properties }, bindingContext);
    return { controlsDescendantBindings: true };
  }
};