<?php

/*
Copyright 2008 iThemes (email: support@ithemes.com)

Written by Chris Jean
Version 1.0.7

Version History
	1.0.1 - 2008-10-30
		Initial Release
	1.0.2 - 2008-11-04
		Created theme tab based upon $wp_theme_page_name global variable
	1.0.3 - 2008-11-07
		Standardized file structure into theme-options directory
	1.0.4 - 2008-11-17
		Updated to latest _setVars code
		Added PHP4 object compatibility
	1.0.5 - 2008-11-25
		Switched references to get_option( 'home' ) to get_option( 'siteurl' )
	1.0.6 - 2008-12-02
		Added patch to handle interim milestone 2.6.5
	1.0.7 - 2009-01-12
		Changed URL to use $wp_tutorial_var rather than $wp_theme_shortname
*/


if ( ! class_exists( 'iThemesTutorials' ) ) {
	class iThemesTutorials {
		var $_var = 'ithemes-tutorials';
		var $_name = 'iThemes Tutorials';
		var $_version = '1.0.7';
		var $_page = 'ithemes-tutorials';
		
		var $_pluginPath = '';
		var $_pluginRelativePage = '';
		var $_pluginURL = '';
		var $_pageRef = '';
		
		
		function iThemesTutorials() {
			global $wp_theme_page_name;
			
			if ( ! empty( $wp_theme_page_name ) )
				$this->_page = $wp_theme_page_name;
			
			
			$this->_setVars();
			
			add_action( 'admin_menu', array( &$this, 'addPages' ), -10 );
		}
		
		function addPages() {
			global $wp_theme_name, $wp_theme_page_name, $wp_version;
			
			if ( ! empty( $wp_theme_page_name ) ) {
				global $menu;
				
				if ( version_compare( $wp_version, '2.6.9', '>' ) ) {
					$last_item = array_pop( $menu );
					
					if ( 'wp-menu-separator-last' === $last_item[4] ) {
						$last_item[4] = 'wp-menu-separator';
						array_push( $menu, $last_item );
					}
					else
						$menu[] = array( '', 'edit_themes', '', '', 'wp-menu-separator' );
				}
				
				$this->_pageRef = add_menu_page( 'Start Here', $wp_theme_name, 'edit_themes', $this->_page, array( &$this, 'index' ) );
				add_submenu_page( $this->_page, 'Start Here', 'Start Here', 'edit_themes', $this->_page, array( &$this, 'index' ) );
				
				if ( version_compare( $wp_version, '2.6.9', '>' ) ) {
					$last_entry = end( $menu );
					
					
					$menu_entries[] = array_pop( $menu );
					$menu_entries[] = array_pop( $menu );
					
					if ( preg_match( '/wp-menu-separator-last/', $last_entry[4] ) ) {
						$menu_entries[] = array_pop( $menu );
						
						$menu[] = array_shift( $menu_entries );
					}
					
					if ( empty( $menu[27] ) && empty ( $menu[28] ) ) {
						$menu[27] = $menu_entries[1];
						$menu[28] = $menu_entries[0];
					}
					
					if ( file_exists( $GLOBALS['ithemes_theme_path'] . '/images/menu_icon.png' ) )
						$menu[28][6] = $GLOBALS['ithemes_theme_url'] . '/images/menu_icon.png';
					
					
					reset( $menu );
				}
			}
			else
				$this->_pageRef = add_theme_page( "$wp_theme_name Start Here", "$wp_theme_name Start Here", 'edit_themes', $this->_page, array( &$this, 'index' ) );
			
			
			add_action( 'admin_print_scripts-' . $this->_pageRef, array( $this, 'addScripts' ) );
			add_action( 'admin_print_styles-' . $this->_pageRef, array( $this, 'addStyles' ) );
		}
		
		function addScripts() {
			wp_enqueue_script( $this->_var . '-dw_viewport', $this->_pluginURL . '/js/dw_viewport.js' );
			wp_enqueue_script( $this->_var . '-tutorials', $this->_pluginURL . '/js/tutorials.js' );
		}
		
		function addStyles() {
			wp_enqueue_style( $this->_var . '-tutorials', $this->_pluginURL . '/css/tutorials.css' );
		}
		
		function _setVars() {
			$this->_pluginPath = dirname( __FILE__ );
			$this->_pluginRelativePath = ltrim( str_replace( '\\', '/', str_replace( rtrim( ABSPATH, '\\\/' ), '', $this->_pluginPath ) ), '\\\/' );
			$this->_pluginURL = get_option( 'siteurl' ) . '/' . $this->_pluginRelativePath;
		}
		
		
		// Pages //////////////////////////////////////
		
		function index() {
			global $wp_tutorial_var;
			
?>
	<div style="text-align:center;" id="tutorial_frame_container">
		<iframe name="tutorials" id="tutorial_frame" src="http://ithemes.com/wordpress-tutorials/<?php echo $wp_tutorial_var; ?>/tutorial.html?<?php echo mktime(); ?>" frameborder="0" height="100%"></iframe>
	</div>
<?php
			
		}
	}
}

global $ithemes_tutorials;
$ithemes_tutorials =& new iThemesTutorials();

?>