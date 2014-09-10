<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class CGC_Groups_Actions {

	public function __construct() {

		add_action( 'admin_init', array( $this, 'add_group' ) );
		add_action( 'init', array( $this, 'edit_group' ) );
		add_action( 'init', array( $this, 'add_member_to_group' ) );
		add_action( 'init', array( $this, 'remove_member_from_group' ) );
		add_action( 'init', array( $this, 'make_member_admin' ) );
		add_action( 'init', array( $this, 'make_admin_member' ) );
		add_action( 'wp_ajax_cgc_search_users', array( $this, 'search_users' ) );

	}

	public function add_group() {

		if( empty( $_REQUEST['cgcg-action'] ) ) {
			return;
		}

		if( 'add-group' != $_REQUEST['cgcg-action'] ) {
			return;
		}

		if( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if( empty( $_REQUEST['user_id'] ) ) {
			return;
		}

		if( empty( $_REQUEST['name'] ) ) {
			return;
		}

		$user_id     = absint( $_REQUEST['user_id'] );
		$name        = sanitize_text_field( $_REQUEST['name'] );
		$description = ! empty( $_REQUEST['description'] ) ? sanitize_text_field( $_REQUEST['description'] ) : '';

		$group_id    = cgc_group_accounts()->groups->add( array( 'owner_id' => $user_id, 'name' => $name, 'description' => $description ) );

		cgc_group_accounts()->members->add( array( 'user_id' => $user_id, 'group_id' => $group_id, 'role' => 'owner' ) );

		wp_redirect( add_query_arg( array( 'cgcg-action' => false, 'message' => 'group-updated' ), $_SERVER['HTTP_REFERER'] ) );
		exit;

	}

	public function edit_group() {

		if( empty( $_REQUEST['cgcg-action'] ) ) {
			return;
		}

		if( 'edit-group' != $_REQUEST['cgcg-action'] ) {
			return;
		}

		if( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if( empty( $_REQUEST['group'] ) ) {
			return;
		}

		if( ! cgc_group_accounts()->capabilities->can( 'manage_members', get_current_user_id(), $_REQUEST['group'] ) ) {
			return;
		}

		if( empty( $_REQUEST['name'] ) ) {
			return;
		}

		$group       = absint( $_REQUEST['group'] );
		$name        = sanitize_text_field( $_REQUEST['name'] );
		$description = ! empty( $_REQUEST['description'] ) ? sanitize_text_field( $_REQUEST['description'] ) : '';

		$group_id    = cgc_group_accounts()->groups->update( $group, array( 'name' => $name, 'description' => $description ) );

		wp_redirect( add_query_arg( array( 'cgcg-action' => false, 'message' => 'group-added' ), $_SERVER['HTTP_REFERER'] ) );
		exit;

	}

	public function add_member_to_group() {

		if( empty( $_REQUEST['cgcg-action'] ) ) {
			return;
		}

		if( 'add-member' != $_REQUEST['cgcg-action'] ) {
			return;
		}

		if( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if( empty( $_REQUEST['user_id'] ) ) {
			return;
		}

		if( empty( $_REQUEST['group'] ) ) {
			return;
		}

		$group_id  = absint( $_REQUEST['group'] );
		$user_id   = absint( $_REQUEST['user_id'] );

		cgc_group_accounts()->members->add( array( 'user_id' => $user_id, 'group_id' => $group_id ) );

		wp_redirect( add_query_arg( array( 'cgcg-action' => false, 'message' => 'added' ), $_SERVER['HTTP_REFERER'] ) );
		exit;

	}

	public function remove_member_from_group() {

		if( empty( $_REQUEST['cgcg-action'] ) ) {
			return;
		}

		if( 'remove-member' != $_REQUEST['cgcg-action'] ) {
			return;
		}

		if( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if( empty( $_REQUEST['member'] ) ) {
			return;
		}

		$group_id  = absint( $_REQUEST['group'] );
		$member_id = absint( $_REQUEST['member'] );

		cgc_group_accounts()->members->remove( $member_id );

		wp_redirect( add_query_arg( array( 'cgcg-action' => false, 'message' => 'removed' ), $_SERVER['HTTP_REFERER'] ) );
		exit;

	}

	public function make_member_admin() {

		if( empty( $_REQUEST['cgcg-action'] ) ) {
			return;
		}

		if( 'make-admin' != $_REQUEST['cgcg-action'] ) {
			return;
		}

		if( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if( empty( $_REQUEST['member'] ) ) {
			return;
		}

		$group_id  = absint( $_REQUEST['group'] );
		$member_id = absint( $_REQUEST['member'] );

		cgc_group_accounts()->members->update( $member_id, array( 'role' => 'admin' ) );

		wp_redirect( add_query_arg( array( 'cgcg-action' => false, 'message' => 'role-updated' ), $_SERVER['HTTP_REFERER'] ));
		exit;

	}

	public function make_admin_member() {

		if( empty( $_REQUEST['cgcg-action'] ) ) {
			return;
		}

		if( 'make-member' != $_REQUEST['cgcg-action'] ) {
			return;
		}

		if( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if( empty( $_REQUEST['member'] ) ) {
			return;
		}

		$group_id  = absint( $_REQUEST['group'] );
		$member_id = absint( $_REQUEST['member'] );

		cgc_group_accounts()->members->update( $member_id, array( 'role' => 'member' ) );

		wp_redirect( add_query_arg( array( 'cgcg-action' => false, 'message' => 'role-updated' ), $_SERVER['HTTP_REFERER'] ) );
		exit;

	}

	// retrieves a list of users via live search
	function search_users() {

		if( empty( $_POST['user_name'] ) ) {
			die( '-1' );
		}

		$search_query = htmlentities2( trim( $_POST['user_name'] ) );

		$found_users = get_users( array(
				'number' => 9999,
				'search' => $search_query . '*'
			)
		);

		if( $found_users ) {
			$user_list = '<ul>';
			foreach( $found_users as $user ) {
				$user_list .= '<li><a href="#" data-id="' . esc_attr( $user->ID ) . '" data-login="' . esc_attr( $user->user_login ) . '">' . esc_html( $user->user_login ) . '</a></li>';
			}
			$user_list .= '</ul>';

			echo json_encode( array( 'results' => $user_list, 'id' => 'found' ) );

		} else {
			echo json_encode( array( 'results' => '<p>' . __( 'No users found', 'affiliate-wp' ) . '</p>', 'id' => 'fail' ) );
		}

		die();
	}

}
new CGC_Groups_Actions;