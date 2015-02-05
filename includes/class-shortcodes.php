<?php

class CGC_Group_Shortcodes {

	public function __construct() {

		add_shortcode( 'group_dashboard', array( $this, 'dashboard' ) );

	}

	public function dashboard( $atts, $content = null ) {

		ob_start();
		
		wp_enqueue_script( 'groups-dashboard', CGC_GROUPS_PLUGIN_URL . 'includes/frontend/groups-dashboard.js', array( 'jquery' ), filemtime( CGC_GROUPS_PLUGIN_DIR . 'includes/frontend/groups-dashboard.js' ) );
		wp_localize_script( 'groups-dashboard', 'cgc_group_vars', array(
			'ajax_url' => admin_url( 'admin-ajax.php' )
		) );

		include CGC_GROUPS_PLUGIN_DIR . 'includes/frontend/dashboard.php';

		return ob_get_clean();

	}

}
new CGC_Group_Shortcodes;