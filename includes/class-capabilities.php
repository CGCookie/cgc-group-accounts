<?php

class CGC_Group_Capabilities {
	
	public function __construct() {

	}

	public function get_roles() {
		return array( 'owner', 'admin', 'member' );
	}

	public function get_tasks() {
		return array( 'manage_billing', 'manage_members', 'view_group' );
	}

	public function get_tasks_of_role( $role = '' ) {

		$tasks = array();

		if( empty( $role ) ) {
			return $tasks;
		}

		switch( $role ) {

			case 'owner' :
			
				$tasks[] = 'manage_billing';
				$tasks[] = 'manage_members';
				$tasks[] = 'view_group';
			
				break;

			case 'admin' :

				$tasks[] = 'manage_members';
				$tasks[] = 'view_group';

				break;

			case 'member' :

				$tasks[] = 'view_group';

		}

		return $tasks;

	}

	public function can( $task = '', $user_id = 0 ) {

		if( empty( $task ) || empty( $user_id ) ) {
			return false;
		}

		// Get the member's role in the group
		$role  = cgc_group_accounts()->members->get_role( $user_id );

		// Get the tasks their role has
		$tasks = $this->get_tasks_of_role( $role );

		return in_array( $task, $tasks );

	}

}