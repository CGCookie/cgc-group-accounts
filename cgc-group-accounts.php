<?php
/*
Plugin Name: CG Cookie - Group Accounts
Plugin URL: https://github.com/CGCookie/cgc-group-accounts
Description: Provides the database and API for group accounts on the CG Cookie education network
Version: 1.4
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

	/** Singleton ************************************************************/

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
	private $version = '1.4';

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
	 * The capabilities instance variable.
	 *
	 * @var CGC_Group_Capabilities
	 * @since 1.0
	 */
	public $capabilities;

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
	 * @return CGC_Group_Accounts
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof CGC_Group_Accounts ) ) {
			self::$instance = new CGC_Group_Accounts;
			self::$instance->constants();
			self::$instance->includes();

			// Setup objects
			self::$instance->groups       = new CGC_Groups;
			self::$instance->members      = new CGC_Group_Members;
			self::$instance->capabilities = new CGC_Group_Capabilities;

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

		require_once CGC_GROUPS_PLUGIN_DIR . 'includes/class-capabilities.php';
		require_once CGC_GROUPS_PLUGIN_DIR . 'includes/class-db-base.php';
		require_once CGC_GROUPS_PLUGIN_DIR . 'includes/class-db-groups.php';
		require_once CGC_GROUPS_PLUGIN_DIR . 'includes/class-db-group-members.php';
		require_once CGC_GROUPS_PLUGIN_DIR . 'includes/class-actions.php';

		if( is_admin() ) {

			require_once CGC_GROUPS_PLUGIN_DIR . 'includes/admin/class-menu.php';
			require_once CGC_GROUPS_PLUGIN_DIR . 'includes/admin/class-notices.php';

		} else {

			require_once CGC_GROUPS_PLUGIN_DIR . 'includes/class-shortcodes.php';
			require_once CGC_GROUPS_PLUGIN_DIR . 'includes/frontend/class-notices.php';

		}

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


/**
 * Create database tables during install
 *
 * @since 1.0
 */
function cgc_group_accounts_install() {

	cgc_group_accounts()->groups->create_table();
	cgc_group_accounts()->members->create_table();

}
register_activation_hook( __FILE__, 'cgc_group_accounts_install' );
