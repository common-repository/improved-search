<?php
	/* Improved Search admin settings template */
	global $mbis;
?>
<div class="wrap">
	<h1 id="improved-search-main-header">Improved Search</h1>

	<div id="improved-search-admin-navigation" class="nav-tab-wrapper">
		<a href="#general" class="nav-tab nav-tab-active" data-tab="general">General</a>
		<a href="#about" class="nav-tab" data-tab="about">About</a>
	</div>

	<form method="post" action="?page=improved-search">
	<div id="general" class="improved-search-admin-content" style="display: block;">
	<?php
		$general = ($mbis->Builder->create_section([
			'label' => 'General',
			'description' => 'Settings to configure Improved Search.',
		]))
		->add_field([
			'label' => 'Excerpt Length',
			'type' => 'number',
			'name' => 'mbis-excerpt-length',
			'settings_key' => 'excerpt_length',
			'description' => 'The is the number of words to display in the preview of a post\'s content.',
		])
		->add_field([
			'label' => 'Open in New Tab',
			'type' => 'checkbox',
			'name' => 'mbis-open-in-new-tab',
			'settings_key' => 'open_in_new_tab',
			'description' => 'When you click on a post found through search it will open the point in a new tab.',
		])
		->add_field([
			'label' => 'Display "All"',
			'type' => 'checkbox',
			'name' => 'mbis-display-all',
			'settings_key' => 'display_all',
			'description' => 'Display the "All" button to show search results of every post type.',
		])
		->add_field([
			'label' => 'Display "Authors"',
			'type' => 'checkbox',
			'name' => 'mbis-display-authors',
			'settings_key' => 'display_authors',
			'description' => 'Displays the "Author" button to include post authors in the search result.',
		])
		->add_field([
			'label' => 'Redirect to Search page',
			'type' => 'checkbox',
			'name' => 'mbis-redirect-search-landing-page',
			'settings_key' => 'redirect_search_landing_page',
			'description' => 'When you press enter after typing text to search it will redirect to the default WordPress search landing page.',
		])
		->add_field([
			'label' => 'Searchable Post Types',
			'type' => 'select',
			'name' => 'mbis-searchable-post-types',
			'settings_key' => 'searchable_post_types',
			'choices' => $mbis->Builder->get_post_type_choices(),
			'description' => 'Choose the post types Improved Search is able to return in its results.',
			'multiple' => true,
		])
		->add_field([
			'label' => 'Template',
			'type' => 'select',
			'name' => 'mbis-template',
			'settings_key' => 'template',
			'choices' => $mbis->Builder->templates,
			'description' => 'This is the theme the search interface uses.',
		])
		->build();
	?>
	</div>
	</form>

	<div id="about" class="improved-search-admin-content">
		<h3>About</h3>
		<p>Developed by <a href="https://jeremypreed.com" target="_blank">Jeremy P. Reed</a>.</p>
		<hr>
		<p>If you enjoy Improved Search please consider providing a donation to support development</p>
		<p>
			<form action="https://www.paypal.com/donate" method="post" target="_top">
				<input type="hidden" name="hosted_button_id" value="2VTRUM4SX87CA" />
				<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" />
				<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
			</form>
		</p>
	</div>

</div>
<div id="mbis-status"></div>
