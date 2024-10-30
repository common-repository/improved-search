<?php
namespace Mochabear\ImprovedSearch\Includes;

if( ! defined( 'ABSPATH' ) ) die('Not allowed');

class MBIS_Base {
	
	public $version = MB_IMPROVED_SEARCH_VERSION;

	public $settings_option = "mbis-settings";

	public $default_settings = array(
		'advanced_settings' => false,
		'excerpt_length' => 15,
		'display_authors' => false,
		'redirect_search_landing_page' => false,
		'template' => 'default',
		'searchable_post_types' => array( 'post', 'page' ),
	);

	public $templates = array(
		'default' => 'Default',
	);

	function __construct() {
		$this->plugins_url = plugins_url('improved-search');
	}

	public function get_post_type_choices()
	{
		$choices = array();
		$post_types = get_post_types();
		foreach ($post_types as $post_type_slug => $value) {
			$post_type_object = get_post_type_object( $post_type_slug );
			if ( $post_type_object->exclude_from_search ) {
				continue;
			}
			$choices[$post_type_slug] = $post_type_object->label;
		}
		return $choices;
	}
}