<?php

class CGC_Group_Shortcodes {

	public function __construct() {

		add_shortcode( 'group_dashboard', array( $this, 'dashboard' ) );

	}

	public function dashboard( $atts, $content = null ) {

		ob_start();
		
		include CGC_GROUPS_PLUGIN_DIR . 'includes/frontend/dashboard.php';

		return ob_get_clean();

	}

}
new CGC_Group_Shortcodes;