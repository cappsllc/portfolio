<?php

/*
Copyright 2008 iThemes (email: support@ithemes.com)

Written by Chris Jean
Version 1.0.3

Version History
	1.0.1 - 2008-11-05
		Initial Release
	1.0.2 - 2008-11-17
		Updated to latest _setVars code
		Added PHP4 object compatibility
	1.0.3 - 2008-11-25
		Switched references to get_option( 'home' ) to get_option( 'siteurl' )
*/

if ( ! class_exists( 'iThemesContactPage' ) ) {
	class iThemesContactPage {
		var $_var = 'ithemes-contact-page';
		var $_name = 'iThemes Contact Page';
		var $_version = '1.0.3';
		var $_page = 'ithemes-contact-page';
		
		var $_class = '';
		var $_initialized = false;
		var $_options = array();
		
		var $_userID = 0;
		var $_usedInputs = array();
		var $_selectedVars = array();
		var $_pluginPath = '';
		var $_pluginRelativePath = '';
		var $_pluginURL = '';
		
		
		function iThemesContactPage() {
			add_action( 'admin_menu', array( &$this, 'addPages' ) );
			add_action( 'wp_print_scripts', array( &$this, 'addScripts' ) );
			
			$this->_setVars();
			$this->_load();
		}
		
		
		function addPages() {
			global $wp_theme_page_name;
			
			if ( ! empty( $wp_theme_page_name ) )
				add_submenu_page( $wp_theme_page_name, $this->_name, 'Contact Page', 'edit_themes', $this->_page, array( &$this, 'index' ) );
			else
				add_theme_page( $this->_name, 'Contact Page', 'edit_themes', $this->_page, array( &$this, 'index' ) );
		}
		
		function addScripts() {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( $this->_var . '-md5', $this->_pluginURL . '/js/md5.js' );
			wp_enqueue_script( $this->_var . '-contact-page-plugin', $this->_pluginURL . '/js/contact-page-plugin.js' );
		}
		
		function _setVars() {
			$this->_class = get_class( $this );
			
			$user = wp_get_current_user();
			$this->_userID = $user->ID;
			
			
			$this->_pluginPath = dirname( __FILE__ );
			$this->_pluginRelativePath = ltrim( str_replace( '\\', '/', str_replace( rtrim( ABSPATH, '\\\/' ), '', $this->_pluginPath ) ), '\\\/' );
			$this->_pluginURL = get_option( 'siteurl' ) . '/' . $this->_pluginRelativePath;
		}
		
		
		// Options Storage ////////////////////////////
		
		function _initializeOptions() {
			global $user_email;
			
			
			$this->_options = array();
			
			$this->_options['recipient'] = $user_email;
			$this->_options['subject'] = 'Contact Form Message from ' . get_option( 'blogname' );
			
			$this->_save();
		}
		
		function _save() {
			global $wp_theme_shortname;
			
			
			$data = $this->_options;
			
			if ( empty( $wp_theme_shortname ) ) {
				if ( $data == @get_option( $this->_var ) )
					return true;
				
				return @update_option( $this->_var, $data );
			}
			else {
				$theme_options = @get_option( $wp_theme_shortname . '-options' );
				$cur_data = $theme_options[$this->_var];
				
				if ( $data == $cur_data )
					return true;
				
				$theme_options[$this->_var] = $data;
				return @update_option( $wp_theme_shortname . '-options', $theme_options );
			}
		}
		
		function _load() {
			global $wp_theme_shortname;
			
			
			if ( empty( $wp_theme_shortname ) )
				$data = @get_option( $this->_var );
			else {
				$data = @get_option( $wp_theme_shortname . '-options' );
				$data = $data[$this->_var];
			}
			
			if ( is_array( $data ) )
				$this->_options = $data;
			else
				$this->_initializeOptions();
		}
		
		
		// Pages //////////////////////////////////////
		
		function render() {
			if ( ! empty( $_POST[$this->_var . '-send'] ) ) {
				$required_fields = array( 'name' => 'Name', 'email' => 'Email', 'message' => 'Message' );
				
				$errors = false;
				
				foreach ( (array) $required_fields as $field => $name ) {
					if ( empty( $_POST[$this->_var . '-' . $field] ) ) {
						echo "<div style=\"color:red;\">$name is a required field</div>";
						$errors = true;
					}
				}
				
				if ( false === $errors ) {
					$message = '';
					
					$val1 = md5( $_POST[$this->_var . '-name'] );
					$val2 = md5( $_POST[$this->_var . '-email'] );
					$val3 = md5( $_POST[$this->_var . '-website'] );
					$val4 = md5( $val1 . $val2 . $val3 );
					
					if ( $val4 == $_POST[$this->_var . '-' . $val1] ) {
						foreach ( (array) $_POST as $name => $value )
							if ( preg_match( '/^' . $this->_var . '-(.+)/', $name, $matches ) )
								if ( ( 'send' !== $matches[1] ) && ( $val1 !== $matches[1] ) )
									$message .= ucfirst( $matches[1] ) . ': ' . stripslashes( $value ) . "\n";
						
						if ( wp_mail( $this->_options['recipient'], $this->_options['subject'], $message ) )
							$this->_showStatusMessage( 'Message sent' );
						else
							echo "<div style=\"color:red;\">Message send failed</div>";
					}
					else
						echo "<div style=\"color:red;\">Sorry, you must have JavaScript enabled to use this form.</div>";
				}
			}
			
?>
	<form enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="<?php echo $this->_var; ?>" id="<?php echo $this->_var . '-form'; ?>">
		<table>
			<tr><td class="label">Name *</td><td><input type="text" name="<?php echo $this->_var; ?>-name" id="<?php echo $this->_var; ?>-name" size="30"/></td></tr>
			<tr><td class="label">Email *</td><td><input type="text" name="<?php echo $this->_var; ?>-email" id="<?php echo $this->_var; ?>-email" size="30"/></td></tr>
			<tr><td class="label">Website</td><td><input type="text" name="<?php echo $this->_var; ?>-website" id="<?php echo $this->_var; ?>-website" size="30"/></td></tr>
			<tr><td class="label">Message *</td><td><textarea name="<?php echo $this->_var; ?>-message" cols="50" rows="15"></textarea></td></tr>
			<tr><td></td><td><input type="submit" name="<?php echo $this->_var; ?>-send" value="Submit" id="<?php echo $this->_var; ?>-send" /></td></tr>
		</table>
	</form>
<?php
			
		}
		
		function index() {
			if ( ! empty( $_POST['save'] ) ) {
				foreach ( (array) explode( ',', $_POST['used-inputs'] ) as $name ) {
					$var_name = preg_replace( '/^' . $this->_var . '-/', '', $name );
					$this->_options[$var_name] = strip_tags( stripslashes( $_POST[$name] ) );
				}
				
				$this->_save();
				
				$this->_showStatusMessage( "Options Saved" );
			}
			
?>
	<div class="wrap">
		<h2><?php _e( 'Contact Page Options', $this->_var ); ?></h2>
		
		<form enctype="multipart/form-data" method="post" action="<?php echo $this->_getBackLink() ?>">
			<?php wp_nonce_field( $this->_var . '-nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row">Recipient Email Address</th>
					<td>
						<?php $this->_addTextBox( 'recipient', array( 'size' => '30' ) ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row">Subject</th>
					<td>
						<?php $this->_addTextBox( 'subject', array( 'size' => '50' ) ); ?>
					</td>
				</tr>
			</table>
			
			<p class="submit">
				<input class="button-primary" type="submit" name="save" value="Save Options" />
			</p>
			
			<?php $this->_addUsedInputs(); ?>
		</form>
	</div>
<?php
			
		}
		
		
		// Form Functions ///////////////////////////
		
		function _addTextBox( $var, $options = array(), $override_value = false ) {
			$options['type'] = 'text';
			
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addTextArea( $var, $options = array(), $override_value = false ) {
			$options['type'] = 'textarea';
			
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addFileUpload( $var, $options = array(), $override_value = false ) {
			$options['type'] = 'file';
			
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addCheckBox( $var, $options = array(), $override_value = false ) {
			$options['type'] = 'checkbox';
			
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addHidden( $var, $options = array(), $override_value = false ) {
			$options['type'] = 'hidden';
			
			$this->_addSimpleInput( $var, $options, $override_value );
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
			
			$defaults = array(
				'id'		=> $this->_var . '-' . $var,
				'name'		=> $this->_var . '-' . $var,
			);
			
			$options = wp_parse_args( $options, $defaults );
			
			if ( ( false === $override_value ) && ! empty( $this->_options[$var] ) ) {
				if ( 'checkbox' == $options['type'] )
					$options['checked'] = 'checked';
				else
					$options['value'] = $this->_options[$var];
			}
			
			
			$attributes = '';
			
			if ( false !== $options )
				foreach ( (array) $options as $name => $val )
					if ( ! is_array( $val ) && ( true !== $scrublist[$options['type']][$name] ) )
						$attributes .= "$name=\"" . htmlspecialchars( $val ) . "\" ";
			
			$this->_usedInputs[] = $options['name'];
			
?>
	<?php if ( 'textarea' == $options['type'] ) : ?>
		<textarea <?php echo $attributes; ?>><?php echo $options['value']; ?></textarea>
	<?php else : ?>
		<input <?php echo $attributes; ?>/>
	<?php endif; ?>
<?php
			
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
	}
}


if ( empty( $GLOBALS['ithemes_contact_page'] ) )
	$GLOBALS['ithemes_contact_page'] =& new iThemesContactPage();

?>
