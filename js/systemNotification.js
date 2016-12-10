/**
 * Every 30 seconds check for a system notification.
 * 
 * If a new one is returned, display it.
 * When the user hits hide, hide the notification box
 * and set a cookie to keep it hidden for that user
 * until a new notification (one with a different start and/or end date)
 * is sent.
 */
var SystemNotificationUtil = function($) {

  var ONE_SECOND = 1000;
  var HIDE_SYS_NOTE_COOKIE_NAME = "hideSystemNotification";
  var ALERT_SYMBOL = "! "

  var ALERT_SYMBOL_REGEX = RegExp('^' + ALERT_SYMBOL);

  var notificationUrl = "/is/system/notification"
  var latestNotificationData;

  var flashTabIconInterval;


  // We want something in the tab to flash on and off to grab their attention
  function startFlashingTabIcon() {
    flashTabIconInterval = setInterval(function() {
      if (document.title.match(ALERT_SYMBOL_REGEX)) {
        document.title = document.title.replace(ALERT_SYMBOL, "");
      }
      else {
        document.title = ALERT_SYMBOL + document.title;
      }
    }, ONE_SECOND);
  }


  function stopFlashingTabIcon() {
    clearInterval(flashTabIconInterval);
    document.title = document.title.replace(ALERT_SYMBOL, "");
  }


  /**
   * @param callback - This notification check will also act as our session renewal function. 
   * The sessionRenewal interval in sessionExpireModal.js needs to 
   * reset the server invalidation time when the notification check returns successfully,
   * so this callback method allows for that to happen
   */
  function checkForNotification(callback) {
    $.ajax({
      url: notificationUrl,
      type: "GET",
      dataType: "json",
      success: function(data) {
        callback();
        handleResponse(data);
      },
      error: function(data) {
        console.log("Unable to retrieve or display the system notification.");
      }
    });
  }


  function handleResponse(data) {
    if (data.active == true) {
      handleActiveNotification(data);
    }
    else {
      // No active message, so if the modal is visible, hide it, clear cookies
      if ($('#immediate-message').length && $('#immediate-message').is(':visible')) {
        deleteNotificationCookie(data.userId);
        hideDialog();
      }
    }
    
  }


  function handleActiveNotification(data) {
    // We have an active notification (data.active == true)
    latestNotificationData = data;
    if ($('#immediate-message').length && $('#immediate-message').is(':visible')) {
      // If the window is already visible
      // and if there is a cookie for this message (may have been hidden from a different tab), 
      // hide the window 
      if (hasUserHiddenThisNotification(data)) {
        hideDialog();
      }
      else if ($('#immediate-message-text').text() != data.text) {
        // If the window is already visible 
        // but the message has changed,
        // clear cookies (this is a new message, so don't leave it hidden)
        deleteNotificationCookie(data.userId);
        // display the new message
        $('#immediate-message-text').html(data.text);
      }
    }
    else if (!hasUserHiddenThisNotification(data)) {
      // If the window isn't visible and they haven't already hidden this message,
      // clear the hide notification cookie,
      // display the notification
      deleteNotificationCookie(data.userId);
      showDialog(data);
    }
    // if the cookie exists and matches the dates for this notification,
    // then the user has hidden the dialog for this notification. Don't pop it up again.
  }


  function hasUserHiddenThisNotification(data) {
    // If there is no cookie for notifications,
    // or if the cookie doesn't match the current notification dates,
    // then that means the user hasn't already hidden the dialog for this notification.
    if (getNotificationCookieValue(data.userId)) {
      var cookieVal = $.parseJSON(getNotificationCookieValue(data.userId));      
      if (cookieVal.activationDate == data.activationDate
          && cookieVal.expirationDate == data.expirationDate) {
        return true;
      }
    }
    return false;
  }


  function getFullCookieName(userId) {
    // we want one cookie per user per browser so that each user can hide their own notifications
    return HIDE_SYS_NOTE_COOKIE_NAME + "_" + userId;
  }


  // Save a cookie that tells the browser to hide this notification
  function createHideNotificationCookie(data) {
    var oneDay = 1 * 24 * 60 * 60 * 1000;
    var d = new Date();
    d.setTime(d.getTime() + oneDay);
    var expires = "expires=" + d.toUTCString();
    // the cookie text will be a stringified json object
    // containing the activation date and expiration date.
    // If the new data has different dates than cookie's dates,
    // we consider that a new notification.
    var cookieJSON = {};
    cookieJSON.activationDate = data.activationDate;
    cookieJSON.expirationDate = data.expirationDate;
    document.cookie= getFullCookieName(data.userId) + "=" +
      JSON.stringify(cookieJSON) + 
      "; path=/; " + expires;
  }


  function getNotificationCookieValue(userId) {
    var name = getFullCookieName(userId) + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
  }


  function deleteNotificationCookie(userId) {
    if( getNotificationCookieValue(userId) ) {
      document.cookie = getFullCookieName(userId) + "={}" +
        "; path=/" +
        "; expires=Thu, 01 Jan 1970 00:00:01 GMT";
    }
  }


  function handleClickHideNotification() {
    createHideNotificationCookie(latestNotificationData);
    hideDialog();
  }


  // Pass this function into the template engine as a callback.  We can't
  // register click events on the notification modal until that modal template
  // has been loaded and rendered into the DOM.
  function registerNotificationModalEvents() {
    // register the hide button click event
    $('#immediate-message button').click(function() {
      handleClickHideNotification();
    });
  }


  function hideDialog() {
    $('#immediate-message').hide();
    stopFlashingTabIcon();
  }


  function showDialog(data) {
    startFlashingTabIcon();
    if ($('#immediate-message').length) {
      // If the notification is already in the dom but hidden, just set the text and show it
      $('#immediate-message-text').html(data.text);
      $('#immediate-message').show();
    }
    else {
      // the notification html isn't in the dom yet, so render it
      var templateLoc = '/_scripts/common/systemNotification.hbs';
      ISHandlebars.displayHandlebarsTemplate(templateLoc, data, 'body', registerNotificationModalEvents, ISHandlebars.PREPEND);
    }
  }


  return {
    checkForNotification: checkForNotification
  }

}(jQuery);
