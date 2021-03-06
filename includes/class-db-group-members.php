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
	 * Determine if the user is a member of a group
	 *
	 * @access  public
	 * @since   1.0
	 * @return  bool
	 */
	public function is_group_member( $user_id = 0 ) {
		if( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		return (bool) $this->get_group_id( $user_id );
	}

	/**
	 * Get the member role
	 *
	 * @access  public
	 * @since   1.0
	 * @return  string
	 */
	public function get_role( $user_id = 0 ) {

		if( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		$role = $this->get_column( 'role', $user_id );

		return $role ? $role : false;
	}

	/**
	 * Get the group name for this member
	 *
	 * @access  public
	 * @since   1.0
	 * @return  string
	 */
	public function get_group_name( $user_id = 0 ) {

		if( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		$group_id = $this->get_column( 'group_id', $user_id );
		if( empty( $group_id ) ) {
			return false;
		}

		return cgc_group_accounts()->groups->get_name( $group_id );
	}

	/**
	 * Get the group ID this memer belongs to
	 *
	 * @access  public
	 * @since   1.0
	 * @return  int
	 */
	public function get_group_id( $user_id = 0 ) {

		if( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		return $this->get_column( 'group_id', $user_id );
	}

	/**
	 * Adds a new member to a group
	 *
	 * @access  public
	 * @since   1.0
	 * @return  int|false
	 */
	public function add( $args = array() ) {

		$defaults = array(
			'user_id'    => 0,
			'group_id'   => 0,
			'role'       => 'member',
			'date_added' => current_time( 'mysql' ),
		);

		$args = wp_parse_args( $args, $defaults );

		if( empty( $args['user_id'] ) || empty( $args['group_id'] ) ) {
			return false;
		}

		$add = $this->insert( $args, 'member' );

		do_action( 'cgc_add_group_member', $add );

		//wp_cache_delete( 'cgc_group_' . $args['group_id'] . '_members', 'groups' );

		cgc_group_accounts()->groups->increment_count( $args['group_id'] );

		return true;

	}

	/**
	 * Removes a user from any group they belong to
	 *
	 * @access  public
	 * @since   1.0
	 * @return  int|false
	 */
	public function remove( $user_id = 0 ) {

		if(  empty( $user_id ) ) {
			return false;
		}

		$group_id = $this->get_group_id( $user_id );

		$this->delete( $user_id );

		do_action( 'cgc_remove_group_member', $user_id );

		//wp_cache_delete( 'cgc_group_' . $group_id . '_members', 'groups' );

		cgc_group_accounts()->groups->decrement_count( $group_id );

	}

	/**
	 * Deletes all members from a specific group. This is for when we delete a group
	 *
	 * @access  public
	 * @since   1.0
	 * @return  int|false
	 */
	public function remove_all_from_group( $group_id = 0 ) {

		global $wpdb;

		if(  empty( $group_id ) ) {
			return false;
		}

		$wpdb->query( $wpdb->prepare( "DELETE FROM $this->table_name WHERE group_id = '%d'", $group_id ) );

		//wp_cache_delete( 'cgc_group_' . $group_id . '_members', 'groups' );


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