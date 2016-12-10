/**
 * @require systemNotification.js
 */

var addTargetForLogout = false;

var MultiTabTimeoutWarningUtil = function($) {
  // Defaults and globals.
  var _title = 'Session Expiration Warning';
  var _message = '<form id="renewForm" name="renewForm" action="/renew/logout.taz" method="GET">';
    _message += '<p>Your session is about to expire.<strong> Time remaining is <span id="session-time-remaining">3:00</span></strong>.</p><p>To continue with this session, click Renew.</p>';
    _message += '<input type="hidden" id="token" name="token"></form>';

  var THIRTY_MINUTES_IN_SECONDS = 30 * 60;
  var THREE_MINUTES_IN_SECONDS = 3*60;
  var ONE_SECOND_IN_MS = 1000;
  var TIMEOUT_WARN_SECONDS = THREE_MINUTES_IN_SECONDS;

  var timeoutInterval;
  var serverSecondsTimeout = THIRTY_MINUTES_IN_SECONDS;
  var GRACE_PERIOD_SECONDS = 10;
  var timeoutBootbox = false;

  var init = function(serverSecondsTimeoutParam) {
    // Allow initialization of the server timeout value from outside this file.
    if (serverSecondsTimeoutParam !== undefined && serverSecondsTimeoutParam !== null) {
      serverSecondsTimeout = serverSecondsTimeoutParam;
    }
    setupTimeoutInterval();
    resetSessionInvalidationTime(serverSecondsTimeout);
  };


  function resetSessionInvalidationTime(sessionInactiveInterval) {
    if(sessionInactiveInterval) {
      var serverSessionInvalidationTime = (new Date().getTime()) + (sessionInactiveInterval * 1000);
      setTimeoutCookieValue(serverSessionInvalidationTime);
    }
  }


  function getTimeoutCookieValue() {
    var name = 'serverSessionInvalidationTime' + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0)==' ') c = c.substring(1);
      if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
  }

  function setTimeoutCookieValue(cvalue) {
    var oneDay = 1 * 24 * 60 * 60 * 1000;
    var d = new Date();
    d.setTime(d.getTime() + oneDay);
    var expires = "expires=" + d.toUTCString();
    document.cookie = 'serverSessionInvalidationTime' + "=" + cvalue + "; "
        + expires + "; path=/";
  }

  function getSecondsUntilServerTimeout() {
    var invalidationTimestamp = getTimeoutCookieValue();
    var msTilInvalidation = (invalidationTimestamp - new Date().getTime());
    return Math.ceil(msTilInvalidation / ONE_SECOND_IN_MS) - GRACE_PERIOD_SECONDS;
  }

  function setupTimeoutInterval() {
    clearInterval(timeoutInterval);
    timeoutInterval = setInterval(function () {
      toggleModal();
    }, ONE_SECOND_IN_MS);
  }

  function toggleModal() {
    var secondsUntilServerTimeout = getSecondsUntilServerTimeout();
    if (secondsUntilServerTimeout < TIMEOUT_WARN_SECONDS) {
      var popupMessage = $('#session-time-remaining');
      if (popupMessage.length == 0 || popupMessage.is(':hidden')) {
        createDialog();
      }
      handleCountdown(secondsUntilServerTimeout);
    } else if (timeoutBootbox) {
      timeoutBootbox.modal('hide');
    }
  }


  function renewSession() {
    // This requires systemNotification.js as a dependency.
    // The notification check will renew the session as a byproduct since it hits the server.
    SystemNotificationUtil.checkForNotification(MultiTabTimeoutWarningUtil.handleAjaxRenewPostBack);
  };


  function createDialog() {
    timeoutBootbox = bootbox.dialog({
      thisDialog: this,
      show: true,
      message: _message,
      className: "session-timer-modal modal-danger",
      closeButton: false,
      title: _title,
      buttons: {
        logout: {
          label: "Logout",
          className: "session-timer-logout btn btn-link",
          callback: function() {
            exhaustSession();
          }
        },
        renew: {
          label: "Renew",
          className: "session-timer-renew btn btn-primary",
          callback: function() {
            renewSession();
          }
        }
      }
    });
  }

  function handleCountdown(secondsUntilServerTimeout) {
    if(secondsUntilServerTimeout > 0) {
      updateRemainingTimeSpan(secondsUntilServerTimeout);
    } else {
      exhaustSession();
    }
  }

  function updateRemainingTimeSpan(secondsUntilServerTimeout) {
    timeLeft = clock(secondsUntilServerTimeout);
    $('#session-time-remaining').html(timeLeft);
  }

  //builds the string for displaying the remaining time.
  function clock(secs) {
    var hr = Math.floor(secs / 3600);
    var min = Math.floor((secs - (hr * 3600))/60);
    var sec = secs - (hr * 3600) - (min * 60);

    if (min < 10 && hr > 0) {min = "0" + min;}
    if (hr <= 0) {
      hr = "";
    }
    else {
      hr += ":";
    }
    if (sec < 10) {sec = "0" + sec;}
    return hr + min + ':' + sec;
  }


  /*
   * function to renew session, clear all the intervals, hide the timer,
   * and log the user out
   */ 
  function exhaustSession()
  {
    setTimeoutCookieValue(new Date().getTime());
    addTargetForLogout = true;
    logout();
  }

  // callback method for ajax when user chooses to renew
  function handleAjaxRenewPostBack() {
    if (timeoutBootbox) {
      timeoutBootbox.modal('hide');
    }
    init();
  }


  // name explains it all :)
  function logout() {
    clearInterval(timeoutInterval);
    $('#renewForm .token').val(getTokenValue());
    $('#renewForm').submit();
  }

  return {
    init : init,
    handleAjaxRenewPostBack: handleAjaxRenewPostBack
  }
  
}(jQuery);


function getTokenValue() {
  var name = 'CSRF_TOKEN' + "=";
  var ca = document.cookie.split(';');
  for(var i=0; i<ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0)===' '){
      c = c.substring(1);
    }
    if (c.indexOf(name) === 0){
      return c.substring(name.length,c.length);
    }
  }

  var tokenId = 'token'; // value from NonceCache.CSRF_NONCE_REQUEST_PARAM
  var token = document.getElementById(tokenId);
  return (token) ? token.value : '';
}


// Don't run this on the login page, or interchange pages.
if (!window.location.pathname.match(/\/(login|recover|sso|saml|send\/interchange)/g)) {

  //Anonymous function to let me use $
  (function($) {

  	function shouldAutoRenew() {
  	  //Pages that wish to have the session auto renew must define a visible element with the id "neverTimeout"
  	  return $('#neverTimeout').is(':visible');
  	};

  	var autoRenewMinutes = 0.5;
  	//When the page is ready.
  	//'body' may not exist when script is executed.
  	$(function() {
  	  // initialize the timeout warning
  	  MultiTabTimeoutWarningUtil.init();

  	  // Do one initial check for a notification once the page has finished loaded.
  	  // Start the interval after this to continue checking periodically.
  	  SystemNotificationUtil.checkForNotification(MultiTabTimeoutWarningUtil.handleAjaxRenewPostBack);

  	  var shouldRenew = false;
  	  //capture keypresses and mousemovements
  	  $('body').on('keypress mousemove', function() {
  	    shouldRenew = true;
  	  });
  	  //if we have had key presses or mouse movements within the past minute, automatically renew the session.
  	  setInterval(function() {
  	    if (shouldRenew || shouldAutoRenew()) {
  	      shouldRenew = false;
  	      // This requires systemNotification.js as a dependency.
  	      // The notification check will renew the session as a byproduct since it hits the server.
  	      SystemNotificationUtil.checkForNotification(MultiTabTimeoutWarningUtil.handleAjaxRenewPostBack);
  	    }
  	  }, autoRenewMinutes * 60 * 1000)
  	});

  })(jQuery);
}