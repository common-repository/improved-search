<?php
/*
Plugin Name: Improved Search
Plugin URI: https://www.mocha-bear.com/improved-search
Description: Improved Search is a AJAX search interface you can place anywhere on your site and get instant search results as soon as you beginning typing.
Version: 1.1.0
Author: Jeremy P. Reed
Author URI: https://www.mocha-bear.com
Text Domain: mbis
*/

namespace Mochabear\ImprovedSearch;

use Mochabear\ImprovedSearch\Includes\MBIS_SettingsMenu as SettingsMenu;
use Mochabear\ImprovedSearch\Includes\MBIS_Search as Search;
use Mochabear\ImprovedSearch\Includes\MBIS_Widget as Widget;
use Mochabear\ImprovedSearch\Includes\MBIS_Shortcode as Shortcode;
use Mochabear\ImprovedSearch\Includes\MBIS_TemplateBuilder as Builder;

if( ! defined( 'ABSPATH' ) ) die('Not allowed');

define( 'MB_IMPROVED_SEARCH_VERSION', '1.1.0' );

class MBImprovedSearch {

	public $version = MB_IMPROVED_SEARCH_VERSION;
	public $SettingsMenu;
	public $Widget;

	function __construct() {}

	function init() {
		$this->load_classes();
		$this->check_version();

		// Register and load the widget
		add_action( 'widgets_init', function() {
			register_widget( $this->Widget );
		});

	}

	function load_classes() {
		require_once 'includes/class-base.php';
		require_once 'includes/class-search.php';
		require_once 'includes/class-settings-menu.php';
		require_once 'includes/class-widget.php';
		require_once 'includes/class-shortcode.php';
		require_once 'includes/class-template-builder.php';

		$this->SettingsMenu = new SettingsMenu();
		$this->Search = new Search();
		$this->Widget = new Widget();
		$this->Shortcode = new Shortcode();
		$this->Builder = new Builder();
	}

	function check_version() {
		/* Save plugin version */
		if (get_option('mbis-version', false) == false) {
			update_option( 'mbis-version', $this->version );
		} else if (get_option('mbis-version', false) < $this->version) {
			// Upgrading
			update_option( 'mbis-version', $this->version );
		} else if (get_option('mbis-version', false) > $this->version) {
			// Downgrading
			update_option( 'mbis-version', $this->version );
		}
	}
}

function MBImprovedSearch() {
	global $mbis;
	
	// Instantiate only once.
	if( !isset($mbis) ) {
		$mbis = new MBImprovedSearch();
		$mbis->init();
	}
	return $mbis;
}

MBImprovedSearch();