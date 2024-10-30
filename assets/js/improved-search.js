let MBIS = {
	searchTerm: "",
	config: MBISConfig || {},
	elements: {
		searchBox: jQuery('#improved-search-searchbox'),
		navigation: jQuery('#improved-search-results ul.navigation'),
		content: jQuery('#improved-search-results ul.content'),
		closeButton: jQuery('#improved-search-close a'),
	},
	init: function(){
		MBIS.registerListeners();
		return this;
	},
	search: function(searchTerm){
		MBIS.searchTerm = MBIS.elements.searchBox.val() || MBIS.searchTerm;
		jQuery.post(
			MBIS.config.ajaxurl, 
			{
				action: 'improved_search_search',
				security: MBIS.config.security,
				search: MBIS.searchTerm
			}, 
			function(response) {
				MBIS.response = JSON.parse(response);
				MBIS.buildResultView();
			}
		);
		return this;
	},
	buildResultView: function(){
		MBIS.elements.navigation.html("");
		MBIS.elements.content.html("");

		if (MBIS.response.post_types == null & MBIS.response.search_term !== ""){
			MBIS.elements.content.html('<div class="no-results">No results.</div>');
			return;
		} else {
			MBIS.buildNavigation().buildPostTypeResults().buildAuthorResults();
		}
		return this;
	},
	buildNavigation: function(){
		MBIS.addNavigationLink('all');
		for (let postType in MBIS.response.post_types){
			MBIS.addNavigationLink(postType);
		}
		return this;
	},
	addNavigationLink: function(postType){
		let li;
		switch(postType){
			case "all":
				if (MBIS.config.settings.display_all != "true") return;
				li = jQuery('<li data-type="all" class="active"><a href="#">All</a></li>');
			break;
			case "author":
				if (MBIS.config.settings.display_authors != "true") return;
				li = jQuery('<li data-type="author"><a href="#">Authors</a></li>');
			break;
			default:
				let postTypeResponse = MBIS.response.post_types[postType];
				let postTypeCount = '('+ postTypeResponse.results.length +')';
				li = jQuery('<li data-type="'+postType+'"><a href="#">' + postTypeResponse.label + ' ' + postTypeCount + '</a></li>');
			break;
		}
		li.find('a').click(function(e){
				e.preventDefault();
				// Remove active from nav items
				MBIS.elements.navigation.find('li a').each(function(i,e){
					jQuery(e.parentElement).removeClass('active');
				});
				// Add active to current item
				jQuery(e.target.parentElement).addClass('active');
				// Build CSS
				if (postType == 'all')
					jQuery('#improved-search-style').html('');
				else
					jQuery('#improved-search-style').html('<style>#improved-search-results ul.content li:not(.'+postType+') {display:none;}</style>');
		});
		MBIS.elements.navigation.append(li);	
	},
	buildPostTypeResults: function(){
		for (let postType in MBIS.response.post_types){
			for (let i = 0; i < MBIS.response.post_types[postType].results.length; i++) {
				MBIS.elements.content.append( MBIS.getResultLiHTML(postType, i) );
			}
		}
		return this;
	},
	buildAuthorResults: function(){
		if (MBIS.config.settings.display_authors == "true"){
			for (let author in MBIS.response.authors) {
				MBIS.elements.content.append( MBIS.getResultLiHTML('author', author) );
			}
			MBIS.addNavigationLink('author');
		}
	},
	getResultLiHTML: function(postType,resultIndex){
		let postTypeResponse = postType == 'author' ? MBIS.response.authors[resultIndex] : MBIS.response.post_types[postType];
		let href = postType == 'author' ? "" : postTypeResponse.results[resultIndex].permalink;
		let hrefTarget = MBIS.config.settings.open_in_new_tab == "true" ? 'target="_blank"' : 'target=""';
		let a = jQuery('<a href="' + href + '"'+ hrefTarget +'></a>');
		let li = jQuery('<li class="'+postType+'"></li>');
		var link = jQuery('<a href="'+ ( postType == 'author' ? postTypeResponse.user_url : postTypeResponse.results[resultIndex].permalink ) +'"></a>');
		var listItem = jQuery('<div class="list-item"></div>');
		var itemImg = jQuery('<div class="item-img"></div>');
		var itemDetails = jQuery('<div class="item-details"></div>');
		itemImg.html( postType == 'author' ? '<img src="'+postTypeResponse.avatar+'" />' : postTypeResponse.results[resultIndex].thumbnail );
		itemDetailsHTML = postType == 'author' ? 
			'<p class="title">' + postTypeResponse.display_name + '</p><p>' + postTypeResponse.user_email + '</p>'
			: '<p class="title">' + postTypeResponse.results[resultIndex].post_title + '</p><p>' + postTypeResponse.results[resultIndex].excerpt_trim + '</p>';
		itemDetails.html( itemDetailsHTML );
		listItem.append(itemImg).append(itemDetails);
		li.html(listItem);
		a.html(li);
		return a;
	},
	registerListeners: function(){
		MBIS.elements.searchBox.focusin(function(){
			jQuery("#improved-search").addClass("active");
		});
		MBIS.elements.closeButton.click(function(){
			jQuery("#improved-search").removeClass("active");
		});
		MBIS.elements.searchBox.keypress(function(e) {
			if(e.which == 13 && MBIS.config.settings.redirect_search_landing_page == "true") {
				let url = new URL( MBIS.config.site_url );
				url.searchParams.set('s', MBIS.searchTerm );
				window.document.location = url.href;
			}
		});
		return this;
	}
};
jQuery(document).ready(function() {
	MBIS.init();
});