<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class CGC_Groups_Actions {

	public function __construct() {

		add_action( 'admin_init', 					array( $this, 'add_group' ) ); // admin
		add_action( 'admin_init', 					array( $this, 'delete_group' ) ); // admin
		add_action( 'admin_init', 					array( $this, 'edit_group' ) ); // admin
		add_action( 'admin_init', 					array( $this, 'admin_remove_member_from_group' ) ); // admin
		add_action( 'admin_init', 					array( $this, 'admin_make_member_admin' ) ); // admin

		add_action( 'init', 						array( $this, 'edit_group_front' ) ); // front-end
		add_action( 'init', 						array( $this, 'add_member_to_group' ) ); // front-end
		add_action( 'init', 						array( $this, 'remove_member_from_group' ) ); // front-end
		add_action( 'init', 						array( $this, 'make_member_admin' ) ); // front-end
		add_action( 'init', 						array( $this, 'set_member_password' ) ); // front-end

		add_action( 'init', 						array( $this, 'import_members_to_group' ) ); // not in use

	}

	/**
	*	Add a group
	*
	*	BACK-END
	*/
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
		$description = ! empty( $_REQUEST['description'] ) ? wp_kses( $_REQUEST['description'], wp_kses_allowed_html( 'post' ) ) : '';
		$seats       = ! empty( $_REQUEST['seats'] ) ? absint( $_REQUEST['seats'] ) : 0;
		$expiration  = !empty( $_REQUEST['expiration'] ) ? sanitize_text_field( $_REQUEST['expiration'] ) : '';

		if( cgc_group_accounts()->groups->is_group_owner( $user_id ) ) {
			wp_die( sprintf( 'User ID %d is already the owner of a group. Users may only be the owner of one group at a time.', $user_id ) );
		}

		$group_id    = cgc_group_accounts()->groups->add(
			array(
				'owner_id' => $user_id,
				'name' => $name,
				'description' => $description,
				'seats' => $seats,
				'expiration' => $expiration
		) );

		if( $group_id ) {

			cgc_group_accounts()->members->add( array( 'user_id' => $user_id, 'group_id' => $group_id, 'role' => 'owner' ) );

		}

		wp_redirect( add_query_arg( array( 'cgcg-action' => false, 'message' => 'group-added', 'view' => false ), $_SERVER['HTTP_REFERER'] ) );
		exit;

	}

	/**
	*	Edit a group
	*
	*	BACK-END
	*/
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
		$description = ! empty( $_REQUEST['description'] ) ? wp_kses( $_REQUEST['description'], wp_kses_allowed_html( 'post' ) ) : '';
		$seats       = ! empty( $_REQUEST['seats'] ) ? absint( $_REQUEST['seats'] ) : 0;
		$expiration  = !empty( $_REQUEST['expiration'] ) ? $_REQUEST['expiration']: '';

		$args        = array( 'name' => $name, 'description' => $description, 'seats' => $seats, 'expiration' => $expiration );

		$group_id    = cgc_group_accounts()->groups->update( $group, $args );

		wp_redirect( add_query_arg( array( 'cgcg-action' => false, 'message' => 'group-updated', 'view' => false ), $_SERVER['HTTP_REFERER'] ) );
		exit;

	}

	/**
	*	Edit a group
	*
	*	FRONT-END
	*/
	public function edit_group_front() {

		if( empty( $_POST['action'] ) ) {
			return;
		}

		if( 'edit-group' != $_POST['action'] ) {
			return;
		}

		if( empty( $_POST['group'] ) ) {
			return;
		}

		if( ! cgc_group_accounts()->capabilities->can( 'manage_members', get_current_user_id(), $_POST['group'] ) ) {
			return;
		}

		if( empty( $_POST['name'] ) ) {
			return;
		}

		$group       = absint( $_POST['group'] );
		$name        = sanitize_text_field( $_POST['name'] );
		$description = ! empty( $_POST['description'] ) ? wp_kses( $_POST['description'], wp_kses_allowed_html( 'post' ) ) : '';

		$args        = array( 'name' => $name, 'description' => $description );

		$group_id    = cgc_group_accounts()->groups->update( $group, $args );

		wp_send_json_success();

	}

	/**
	*	Delete a group
	*
	*	BACK-END
	*/
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

		$group = absint( $_REQUEST['group'] );

		cgc_group_accounts()->members->remove_all_from_group( $group );
		cgc_group_accounts()->groups->delete( $group );

		wp_redirect( add_query_arg( array( 'cgcg-action' => false, 'message' => 'group-deleted' ), $_SERVER['HTTP_REFERER'] ) );
		exit;

	}

	/**
	*	Add a member to a group
	*
	*	FRONT-END
	*/
	public function add_member_to_group() {

		$error = false;

		if( empty( $_REQUEST['cgcg-action'] ) ) {
			return;
		}

		if( 'add-member' != $_REQUEST['cgcg-action'] ) {
			return;
		}

		if( ! cgc_group_accounts()->capabilities->can( 'manage_members', get_current_user_id(), $_REQUEST['group'] ) ) {
			$error = 'no-permission';
		}

		if( empty( $_REQUEST['user_email'] ) ) {
			$error = 'empty-email';
		}

		if( empty( $_REQUEST['group'] ) ) {
			$error = 'no-group';
		}

		if  ( !is_email( $_REQUEST['user_email'] ) )
			$error = 'not-an-email-address';

		if( ! $error ) {

			$group_id  = absint( $_REQUEST['group'] );
			$email     = sanitize_text_field( $_REQUEST['user_email'] );
			$user      = get_user_by( 'email', $email );

			$is_citizen = $user && class_exists('cgcUserAPI') ? cgcUserAPI::is_user_citizen( $user->ID ) : false;

			if( !$user ) {

				// No user found, create one
				$args = array(
					'user_login' => $email,
					'user_email' => $email,
					'role'       => 'subscriber',
					'user_pass'  => wp_generate_password()
				);

				$user_id = wp_insert_user( $args );

			} else if ( $is_citizen ) {

				$error = 'user-is-citizen';
				wp_send_json_error( array( 'message' => 'user-is-citizen' ) );

			} else {

				$user_id = $user->ID;

			}

			$seats_count = cgc_group_accounts()->groups->get_seats_count( $group_id );
			$mem_count   = cgc_group_accounts()->groups->get_member_count( $group_id );
			$seats_left  = $seats_count - $mem_count;

			if( $seats_left < 1 && ! current_user_can( 'manage_options' ) ) {
				wp_die( 'You do not have enough seats left in your group to add this members.' );
			}

			if( ! $error ) {

				cgc_group_accounts()->members->add( array( 'user_id' => $user_id, 'group_id' => $group_id ) );

				$userdata = get_userdata( $user_id );

				$payload = array(
					'user_id' 		=> $user_id,
					'name'			=> $userdata->display_name,
					'group_id' 		=> $group_id,
					'user_email' 	=> $email,
					'avatar'		=> cgcUserAPI::get_profile_avatar( $user_id, 48, true )
				);
				wp_send_json_success( $payload );

			}

		} else {

			$message = $error;
			wp_send_json_error();
		}

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
			$redirect = home_url( '/settings/?message=group-members-imported#manage-group' );
		}

		wp_redirect( $redirect );
		exit;

	}

	/**
	*	Remove a member from a group
	*
	*	FRONT-END
	*/
	public function remove_member_from_group() {

		if( empty( $_POST['action'] ) ) {
			return;
		}

		if( 'remove-group-member' != $_POST['action'] ) {
			return;
		}

		if( !cgc_group_accounts()->capabilities->can( 'manage_members', get_current_user_id(), $_POST['group'] ) ) {
			return;
		}

		if( empty( $_POST['member'] ) ) {
			return;
		}

		$group_id  = absint( $_POST['group'] );
		$member_id = absint( $_POST['member'] );

		cgc_group_accounts()->members->remove( $member_id );

		wp_send_json_success();

	}

	/**
	*	Remove a member from a group
	*
	*	FRONT-END
	*/
	public function admin_remove_member_from_group() {


		if( empty( $_REQUEST['cgcg-action'] ) ) {
			return;
		}

		if( 'remove-member' != $_REQUEST['cgcg-action'] ) {
			return;
		}

		if( !cgc_group_accounts()->capabilities->can( 'manage_members', get_current_user_id(), $_REQUEST['group'] ) ) {
			return;
		}

		if( empty( $_REQUEST['member'] ) ) {
			return;
		}


		$group_id  = absint( $_REQUEST['group'] );
		$member_id = absint( $_REQUEST['member'] );

		cgc_group_accounts()->members->remove( $member_id );

		wp_redirect( add_query_arg( array( 'cgcg-action' => false, 'message' => 'group-deleted' ), $_SERVER['HTTP_REFERER'] ) );
		exit;

	}

	public function make_member_admin() {

		if( empty( $_POST['action'] ) || empty( $_POST['member'] ) ) {
			return;
		}

		if( ! cgc_group_accounts()->capabilities->can( 'manage_members', get_current_user_id(), $_POST['group'] ) ) {
			return;
		}

		$group_id  = absint( $_POST['group'] );
		$member_id = absint( $_POST['member'] );

		if( 'make-admin' == $_POST['action'] ) {

			cgc_group_accounts()->members->update( $member_id, array( 'role' => 'admin' ) );

		} else if ( 'make-member' == $_POST['action'] ) {

			cgc_group_accounts()->members->update( $member_id, array( 'role' => 'member' ) );

		} else {

			wp_send_json_error();
		}

		wp_send_json_success();

	}

	public function admin_make_member_admin() {

		if( empty( $_REQUEST['cgcg-action'] ) || empty( $_REQUEST['member'] ) ) {
			return;
		}

		if( ! cgc_group_accounts()->capabilities->can( 'manage_members', get_current_user_id(), $_REQUEST['group'] ) ) {
			return;
		}

		$group_id  = absint( $_REQUEST['group'] );
		$member_id = absint( $_REQUEST['member'] );

		if( 'make-admin' == $_REQUEST['cgcg-action'] ) {

			cgc_group_accounts()->members->update( $member_id, array( 'role' => 'admin' ) );

		} else if ( 'make-member' == $_REQUEST['cgcg-action'] ) {

			cgc_group_accounts()->members->update( $member_id, array( 'role' => 'member' ) );

		} else {

			return;
		}

		wp_cache_delete( 'cgc_group_' . $group_id . '_members', 'groups' );

		wp_redirect( add_query_arg( array( 'cgcg-action' => false, 'message' => 'member-updated' ), $_SERVER['HTTP_REFERER'] ) );
		exit;

	}

	public function set_member_password() {

		if( empty( $_POST['action'] ) ) {
			return;
		}

		if( 'set-member-password' != $_POST['action'] ) {
			return;
		}

		if( ! cgc_group_accounts()->capabilities->can( 'manage_members', get_current_user_id(), $_POST['group'] ) ) {
			return;
		}

		if( empty( $_POST['user_id'] ) ) {
			wp_die( 'Something has gone wrong; no member was specified' );
		}

		if( empty( $_POST['pass'] ) ) {
			wp_die( 'Please provide a password' );
		}

		if( empty( $_POST['pass2'] ) ) {
			wp_die( 'Please confirm the password' );
		}

		if( sanitize_text_field( $_POST['pass'] ) !== sanitize_text_field( $_POST['pass2'] ) ) {
			wp_die( 'Passwords do not match' );
		}

		$group_id  = absint( $_POST['group'] );
		$role      = cgc_group_accounts()->members->get_role( $_POST['user_id'] );

		if( strtolower( $role ) !== 'member' ) {
			wp_die( 'Owner and admin passwords cannot be changed' );
		}

		wp_update_user( array( 'ID' => $_POST['user_id'], 'user_pass' => sanitize_text_field( $_POST['pass'] ) ) );

		wp_send_json_success();

	}

}
new CGC_Groups_Actions;