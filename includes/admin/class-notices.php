<?php

class CGC_Groups_Admin_Notices {

	function __construct() {

		add_action( 'admin_notices', array( $this, 'show_notices' ) );
	}


	public function show_notices() {

		if( ! isset( $_GET['page'] ) ) {
			return;
		}

		if( 'cgc-groups' !== $_GET['page'] ) {
			return;
		}

		$class = 'updated';

		if ( isset( $_GET['message'] ) && $_GET['message'] ) {

			switch( $_GET['message'] ) {

				case 'added' :

					$message = 'Group member added successfully';

					break;

				case 'removed' :

					$message = 'Group member removed successfully';

					break;

				case 'role-updated' :

					$message = 'Member\'s role successfully updated';

					break;

				case 'group-added' :

					$message = 'Group added successfully';

					break;

				case 'group-updated' :

					$message = 'Group updated successfully';

					break;

				case 'group-deleted' :

					$message = 'Group deleted successfully';

					break;

			}
		}

		if ( ! empty( $message ) ) {
			echo '<div class="' . esc_attr( $class ) . '"><p><strong>' .  $message  . '</strong></p></div>';
		}

	}

}
new CGC_Groups_Admin_Notices;