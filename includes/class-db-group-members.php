<?php

class CGC_Group_Members extends CGC_Groups_DB {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function __construct() {
		global $wpdb;

		$this->table_name  = 'group_members';
		$this->primary_key = 'user_id';
		$this->version     = '0.1';
	}

	/**
	 * Get table columns and date types
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function get_columns() {
		return array(
			'user_id'    => '%d',
			'group_id'   => '%d',
			'role'       => '%s',
			'date_added' => '%s',
		);
	}

	/**
	 * Get default column values
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function get_column_defaults() {
		return array(
			'user_id'  => get_current_user_id()
		);
	}

	/**
	 * Get the member role
	 *
	 * @access  public
	 * @since   1.0
	 * @return  string
	 */
	public function get_role( $user_id = 0 ) {
		$role = $this->get_column( 'role', $user_id );
		if( empty( $role ) ) {
			$role = 'member';
		}
		return $role;
	}

	/**
	 * Get the group name for this member
	 *
	 * @access  public
	 * @since   1.0
	 * @return  string
	 */
	public function get_group_name( $user_id = 0 ) {
		$group_id = $this->get_column( 'group_id', $user_id );
		if( empty( $group_id ) ) {
			return false;
		}

		return cgc_group_accounts()->groups->get_name( $group_id );
	}

	/**
	 * Create the table
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function create_table() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql = "CREATE TABLE {$this->table_name} (
			`user_id` bigint(20) NOT NULL,
			`group_id` bigint(20) NOT NULL,
			`role` tinytext NOT NULL,
			`date_added` datetime NOT NULL,
			PRIMARY KEY  (user_id)
			) CHARACTER SET utf8 COLLATE utf8_general_ci;";

		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}

}