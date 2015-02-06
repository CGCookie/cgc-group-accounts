<?php

class CGC_Groups_Frontend_Notices {

	function __construct() {

		add_action( 'init', array( $this, 'add_notices' ) );
	}


	public function add_notices() {

		if( ! isset( $_GET['message'] ) ) {
			return;
		}

		$type   = 'success';
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

			case 'password-updated' :

				$message = 'Member\'s password successfully updated';

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

			case 'no-user' :

				$message = 'That email does not appear to exist in our system';
				$type    = 'error';

				break;

			case 'empty-email' :

				$message = 'Please enter an email address';
				$type    = 'error';

				break;

			case 'no-permission';
				$message = 'You do not have permission to perform that action';
				$type    = 'error';

				break;

			case 'no-group';
				$message = 'Oops, no group ID was specified. How did that happen? We do not know';
				$type    = 'error';

				break;

		}

		if ( ! empty( $message ) && function_exists( 'cgc_add_notice' ) ) {
			cgc_add_notice( $message, $type );
		}

	}

}
new CGC_Groups_Frontend_Notices;