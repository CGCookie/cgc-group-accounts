<?php
/*
Plugin Name: CG Cookie - Group Accounts
Plugin URL: https://github.com/CGCookie/cgc-group-accounts
Description: Provides the database and API for group accounts on the CG Cookie education network
Version: 0.1
Author: Pippin Williamson
Author URI: http://cgcookie.com
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'CGC_Group_Accounts' ) ) :

/**
 * Main CGC_Group_Accounts Class
 *
 * @since 1.0
 */
final class CGC_Group_Accounts {

	/** Singleton *************************************************************/

	/**
	 * @var CGC_Group_Accounts The one true CGC_Group_Accounts
	 * @since 1.0
	 */
	private static $instance;

	/**
	 * The version number of CGC Groups
	 *
	 * @since 1.0
	 */
	private $version = '0.1';

	/**
	 * The groups DB instance variable.
	 *
	 * @var CGC_Groups
	 * @since 1.0
	 */
	public $groups;

	/**
	 * The members instance variable.
	 *
	 * @var CGC_Group_Members
	 * @since 1.0
	 */
	public $members;

	/**
	 * Main CGC_Group_Accounts Instance
	 *
	 * Insures that only one instance of CGC_Group_Accounts exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0
	 * @static var array $instance
	 * @uses CGC_Group_Accounts::includes() Include the required files
	 * @uses CGC_Group_Accounts::setup_actions() Setup the hooks and actions
	 * @uses CGC_Group_Accounts::updater() Setup the plugin updater
	 * @return CGC_Group_Accounts
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof CGC_Group_Accounts ) ) {
			self::$instance = new CGC_Group_Accounts;
			self::$instance->constants();
			self::$instance->includes();

			// Setup objects
			self::$instance->groups  = new CGC_Groups;
			self::$instance->members = new CGC_Group_Members;

		}
		return self::$instance;
	}

	/**
	 * Setup plugin constants
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function constants() {
		// Plugin version
		if ( ! defined( 'CGC_GROUPS_VERSION' ) ) {
			define( 'CGC_GROUPS_VERSION', $this->version );
		}

		// Plugin Folder Path
		if ( ! defined( 'CGC_GROUPS_PLUGIN_DIR' ) ) {
			define( 'CGC_GROUPS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL
		if ( ! defined( 'CGC_GROUPS_PLUGIN_URL' ) ) {
			define( 'CGC_GROUPS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File
		if ( ! defined( 'CGC_GROUPS_PLUGIN_FILE' ) ) {
			define( 'CGC_GROUPS_PLUGIN_FILE', __FILE__ );
		}
	}

	/**
	 * Include required files
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function includes() {

		require_once CGC_GROUPS_PLUGIN_DIR . 'includes/class-db-base.php';
		require_once CGC_GROUPS_PLUGIN_DIR . 'includes/class-db-groups.php';
		require_once CGC_GROUPS_PLUGIN_DIR . 'includes/class-db-group-members.php';

	}

}

endif; // End if class_exists check


/**
 * The main function responsible for returning the one true CGC_Group_Accounts
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $CGC_Group_Accounts = CGC_Group_Accounts(); ?>
 *
 * @since 1.0
 * @return object The one true CGC_Group_Accounts Instance
 */
function cgc_group_accounts() {
	return CGC_Group_Accounts::instance();
}
cgc_group_accounts();