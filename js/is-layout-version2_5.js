/**
* Javascript for the Instascreen v2.5 header.
*/

var instascreenHeader = {
  
  init: function() {
    var _this = this;
    _this.initSearchElements();
  },
  
  
  initSearchElements: function() {
    var _this = this;
    
    var searchDropdownLinks = jQuery('#header-search .dropdown-menu a');
    searchDropdownLinks.click(function() {
      _this.onChangeSearchOption(this);
    });
    
    var searchButton = jQuery('#header-search-button');
    searchButton.click(function(event) {
      _this.onClickSearchButton(event);
    });
    
    jQuery('#header-search').on('keyup', '#header-search-input', function(event) {
      var code = event.keyCode || event.which;
      if(code == 13) { //Enter keycode
        _this.onClickSearchButton(event);
      }
    });
    
    jQuery('#nav2-main-link').click(function(event) {
      var href = jQuery(this).attr('data-link');
      if (href != null && href.length > 0) {
        window.location.href = href;
      }
    });
  },
  
  
  onChangeSearchOption: function(el) {
    // clear the previously selected search type
    var searchDropdownLinks = jQuery('#header-search .dropdown-menu a');
    searchDropdownLinks.parent().removeAttr("data-selected");
    
    // select the new search type
    jQuery(el).parent().attr("data-selected", "yes");
    
    // set the dropdown label
    var label = jQuery(el).html();
    jQuery('#header-dropdown-label').html(label);
    
    var searchInput = jQuery('#header-search-input');
    searchInput.focus();
  },
  
  
  onClickSearchButton: function(event) {
    var selectedSearchOption = jQuery('#header-search .dropdown-menu li[data-selected="yes"]');
    var searchBy = selectedSearchOption.attr('data-value');
    var searchTerm = jQuery('#header-search-input').val();
    if (searchBy == null || searchBy.length === 0) {
      alert("Please choose a search type.");
      event.preventDefault();
    }
    else if(searchTerm == null || searchTerm.length === 0) {
      alert("Please enter a search value");
      event.preventDefault();
    }
    else {
      var keyword = searchTerm.replace(/^\s+|\s+$/g,'');
      if (keyword.length > 0) { // perform search
        var href = '/search/results.taz?criteria='+searchBy+'&keyword='+escape(keyword);
        window.location.href = href;
      }
    }
  }
  
};


jQuery(function() {
  instascreenHeader.init();
});