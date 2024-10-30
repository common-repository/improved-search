<?php

namespace Mochabear\ImprovedSearch\Includes;

if( ! defined( 'ABSPATH' ) ) die('Not allowed');

class MBIS_SettingsMenu extends MBIS_Base {

	function __construct() {
		parent::__construct();
		add_action( 'admin_menu', array( $this, 'create_settings_menu') );
		add_action('admin_enqueue_scripts', array( $this, 'load_admin_scripts') );

		add_action( 'wp_ajax_mbis_settings_update', array($this, 'settings_update_callback'));
		add_filter( "plugin_action_links_improved-search/improved-search.php", array( $this, 'add_plugin_link' ) );
		add_filter( 'plugin_row_meta', array( $this, 'add_donate_link' ), 10, 4 );


	}

	function create_settings_menu() {

		$user = wp_get_current_user();

		if (in_array('administrator', (array) $user->roles)) {
			add_menu_page(
				'Improved Search',
				'Improved Search',
				'read',
				'improved-search',
				array( $this, 'admin_page' ),
				'dashicons-search'
			);
		}
	}

	function load_admin_scripts($hook) {
		if ($hook != 'toplevel_page_improved-search') {
			return;
		}

		wp_enqueue_style(
			'improved-search-admin', 
			$this->plugins_url . '/assets/css/admin.css', 
			true, 
			$this->version
		);

		wp_enqueue_script(
			'improved-search-admin', 
			$this->plugins_url . '/assets/js/admin.js', 
			array('jquery'), 
			$this->version
		);

	    wp_localize_script( 'improved-search-admin', 'ImprovedSearch', array(
	    	'ajaxurl' => admin_url( 'admin-ajax.php' ),
	    	'security' => wp_create_nonce( 'settings-update-event' ),
	    	'settings' => get_option($this->settings_option, $this->default_settings)
	    ));

	    wp_enqueue_script( 'improved-search-admin' );
	}

    function admin_page() {
    	extract(
    		array(
    			"settings" => get_option($this->settings_option, $this->default_settings),
    			"templates" => $this->templates,
    		)
    	);
		include 'template-settings-menu.php';
    }

    public function settings_update_callback() {
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
		header('Content-type: application/json');

		check_ajax_referer( 'settings-update-event' , 'security' );

		if ( ! isset( $_POST['settings'] ) ) {
			echo "settings is required";
			wp_die();
		}

		update_option(
			$this->settings_option,
			$this->clean_settings_post( $_POST['settings'] )
		);

		$response = new \stdClass();
		$response->status = 200;
		$response->data = get_option($this->settings_option);

		echo json_encode( $response );
		wp_die();
    }

    function clean_settings_post( $settings_post ) {
    	return $settings_post;
    	return $this->default_settings;
		$settings = array(
			'generalOptions' => new \stdClass(),
			'socialMediaEnabled' => array(),
			'socialMediaOptions' => array(),
		);

		foreach ($settings_post as $settings_key => $settings_value) {
			$settings_key = sanitize_text_field( $settings_key );

			switch ($settings_key) {
				case "generalOptions":
					foreach ($settings_value as $option_key => $option_value) {
						if ( in_array($option_key, array('openInNewTab') ) )
							$settings['generalOptions']->{ sanitize_text_field( $option_key ) } = sanitize_key( $option_value );
					}	
				break;
				case "socialMediaEnabled":
					foreach ($settings_value as $social_media) {
						if ( isset( $this->social_media[ $social_media ] ) )
							$settings['socialMediaEnabled'][] = sanitize_key( $social_media );
					}
				break;
				case "socialMediaOptions":
					foreach ($settings_value as $social_media => $social_media_properties) {
						if ( isset( $this->social_media[ $social_media ] ) ) {
							$settings['socialMediaOptions'][ sanitize_text_field( $social_media ) ] = array();
							$settings['socialMediaOptions'][ sanitize_text_field( $social_media ) ]['label'] = sanitize_text_field( $social_media_properties['label'] );
							$settings['socialMediaOptions'][ sanitize_text_field( $social_media ) ]['url'] = esc_url_raw( $social_media_properties['url'] );
							$settings['socialMediaOptions'][ sanitize_text_field( $social_media ) ]['order'] = intval( $social_media_properties['order'] );
						}
					}
				break;
				default:
				break;
			}
		}

		return $settings;
    }

	function add_plugin_link( $links ) {
		array_push( $links, '<a href="admin.php?page=improved-search">' . __( 'Settings' ) . '</a>' );
		return $links;
	}

	function add_donate_link( $links, $plugin_file_name, $plugin_data, $status ) {
		if ( strpos( $plugin_file_name, 'improved-search.php' ) ) {
			array_push( $links, '<a href="https://www.paypal.com/donate?hosted_button_id=2VTRUM4SX87CA" target="_blank">' . __( 'Donate' ) . '</a>' );
		}
		return $links;
	}

}
