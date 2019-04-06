<?php
/**
 * Plugin Name: Meilleur Copain
 * Plugin URI: https://imathi.eu/tag/meilleur-copain/
 * Description: Une collection d’amélioration pour BuddyPress.
 * Version: 1.0.0
 * Requires at least: 5.0
 * Tested up to: 5.2
 * License: GNU/GPL 2
 * Author: imath
 * Author URI: https://imathi.eu/
 * Text Domain: meilleur-copain
 * Domain Path: /languages/
 * GitHub Plugin URI: https://github.com/imath/meilleur-copain/
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Meilleur_Copain' ) ) :
/**
 * Main plugin's class
 *
 * @package Meilleur Copain
 *
 * @since 1.0.0
 */
final class Meilleur_Copain {

	/**
	 * Plugin's main instance
	 *
	 * @var object
	 */
	protected static $instance;

	/**
	 * Initialize the plugin
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->globals();
		$this->inc();
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return object A single instance of this class.
	 */
	public static function start() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Setups plugin's globals
	 *
	 * @since 1.0.0
	 */
	private function globals() {
		// Version
		$this->version = '1.0.0';

		// Domain
		$this->domain = 'meilleur-copain';

		// Base name
		$this->file      = __FILE__;
		$this->basename  = plugin_basename( $this->file );

		// Path and URL
		$this->dir          = plugin_dir_path( $this->file );
		$this->url          = plugin_dir_url ( $this->file );
		$this->js_url       = trailingslashit( $this->url . 'dist' );
		$this->assets_url   = trailingslashit( $this->url . 'assets' );
		$this->inc_dir      = trailingslashit( $this->dir . 'inc' );
		$this->php_lang_dir = trailingslashit( $this->dir . 'languages' );
		$this->js_lang_dir  = trailingslashit( $this->php_lang_dir . 'js' );

        $this->post = null;
	}

	/**
	 * Includes plugin's needed files
	 *
	 * @since 1.0.0
	 */
	private function inc() {
		require $this->inc_dir . 'functions.php';
		require $this->inc_dir . 'customize.php';
	}
}

endif;

/**
 * Boot the plugin.
 *
 * @since 1.0.0
 */
function meilleur_copain() {
	return Meilleur_Copain::start();
}
add_action( 'bp_include', 'meilleur_copain', 5 );
