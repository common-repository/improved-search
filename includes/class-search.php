<?php

namespace Mochabear\ImprovedSearch\Includes;

if( ! defined( 'ABSPATH' ) ) die('Not allowed');

class MBIS_Search extends MBIS_Base {

	function __construct() {
		parent::__construct();
		add_action('wp_enqueue_scripts', array( $this, 'load_scripts') );
		add_action('wp_ajax_improved_search_search', array( $this, 'callback') );
		add_action('wp_ajax_nopriv_improved_search_search',  array( $this, 'callback') );
	}

	function load_scripts() {

		//templates
		wp_enqueue_style(
			'improved-search-legacy', 
			$this->plugins_url . '/assets/css/templates/legacy.css', 
			false, 
			$this->version
		);

		wp_enqueue_style(
			'improved-search-full-screen', 
			$this->plugins_url . '/assets/css/templates/default.css', 
			false, 
			$this->version
		);

		// libraries
		wp_enqueue_style(
			'simple-grid', 
			$this->plugins_url . '/assets/lib/grid/simple-grid.min.css', 
			false, 
			$this->version
		);

		wp_enqueue_script(
			'improved-search', 
			$this->plugins_url . '/assets/js/improved-search.js', 
			array('jquery'), 
			$this->version, 
			true
		);

		wp_localize_script(
			'improved-search', 
			'MBISConfig', 
			array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'site_url' => site_url(),
				'security' => wp_create_nonce('improved-search'),
				'settings' => get_option($this->settings_option, $this->default_settings),
		));

	    wp_enqueue_script( 'improved-search-admin' );
	}

	function callback() {

		global $wpdb;
		check_ajax_referer( 'improved-search', 'security' );

		if (!isset($_POST['search'])){
			echo '{}';
			wp_die();
		}
		
		$settings = get_option($this->settings_option, $this->default_settings);
		$search_term = $_POST['search'];
		$authors = array();

		$args = array(
			's' => $search_term,
			'post_type' => $settings['searchable_post_types'],
		);

		$the_query = new \WP_Query( $args );

		if ( $the_query->have_posts() ) {

		    while ( $the_query->have_posts() ) : $the_query->the_post();
				
				if (!isset($post_types[$the_query->post->post_type]['label'])){
					$post_type_details = get_post_type_object($the_query->post->post_type);
					$post_types[$the_query->post->post_type]['label'] = $post_type_details->label;
					$post_types[$the_query->post->post_type]['description'] = $post_type_details->description;
				}
		        
				if (!isset($authors[$the_query->post->post_author])){
					$authors[$the_query->post->post_author]['display_name'] = get_the_author_meta('display_name');
					$authors[$the_query->post->post_author]['description'] = get_the_author_meta('description');
					$authors[$the_query->post->post_author]['first_name'] = get_the_author_meta('first_name');
					$authors[$the_query->post->post_author]['last_name'] = get_the_author_meta('last_name');
					$authors[$the_query->post->post_author]['nickname'] = get_the_author_meta('nickname');
					$authors[$the_query->post->post_author]['user_email'] = get_the_author_meta('user_email');
					$authors[$the_query->post->post_author]['user_url'] = get_the_author_meta('user_url');
					$authors[$the_query->post->post_author]['avatar'] = get_avatar_url($the_query->post->post_author);
					$authors[$the_query->post->post_author]['relevance'] = 1;
				} else {
					$authors[$the_query->post->post_author]['relevance']++;
				}

				$post_details = $the_query->post;
				$post = new \stdClass();
				$post->ID = $post_details->ID;
				$post->post_title = $post_details->post_title;
				$post->post_content = wp_trim_words( $post_details->post_content , $settings['excerpt_length'] );   // limit to setings content length
				$post->post_name = $post_details->post_name;
				$post->post_excerpt = $post_details->post_excerpt;
				$post->post_author =  $post_details->post_author;
				$post->relevance = substr_count($post->post_content, $search_term);
				$post->thumbnail = get_the_post_thumbnail();
				$post->permalink = get_permalink();
				$post->excerpt_trim = wp_trim_words( strip_shortcodes( get_the_excerpt() ) , $settings['excerpt_length'] );
		        $post_types[$the_query->post->post_type]['results'][] = $post;

		    endwhile;

		}

		wp_reset_postdata();

		$result = new \stdClass();
		$result->post_types = ($search_term == "" ? null : $post_types);
		$result->authors = $authors;
		$result->search_term = $search_term;

		echo json_encode($result);
		wp_die();
	}

}