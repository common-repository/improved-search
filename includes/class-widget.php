<?php

namespace Mochabear\ImprovedSearch\Includes;

// require_once ABSPATH.'wp-includes/class-wp-widget.php';

if( ! defined( 'ABSPATH' ) ) die('Not allowed');

class MBIS_Widget extends \WP_Widget {

	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'improved_search_widget', 
			// Widget name will appear in UI
			__('Improved Search', 'improved_search_widget_domain'), 
			// Widget description
			array( 'description' => __( 'Improved Search for WordPress', 'improved_search_widget_domain' ), ) 
		);
	}

	// Creating widget front-end
	public function widget( $args, $instance ) {
		
		$mb = new MBIS_Base();

		extract(
    		array(
    			"settings" => get_option($mb->settings_option, $mb->default_settings),
    		)
    	);

		$title = apply_filters( 'widget_title', $instance['title'] );

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];

		// This is where you run the code and display the output
		include 'template-search.php';

		echo $args['after_widget'];
	}

	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Search', 'improved_search_widget_domain' );
		}
		// Widget admin form
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}

	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
}