<?php

namespace Mochabear\ImprovedSearch\Includes;

if( ! defined( 'ABSPATH' ) ) die('Not allowed');

class MBIS_Shortcode extends MBIS_Base {

	function __construct()
	{
		parent::__construct();
		add_action( 'init', array( $this, 'register_shortcodes' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_mbis_assets' ) );
	}

	function register_shortcodes(){
		add_shortcode( 'improved_search', array( $this, 'mbis_function' ) );
	}

	function enqueue_mbis_assets()
	{
	}

	function mbis_function()
	{
		ob_start();

		$mb = new MBIS_Base();

		extract(
    		array(
    			"settings" => get_option($mb->settings_option, $mb->default_settings),
    		)
    	);

		include 'template-search.php';
		
		$html = ob_get_clean();

		return $html;
	}

}