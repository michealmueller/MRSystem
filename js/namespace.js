// JavaScript forEach shim for browser not yet implementing.
if ( !Array.prototype.forEach ) {
  Array.prototype.forEach = function(fn, scope) {
    for(var i = 0, len = this.length; i < len; ++i) {
      fn.call(scope, this[i], i, this);
    }
  };
}

// Standard string functions.
if (typeof String.prototype.startsWith !== 'function') {
  String.prototype.startsWith = function (str){
    return this.slice(0, str.length) === str;
  };
}

if (typeof String.prototype.endsWith !== 'function') {
  String.prototype.endsWith = function(suffix) {
      return this.indexOf(suffix, this.length - suffix.length) !== -1;
  };
}

var tazworks = {
  currentDataPath: window.location.protocol + '//' + window.location.hostname + (window.location.port ? ':' + window.location.port : '') + window.location.pathname + '/data',

  runCheck: function(path, done, error) {
    jQuery.ajax({
      url: path + '?ajax=true',
      type: 'GET'
    }).done(function(data){
      if (done !== undefined) {
        done(data);
      }
    }).error(function(data){
      if (error !== undefined) {
        error(data);
      }
    });
  },

  submitText: function(path, text, csrfToken, done, error) {
    jQuery.ajax({
      url: path + '?ajax=true',
      type: 'POST',
      processData: false,
      headers: {
        'Content-Type': 'text/plain',
        'token': csrfToken
      },
      data: text
    }).done(function(data){
      if (done !== undefined) {
        done(data);
      }
    }).error(function(data){
      if (error !== undefined) {
        error(data);
      }
    });
  },

  submitObject: function(path, object, csrfToken, done, error) {
    var jsonSubmission = JSON.stringify(object);

    jQuery.ajax({
      url: path + '?ajax=true',
      data: jsonSubmission,
      type: 'POST',
      dataType: "json",
      processData: false,
      headers: {
        'Content-Type': 'application/json',
        'token': csrfToken,
        'Accept': 'application/json'
      }
    }).done(function(data){
      if (done !== undefined) {
        done(data);
      }
    }).error(function(data){
      if (error !== undefined) {
        error(data);
      }
    });
  },

  submitObjectWithToken: function(path, object, done, error) {
    tazworks.submitObject(path, object, object.csrfToken, done, error);
  },

  mapFromValues: function(valuesObject, valuesList) {
    valuesList.forEach(function(value) {
      valuesObject[value.name] = value.value;
     });
  },

  mapToValues: function(valuesObject, valuesList) {
    valuesList.forEach(function(value) {
      value.value = valuesObject[value.name];
    });
  },

  loadTemplate: function(path, actionElement, doneCallback) {
    jQuery.ajax({
      url: path + '.html?ajax=true',
      cache: false
    }).done(function(contents){
      if (typeof actionElement === 'string') {
        // Is id for the element to load contents.
        jQuery(actionElement).html(jQuery(contents));
      }

      if (typeof actionElement === 'function') {
        actionElement(contents);
      }

      if (typeof actionElement === 'object' && actionElement.tagName !== undefined) {
        // Is a tag element, contents will be loaded in this element.
        jQuery(actionElement).html(jQuery(contents));
      }

      if (doneCallback !== undefined) {
        doneCallback(contents);
      }
    });
  },

  loadScriptAndRun: function(path, functionName) {
    tazworks.loadScript(path, function(){
      eval(functionName + '();');
    });
  },

  loadScript: function(path, doneCallback) {
    if (!path.endsWith('.js')) {
      path += '.js';
    }

    jQuery.ajax({
      url: path + '?ajax=true',
      dataType: 'script',
      cache: false
    }).done(function(content){
      if (doneCallback !== undefined) {
        doneCallback(content);
      }
    }).fail(function( jqxhr, settings, exception ) {
      console.log(exception);
    });
  },

  loadTemplateAndScript: function(path, actionElement, doneCallback) {
    tazworks.loadTemplate(path, actionElement, function(){
      tazworks.loadScript(path, function() {
        if (doneCallback !== undefined) {
          doneCallback();
        }
      });
    });
  },

  /**
   * Looks up  specific zipcode for state and city. Data is cache din session storage if
   * available
   *
   * @param {string} zipCode
   * @param {element} cityElement
   * @param {element} stateElement
   * @param {function} callback
   * @returns {undefined}
   */
  lookupZipCode: function(zipCode, cityElement, stateElement, callback) {
    if (zipCode.length > 4) {
      var zipCheck = null;
      if (typeof(Storage) !== undefined) {
        zipCheck = sessionStorage.getItem('zip-' + zipCode);
      }

      if (zipCheck === null) {
        jQuery.get('/is/lookup/zip/' + zipCode + '/data', function(results){
          if (results.length > 0) {
            jQuery(cityElement).val(results[0].city);
            jQuery(stateElement).val(results[0].state);

            if (typeof(Storage) !== undefined) {
              sessionStorage.setItem('zip-' + zipCode, JSON.stringify(results));
            }

            if (callback !== undefined) {
              callback(results[0].city, results[0].state);
            }
          }
        });
      } else {
        var results = JSON.parse(zipCheck);

        if (results.length > 0) {
          jQuery(cityElement).val(results[0].city);
          jQuery(stateElement).val(results[0].state);

          if (callback !== undefined) {
            callback(results[0].city, results[0].state);
          }
        }
      }
    }
  },

  /**
   * Runs standard ko.observable but also automatically subscribes to the new value and
   * sets the source view model when the value in the observable changes. The changed
   * callback function will be called if defined with the new value.
   *
   * @param {string} name
   * @param {object} originalObject
   * @param {object} targetObject
   * @param {function} changed
   * @returns {ko.observable.observable|tazworks.observable.targetObject}
   */
  observable: function(name, originalObject, targetObject, changed) {
    targetObject[name] = ko.observable(originalObject[name]);

    targetObject[name].subscribe(function(newValue) {
      if (changed !== undefined) {
        changed(newValue);
      }

      originalObject[name] = newValue;
    });

    return targetObject[name];
  },

  validation: {
    formatters: {
      'Price': function(value, fullFormat) {
        var price = value.replace(/[^0-9.]/g,'');

        // Eliminate leading zero when second character is not '.'
        if (price.length > 1 && price[0] === '0' && price[1] !== '.') {
          price = price.substring(1);
        }

        if (price.split(".").length - 1 > 1) {
          // Trim second period and everything after.
          price = price.substring(0, price.indexOf('.', price.indexOf('.') + 1));
        }

        // Trim to only two decimal places
        if (price.indexOf('.') > -1) {
          var centsStart = price.indexOf('.') + 1;
          var cents = price.substring(centsStart);
          if (cents.length > 2) {
            price = price.substring(0, centsStart + 2);
          }

          if (fullFormat) {
            if (cents.length < 1) {
              price += '00';
            } else {
              if (cents.length < 2) {
                price += '0';
              }
            }
          }
        } else {
          if (fullFormat) {
            price += '.00';
          }
        }

        return price;
      },
      'price': function(value, fullFormat) {
        return tazworks.validation.formatters.Price(value, fullFormat);
      },
      'Phone': function(value) {
        var digitsOnly = value.replace(/\D+/g,'');
        var number;
        if (digitsOnly.length > 3 && digitsOnly.length <= 6) {
          number = digitsOnly.replace(/(\d{3})(\d)/, "($1) $2");
        } else {
          if (digitsOnly.length > 6 && digitsOnly.length < 10) {
            number = digitsOnly.replace(/(\d{3})(\d{3})(\d)/, "($1) $2-$3");
          } else {
            if (digitsOnly.length >= 10) {
              digitsOnly = digitsOnly.substring(0, 10);
              number = digitsOnly.replace(/(\d{3})(\d{3})(\d{4})/, "($1) $2-$3");
            } else {
              number = digitsOnly;
            }
          }
        }

        return number;
      },
      'phone': function(value) {
        return tazworks.validation.formatters.Phone(value);
      },
      'Date': function(value) {
        // replace any non digits with whitespace
        var digitsOnly = value.replace(/\D+/g,'');
        if (digitsOnly.length > 8) {
          digitsOnly = digitsOnly.substring(0,8);
        }

        // add slashes where appropriate
        var newValue = '';
        for (i = 0; i < digitsOnly.length; i++) {
          newValue += digitsOnly.charAt(i);
          if (i === 1 || i === 3) {
            newValue += '/';
          }
        }

        return newValue;
      },
      'date': function(value) {
        tazworks.validation.formatters.Date(value);
      },
      'Extension': function(value) {
        // replace any non digits with whitespace
        var digitsOnly = value.replace(/\D+/g,'');

        if (digitsOnly.length >= 6) {
          digitsOnly = digitsOnly.substring(0, 6);
        }

        return digitsOnly;
      },
      'extension': function(value) {
        tazworks.validation.formatters.Extension(value);
      }
    },
    isControlCode: function(code) {
      if (code === 8 || code === 9 || code === 16 ||
         (code >= 35 && code <= 39) || code === 46) {
        return true;
      }
      return false;
    },
    clearValidationErrors: function(rootId) {
      jQuery('#' + rootId).find('select').each(function(index, element) {
        var $element = jQuery(element);

        // Clear previous error tooltip. will be reassigned if condition not met.
        if ($element.attr('data-original-title')) {
          $element.removeClass('error').tooltip('destroy');
        }
      });

      jQuery('#' + rootId).find('input').each(function(index, element) {
        var $element = jQuery(element);

        if ($element.attr('data-original-title')) {
          $element.removeClass('error').tooltip('destroy');
        }
      });

      jQuery('#' + rootId).find(".nav-tabs li").removeClass("error");
      jQuery('#' + rootId).find(".nav-tabs li i").remove();
    },
    autoValidate: function(rootId, callback) {
      var validationErrorFound = false;
      var errorField = [];

      // Check email fields have proper content if anu value has been input.
      jQuery('#' + rootId).find('input[type=email]').each(function(index, element){

        var $element = jQuery(element);

        // Clear previous error tooltip. will be reassigned if condition not met.
        if ($element.attr('data-original-title')) {
          $element.removeClass('error').tooltip('destroy');
        }

        if ($element.val()) {

          //validate multiple email addresses
          var isEmailValid = true;
          var emailValue = $element.val();

          // Allow them to enter the list of email addresses with semicolons as delimiters,
          // but replace those semicolons with commas before saving.
          if (emailValue.indexOf(';') >= 0) {
            $element.val(emailValue.replace(/;/g , ","));
            // In knockout, the data values watch for change events in the fields they are bound to.
            // Simply setting a new value in the element won't bind the new value to the data.
            // We have to trigger a change event manually.  There may be another way to do this, but I'm not sure.  (SSJ)
            $element.triggerHandler('change');
          }

          var allAddresses = emailValue.split(',');
          for (i = 0; i < allAddresses.length; i++) {
            var email = allAddresses[i];

            if (email.indexOf('@') < 0) {
              isEmailValid = false;
              break;
            }

            var temp = email.substr(email.indexOf('@') + 1);
            if (temp.indexOf('.') > 0)
            {
              temp = temp.substring(temp.indexOf('.') + 1);
              if (temp.length < 2)
              {
                isEmailValid = false;
                break;
              }
            }
            else
            {
              isEmailValid = false;
              break;
            }
          }

          if (!isEmailValid) {
            errorField.push(element);
            $element.addErrorWithTooltip('This field requires a proper email address. If more than one email address is included, they must be separated by commas.');
            validationErrorFound = true;
          }
        }
      });

      // Check for valid select fields.
      jQuery('#' + rootId).find('select').each(function(index, element) {
        var $element = jQuery(element);

        // Clear previous error tooltip. will be reassigned if condition not met.
        if ($element.attr('data-original-title')) {
          $element.removeClass('error').tooltip('destroy');
        }

        // Don't process a disabled element.
        if (!element.disabled) {
          // Required text field.
          if(element.required && !element.value) {
            $element.addErrorWithTooltip('This field is required, an option must be selected.');
            validationErrorFound = true;
          }
        }
      });

      // Check for valid input fields.
      jQuery('#' + rootId).find('input').each(function(index, element) {
        if (errorField.indexOf(element) > -1) {
          // Error already processed.
          return;
        }

        var $element = jQuery(element);

        // Clear previous error tooltip. will be reassigned if condition not met.
        if ($element.attr('data-original-title')) {
          $element.removeClass('error').tooltip('destroy');
        }

        // Don't process a disabled element.
        if (!element.disabled) {
          // Required text field.
          if(element.required) {
            if (!element.value) {
              $element.addErrorWithTooltip('This field is required and cannot be empty.');
              validationErrorFound = true;
            }
            else if (element.type === 'checkbox' && !element.checked ) {
              $element.addErrorWithTooltip('This field must be checked to proceed.');
              validationErrorFound = true;
            }
          }

          // As this is specific to a page it probably shouldn't be in this common class
          if (element.id === 'inquiryName' && element.value !== '') {
            var pattern = /^[a-zA-Z0-9 _-]+$/;
            if (!pattern.test(element.value)) {
              $element.addErrorWithTooltip('The inquiry name is not valid.');
              validationErrorFound = true;
            }
          }

          // As this is specific to a page it probably shouldn't be in this common class
          if (element.id === 'clientUserIPList' && element.value !== '' && !tazworks.validation.validators.isValidIpAddressList(element.value)) {
            $element.addErrorWithTooltip('The User Access IP List is not valid.');
            validationErrorFound = true;
          }

          // As this is specific to a page it probably shouldn't be in this common class
          // check credit card fields, don't validate if masked
          if (element.id === 'ccNumberMasked' && tazworks.validation.validators.newCreditCardProvided(element.value) && !tazworks.validation.validators.isValidCreditCard(element.value)) {
            $element.addErrorWithTooltip('The credit card number is not valid.');
            validationErrorFound = true;
          }
        }
      });

      if (callback !== undefined) {
        callback(validationErrorFound);
      }
    },
    validators: {
      isPositivePrice: function(str, forceWholeCents) {
        // Parse value to make sure it is positive quickly
        var float = parseFloat(str);
        var valid = false;

        if (float > 0) {
          if (forceWholeCents) {
            valid = /^[0-9]+(\.[0-9]{0,2})?$/g.test(str);
          }
          else {
            // String contains 1 or less dots (careful to avoid null pointer).
            if ((str.match(/\./g) || []).length < 2) {
              // Check that the value doesn't have text in it as well
              valid = /^[0-9\.]+$/g.test(str);
            }
          }
        }

        return valid;
      },

      isValidIpAddressList: function(val) {

        // split string on ';'
        var string1 = val.split(';');
        for (i = 0; i < string1.length; i++) {
          var string2 = string1[i];

          // try to split string on '-' to check for range
          var string3 = string2.split('-');
          if (string2.indexOf('-') >= 0 && string3.length != 2) {
            return false;
          }

          // check each IP address separately
          for (j = 0; j < string3.length; j++) {
            var string4 = string3[j];

            if (!tazworks.validation.validators.isValidIpAddress(string4)) {
              return false;
            }
          }
        }

        return true;
      },
      isValidIpAddress: function(val) {
        // IPv4 regex: basically just 4 non-negative numbers less than 256 separated by periods
        // Reads:
        // Three digits starting with 25 and ending with 0-4
        // Three digits starting with 2, followed by 0-4 and ending with 0-9
        // Any thing under that gets full 0-9 but if three digits the first has to be 1 or 0
        //    (this technically would allow 001 which might cause problems elsewhere)
        // First 3 have to end with a period (.) last one doesn't.
        var regex = /^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;

        return regex.test(String(val));
      },
      isValidCreditCard: function(val) {
        var $ccType = jQuery('#ccType');
        var pattern = null;

        if ($ccType.val() === 'visa'){
          pattern = /^4[0-9]{15}$/;
        } else if ($ccType.val() === 'mastercard'){
          pattern = /^5[1-5][0-9]{14}$/;
        } else if ($ccType.val() === 'discover'){
          pattern = /^6[0-9]{15}$/;
        } else if ($ccType.val() === 'amex'){
          pattern = /^3[47][0-9]{13}$/;
        }

        // if the cc type doesn't designate a length or pattern, don't validate
        if (pattern !== null && !pattern.test(val)){
          return false;
        }
        return true;
      },
      newCreditCardProvided: function(val) {
        return val !== '' && val.indexOf('X') === -1;
      },
      isInteger: function(val) {
        var regex = /^[0-9]+$/;
        return regex.test(String(val));
      }
    }
  },

  address: {
    /**
     * Loads the country list from the server. Cached in session storage,
     * if storage available feature.
     *
     * @returns {undefined}
     */
    loadCountries: function(callback) {
      if (typeof(Storage) !== undefined) {
        tazworks.address.countries = sessionStorage.getItem('countries');

        if (tazworks.address.countries !== null) {
          tazworks.address.countries = JSON.parse(tazworks.address.countries);

          if (callback !== undefined) {
            callback();
          }
        }
      }

      if (tazworks.address.countries === null || tazworks.address.countries === undefined) {
        jQuery.get('/is/lookup/countries', function(countries){
          tazworks.address.countries = countries;

          if (typeof(Storage) !== undefined) {
            sessionStorage.setItem('countries', JSON.stringify(countries));
          }

          if (callback !== undefined) {
            callback();
          }
        });
      }
    },
    countries: undefined,
    stateList: [
      {code: 'AA', name: 'U.S. Armed Forces - Americas', stateOnly: false},
      {code: 'AE', name: 'U.S. Armed Forces - Europe', stateOnly: false},
      {code: 'AP', name: 'U.S. Armed Forces - Pacific', stateOnly: false},
      {code: 'AL', name: 'Alabama', stateOnly: true},
      {code: 'AK', name: 'Alaska', stateOnly: true},
      {code: 'AZ', name: 'Arizona', stateOnly: true},
      {code: 'AR', name: 'Arkansas', stateOnly: true},
      {code: 'CA', name: 'California', stateOnly: true},
      {code: 'CO', name: 'Colorado', stateOnly: true},
      {code: 'CT', name: 'Connecticut', stateOnly: true},
      {code: 'DE', name: 'Delaware', stateOnly: true},
      {code: 'DC', name: 'District of Columbia', stateOnly: true},
      {code: 'FL', name: 'Florida', stateOnly: true},
      {code: 'GA', name: 'Georgia', stateOnly: true},
      {code: 'HI', name: 'Hawaii', stateOnly: true},
      {code: 'ID', name: 'Idaho', stateOnly: true},
      {code: 'IL', name: 'Illinois', stateOnly: true},
      {code: 'IN', name: 'Indiana', stateOnly: true},
      {code: 'IA', name: 'Iowa', stateOnly: true},
      {code: 'KS', name: 'Kansas', stateOnly: true},
      {code: 'KY', name: 'Kentucky', stateOnly: true},
      {code: 'LA', name: 'Louisiana', stateOnly: true},
      {code: 'ME', name: 'Maine', stateOnly: true},
      {code: 'MD', name: 'Maryland', stateOnly: true},
      {code: 'MA', name: 'Massachusetts', stateOnly: true},
      {code: 'MI', name: 'Michigan', stateOnly: true},
      {code: 'MN', name: 'Minnesota', stateOnly: true},
      {code: 'MS', name: 'Mississippi', stateOnly: true},
      {code: 'MO', name: 'Missouri', stateOnly: true},
      {code: 'MT', name: 'Montana', stateOnly: true},
      {code: 'NE', name: 'Nebraska', stateOnly: true},
      {code: 'NV', name: 'Nevada', stateOnly: true},
      {code: 'NH', name: 'New Hampshire', stateOnly: true},
      {code: 'NJ', name: 'New Jersey', stateOnly: true},
      {code: 'NM', name: 'New Mexico', stateOnly: true},
      {code: 'NY', name: 'New York', stateOnly: true},
      {code: 'NC', name: 'North Carolina', stateOnly: true},
      {code: 'ND', name: 'North Dakota', stateOnly: true},
      {code: 'OH', name: 'Ohio', stateOnly: true},
      {code: 'OK', name: 'Oklahoma', stateOnly: true},
      {code: 'OR', name: 'Oregon', stateOnly: true},
      {code: 'PA', name: 'Pennsylvania', stateOnly: true},
      {code: 'RI', name: 'Rhode Island', stateOnly: true},
      {code: 'SC', name: 'South Carolina', stateOnly: true},
      {code: 'SD', name: 'South Dakota', stateOnly: true},
      {code: 'TN', name: 'Tennessee', stateOnly: true},
      {code: 'TX', name: 'Texas', stateOnly: true},
      {code: 'UT', name: 'Utah', stateOnly: true},
      {code: 'VT', name: 'Vermont', stateOnly: true},
      {code: 'VA', name: 'Virginia', stateOnly: true},
      {code: 'WA', name: 'Washington', stateOnly: true},
      {code: 'WV', name: 'West Virginia', stateOnly: true},
      {code: 'WI', name: 'Wisconsin', stateOnly: true},
      {code: 'WY', name: 'Wyoming', stateOnly: true},
      {code: 'AS', name: 'UST - American Samoa', stateOnly: false},
      {code: 'FM', name: 'UST - Fed States of Micronesia', stateOnly: false},
      {code: 'GU', name: 'UST - Guam', stateOnly: false},
      {code: 'MH', name: 'UST - Marshall Islands', stateOnly: false},
      {code: 'MP', name: 'UST - Northern Mariana Islands', stateOnly: false},
      {code: 'PW', name: 'UST - Palau', stateOnly: false},
      {code: 'PR', name: 'UST - Puerto Rico', stateOnly: false},
      {code: 'VI', name: 'UST - Virgin Islands', stateOnly: false},
      {code: 'AB', name: 'CAN - Alberta', stateOnly: false},
      {code: 'BC', name: 'CAN - British Columbia', stateOnly: false},
      {code: 'MB', name: 'CAN - Manitoba', stateOnly: false},
      {code: 'NB', name: 'CAN - New Brunswick', stateOnly: false},
      {code: 'NL', name: 'CAN - Newfoundland & Labrador', stateOnly: false},
      {code: 'NT', name: 'CAN - Northwest Territories', stateOnly: false},
      {code: 'NS', name: 'CAN - Nova Scotia', stateOnly: false},
      {code: 'NU', name: 'CAN - Nunavut', stateOnly: false},
      {code: 'ON', name: 'CAN - Ontario', stateOnly: false},
      {code: 'PE', name: 'CAN - Prince Edward Island', stateOnly: false},
      {code: 'QC', name: 'CAN - Quebec', stateOnly: false},
      {code: 'SK', name: 'CAN - Saskatchewan', stateOnly: false},
      {code: 'YT', name: 'CAN - Yukon', stateOnly: false}
    ]
  },


  /**
   * If the containerEl doesn't already contain the report details,
   * call back to the server for them and copy them into the container.
   *
   * Then show the container.
   */
  REPORT_DETAILS_URL: '/dynamictooltip/report.taz',

  getReportTooltip: function(containerId, orderId) {
    var _this = this;
    var containerjQEl = jQuery('#'+containerId);
    if (containerjQEl.length > 0) {
      if (jQuery.trim(jQuery(containerjQEl).html())=='') {
        jQuery(containerjQEl).html("Please wait...");
        jQuery.ajax({
          url: _this.REPORT_DETAILS_URL,
          type: "GET",
          data: 'ajax=true&detailed=false&orderId='+orderId,
          success: function(data) {
            jQuery(containerjQEl).html(data);
          },
          error: function(data) {

          }
        });

      }
    }
  }, // END getReportTooltip

  /**
   * Format a string into a dollar format.
   *
   * @param {string} value
   * @returns {string}
   */
  dollarFormat: function(value) {
    var i = parseInt(value) + "", j = (j = i.length) > 3 ? j % 3 : 0;
    return '$' + (j ? i.substr(0, j) + ',' : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + ',');
  },


  /**
   * Set of handlebars templates that have already been read from the file
   * system into memory and compiled.
   */
  handlebarsTemplates: {},

  /**
   * Applies the data to the Handlebars template and renders it into the
   * specified location
   * @param compiledTemplate
   * @param data
   * @param targetEntity
   */
  renderHandlebarsTemplate: function(compiledTemplate, data, targetEntity) {
    if (compiledTemplate !== undefined) {
      var html = compiledTemplate(data);
     jQuery(targetEntity).html(html);
    }
  },

  /**
   * Locates the Handlebars template at that path provided, and applies
   * the data provided to that template.  Then renders the results as the html
   * body of the targetEntity.
   *
   * @param templatePath
   * @param templateData
   * @param targetEntity
   */
  displayHandlebarsTemplate: function(templatePath, templateData, targetEntity) {
    var _this = this;
    // If we've already used this template once while on this page, don't bother
    // reading in that file again.  Just pull it from memory.
    if (_this.handlebarsTemplates[templatePath] === undefined) {
      jQuery.ajax({
        type: 'GET',
        url: templatePath,
        datatype: 'text',
        success: function (templateContents) {
          _this.handlebarsTemplates[templatePath] = Handlebars.compile(templateContents);
          _this.renderHandlebarsTemplate(_this.handlebarsTemplates[templatePath], templateData, targetEntity);
        },
        error: function (data, response, error) {
          bootbox.alert("An error occurred while rendering this screen.  Please try again, and if the problem continues contact your system administrator.", function() {});
        }
      });
    }
    else {
      _this.renderHandlebarsTemplate(_this.handlebarsTemplates[templatePath], templateData, targetEntity);
    }
  },

  /**
   * Attaches popover actions to any item with a data-poload attribute
   * The value should be the url of page whose content is to load in popover.
   */
  activateDynamicPopovers: function () {
    jQuery('*[data-poload]').popover({
      placement: 'right',
      container: 'body',
      html: true,
      content: function () {
        return jQuery.ajax({
          url: jQuery(this).data('poload'),
          dataType: 'html',
          async: false // Some browsers will not like this, but the library doesn't have a better option right now
        }).responseText;
      }
    }).bind('mouseover', function () {
      jQuery(this).popover('show');
    }).bind('mouseout', function () {
      jQuery(this).popover('hide');
    }).on('shown.bs.popover', function () {
      // Select visible popover
      var popover = jQuery('.popover.in');

      // Calculate bottom of popover
      var popoverBottom = popover.position().top + popover.height() + 18;

      // If it goes beyond the bottom of the screen, adjust it up.
      if (popoverBottom > window.innerHeight) {
        var arrow = popover.find('.arrow');

        // Calculate how much it needs to move up
        var adjustment = popoverBottom - window.innerHeight;

        // If arrow will not display correctly attached to bubble (got moved off of it), adjust adjustment.
        if (popover.height() < (arrow.position().top + adjustment + 11)) {
          adjustment -= ((arrow.position().top + adjustment + 11) - popover.height());
        }

        // Move popover content bubble
        popover.css({
          'top': (popover.position().top - adjustment) + 'px'
        });

        // Move arrow down to point at the correct icon
        arrow.css({
          'top': (arrow.position().top + adjustment) + 'px'
        });
      }
    });
  }
};