var ImprovedSearch = ImprovedSearch || {};

ImprovedSearch.loadNavigation = function() {

	jQuery('#improved-search-admin-navigation .nav-tab').click(function(e){

		var tabId = jQuery(this).data("tab");

		// Get all elements with class="tabcontent" and hide them
		var tabContent = document.getElementsByClassName('improved-search-admin-content');
		for (i = 0; i < tabContent.length; i++) {
			tabContent[i].style.display = "none";
		}

		// Get all elements with class="tablinks" and remove the class "active"
		var tabs = document.getElementsByClassName("nav-tab");
		for (i = 0; i < tabs.length; i++) {
			tabs[i].className = tabs[i].className.replace(" nav-tab-active", "");
		}

		// Show the current tab, and add an "active" class to the button that opened the tab
		document.getElementById(tabId).style.display = "block";
		e.currentTarget.className += " nav-tab-active";

	});

};

ImprovedSearch.registerSettingsListeners = function() {
	ImprovedSearch.settings = ImprovedSearch.settings || {};
	jQuery('#mbis-excerpt-length').change(function(e) {
		ImprovedSearch.settings.excerpt_length = jQuery('#mbis-excerpt-length')[0].value;
		ImprovedSearch.saveOptions();
	});
	jQuery('#mbis-open-in-new-tab').change(function(e) {
		ImprovedSearch.settings.open_in_new_tab = jQuery('#mbis-open-in-new-tab')[0].checked;
		ImprovedSearch.saveOptions();
	});
	jQuery('#mbis-display-all').change(function(e) {
		ImprovedSearch.settings.display_all = jQuery('#mbis-display-all')[0].checked;
		ImprovedSearch.saveOptions();
	});
	jQuery('#mbis-display-authors').change(function(e) {
		ImprovedSearch.settings.display_authors = jQuery('#mbis-display-authors')[0].checked;
		ImprovedSearch.saveOptions();
	});
	jQuery('#mbis-redirect-search-landing-page').change(function(e) {
		ImprovedSearch.settings.redirect_search_landing_page = jQuery('#mbis-redirect-search-landing-page')[0].checked;
		ImprovedSearch.saveOptions();
	});
	jQuery('#mbis-searchable-post-types').change(function(e) {
		ImprovedSearch.settings.searchable_post_types = jQuery('#mbis-searchable-post-types').val();
		ImprovedSearch.saveOptions();
	});
	jQuery('#mbis-template').change(function(e) {
		ImprovedSearch.settings.template = jQuery('#mbis-template').val();
		ImprovedSearch.saveOptions();
	});
};

ImprovedSearch.setStatus = function(statusMessage) {
  var status = document.getElementById("mbis-status");
  jQuery('#mbis-status').html("<p>"+statusMessage+"</p>");
  status.className = "show";
  ImprovedSearch.statusTimeout = setTimeout(function(){ status.className = status.className.replace("show", ""); }, 3000);
}

ImprovedSearch.saveOptions = function(){
	ImprovedSearch.setStatus('<i class="fa fa-cog fa-spin fa-fw"></i><span class="sr-only">Saving...</span>');
	var data = {
		action: 'mbis_settings_update',
		security: ImprovedSearch.security,
		settings: ImprovedSearch.settings
	};
	jQuery.post(ImprovedSearch.ajaxurl, data, function(response) {
		// console.log(response);
		switch(response.status)
		{
			case 200:
			default:
			ImprovedSearch.setStatus("Saved!");
			break;
		}
	});
};

jQuery(document).ready(function() {
	ImprovedSearch.loadNavigation();
	ImprovedSearch.registerSettingsListeners();
});
