<?php
/*
 * Plugin Name: BBP DoFollow
 * Version: 1.1
 * Plugin URI: https://wordpress.org/plugins/bbp-dofollow/
 * Description: This is a very simple but very helpful tool to help your bbpress makes internal links dofollow and external links nofollow.
 * Author: 4Games
 * Author URI: https://www.sockscap64.com/bbpress-dofollow/
 * Requires at least: 4.0
 * Tested up to: 5.0.2
 *
 * Text Domain: bbp-dofollow
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author 4Games
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Load plugin class files
require_once( 'includes/class-bbpress-dofollow.php' );

/**
 * Returns the main instance of BBPress_DoFollow to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object BBPress_DoFollow
 */
function BBPress_DoFollow () {
	$instance = BBPress_DoFollow::instance( __FILE__, '1.0.0' );

	return $instance;
}

BBPress_DoFollow();