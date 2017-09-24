<?php
/**
 * Plugin Name: EchBay Phonering Alo
 * Description: Add Facebook messenger box to your website. Easily custom your style for chat box.
 * Plugin URI: https://www.facebook.com/webgiare.org/
 * Author: Dao Quoc Dai
 * Author URI: https://www.facebook.com/ech.bay/
 * Version: 1.0.0
 * Text Domain: echbayepa
 * Domain Path: /languages/
 * License: GPLv2 or later
 */

// Exit if accessed directly
if (! defined ( 'ABSPATH' )) {
	exit ();
}

define ( 'EPA_DF_VERSION', '1.0.0' );
// echo EPA_DF_VERSION . "\n";

// define( 'EPA_DF_MAIN_FILE', __FILE__ );
// echo EPA_DF_MAIN_FILE . "\n";

define ( 'EPA_DF_DIR', dirname ( __FILE__ ) . '/' );
// echo EPA_DF_DIR . "\n";

// define( 'EPA_DF_TEXT_DOMAIN', 'echbayepa' );
// echo EPA_DF_TEXT_DOMAIN . "\n";

//define ( 'EPA_DF_ROOT_DIR', basename ( EPA_DF_DIR ) );
// echo EPA_DF_ROOT_DIR . "\n";

//define ( 'EPA_DF_NONCE', EPA_DF_ROOT_DIR . EPA_DF_VERSION );
// echo EPA_DF_NONCE . "\n";

//define ( 'EPA_DF_URL', plugins_url () . '/' . EPA_DF_ROOT_DIR . '/' );
// echo EPA_DF_URL . "\n";

//define ( 'EPA_DF_PREFIX_OPTIONS', '___epa___' );
// echo EPA_DF_PREFIX_OPTIONS . "\n";

define ( 'EPA_THIS_PLUGIN_NAME', 'EchBay Phonering Alo' );
// echo EPA_THIS_PLUGIN_NAME . "\n";




// global echbay plugins menu name
// check if not exist -> add new
if ( ! defined ( 'EBP_GLOBAL_PLUGINS_SLUG_NAME' ) ) {
	define ( 'EBP_GLOBAL_PLUGINS_SLUG_NAME', 'echbay-plugins-menu' );
	define ( 'EBP_GLOBAL_PLUGINS_MENU_NAME', 'Webgiare Plugins' );
	
	define ( 'EPA_ADD_TO_SUB_MENU', false );
}
// exist -> add sub-menu
else {
	define ( 'EPA_ADD_TO_SUB_MENU', true );
}









/*
* class.php
*/
// check class exist
if (! class_exists ( 'EPA_Actions_Module' )) {
	
	// my class
	class EPA_Actions_Module {
		
		/*
		* config
		*/
		var $default_setting = array (
				// License -> donate or buy pro version
				'license' => '',
				
				// Hide Powered by ( 0 -> show, 1 -> hide. This is trial version -> default hide )
				'hide_powered' => 1,
				
				// Minimized Width
				'widget_width' => 45,
				
				// Set Width for mobile device
				'mobile_width' => 775,
				
				// Header Background
				'header_bg' => '#0084FF',
				
				// Position
				'widget_position' => 'bl',
				
				// Custom style
				'custom_style' => '/* Custom CSS */',
				
				// Phone number to call
				'phone_number' => ''
		);
		
		var $custom_setting = array ();
		
		var $eb_plugin_media_version = EPA_DF_VERSION;
		
		var $eb_plugin_prefix_option = '___epa___';
		
		var $eb_plugin_root_dir = '';
		
		var $eb_plugin_url = '';
		
		var $eb_plugin_nonce = '';
		
		var $eb_plugin_admin_dir = 'wp-admin';
		
		var $web_link = '';
		
		
		/*
		* begin
		*/
		function load() {
			
			/*
			* test in localhost
			*/
			/*
			if ( $_SERVER['HTTP_HOST'] == 'localhost:8888' ) {
				$this->eb_plugin_media_version = time();
			}
			*/
			
			
			/*
			* Check and set config value
			*/
			// root dir
			$this->eb_plugin_root_dir = basename ( EPA_DF_DIR );
			
			// Get version by time file modife
			$this->eb_plugin_media_version = filemtime( EPA_DF_DIR . 'style.css' );
			
			// URL to this plugin
//			$this->eb_plugin_url = plugins_url () . '/' . EPA_DF_ROOT_DIR . '/';
			$this->eb_plugin_url = plugins_url () . '/' . $this->eb_plugin_root_dir . '/';
			
			// nonce for echbay plugin
//			$this->eb_plugin_nonce = EPA_DF_ROOT_DIR . EPA_DF_VERSION;
			$this->eb_plugin_nonce = $this->eb_plugin_root_dir . EPA_DF_VERSION;
			
			//
			if ( defined ( 'WP_ADMIN_DIR' ) ) {
				$this->eb_plugin_admin_dir = WP_ADMIN_DIR;
			}
			
			
			/*
			* Load custom value
			*/
			$this->get_op ();
		}
		
		// get options
		function get_op() {
			global $wpdb;
			
			//
			$pref = $this->eb_plugin_prefix_option;
			
			$sql = $wpdb->get_results ( "SELECT option_name, option_value
			FROM
				`" . $wpdb->options . "`
			WHERE
				option_name LIKE '{$pref}%'
			ORDER BY
				option_id", OBJECT );
			
			foreach ( $sql as $v ) {
				$this->custom_setting [str_replace ( $this->eb_plugin_prefix_option, '', $v->option_name )] = $v->option_value;
			}
			// print_r( $this->custom_setting ); exit();
			
			
			/*
			* https://codex.wordpress.org/Validating_Sanitizing_and_Escaping_User_Data
			*/
			// set default value if not exist or NULL
			foreach ( $this->default_setting as $k => $v ) {
				if (! isset ( $this->custom_setting [$k] )
				|| $this->custom_setting [$k] == ''
//				|| $this->custom_setting [$k] == 0
				|| $this->custom_setting [$k] == '0') {
					$this->custom_setting [$k] = $v;
				}
			}
			
			// esc_ custom value
			foreach ( $this->custom_setting as $k => $v ) {
				if ( $k == 'custom_style' ) {
					$v = esc_textarea( $v );
				}
				else {
					$v = esc_html( $v );
				}
				$this->custom_setting [$k] = $v;
			}
			
//			print_r( $this->custom_setting ); exit();
		}
		
		// add checked or selected to input
		function ck($v1, $v2, $e = ' checked') {
			if ($v1 == $v2) {
				return $e;
			}
			return '';
		}
		
		function get_web_link () {
			if ( $this->web_link != '' ) {
				return $this->web_link;
			}
			
			//
			if ( defined('WP_SITEURL') ) {
				$this->web_link = WP_SITEURL;
			}
			else if ( defined('WP_HOME') ) {
				$this->web_link = WP_HOME;
			}
			else {
				$this->web_link = get_option ( 'siteurl' );
			}
			
			//
			$this->web_link = explode( '/', $this->web_link );
//			print_r( $this->web_link );
			
			$this->web_link[2] = $_SERVER['HTTP_HOST'];
//			print_r( $this->web_link );
			
			// ->
			$this->web_link = implode( '/', $this->web_link );
			
			//
			if ( substr( $this->web_link, -1 ) == '/' ) {
				$this->web_link = substr( $this->web_link, 0, -1 );
			}
//			echo $this->web_link; exit();
			
			//
			return $this->web_link;
		}
		
		// update custom setting
		function update() {
			if ($_SERVER ['REQUEST_METHOD'] == 'POST' && isset( $_POST['_ebnonce'] )) {
				
				// check nonce
				if( ! wp_verify_nonce( $_POST['_ebnonce'], $this->eb_plugin_nonce ) ) {
					wp_die('404 not found!');
				}

				
				// print_r( $_POST );
				
				//
				foreach ( $_POST as $k => $v ) {
					// only update field by epa
					if (substr ( $k, 0, 5 ) == '_epa_') {
						
						// add prefix key to option key
						$key = $this->eb_plugin_prefix_option . substr ( $k, 5 );
						// echo $k . "\n";
						
						//
						delete_option ( $key );
						
						// ensure it's an int() before update
						if ( $k == '_epa_widget_width'
						|| $k == '_epa_mobile_width' ) {
							$v = (int) $v;
						}
						// text value
						else {
							$v = stripslashes ( stripslashes ( stripslashes ( $v ) ) );
							
							// remove all HTML tag, HTML code is not support in this plugin
							$v = strip_tags( $v );
							
							//
							$v = sanitize_text_field( $v );
						}
						
						//
						add_option( $key, $v, '', 'no' );
//						add_option ( $key, $v );
					}
				}
				
				//
				die ( '<script type="text/javascript">
// window.location = window.location.href;
alert("Update done!");
</script>' );
				
				//
				// wp_redirect( '//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
				
				//
				// exit();
			} // end if POST
		}
		
		// form admin
		function admin() {
			$arr_position = array (
					"tr" => 'Top Right',
					"tl" => 'Top Left',
					"cr" => 'Center Right',
					"cl" => 'Center Left',
					"br" => 'Bottom Right',
					"bl" => 'Bottom Left' 
			);
			$str_position = '';
			foreach ( $arr_position as $k => $v ) {
				$str_position .= '<option value="' . $k . '"' . $this->ck ( $this->custom_setting ['widget_position'], $k, ' selected' ) . '>' . $v . '</option>';
			}
			
			// admin -> used real time version
			$this->eb_plugin_media_version = time();
			$this->get_web_link();
			
			//
			$main = file_get_contents ( EPA_DF_DIR . 'admin.html', 1 );
			
			$main = $this->template ( $main, $this->custom_setting + array (
				'_ebnonce' => wp_create_nonce( $this->eb_plugin_nonce ),
				
				'str_position' => $str_position,
				
				'epa_plugin_url' => $this->eb_plugin_url,
				'epa_plugin_version' => $this->eb_plugin_media_version,
			) );
			
			$main = $this->template ( $main, $this->default_setting, 'aaa' );
			
			echo $main;
			
			echo '<p>* Other <a href="' . $this->web_link . '/' . $this->eb_plugin_admin_dir . '/plugin-install.php?s=itvn9online&tab=search&type=author" target="_blank">WordPress Plugins</a> written by the same author. Thanks for choose us!</p>';
			
		}
		
		function deline ( $str, $reg = "/\r\n|\n\r|\n|\r|\t/i", $re = "" ) {
			// v2
			$a = explode( "\n", $str );
			$str = '';
			foreach ( $a as $v ) {
				$v = trim( $v );
				if ( $v != '' ) {
					if ( strstr( $v, '//' ) == true ) {
						$v .= "\n";
					}
					$str .= $v;
				}
			}
			return $str;
			
			// v1
			return preg_replace( $reg, $re, $str );
		}
		
		// get html for theme
		function guest() {
			
			// style auto create
			$epa_custom_css = str_replace( ';}', '}', $this->deline( trim ( '
.phonering-alo-phone.phonering-alo-green .phonering-alo-ph-img-circle{background-color: ' . $this->custom_setting ['header_bg'] . '}
.phonering-alo-phone.phonering-alo-green .phonering-alo-ph-img-circle a{color: ' . $this->custom_setting ['header_bg'] . '}

.phonering-alo-ph-img-circle {
	width: ' . $this->custom_setting ['widget_width'] . 'px;
	height: ' . $this->custom_setting ['widget_width'] . 'px;
}
.phonering-alo-ph-img-circle a {
	width: ' . $this->custom_setting ['widget_width'] . 'px;
	line-height: ' . $this->custom_setting ['widget_width'] . 'px;
}

.phonering-alo-ph-circle-fill {
	width: ' . ( $this->custom_setting ['widget_width'] + 40 ) . 'px;
	height: ' . ( $this->custom_setting ['widget_width'] + 40 ) . 'px;
}

.phonering-alo-ph-circle {
	width: ' . ( $this->custom_setting ['widget_width'] + 100 ) . 'px;
	height: ' . ( $this->custom_setting ['widget_width'] + 100 ) . 'px;
}

@media screen and (max-width:' . $this->custom_setting ['mobile_width'] . 'px) {
.echbay-alo-phone { display: block; }
}
			' ) ) ) .
			// style by custom
			trim ( $this->custom_setting ['custom_style'] );
			
			$main = file_get_contents ( EPA_DF_DIR . 'guest.html', 1 );
			
			
			// another value
			$main = $this->template ( $main, $this->custom_setting + array (
					'bloginfo_name' => get_bloginfo( 'name' ),
					'epa_custom_css' => '<style type="text/css">' . $epa_custom_css . '</style>',
					'epa_plugin_url' => $this->eb_plugin_url,
					'epa_plugin_version' => $this->eb_plugin_media_version,
			) );
			
			echo $main;
		}
		
		// add value to template file
		function template($temp, $val = array(), $tmp = 'tmp') {
			foreach ( $val as $k => $v ) {
				$temp = str_replace ( '{' . $tmp . '.' . $k . '}', $v, $temp );
			}
			
			return $temp;
		}
	} // end my class
} // end check class exist




/*
 * Show in admin
 */
function EPA_show_setting_form_in_admin() {
	global $EPA_func;
	
	$EPA_func->update ();
	
	$EPA_func->admin ();
}

function EPA_add_menu_setting_to_admin_menu() {
	// only show menu if administrator login
	if ( ! current_user_can('manage_options') )  {
		return false;
	}
	
	// menu name
	$a = EPA_THIS_PLUGIN_NAME;
	
	// add main menu
	if ( EPA_ADD_TO_SUB_MENU == false ) {
		add_menu_page( $a, EBP_GLOBAL_PLUGINS_MENU_NAME, 'manage_options', EBP_GLOBAL_PLUGINS_SLUG_NAME, 'EPA_show_setting_form_in_admin', NULL, 99 );
	}
	
	// add sub-menu
	add_submenu_page( EBP_GLOBAL_PLUGINS_SLUG_NAME, $a, trim( str_replace( 'EchBay', '', $a ) ), 'manage_options', strtolower( str_replace( ' ', '-', $a ) ), 'EPA_show_setting_form_in_admin' );
}



/*
 * Show in theme
 */
function EPA_show_facebook_messenger_box_in_site() {
	global $EPA_func;
	
	$EPA_func->guest ();
}




// Add settings link on plugin page
function EPA_plugin_settings_link ($links) { 
	$settings_link = '<a href="admin.php?page=' . strtolower( str_replace( ' ', '-', EPA_THIS_PLUGIN_NAME ) ) . '">Settings</a>'; 
	array_unshift($links, $settings_link); 
	return $links; 
}


// end class.php






//
$EPA_func = new EPA_Actions_Module ();

// load custom value in database
$EPA_func->load ();

// check and call function for admin
if (is_admin ()) {
	add_action ( 'admin_menu', 'EPA_add_menu_setting_to_admin_menu' );
	
	
	// Add menu setting to plugins page
	if ( strstr( $_SERVER['REQUEST_URI'], 'plugins.php' ) == true ) {
		$plugin = plugin_basename(__FILE__); 
		add_filter("plugin_action_links_$plugin", 'EPA_plugin_settings_link' );
	}
}
// or guest (public in theme)
else {
	add_action ( 'wp_footer', 'EPA_show_facebook_messenger_box_in_site' );
}




