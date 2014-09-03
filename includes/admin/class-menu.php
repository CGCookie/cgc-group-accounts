<?php

class CGC_Groups_Admin_Menu {
	

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menus' ), 100 );
	}

	public function register_menus() {
		add_submenu_page( 'rcp-members','Groups', 'Groups', 'rcp_view_members', 'cgc-groups', array( $this, 'groups_admin' ) );
	}

	public function groups_admin() {

		$view = isset( $_GET['view'] ) ? $_GET['view'] : '';

		switch( $view ) {

			case 'edit' :

				break;

			case 'view-members' :

				include CGC_GROUPS_PLUGIN_DIR . 'includes/admin/groups/members.php';
		
				break;

			case 'add-member' :

				include CGC_GROUPS_PLUGIN_DIR . 'includes/admin/groups/add-member.php';
		
				break;

			default:

				include CGC_GROUPS_PLUGIN_DIR . 'includes/admin/groups/list.php';
	
				break;

		}

	}

}
$cgc_groups_admin_menu = new CGC_Groups_Admin_Menu;