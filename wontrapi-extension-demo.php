<?php 
/**
 * Plugin Name: Wontrapi Extension Demo
 * Plugin URI:  https://github.com/oakwoodgates/wontrapi-extension-demo
 * Description: 
 * Version:     0.1.0
 * Author:      OakwoodGates
 * Author URI:  https://wpguru4u.com
 * Donate link: https://github.com/oakwoodgates/wontrapi-extension-demo
 * License:     GPLv2
 * Text Domain: wontrapi-extension-demo
 * Domain Path: /languages
 *
 * @link    https://github.com/oakwoodgates/wontrapi-extension-demo
 *
 * @package Wontrapi_Extension_Demo
 * @version 0.1.0
 *
 */

/**
 * Copyright (c) 2019 OakwoodGates (email : wpguru4u@gmail.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( ! defined( 'ABSPATH' ) ) 
	exit;

class Wontrapi_Extension_Demo {

	/**
	 * Current version.
	 *
	 * @var    string
	 * @since  0.1.0
	 */
	const VERSION = '0.1.0';

	/**
	 * URL of plugin directory.
	 *
	 * @var    string
	 * @since  0.1.0
	 */
	protected $url = '';

	/**
	 * Path of plugin directory.
	 *
	 * @var    string
	 * @since  0.1.0
	 */
	protected $path = '';

	/**
	 * Plugin basename.
	 *
	 * @var    string
	 * @since  0.1.0
	 */
	protected $basename = '';

	/**
	 * Detailed activation error messages.
	 *
	 * @var    array
	 * @since  0.1.0
	 */
	protected $activation_errors = array();

	/**
	 * FS Addon
	 * 
	 * @var    array
	 * @since  0.1.0 
	 */
	public static $fs;

	/**
	 * Singleton instance of plugin.
	 *
	 * @var    Wontrapi_Extension_Demo
	 * @since  0.1.0
	 */
	protected static $single_instance = null;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since   0.1.0
	 * @return  Wontrapi_Extension_Demo A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Constructor
	 *
	 * @since  0.1.0
	 * @param  object $plugin Main plugin object.
	 * @return void
	 */
	public function __construct() {
		$this->basename = plugin_basename( __FILE__ );
		$this->url 		= plugin_dir_url(  __FILE__ );
		$this->path 	= plugin_dir_path( __FILE__ );
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  0.1.0
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Init hooks
	 *
	 * @since  0.1.0
	 */
	public function init() {

		// Load translated strings for plugin.
		load_plugin_textdomain( 'wontrapi-extension-demo', false, dirname( $this->basename ) . '/languages/' );

		$this->include_dependencies();

		// Bail early if requirements aren't met.
		if ( ! $this->check_requirements() ) {
			return;
		}

		// Initialize plugin classes.
		// $this->plugin_classes();
	}

	/**
	 * Check if the plugin meets requirements and
	 * disable it if they are not present.
	 *
	 * @since  0.1.0
	 *
	 * @return boolean True if requirements met, false if not.
	 */
	public function check_requirements() {

		// Bail early if plugin meets requirements.
		if ( $this->meets_requirements() ) {
			return true;
		}

		// Add a dashboard notice.
		add_action( 'all_admin_notices', array( $this, 'requirements_not_met_notice' ) );

		// Deactivate our plugin.
		add_action( 'admin_init', array( $this, 'deactivate_me' ) );

		// Didn't meet the requirements.
		return false;
	}

	/**
	 * Check that all plugin requirements are met.
	 *
	 * @since  0.1.0
	 *
	 * @return boolean True if requirements are met.
	 */
	public function meets_requirements() {

		// Do checks for required classes / functions or similar.
		// Add detailed messages to $this->activation_errors array.
		if ( ! class_exists( 'Wontrapi') ) {
			return false;
		}

		return true;
	}

	public function include_dependencies() {
		if ( $this->is_parent_loaded() ) {
			// If parent already included, init add-on.
			$this->addon();
		} else if ( $this->is_parent_active() ) {
			// Init add-on only after the parent is loaded.
			add_action( 'wontrapi_fs_loaded', array( $this, 'addon' ) );
		} else {
			// Even though the parent is not activated, execute add-on for activation / uninstall hooks.
			$this->addon();
		}
	}

	/**
	 * Adds a notice to the dashboard if the plugin requirements are not met.
	 *
	 * @since  0.1.0
	 */
	public function requirements_not_met_notice() {

		// Compile default message.
		$default_message = sprintf( __( 'Wontrapi XD is missing requirements and has been <a href="%s">deactivated</a>. Please make sure all requirements are available.', 'wontrapi-extension-demo' ), admin_url( 'plugins.php' ) );

		// Default details to null.
		$details = null;

		// Add details if any exist.
		if ( $this->activation_errors && is_array( $this->activation_errors ) ) {
			$details = '<small>' . implode( '</small><br /><small>', $this->activation_errors ) . '</small>';
		}

		// Output errors.
		?>
		<div id="message" class="error">
			<p><?php echo wp_kses_post( $default_message ); ?></p>
			<?php echo wp_kses_post( $details ); ?>
		</div>
		<?php
	}

	public function is_parent_loaded() {
		if ( class_exists( 'Wontrapi' ) && ! empty( Wontrapi::$loaded ) ) {
			return true;
		}
		return false;
	}

	public function is_parent_active() {
		$active_plugins = get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$network_active_plugins = get_site_option( 'active_sitewide_plugins', array() );
			$active_plugins         = array_merge( $active_plugins, array_keys( $network_active_plugins ) );
		}

		foreach ( $active_plugins as $basename ) {
			if ( 0 === strpos( $basename, 'wontrapi/' ) ) {
				return true;
			}
		}

		return false;
	}

	public function addon() {
		$this->fs();
		// Signal that the add-on's SDK was initiated.
		do_action( 'wontrapi_xd_fs_loaded' );
	}

	public function fs() {
		if ( ! isset( self::$fs ) ) {
			// Include Freemius SDK.
			if ( file_exists( dirname( dirname( __FILE__ ) ) . '/wontrapi/vendor/freemius/start.php' ) ) {
				// Try to load SDK from parent plugin folder.
				require_once dirname( dirname( __FILE__ ) ) . '/wontrapi/vendor/freemius/start.php';

				self::$fs = fs_dynamic_init( array(
					'id'                  => '4748',
					'slug'                => 'wontrapi-extension-demo',
					'type'                => 'plugin',
					'public_key'          => 'pk_49ac0804e922f5071ca56690f44a1',
					'is_premium'          => true,
					'is_premium_only'     => true,
					'has_paid_plans'      => true,
					'is_org_compliant'    => false,
					'parent'              => array(
						'id'         => '1284',
						'slug'       => 'wontrapi',
						'public_key' => 'pk_f3f99e224cd062ba9d7fda46ab973',
						'name'       => 'Wontrapi',
					),
					'menu'                => array(
						'first-path'     => 'plugins.php',
						'support'        => false,
					),
				) );
			}
		}
		return self::$fs;
	}

	/**
	 * Deactivates this plugin, hook this function on admin_init.
	 *
	 * @since  0.1.0
	 */
	public function deactivate_me() {

		// We do a check for deactivate_plugins before calling it, to protect
		// any developers from accidentally calling it too early and breaking things.
		if ( function_exists( 'deactivate_plugins' ) ) {
			deactivate_plugins( $this->basename );
		}
	}

	/**
	 * Activate the plugin.
	 *
	 * @since  0.1.0
	 */
	public function _activate() {
		// Bail early if requirements aren't met.
		if ( ! $this->check_requirements() ) {
			return;
		}

		// Make sure any rewrite functionality has been loaded.
		flush_rewrite_rules();
	}

	/**
	 * Deactivate the plugin.
	 * Uninstall routines should be in uninstall.php.
	 *
	 * @since  0.1.0
	 */
	public function _deactivate() {
		// Add deactivation cleanup functionality here.
	}

}

/**
 * Grab the Wontrapi_Extension_Demo object and return it.
 * Wrapper for Wontrapi_Extension_Demo::get_instance().
 *
 * @since  0.1.0
 * @return Wontrapi_Extension_Demo  Singleton instance of plugin class.
 */
function wontrapi_xd() {
	return Wontrapi_Extension_Demo::get_instance();
}

// Kick it off.
add_action( 'plugins_loaded', array( wontrapi_xd(), 'hooks' ) );

// Activation and deactivation.
register_activation_hook( __FILE__, array( wontrapi_xd(), '_activate' ) );
register_deactivation_hook( __FILE__, array( wontrapi_xd(), '_deactivate' ) );
