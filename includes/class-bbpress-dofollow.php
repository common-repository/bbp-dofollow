<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class BBPress_DoFollow {

	/**
	 * The single instance of BBPress_DoFollow.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Settings class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $file = '', $version = '1.0.0' ) {
		$this->_version = $version;
		$this->_token = 'bbpress_dofollow';

		// Load plugin environment variables
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		// Handle localisation
		add_action( 'init', array( $this, 'load_localisation' ), 0 );
		
		add_filter( 'bbp_get_reply_author_link',      	array( $this, 'bbpress_rel_dofollow'),999 );
		add_filter( 'bbp_get_topic_author_link',      	array( $this, 'bbpress_rel_dofollow'),999 );
		add_filter( 'bbp_get_user_profile_link',      	array( $this, 'bbpress_rel_dofollow'),999 );
		add_filter( 'bbp_get_user_profile_edit_link', 	array( $this, 'bbpress_rel_dofollow'),999 );
		add_filter( 'bbp_get_reply_content', 			array( $this, 'bbpress_rel_dofollow'),999 );
		add_filter( 'bbp_get_topic_content', 			array( $this, 'bbpress_rel_dofollow'),999 );
	} // End __construct ()

	/**
	 * Catches links so add internal links dofollow and external links nofollow
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return string with replaced link
	 */
	public function bbpress_rel_dofollow( $text = '' ) {
		return preg_replace_callback( '|<a (.+?)>|i', array( $this,'bbpress_rel_dofollow_callback'), $text );
	}

	/**
	 * Adds rel=nofollow to a link
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return string with replaced link
	 */
	public function bbpress_rel_dofollow_callback( $matches = array() ) {
		$text = $matches[1];
		$text = str_replace( array( ' rel="nofollow"', " rel='nofollow'",' rel="dofollow"', " rel='dofollow'",' rel="noopener"', " rel='noopener'" ), '', $text );
		if( preg_match( '|href="(.+?)"|i',$text, $href_matches) ){
			$href = $href_matches[1];
			
			// mailto, tel, sms, about all set to nofollow
			if( !$this->start_with( $href,'mailto:' )
				&& !$this->start_with( $href,'tel:' )
				&& !$this->start_with( $href,'sms:' )
				&& !$this->start_with( $href,'about:' ) )
			{
				// is absolate link: https://, http://, ftp://... 
				if( preg_match( '|^(?:[a-z]+:)?//|i',$href ) )
				{
					$href = parse_url( $href )['host'];
					$site_url = get_site_url();
					$site_url = parse_url( $site_url )['host'];
					
					if( strcasecmp ( $href,$site_url ) == 0 ){
						return "<a $text>";
					}
				}
				// is relative link, all relative links are set to dofollow
				else {
					return "<a $text>";
				}
			}
		}
		
		return "<a $text rel=\"nofollow noopener\">";
	}
	/** is a string start with '...'?
	*
	* @access  public
	* @since   1.0.0
	* @return true: yes, false: no
	*/
	public function start_with( $text, $handle){
		if( strncasecmp( $text, $handle, strlen( $handle ) ) == 0 )
			return true;
		
		return false;
	}
	
	/**
	 * Load plugin localisation
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'bbp-dofollow', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation ()

	/**
	 * Main BBPress_DoFollow Instance
	 *
	 * Ensures only one instance of BBPress_DoFollow is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see BBPress_DoFollow()
	 * @return Main BBPress_DoFollow instance
	 */
	public static function instance ( $file = '', $version = '1.0.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	} // End instance ()
}