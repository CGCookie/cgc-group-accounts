<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class CGC_Groups_Actions {

	public function __construct() {

		add_action( 'admin_init', array( $this, 'add_group' ) );
		add_action( 'init', array( $this, 'edit_group' ) );
		add_action( 'init', array( $this, 'delete_group' ) );
		add_action( 'init', array( $this, 'add_member_to_group' ) );
		add_action( 'init', array( $this, 'import_members_to_group' ) );
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

		if( empty( $_REQUEST['user_email'] ) ) {
			return;
		}

		if( empty( $_REQUEST['name'] ) ) {
			return;
		}

		$user = get_user_by( 'email', $_REQUEST['user_email'] );

		if( ! $user ) {
			wp_die( 'No user account with that email address found' );
		}

		$user_id     = $user->ID;
		$name        = sanitize_text_field( $_REQUEST['name'] );
		$description = ! empty( $_REQUEST['description'] ) ? sanitize_text_field( $_REQUEST['description'] ) : '';
		$seats       = ! empty( $_REQUEST['seats'] ) ? absint( $_REQUEST['seats'] ) : 0;

		$group_id    = cgc_group_accounts()->groups->add( array( 'owner_id' => $user_id, 'name' => $name, 'description' => $description, 'seats' => $seats ) );

		if( $group_id ) {

			cgc_group_accounts()->members->add( array( 'user_id' => $user_id, 'group_id' => $group_id, 'role' => 'owner' ) );

		}

		wp_redirect( add_query_arg( array( 'cgcg-action' => false, 'message' => 'group-added', 'view' => false ), $_SERVER['HTTP_REFERER'] ) );
		exit;

	}

	public function edit_group() {

		if( empty( $_REQUEST['cgcg-action'] ) ) {
			return;
		}

		if( 'edit-group' != $_REQUEST['cgcg-action'] ) {
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
		$seats       = ! empty( $_REQUEST['seats'] ) ? absint( $_REQUEST['seats'] ) : 0;

		$args        = array( 'name' => $name, 'description' => $description, 'seats' => $seats );

		if( ! current_user_can( 'manage_options' ) ) {
			unset( $args['seats'] );
		}

		$group_id    = cgc_group_accounts()->groups->update( $group, $args );

		wp_redirect( add_query_arg( array( 'cgcg-action' => false, 'message' => 'group-updated', 'view' => false ), $_SERVER['HTTP_REFERER'] ) );
		exit;

	}

	public function delete_group() {

		if( empty( $_REQUEST['cgcg-action'] ) ) {
			return;
		}

		if( 'delete-group' != $_REQUEST['cgcg-action'] ) {
			return;
		}

		if( empty( $_REQUEST['group'] ) ) {
			return;
		}

		if( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$group       = absint( $_REQUEST['group'] );
		cgc_group_accounts()->groups->delete( $group );

		wp_redirect( add_query_arg( array( 'cgcg-action' => false, 'message' => 'group-deleted' ), $_SERVER['HTTP_REFERER'] ) );
		exit;

	}

	public function add_member_to_group() {

		if( empty( $_REQUEST['cgcg-action'] ) ) {
			return;
		}

		if( 'add-member' != $_REQUEST['cgcg-action'] ) {
			return;
		}

		if( ! cgc_group_accounts()->capabilities->can( 'manage_members', get_current_user_id(), $_REQUEST['group'] ) ) {
			return;
		}

		if( empty( $_REQUEST['user_email'] ) ) {
			return;
		}

		if( empty( $_REQUEST['group'] ) ) {
			return;
		}

		$group_id  = absint( $_REQUEST['group'] );
		$email     = sanitize_text_field( $_REQUEST['user_email'] );
		$user      = get_user_by( 'email', $email );

		if( ! $user ) {
			return;
		}

		$seats_count = cgc_group_accounts()->groups->get_seats_count( $group_id );
		$mem_count   = cgc_group_accounts()->groups->get_member_count( $group_id );
		$seats_left  = $seats_count - $mem_count;

		if( $seats_left < 1 && ! current_user_can( 'manage_options' ) ) {
			wp_die( 'You do not have enough seats left in your group to add this members.' );
		}

		cgc_group_accounts()->members->add( array( 'user_id' => $user->ID, 'group_id' => $group_id ) );

		if( is_admin() && current_user_can( 'manage_options' ) ) {
			$redirect = admin_url( 'admin.php?page=cgc-groups&view=view-members&group=' . $group_id );
			$redirect = add_query_arg( array( 'cgcg-action' => false, 'message' => 'added' ), $redirect );
		} else {
			$redirect = home_url( '/settings/?message=group-member-added#manage-group' );
		}

		wp_redirect( $redirect );
		exit;

	}

	public function import_members_to_group() {

		if( empty( $_REQUEST['cgcg-action'] ) ) {
			return;
		}

		if( 'import-members' != $_REQUEST['cgcg-action'] ) {
			return;
		}
		if( ! cgc_group_accounts()->capabilities->can( 'manage_members', get_current_user_id(), $_REQUEST['group'] ) ) {
			return;
		}

		if( empty( $_FILES['group_csv'] ) ) {
			wp_die( 'Please upload a CSV file' );
		}

		if( empty( $_REQUEST['group'] ) ) {
			return;
		}

		$group_id  = absint( $_REQUEST['group'] );

		if( ! class_exists( 'parseCSV' ) ) {

			require_once dirname( __FILE__ ) . '/parsecsv.lib.php';
		}

		$import_file = ! empty( $_FILES['group_csv'] ) ? $_FILES['group_csv']['tmp_name'] : false;

		if( ! $import_file ) {
			wp_die( 'Something went wrong with your CSV file, please try again.' );
		}

		$csv         = new parseCSV( $import_file );
		$members     = $csv->data;
		$seats_count = cgc_group_accounts()->groups->get_seats_count( $group_id );
		$mem_count   = cgc_group_accounts()->groups->get_member_count( $group_id );
		$row_count   = count( $members );
		$seats_left  = $seats_count - $mem_count;

		if( $row_count > $seats_left ) {
			wp_die( sprintf( 'You do not have enough seats left in your group to import this many members. You have %d seats left and you tried to import %d members', $seats_left, $row_count ) );
		}

		if( ! $members ) {
			wp_die( 'Something went wrong with your CSV file, please try again.' );
		}

		foreach( $members as $member ) {

			$exists = get_user_by( 'email', $member['email'] );

			if( $exists ) {

				$user_id = $exists->ID;

			} else {

				$user_data  = array(
					'user_login' => $member['email'],
					'user_email' => $member['email'],
					'first_name' => $member['first_name'],
					'last_name'  => $member['last_name'],
					'user_pass'  => $member['password'],
					'role'       => 'subscriber'
				);

				$user_id = wp_insert_user( $user_data );

			}

			cgc_group_accounts()->members->add( array( 'user_id' => $user_id, 'group_id' => $group_id ) );
		}


		if( is_admin() && current_user_can( 'manage_options' ) ) {
			$redirect = admin_url( 'admin.php?page=cgc-groups&view=view-members&group=' . $group_id );
			$redirect = add_query_arg( array( 'cgcg-action' => false, 'message' => 'added' ), $redirect );
		} else {
			$redirect = home_url( '/settings/?message=group-member-added#manage-group' );
		}

		wp_redirect( $redirect );
		exit;

	}

	public function remove_member_from_group() {

		if( empty( $_REQUEST['cgcg-action'] ) ) {
			return;
		}

		if( 'remove-member' != $_REQUEST['cgcg-action'] ) {
			return;
		}

		if( ! cgc_group_accounts()->capabilities->can( 'manage_members', get_current_user_id(), $_REQUEST['group'] ) ) {
			return;
		}

		if( empty( $_REQUEST['member'] ) ) {
			return;
		}

		$group_id  = absint( $_REQUEST['group'] );
		$member_id = absint( $_REQUEST['member'] );

		cgc_group_accounts()->members->remove( $member_id );

		if( is_admin() && current_user_can( 'manage_options' ) ) {
			$redirect = admin_url( 'admin.php?page=cgc-groups&view=view-members&group=' . $group_id );
			$redirect = add_query_arg( array( 'cgcg-action' => false, 'message' => 'removed' ), $redirect );
		} else {
			$redirect = home_url( '/settings/?message=group-member-removed#manage-group' );
		}

		wp_redirect( $redirect );

		exit;

	}

	public function make_member_admin() {

		if( empty( $_REQUEST['cgcg-action'] ) ) {
			return;
		}

		if( 'make-admin' != $_REQUEST['cgcg-action'] ) {
			return;
		}

		if( ! cgc_group_accounts()->capabilities->can( 'manage_members', get_current_user_id(), $_REQUEST['group'] ) ) {
			return;
		}

		if( empty( $_REQUEST['member'] ) ) {
			return;
		}

		$group_id  = absint( $_REQUEST['group'] );
		$member_id = absint( $_REQUEST['member'] );

		cgc_group_accounts()->members->update( $member_id, array( 'role' => 'admin' ) );

		wp_cache_delete( 'cgc_group_' . $group_id . '_members', 'groups' );

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

		wp_cache_delete( 'cgc_group_' . $group_id . '_members', 'groups' );

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