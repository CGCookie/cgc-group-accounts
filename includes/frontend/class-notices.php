<?php

class CGC_Groups_Frontend_Notices {

	function __construct() {

		add_action( 'cgc_notices', array( $this, 'show_notices' ) );
	}


	public function show_notices() {

		if( ! isset( $_GET['message'] ) ) {
			return;
		}

		$class  = 'updated';
		$notice = $_GET['message'];

		switch( $notice ) {

			case 'group-member-added' :

				$message = 'Group member added successfully';

				break;

			case 'group-member-removed' :

				$message = 'Group member removed successfully';

				break;

			case 'group-members-imported' :

				$message = 'Group members imported successfully';

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

		if ( ! empty( $message ) ) {
			echo '<div class="' . esc_attr( $class ) . '"><p><strong>' .  $message  . '</strong></p></div>';
		}

	}

}
new CGC_Groups_Frontend_Notices;