<?php

class CGC_Groups_Admin_Menu {
	

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menus' ), 100 );
	}

	public function register_menus() {
		add_submenu_page( 'rcp-members','Groups', 'Groups', 'rcp_view_members', 'cgc-groups', array( $this, 'groups_admin' ) );
	}

	public function groups_admin() {

		if( isset( $_GET['edit-group'] ) ) {

		} elseif( isset( $_GET['add-member'] ) ) {
		
		} elseif( isset( $_GET['group'] ) ) {

			include CGC_GROUPS_PLUGIN_DIR . 'includes/admin/groups/members.php';

		} else {

			include CGC_GROUPS_PLUGIN_DIR . 'includes/admin/groups/list.php';

		}

	}

}
$cgc_groups_admin_menu = new CGC_Groups_Admin_Menu;