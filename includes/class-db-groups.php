<?php

class CGC_Groups extends CGC_Groups_DB {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function __construct() {
		global $wpdb;

		$this->table_name  = 'groups';
		$this->primary_key = 'group_id';
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
			'group_id'      => '%d',
			'owner_id'      => '%d',
			'name'          => '%s',
			'description'   => '%s',
			'member_count'  => '%d',
			'fixed_billing' => '%d',
			'date_created'  => '%s',
		);
	}

	/**
	 * Get the group name
	 *
	 * @access  public
	 * @since   1.0
	 * @return  string
	 */
	public function get_name( $group_id = 0 ) {
		return $this->get_column( 'name', $group_id );
	}

	/**
	 * Get the group description
	 *
	 * @access  public
	 * @since   1.0
	 * @return  string
	 */
	public function get_description( $group_id = 0 ) {
		return $this->get_column( 'description', $group_id );
	}

	/**
	 * Get the group member_count
	 *
	 * @access  public
	 * @since   1.0
	 * @return  int
	 */
	public function get_member_count( $group_id = 0 ) {
		return absint( $this->get_column( 'member_count', $group_id ) );
	}

	/**
	 * Get the group owner_id
	 *
	 * @access  public
	 * @since   1.0
	 * @return  int
	 */
	public function get_owner_id( $group_id = 0 ) {
		return $this->get_column( 'owner_id', $group_id );
	}

	/**
	 * Adds a new group
	 *
	 * @access  public
	 * @since   1.0
	 * @return  int|false
	 */
	public function add( $args = array() ) {

		$defaults = array(
			'owner_id'      => 0,
			'description'   => '',
			'name'          => '',
			'member_count'  => 0,
			'fixed_billing' => 0,
			'date_created'  => current_time( 'mysql' ),
		);

		$args = wp_parse_args( $args, $defaults );

		if(  empty( $args['owner_id'] ) ) {
			return false;
		}

		$add = $this->insert( $args, 'group' );

		if( $add ) {

			do_action( 'cgc_add_group', $add );

			return $add;
		}

		return false;

	}

	/**
	 * Increment the group's member count
	 *
	 * @access  public
	 * @since   1.0
	 * @return  int New count
	 */
	public function increment_count( $group_id = 0 ) {

		if( empty( $group_id ) ) {
			return 0;
		}

		$count = $this->get_member_count( $group_id );
		$count += 1;
		$this->update( $group_id, array( 'member_count' => $count ) );

	}

	/**
	 * Decrement the group's member count
	 *
	 * @access  public
	 * @since   1.0
	 * @return  int New count
	 */
	public function decrement_count( $group_id = 0 ) {

		if( empty( $group_id ) ) {
			return 0;
		}

		$count = $this->get_member_count( $group_id );
		$count -= 1;
		$this->update( $group_id, array( 'member_count' => $count ) );

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
			`group_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`owner_id` bigint(20) NOT NULL,
			`name` mediumtext NOT NULL,
			`description` longtext NOT NULL,
			`member_count` bigint(20) NOT NULL,
			`fixed_billing` char(1) NOT NULL,
			`date_created` datetime NOT NULL,
			PRIMARY KEY (group_id)
			) CHARACTER SET utf8 COLLATE utf8_general_ci;";

		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}

}