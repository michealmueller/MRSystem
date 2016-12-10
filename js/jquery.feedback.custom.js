(function($) {
  $.fn.feedback = function (featureName, betaThreshold, customMessage) {
    function buildFeedbackStars (message) {
      var form = $(document.createElement("div")).attr('id', 'feedbackStars');
      form.append($(document.createElement('p')).html(message));
      
      var stars = $(document.createElement('div')).attr('id', 'ratingStars').addClass('center');
      
      // Create each of the stars with their interations.
      for (var score = 1; score <= 5; score++) {
        var i = $(document.createElement('i')).addClass('fa fa-star-o fa-lg text-warning')
                                              .attr('id', 'ratingScore' + score)
                                              .css({
                                                padding: '0 10px',
                                                cursor: 'pointer'
                                              });
        i.hover(
          function() {
            // Remove existing stars
            $('#ratingStars i').addClass('fa-star-o').removeClass('fa-star');

            // Highlight all the ones under the current selection.
            var starId = 'ratingScore';
            var starIdLength = starId.length;
            var starValue = $(this).attr('id').substring(starIdLength, starIdLength + 1);
            for (var score = 1; score <= starValue; score++) {
              $('#' + starId + score).addClass('fa-star').removeClass('fa-star-o');
            }
          }, 
          function() {
            // Remove highlight.
            $('#ratingStars i').addClass('fa-star-o').removeClass('fa-star');
          }
        ).click(processQuickScore);
        stars.append(i);
      }
      
      form.append(stars);
      
      return form;
    }
    
    function processQuickScore (event) {
      
      // Get value of the current star.
      var starId = 'ratingScore';
      var starIdLength = starId.length;
      var starValue = $(this).attr('id').substring(starIdLength, starIdLength + 1);

      $.ajax({
        url: '/is/feedback/currentuser/starfeedback',
        type: 'POST',
        processData: false,
        contentType: 'application/json',
        dataType: 'json',
        data: JSON.stringify({
            starRating: starValue,
            featureName: featureName,
            betaThreshold: betaThreshold
        })
      });

      // Change to ask what they like instead of what to improve.
      if (starValue > 3) {
        $("#textFeedbackLabel").text('What do you like about this feature?');
      }

      // Hide stars and show explanation input.
      $('#feedbackStars').hide();
      $('#feedbackDetails').show();
    }
    
    function buildFeedbackInput () {
      var form = $(document.createElement('div')).attr('id', 'feedbackDetails').hide();
      var p = $(document.createElement('p'));
      p.append($(document.createElement('label')).text('What issues have you had with this feature?')
                                                 .attr('for', 'textFeedback')
                                                 .attr('id', 'textFeedbackLabel'));
      p.append($(document.createElement('textarea')).attr('id', 'textFeedback')
                                                    .addClass('form-control')
                                                    .attr('rows', '3')
                                                    .attr('cols', '60')
                                                    .attr('maxlength', '1000')
                                                    .css('fontSize', '1.2rem'));
      var button = $(document.createElement('input')).addClass('btn btn-primary btn-sm').attr('value', 'Send');
      button.click(function () {
        var feedbackText = $.parseHTML($('#textFeedback').val());
        var sanitizedFeedbackText = $(feedbackText).text();
        $.ajax({
          url: '/is/feedback/currentuser/textfeedback',
          type: 'POST',
          processData: false,
          contentType: 'application/json',
          dataType: 'json',
          data: JSON.stringify({
              feedbackText: sanitizedFeedbackText,
              featureName: featureName,
              betaThreshold: betaThreshold
          })
        });
        
        closeFeedbackRequest(true);
      }); 

      form.append(p);
      form.append(button);

      return form;
    }
    
    function closeFeedbackRequest (sent) {
      // Hide popover.
      $('div[data-id="experienceRating"]').popover('hide');

      // Only display thank you message if closing through feedback loop.
      if (typeof sent === 'boolean' && sent) {
        // Thank you message
        $.growl(
          "Thank you for your feedback."
        );
      }
    }
    
    // Determine message for feedback request.
    var message = 'How would you rate your experiences with the recent new features?';
    if (customMessage) {
      message = customMessage;
    }

    // Build initial form.
    var form = $(document.createElement("div"));
    form.append(buildFeedbackStars(message));
    form.append(buildFeedbackInput());

    // data-id added for reference to close.
    var experienceRating = $(document.createElement("div")).attr('data-id', 'experienceRating').css({
      width: '100%',
      zIndex: 50000
    });
    //Don't want to submit forms that this popover may be inside of
    $(experienceRating).click(function(e) {
      e.preventDefault();
    });

    // jQuery currently seleted item.
    var element = $(this);

    // Conditions where the location is not appropriate to display: no context is specified, 
    // the selector returns multiple items, or it is the body.
    if (typeof element.context === 'undefined' || element.length !== 1 || element.selector === 'body') {
      // Add just to the body tag with style to stay at the top of the page.
      experienceRating.css({
        position: 'fixed',
        top: '0'
      }).appendTo('body');
    }
    // Attach to currently selected element.
    else {
      element.wrap($(document.createElement("div")).css('position', 'relative'));
      experienceRating.css({
        position: 'absolute',
        bottom: '0'
      }).appendTo(element);
    }

    // Add popover to element.
    experienceRating.popover({
      container: 'body',
      content: form,
      title: 'Feedback Request',
      html: true,
      placement: 'bottom',
      trigger: 'manual'
    }).popover('show');
    
    var closeButton = $(document.createElement("button")).addClass('close').css('fontSize', '1.8rem').click(function(event) {
      event.preventDefault();
      closeFeedbackRequest(false);
    });
    
    closeButton.append($(document.createElement("i")).addClass('fa fa-fw fa-times pull-right'));

    // Override css to get wider popover.
    var popover = $('.popover:last');
    popover.css('maxWidth', '600px');
    popover.css('minWidth','250px');
    popover.css('color', '#333333');
    
    
    popover.find(".popover-title").append(closeButton);

    return element;
  };
})(jQuery);

function handleFeedbackResponse(obj) {
  while (obj && obj.length > 0) {
    var index = Math.floor(Math.random() * obj.length);
    var feedbackCandidate = obj[index];
    var feedbackElement = jQuery(feedbackCandidate.selector); 
    if (feedbackElement.length > 0 && feedbackElement.is(':visible')) {
      feedbackElement.feedback(feedbackCandidate.featureName, feedbackCandidate.betaThreshold, feedbackCandidate.questionText);
      return;
    } else {
      obj.splice(index, 1);
    }
  }
}
function getRandomIntInclusive(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

function askForFeedback() {
  jQuery.getJSON('/is/feedback/currentuser/feedbackcandidates', handleFeedbackResponse);
}

(function($) {
  var probabilityOfAskingForFeedback = 750;
  window.setTimeout(function() {
    if (getRandomIntInclusive(0, probabilityOfAskingForFeedback) === 1) {
      askForFeedback();
    }
  }, 1000)
})(jQuery);
