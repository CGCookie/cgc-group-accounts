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
			'group_id'     => '%d',
			'owner_id'     => '%d',
			'name'         => '%s',
			'description'  => '%s',
			'member_count' => '%d',
			'date_created' => '%s',
		);
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
			`description` largetext NOT NULL,
			`member_count` bigint(20) NOT NULL,
			`date_created` datetime NOT NULL,
			PRIMARY KEY (group_id)
			) CHARACTER SET utf8 COLLATE utf8_general_ci;";

		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}

}