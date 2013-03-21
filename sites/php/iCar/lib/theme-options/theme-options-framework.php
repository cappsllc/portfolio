<?php

/*
Copyright 2008 iThemes (email: support@ithemes.com)

Written by Nathan Rice & Chris Jean
Version 1.0.5b

Version History
	1.0.1 - 2008-11-07
		Initial release
	1.0.2 - 2008-11-10
		New code milestone
	1.0.3 - 2008-11-17
		Updated to latest _setVars code
	1.0.4 - 2008-11-25
		Switched references to get_option( 'home' ) to get_option( 'siteurl' )
	1.0.5a - 2009-02-11
		Added _addCategoryDropDown function
	1.0.5b - 2009-02-12
		Fixed PHP 4 compatibility bug
*/


if ( ! class_exists( 'iThemesThemeOptionsFramework' ) ) {
	class iThemesThemeOptionsFramework {
		var $_var = 'theme-options';
		var $_name = 'Theme Settings';
		var $_version = '1.0.5b';
		var $_page = 'ithemes-theme-options';
		
		var $_class = '';
		var $_initialized = false;
		var $_options = array();
		var $defaults = array();
		var $force_defaults = array();
		var $_pageRef = '';
		
		var $_userID = 0;
		var $_usedInputs = array();
		var $_selectedVars = array();
		var $_pluginPath = '';
		var $_pluginRelativePath = '';
		var $_pluginURL = '';
		var $_themePath = '';
		var $_themeRelativePath = '';
		var $_themeURL = '';
		
		
		function iThemesThemeOptionsFramework() {
			global $wp_theme_name, $wp_theme_shortname, $wp_theme_page_name;
			
			if ( ! empty( $wp_theme_shortname ) )
				$this->_var = "$wp_theme_shortname-options";
			if ( ! empty( $wp_theme_name ) )
				$this->_name = "$wp_theme_name Theme Settings";
			
			$this->_setVars();
			
			add_action( 'init', array( &$this, '_init' ), 50 );
			add_action( 'ithemes_save', array( &$this, '_savePluginData' ), 10, 2 );
			
			if ( method_exists( $this, 'load' ) )
				$this->load();
		}
		
		
		function _init() {
			do_action( 'ithemes_load_plugins', $this );
			
			$this->_load();
			
			do_action( 'ithemes_init', $this );
			
			if ( method_exists( $this, 'init' ) )
				$this->init();
			
			add_action( 'admin_menu', array( &$this, '_addPages' ), -10 );
		}
		
		function _addPages() {
			global $wp_theme_name, $wp_theme_page_name;
			
			if ( ! empty( $wp_theme_page_name ) )
				$this->_pageRef = add_submenu_page( $wp_theme_page_name, 'Settings', 'Settings', 'edit_themes', $this->_page, array( &$this, '_index' ) );
			else
				$this->_pageRef = add_theme_page( "$wp_theme_name Settings", "$wp_theme_name Settings", 'edit_themes', $this->_page, array( &$this, '_index' ) );
			
			add_action( 'admin_print_scripts-' . $this->_pageRef, array( $this, '_addScripts' ) );
			add_action( 'admin_print_styles-' . $this->_pageRef, array( $this, '_addStyles' ) );
			
			if ( method_exists( $this, 'addPages' ) )
				$this->addPages();
		}
		
		function _addScripts() {
			if ( method_exists( $this, 'addScripts' ) )
				$this->addScripts();
		}
		
		function _addStyles() {
			if ( method_exists( $this, 'addStyles' ) )
				$this->addStyles();
		}
		
		function _setVars() {
			$this->_class = get_class( $this );
			
			$user = wp_get_current_user();
			$this->_userID = $user->ID;
			
			
			$this->_pluginPath = dirname( __FILE__ );
			$this->_pluginRelativePath = ltrim( str_replace( '\\', '/', str_replace( rtrim( ABSPATH, '\\\/' ), '', $this->_pluginPath ) ), '\\\/' );
			$this->_pluginURL = get_option( 'siteurl' ) . '/' . $this->_pluginRelativePath;
			
			$this->_themePath = TEMPLATEPATH;
			$this->_themeRelativePath = ltrim( str_replace( '\\', '/', str_replace( rtrim( ABSPATH, '\\\/' ), '', $this->_themePath ) ), '\\\/' );
			$this->_themeURL = get_template_directory_uri();
			
			global $ithemes_theme_path, $ithemes_theme_relative_path, $ithemes_theme_url;
			$ithemes_theme_path = $this->_themePath;
			$ithemes_theme_relative_path = $this->_themeRelativePath;
			$ithemes_theme_url = $this->_themeURL;
		}
		
		
		// Options Storage ////////////////////////////
		
		function _initializeOptions() {
			$this->_options = array();
			
//			$this->_options['placeholder'] = 1;
			
			$this->_save();
		}
		
		function updateDefaults( $defaults ) {
			$this->defaults = $defaults;
		}
		
		function updateForceDefaults( $force_defaults ) {
			$this->force_defaults = $force_defaults;
		}
		
		function _setDefaults() {
			if ( method_exists( $this, 'setDefaults' ) )
				$this->setDefaults();
			
			do_action( 'ithemes_set_defaults', $this );
			
			$this->defaults = $this->_merge_defaults( $this->defaults, $this->force_defaults );
			
			if ( ! is_array( $this->defaults ) )
				$this->defaults = array();
			
			$this->_options = $this->_merge_defaults( $this->_options, $this->defaults );
			$this->_options = $this->_merge_defaults( $this->_options, $this->force_defaults, true );
			
			$this->_save();
		}
		
		function _savePluginData( $name, $options = false ) {
			if ( false === $options )
				unset( $this->_options[$name] );
			else
				$this->_options[$name] = $options;
			
			$this->_save();
		}
		
		function _save() {
			if ( $this->_options == @get_option( $this->_var ) )
				return true;
			
			$GLOBALS['wp_theme_options'] = $this->_options;
			
			return @update_option( $this->_var, $this->_options );
		}
		
		function _load() {
			$data = @get_option( $this->_var );
			
			if ( is_array( $data ) )
				$this->_options = $data;
			else
				$this->_initializeOptions();
			
			$this->_setDefaults();
			
			if ( method_exists( $this, 'afterLoad' ) )
				$this->afterLoad();
			
			$GLOBALS['wp_theme_options'] = $this->_options;
		}
		
		
		// Pages //////////////////////////////////////
		
		function _index() {
			if ( ! empty( $_POST['action'] ) ) {
				if ( 'save' === $_POST['action'] )
					$this->_saveForm();
				elseif ( 'reset' === $_POST['action'] )
					$this->_resetData();
			}
			
			$this->_renderForm();
		}
		
		function _resetData() {
			delete_option( $this->_var );
			$this->_load();
			
			$this->_showStatusMessage( "Theme Options Reset" );
		}
		
		function _saveForm() {
			check_admin_referer( $this->_var . '-nonce' );
			
			foreach ( (array) explode( ',', $_POST['used-inputs'] ) as $name ) {
				$is_array = ( preg_match( '/\[\]$/', $name ) ) ? true : false;
				
				$name = str_replace( '[]', '', $name );
				$var_name = preg_replace( '/^' . $this->_var . '-/', '', $name );
				
				if ( $is_array && empty( $_POST[$name] ) )
					$_POST[$name] = array();
				
				if ( ! is_array( $_POST[$name] ) )
					$this->_options[$var_name] = stripslashes( $_POST[$name] );
				else
					$this->_options[$var_name] = $_POST[$name];
			}
			
			if ( method_exists( $this, 'beforeSaveForm' ) )
				$this->beforeSaveForm();
			
			$this->_save();
			
			$this->_showStatusMessage( "Theme Settings Saved" );
			
			if ( method_exists( $this, 'afterSaveForm' ) )
				$this->afterSaveForm();
		}
		
		function _renderForm() {
			if ( method_exists( $this, 'beforeRenderForm' ) )
				$this->beforeRenderForm();
			
?>
	<div style="display:none;">
		<!--<?php print_r( $this->_options ); ?>-->
	</div>
	
	<div class="wrap">
		<h2><?php echo $this->_name; ?></h2>
		<form enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="wp_theme_options" id="wp_theme_options_form'; ?>">
			<?php wp_nonce_field( $this->_var . '-nonce' ); ?>
			<table class="form-table">
				<tbody>
<?php
			
			if ( method_exists( $this, 'renderForm' ) )
				$this->renderForm();
			
?>
				</tbody>
			</table>
			
			<p class="submit">
				<?php $this->_addSubmit( 'form-save', 'Save Options &raquo;' ); ?>
				<?php $this->_addHidden( 'action', array( 'value' => 'save', 'name' => 'action' ), true ); ?>
			</p>
			<?php $this->_addUsedInputs(); ?>
		</form>
		<form enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="wp_theme_options_reset" id="wp_theme_options_reset_form'; ?>">
			<?php $this->_newForm(); ?>
			<?php wp_nonce_field( $this->_var . '-nonce' ); ?>
			<p class="submit">
				<?php $this->_addSubmit( 'reset', array( 'value' => 'Reset Options &raquo;', 'onclick' => "javascript:if(!confirm('Are you sure that you wish to reset all theme options to default values?')) return false;" ) ); ?>
				<?php $this->_addHidden( 'action', array( 'value' => 'reset', 'name' => 'action' ), true ); ?>
			</p>
		</form>
	</div>
<?php
			
			if ( method_exists( $this, 'afterRenderForm' ) )
				$this->afterRenderForm();
		}
		
		
		// Form Functions ///////////////////////////
		
		function _newForm() {
			$this->_usedInputs = array();
		}
		
		function _addSubmit( $var, $options = array(), $override_value = true ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'submit';
			$options['name'] = $var;
			$options['class'] = ( empty( $options['class'] ) ) ? 'button-primary' : $options['class'];
			
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addButton( $var, $options = array(), $override_value = true ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'button';
			$options['name'] = $var;
			
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addTextBox( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'text';
			
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addTextArea( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'textarea';
			
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addFileUpload( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'file';
			
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addCheckBox( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'checkbox';
			
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addMultiCheckBox( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'checkbox';
			$var = $var . '[]';
			
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addRadio( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'radio';
			
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addCategoryDropDown( $var, $options = array(), $override_value = false ) {
			$categories = array();
			$this->_getSortedHierarchicalCategories( $categories );
			
			foreach ( (array) $categories as $category ) {
				$pad = '';
				for ( $counter = 0; $counter < $category['depth']; $counter++ )
					$pad .= '--';
				if ( ! empty( $pad ) )
					$pad .= ' ';
				
				$options['value'][$category['id']] = $pad . $category['name'];
			}
			
			$this->_addDropDown( $var, $options, $override_value );
		}
		
		function _getSortedHierarchicalCategories( &$retval, $parent = 0, $depth = 0 ) {
			$categories = get_categories( "hide_empty=0&orderby=name&child_of=$parent" );
			
			if ( empty( $categories ) )
				return array();
			
			foreach ( (array) $categories as $category ) {
				if ( $category->parent != $parent )
					continue;
				
				$retval[] = array( 'name' => $category->name, 'id' => $category->cat_ID, 'depth' => $depth );
				
				$this->_getSortedHierarchicalCategories( $retval, $category->term_id, $depth + 1 );
			}
			
			return $retval;
		}
		
		function _addDropDown( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array();
			elseif ( ! is_array( $options['value'] ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'dropdown';
			
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addHidden( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'hidden';
			
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addHiddenNoSave( $var, $options = array(), $override_value = true ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['name'] = $var;
			
			$this->_addHidden( $var, $options, $override_value );
		}
		
		function _addDefaultHidden( $var ) {
			$options = array();
			$options['value'] = $this->defaults[$var];
			
			$var = "default_option_$var";
			
			$this->_addHiddenNoSave( $var, $options );
		}
		
		function _addUsedInputs() {
			$options['type'] = 'hidden';
			$options['value'] = implode( ',', $this->_usedInputs );
			$options['name'] = 'used-inputs';
			
			$this->_addSimpleInput( 'used-inputs', $options, true );
		}
		
		function _addSimpleInput( $var, $options = false, $override_value = false ) {
			if ( empty( $options['type'] ) ) {
				echo "<!-- _addSimpleInput called without a type option set. -->\n";
				return false;
			}
			
			
			$scrublist['textarea']['value'] = true;
			$scrublist['file']['value'] = true;
			$scrublist['dropdown']['value'] = true;
			
			$defaults = array();
			$defaults['name'] = $this->_var . '-' . $var;
			
			$var = str_replace( '[]', '', $var );
			
			if ( 'checkbox' === $options['type'] )
				$defaults['class'] = $var;
			else
				$defaults['id'] = $var;
			
			$options = $this->_merge_defaults( $options, $defaults );
			
			if ( ( false === $override_value ) && isset( $this->_options[$var] ) ) {
				if ( 'checkbox' === $options['type'] ) {
					if ( $this->_options[$var] == $options['value'] )
						$options['checked'] = 'checked';
				}
				elseif ( 'dropdown' !== $options['type'] )
					$options['value'] = $this->_options[$var];
			}
			
			if ( ( preg_match( '/^' . $this->_var . '/', $options['name'] ) ) && ( ! in_array( $options['name'], $this->_usedInputs ) ) )
				$this->_usedInputs[] = $options['name'];
			
			
			$attributes = '';
			
			if ( false !== $options )
				foreach ( (array) $options as $name => $val )
					if ( ! is_array( $val ) && ( true !== $scrublist[$options['type']][$name] ) )
						if ( ( 'submit' === $options['type'] ) || ( 'button' === $options['type'] ) )
							$attributes .= "$name=\"$val\" ";
						else
							$attributes .= "$name=\"" . htmlspecialchars( $val ) . '" ';
			
			if ( 'textarea' === $options['type'] )
				echo '<textarea ' . $attributes . '>' . $options['value'] . '</textarea>';
			elseif ( 'dropdown' === $options['type'] ) {
				echo "<select $attributes>\n";
				
				foreach ( (array) $options['value'] as $val => $name ) {
					$selected = ( $this->_options[$var] == $val ) ? ' selected="selected"' : '';
					echo "<option value=\"$val\"$selected>$name</option>\n";
				}
				
				echo "</select>\n";
			}
			else
				echo '<input ' . $attributes . '/>';
		}
		
		
		// Plugin Functions ///////////////////////////
		
		function _getBackLink() {
			return $_SERVER['REQUEST_URI'];
		}
		
		function _showStatusMessage( $message ) {
			
?>
	<div id="message" class="updated fade"><p><strong><?php echo $message; ?></strong></p></div>
<?php
			
		}
		
		function _showErrorMessage( $message ) {
			
?>
	<div id="message" class="error"><p><strong><?php echo $message; ?></strong></p></div>
<?php
			
		}
		
		function _merge_defaults( $values, $defaults, $force = false ) {
			if ( ! $this->_is_associative_array( $defaults ) ) {
				if ( ! isset( $values ) )
					return $defaults;
				
				if ( false === $force )
					return $values;
				
				if ( isset( $values ) || is_array( $values ) )
					return $values;
				return $defaults;
			}
			
			foreach ( (array) $defaults as $key => $val )
				$values[$key] = $this->_merge_defaults($values[$key], $val, $force );
			
			return $values;
		}
		
		function _is_associative_array( &$array ) {
			if ( ! is_array( $array ) || empty( $array ) )
				return false;
			
			$next = 0;
			
			foreach ( $array as $k => $v )
				if ( $k !== $next++ )
					return true;
			
			return false;
		}
	}
}

?>