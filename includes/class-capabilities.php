<?php

class CGC_Group_Capabilities {
	
	public function __construct() {

		add_filter( 'rcp_is_active', array( $this, 'rcp_is_active' ), 10, 2 );

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

	public function can( $task = '', $user_id = 0, $group_id = 0 ) {

		if( empty( $task ) || empty( $user_id ) ) {
			return false;
		}

		if( current_user_can( 'manage_users' ) ) {
			return true;
		}

		// Get the member's role in the group
		$role  = cgc_group_accounts()->members->get_role( $user_id );

		// Get the tasks their role has
		$tasks = $this->get_tasks_of_role( $role );

		// Check that this user is in the specified group
		if( ! empty( $group_id ) ) {
			$in_group = (int) cgc_group_accounts()->members->get_group_id( $user_id ) === (int) $group_id;
		} else {
			$in_group = true;
		}

		return in_array( $task, $tasks ) && $in_group;

	}

	public function rcp_is_active( $ret, $user_id ) {
		if( ! $ret ) {

			$group_id = cgc_group_accounts()->members->get_group_id( $user_id );

			if( ! empty( $group_id ) ) {

				$ret = cgc_group_accounts()->groups->is_group_active( $group_id );

			}

		}

		return $ret;

	}

}