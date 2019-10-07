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
 * Text Domain: wontrapi-xd
 * Domain Path: /languages
 *
 * @link    https://github.com/oakwoodgates/wontrapi-extension-demo
 *
 * @package Wontrapi_XD
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


class Wontrapi_XD {

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
	 * Singleton instance of plugin.
	 *
	 * @var    Wontrapi_XD
	 * @since  0.1.0
	 */
	protected static $single_instance = null;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since   0.1.0
	 * @return  Wontrapi_XD A single instance of this class.
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
	 * Init hooks
	 *
	 * @since  0.1.0
	 */
	public function init() {

		// Load translated strings for plugin.
		load_plugin_textdomain( 'wontrapi-xd', false, dirname( $this->basename ) . '/languages/' );

		$this->include_dependencies();

		// Bail early if requirements aren't met.
		if ( ! $this->check_requirements() ) {
			return;
		}

		// Initialize plugin classes.
		$this->plugin_classes();
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
		// Init Freemius.
		//wontrapi_fs();
		// Signal that SDK was initiated.
		//do_action( 'wontrapi_fs_loaded' );
	}

	/**
	 * Adds a notice to the dashboard if the plugin requirements are not met.
	 *
	 * @since  0.1.0
	 */
	public function requirements_not_met_notice() {

		// Compile default message.
		$default_message = sprintf( __( 'Wontrapi XD is missing requirements and has been <a href="%s">deactivated</a>. Please make sure all requirements are available.', 'wontrapi-xd' ), admin_url( 'plugins.php' ) );

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

	/**
	 * Initiate our hooks.
	 *
	 * @since  0.1.0
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ), 0 );
	}
}

/**
 * Grab the Wontrapi_XD object and return it.
 * Wrapper for Wontrapi_XD::get_instance().
 *
 * @since  0.1.0
 * @return Wontrapi_XD  Singleton instance of plugin class.
 */
function wontrapi_xd() {
	return Wontrapi_XD::get_instance();
}

// Kick it off.
add_action( 'plugins_loaded', array( wontrapi_xd(), 'hooks' ) );
