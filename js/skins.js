//this awesome method disables all buttons and submits in the system for
//1.5 seconds after the user clicks on them in order to prevent double-
//clicks in the system.
jQuery(document).ready(function () {
  bindButtonsAndForms();
});

function bindButtonsAndForms() {
  jQuery(":input[type=button], :submit").click(function () {
    jQuery("input[type=submit]", jQuery(this).parents("form")).removeAttr("clicked");
    var button = jQuery(this);
    jQuery(this).attr("clicked", "true");
    
    setTimeout(function () {
      jQuery(button).removeAttr("clicked");
    }, 1.5 * 1000);
  });


  jQuery("form").submit(function () {
    var button = jQuery("input[type=submit][clicked=true]");
    if (!button) {
      button = jQuery("input[type=button][clicked=true]");
    }

    if (button) {
      disableButton(button);
    }
  });
}

function disableButton(button) {
  jQuery(button).attr("disabled", "disabled");

  // set timer to re-enable the button 
  setTimeout(function () {
    jQuery(button).removeAttr("disabled");
  }, 1.5 * 1000);
}


/*
 * These functions can go away once everything has been converted to jQuery
 */
function onloadExecute() {
  bustFrames();
  if (window.onloadPage) {
    onloadPage();
  }
}

function onunloadExecute() {
  if (window.onunloadPage) {
    onunloadPage();
  }
}


/*
 * JavaScript to prevent conflicts between prototype and jQuery
 */
(function() {
  var isBootstrapEvent = false;
  if (window.jQuery) {
      jQuery('*').on('hide.bs.dropdown', function( event ) {
          isBootstrapEvent = true;
      });

      jQuery('*').on('hide.bs.collapse', function( event ) {
          isBootstrapEvent = true;
      });

      jQuery('*').on('hide.bs.modal', function( event ) {
          isBootstrapEvent = true;
      });
  }
  if(typeof Prototype!=='undefined') {
    var originalHide = Element.hide;
    Element.addMethods({
        hide: function(element) {
            if(isBootstrapEvent) return element;
            return originalHide(element);
        }
    });
  }
})();


/*
 * Login page functions
 */
function showCert() {
  var certURL = 'https://smp-01.verizonbusiness.com/certinfo/certified.do?CERTID=052804B200';
  var opts = 'location=no,scrollbars=yes,width=800,height=400';
  var certWindow = window.open(certURL,'certified',opts);
  certWindow.focus();
}

/*
 * Functions for "formless" search fields
 */
function clearSearch(field) {
  if (field.value == "Search Criteria")
    field.value = "";
}

function focusSearch() {
  var searchTerm = document.getElementById('searchTerm');
  if (searchTerm) {
    searchTerm.focus();
    searchTerm.select();
  }
}

function submitSearch(event) {
  var evt = event || window.event;
  if (event == null || (evt && evt.keyCode == 13)) { // enter was pressed
    var searchBy = document.getElementById('searchBy');
    var searchTerm = document.getElementById('searchTerm');
    if (searchBy && searchTerm && searchTerm.value != "Search Criteria") {
      var criteria = searchBy.options[searchBy.selectedIndex].value;
      var keyword = searchTerm.value.replace(/^\s+|\s+$/g,'');
      if (keyword.length > 0) { // perform search
        var href = '/search/results.taz?criteria='+criteria+'&keyword='+escape(keyword);
        location.href = href;
      }
    }
  }
}

function openMenu(menuId) {
  jQuery('#'+menuId).show();
}

function blurMenu(menuId) {
  jQuery('#'+menuId).hide();
}

function toggleTrace() {
  var elem = document.getElementById('list-message-exception');
  var button = document.getElementById('toggle');
  var visible = elem.style.visibility == 'visible' ? true : false;
  if (visible) {
    elem.style.display = 'none';
    elem.style.visibility = 'hidden';
    button.value = 'Show Exception';
  }
  else {
    elem.style.display = 'block';
    elem.style.visibility = 'visible';
    button.value = 'Hide Exception';
  }
}

/*
 * Open a new browser window and display help.
 */
function showHelp(helpURI,w,h) {
  if (w == undefined) w = '450';
  if (h == undefined) h = '300';
  var url = "/help/displayHelp.taz?helpURI="+helpURI;
  window.open(url, "helpWindow", "scrollbars=yes,width="+w+",height="+h+",resizable=no,toolbar=no,location=no,menubar=no");
}

/*
 * Open a new browser window and display the training video.
 */

function showTraining(videoEnum) {
  var url = "/help/displayTraining.taz?trainingVideo=" + videoEnum;
  window.open(url, "trainingWindow", "scrollbars=yes,height=770,width=1084,resizable=no,toolbar=no,location=no,menubar=no");
}

function legacyReports()
{
  window.open("/workspace/legacyReports.taz", "legacyReports", config="").focus();
}

/*
 * Frame busting script moved from skin to here.
 */
function bustFrames()
{
  if (top.location != window.location) {
	  var exceptionList = [
	    '/formi9/index.taz',
	    '/formi9/blank.taz',
	    '/formi9/action.taz',
	    '/i9advantage/index.taz',
	    '/i9advantage/blank.taz',
	    '/i9advantage/action.taz',
	    '/quickviewdisclosure/view.taz',
	    '/send/interchange'
	  ];
	  for (var i = 0; i < exceptionList.length; i ++)
	  {
		//Use == 0 Otherwise this logic can be defeated by using a link such as mylink.com/index.html#/formi9/index.taz
	    if(window.location.pathname.indexOf(exceptionList[i]) == 0)
	    {
	      return;
	    }
	  }
	  //Kill the entire page instead of redirecting. Redirect can cause loops.
	  jQuery('body').empty();
  }
}

/*
 * Used by the T2 manu navigation to prevent activating multiple of the same 
 * process simultaneously.
 */
var alreadyNavigatedOnce = {};
function navigateOnce(href) {
  if (alreadyNavigatedOnce[href] === undefined) {
    alreadyNavigatedOnce[href] = true;
    window.location = href;
  }
}