<?php

/*
Copyright 2008 iThemes (email: support@ithemes.com)

Written by Chris Jean
Version 1.1.0

Version History
	1.0.1 - 2008-09-16
	1.0.2 - 2008-10-02
	1.0.3 - 2008-10-06
	1.0.4 - 2008-10-07
	1.0.5 - 2008-10-31
		Patched code to handle edge case conditions on some Windows servers
	1.0.6 - 2008-11-10
		Updated page-linking code to match the new Flexx system.
		This version will not work with older themes.
	1.0.7 - 2008-11-13
		Fixed default link value to point to the home option rather than siteurl option.
	1.0.8 - 2008-11-13
		Small bug fix that caused incorrect img alt attribute for preview images
	1.0.9 - 2008-11-17
		Updated _load function to pull from wp_theme_options rather than ithemes_theme_options
		in order to fix problems with select hosts.
	1.0.10 - 2008-11-17
		Updated to latest _setVars code
		Added PHP4 object compatibility
	1.0.11
		Switched references to get_option( 'home' ) to get_option( 'siteurl' )
	1.1.0
		Added new option to allow for disabling height modification
*/


if ( ! class_exists( 'iThemesFeaturedImages' ) ) {
	class iThemesFeaturedImages {
		var $_var = 'ithemes_featured_images';
		var $_name = 'Featured Images';
		var $_version = '1.1.0';
		var $_page = 'ithemes-featured-images';
		
		var $_defaults = array(
			'id_name'		=> 'featured-image-fade',
			'width'			=> '100',
			'height'		=> '100',
			'sleep'			=> '2',
			'fade'			=> '1',
			'image_ids'		=> array(),
			'fade_sort'		=> 'ordered',
			'enable_fade'	=> '1',
			'link'			=> '',
			'modify_height'	=> true
		);
		
		var $_width = '';
		var $_height = '';
		var $_sleep = '';
		var $_fade = '';
		
		var $_options = array();
		
		var $_class = '';
		var $_initialized = false;
		
		var $_userID = 0;
		var $_usedInputs = array();
		var $_selectedVars = array();
		var $_pluginPath = '';
		var $_pluginRelativePath = '';
		var $_pluginURL = '';
		var $_pageRef = '';
		
		
		function iThemesFeaturedImages( $options ) {
			$this->_defaults['link'] = get_option( 'home' );
			
			foreach ( (array) $options as $name => $val )
				$this->_defaults[$name] = $val;
			
			$this->_setVars();
			
			add_action( 'wp_head', array( &$this, 'initImages' ) );
			add_action( 'wp_print_scripts', array( &$this, 'addScripts' ) );
			add_action( 'ithemes_set_defaults', array( &$this, 'setDefaults' ) );
			add_action( 'ithemes_init', array( &$this, 'init' ) );
			add_action( 'admin_menu', array( &$this, 'addPages' ) );
		}
		
		function init() {
			$this->_load();
		}
		
		function addPages() {
			global $wp_theme_name, $wp_theme_page_name;
			
			if ( ! empty( $wp_theme_page_name ) )
				$this->_pageRef = add_submenu_page( $wp_theme_page_name, $this->_name, $this->_name, 'edit_themes', $this->_page, array( &$this, 'index' ) );
			else
				$this->_pageRef = add_theme_page( $wp_theme_name . ' ' . $this->_name, $wp_theme_name . ' ' . $this->_name, 'edit_themes', $this->_page, array( &$this, 'index' ) );
		}
		
		function addScripts() {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-cross-slide', $this->_pluginURL . '/js/jquery.cross-slide.js' );
		}
		
		function initImages() {
			$this->_fadeImages();
		}
		
		function _setVars() {
			$this->_class = get_class( $this );
			
			$user = wp_get_current_user();
			$this->_userID = $user->ID;
			
			
			$this->_pluginPath = dirname( __FILE__ );
			$this->_pluginRelativePath = ltrim( str_replace( '\\', '/', str_replace( rtrim( ABSPATH, '\\\/' ), '', $this->_pluginPath ) ), '\\\/' );
			$this->_pluginURL = get_option( 'siteurl' ) . '/' . $this->_pluginRelativePath;
			
			$this->_selfLink = array_shift( explode( '?', $_SERVER['REQUEST_URI'] ) ) . '?page=' . $this->_page;
		}
		
		
		// Options Storage ////////////////////////////
		
		function setDefaults() {
			global $ithemes_theme_options;
			$ithemes_theme_options->force_defaults[$this->_var] = $this->_defaults;
		}
		
		function _save() {
			do_action( 'ithemes_save', $this->_var, $this->_options );
			
			return true;
		}
		
		function _load() {
			global $ithemes_theme_options;
			
			
			$this->_options = $ithemes_theme_options->_options[$this->_var];
			
			$this->_options['sleep'] = floatval( $this->_options['sleep'] );
			$this->_options['fade'] = floatval( $this->_options['fade'] );
			
			if ( $this->_options['sleep'] <= 0 )
				$this->_options['sleep'] = $this->_defaults['sleep'];
			if ( $this->_options['fade'] <= 0 )
				$this->_options['fade'] = $this->_defaults['fade'];
			if ( empty( $this->_options['fade_sort'] ) )
				$this->_options['fade_sort'] = 'ordered';
			
			foreach ( array( 'width', 'height', 'sleep', 'fade' ) as $option )
				if ( ! is_numeric( $this->_defaults[$option] ) )
					$this->_options[$option] = $GLOBALS[$this->_defaults[$option]];
				elseif ( ( empty( $this->_options[$option] ) ) && ( '0' !== $this->_options[$option] ) )
					$this->_options[$option] = $this->_defaults[$option];
			
			$this->_options['id_name'] = $this->_defaults['id_name'];
			
			
			if ( empty( $this->_options['image_ids'] ) )
				$this->_initializeImages();
			
			
			$this->_save();
		}
		
		
		// Pages //////////////////////////////////////
		
		function index() {
			if ( function_exists( 'current_user_can' ) && ! current_user_can( 'edit_themes' ) )
				die( __( 'Cheatin uh?' ) );
			
			
			$action = $_REQUEST['action'];
			
			if ( 'save' === $action )
				$this->_saveForm();
			elseif ( 'upload' === $action )
				$this->_uploadImage();
			elseif ( 'delete' === $action )
				$this->_deleteImage();
			
			$this->_showForm();
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
			
			
			$errorCount = 0;
			
			if ( ( $this->_options['sleep'] != floatval( $this->_options['sleep'] ) ) || ( floatval( $this->_options['sleep'] ) <= 0 ) )
				$errorCount++;
			if ( ( $this->_options['fade'] != floatval( $this->_options['fade'] ) ) || ( floatval( $this->_options['fade'] ) <= 0 ) )
				$errorCount++;
			if ( true === $this->_defaults['modify_height'] )
				if ( ( $this->_options['height'] != intval( $this->_options['height'] ) ) || ( intval( $this->_options['height'] ) < 0 ) )
					$errorCount++;
			
			if ( $errorCount < 1 ) {
				$this->_options['sleep'] = floatval( $this->_options['sleep'] );
				$this->_options['fade'] = floatval( $this->_options['fade'] );
				
				if ( $this->_options['sleep'] <= 0 )
					$this->_options['sleep'] = $this->_defaults['sleep'];
				if ( $this->_options['fade'] <= 0 )
					$this->_options['fade'] = $this->_defaults['fade'];
				if ( empty( $this->_options['fade_sort'] ) )
					$this->_options['fade_sort'] = 'ordered';
				
				foreach ( array( 'width', 'height', 'sleep', 'fade' ) as $option )
					if ( ! is_numeric( $this->_defaults[$option] ) )
						$this->_options[$option] = $GLOBALS[$this->_defaults[$option]];
					elseif ( ( empty( $this->_options[$option] ) ) && ( '0' !== $this->_options[$option] ) )
						$this->_options[$option] = $this->_defaults[$option];
				
				$this->_options['id_name'] = $this->_defaults['id_name'];
				
				if ( $this->_save() )
					$this->_showStatusMessage( __( 'Settings updated', $this->_var ) );
				else
					$this->_showErrorMessage( __( 'Error while updating settings', $this->_var ) );
			}
			else {
				$this->_showErrorMessage( __( 'The fade options timing values must be numeric values greater than 0.', $this->_var ) );
				
				$this->_showErrorMessage( __ngettext( 'Please fix the input marked in red below.', 'Please fix the inputs marked in red below.', $errorCount ) );
			}
		}
		
		function _uploadImage() {
			if ( is_array( $_FILES['image_file'] ) && ( 0 === $_FILES['image_file']['error'] ) ) {
				require_once( $GLOBALS['ithemes_theme_path'] . '/lib/iThemesFileUtility.php' );
				
				check_admin_referer( $this->_var . '-nonce' );
				
				$file = iThemesFileUtility::uploadFile( 'image_file' );
				
				if ( is_wp_error( $file ) )
					$this->_showErrorMessage( 'Unable to save uploaded image. Ensure that the web server has permissions to write to the uploads folder' );
				else {
					$this->_options['image_ids'][] = $file['id'];
					$this->_save();
				}
			}
			else
				$this->_showErrorMessage( 'You must add a file by clicking the browse button first.' );
		}
		
		function _deleteImage() {
			wp_delete_attachment( $_GET['delete'] );
			
			$ids = array();
			
			foreach ( (array) $this->_options['image_ids'] as $id )
				if ( $id != $_GET['delete'] )
					$ids[] = $id;
			
			
			if ( empty( $ids ) )
				$this->_initializeImages();
			else {
				$this->_options['image_ids'] = $ids;
				$this->_save();
			}
		}
		
		function _showForm() {
			
?>
	<div class="wrap">
		<h2><?php _e( 'Images', $this->_var ); ?></h2>
		<br />
		<div>The uploaded image should be <?php echo $this->_options['width'] . 'x' . $this->_options['height']; ?> (<?php echo $this->_options['width']; ?>px wide by <?php echo $this->_options['height']; ?>px high).</div>
		<div>Images not matching the exact size will be resized and cropped to fit upon display.</div>
		
		<table class="form-table">
<?php
			
			$files = array();
			
			foreach ( (array) $this->_options['image_ids'] as $id ) {
				if ( wp_attachment_is_image( $id ) ) {
					$post = get_post( $id );
					
					$file = array();
					$file['ID'] = $id;
					$file['file'] = get_attached_file( $id );
					$file['url'] = wp_get_attachment_url( $id );
					$file['title'] = $post->post_title;
					$file['name'] = basename( get_attached_file( $id ) );
					
					$files[] = $file;
				}
			}
			
			usort( $files, array( &$this, _sortFiles ) );
			
			
			require_once( $GLOBALS['ithemes_theme_path'] . '/lib/iThemesFileUtility.php' );
			
?>
			<?php foreach ( (array) $files as $file ) : ?>
				<?php $thumb = iThemesFileUtility::resize_image( $file['file'], 100, 100, false ); ?>
				<tr>
					<th scope="row">
						<a href="<?php echo $file['url']; ?>" target="imagePreview">
							<?php if ( ! is_wp_error( $thumb ) ) : ?>
								<img src="<?php echo $thumb['url']; ?>" alt="<?php echo $file['name']; ?>" />
							<?php else : ?>
								Thumbnail generation error: <?php echo $thumb->get_error_message(); ?>
							<?php endif; ?>
						</a>
					</th>
					<td style="vertical-align:top;">
						<div><?php echo $file['name']; ?>&nbsp;&nbsp;&nbsp;<a href="<?php echo $this->_selfLink; ?>&amp;action=delete&amp;delete=<?php echo urlencode( $file['ID'] ); ?>" onclick="if(!confirm('Are you sure you want to delete <?php echo $file['name']; ?>?')) return false;">Delete</a></div>
					</td>
				</tr>
			<?php endforeach; ?>
			
			<tr>
				<th scope="row">Add a new image</th>
				<td>
					<form enctype="multipart/form-data" method="post" action="<?php echo $this->_selfLink; ?>">
						<?php echo wp_nonce_field( $this->_var . '-nonce' ); ?>
						<?php $this->_addHiddenNoSave( 'action', 'upload' ); ?>
						<div>Select a file: <?php $this->_addFileUpload( 'image_file' ); ?>&nbsp;&nbsp;&nbsp;<?php $this->_addSubmit( 'upload', 'Upload' ); ?></div>
					</form>
				</td>
			</tr>
		</table>
		<br /><br />
		
		<h2><?php _e( 'Settings', $this->_var ); ?></h2>
		
		<form enctype="multipart/form-data" method="post" action="<?php echo $this->_selfLink; ?>">
			<table class="form-table">
				<?php if ( true === $this->_defaults['modify_height'] ) : ?>
					<tr>
						<th scope="row">Header&nbsp;Image&nbsp;Height</th>
						<td>
							<table>
								<tr>
									<td style="margin:0px; border-bottom-width:0px; line-height:10px; padding:0px 10px 0px 0px;">
										Height in pixels:
									</td>
									<?php if ( ( ! empty( $_POST['save'] ) ) && ( intval( $_POST[$this->_var . '-height'] ) < 0 ) ) : ?>
										<td style="margin:0px; border-bottom-width:0px; line-height:10px; text-align:center; padding:3px; background-color:red;">
									<?php else: ?>
										<td style="margin:0px; border-bottom-width:0px; line-height:10px; text-align:center; padding:0px;">
									<?php endif; ?>
										<?php $this->_addTextBox( 'height', array( 'size' => '3', 'maxlength' => '5' ) ); ?>
									</td>
								</tr>
							</table>
						</td>
					<?php endif; ?>
				</tr>
				<tr>
					<th scope="row">Header&nbsp;Image&nbsp;Link</th>
					<td>
						<?php $this->_addTextBox( 'link', array( 'size' => '70' ) ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row">Fade Animation</th>
					<td>
						<div>The fade animation will show each of the images with a smooth fade transition between each image.</div>
						<div>If the animation is disabled, a single random image will be shown.</div>
						<br />
						
						<?php $this->_addCheckBox( 'enable_fade', '1' ); ?> Enable Fade
					</td>
				</tr>
				<tr>
					<th scope="row">Fade Options</th>
					<td>
						<div>The following options control the fade animation.</div>
						<div>If the animation is disabled, these options will not make any effect.</div>
						<br />
						
						<div>Choose an image sort order: <?php $this->_addDropDown( 'fade_sort', array( 'ordered' => 'Alphabetical by file name (default)', 'random' => 'Random' ) ); ?></div>
						<br />
						
						<table>
							<tr>
								<td style="margin:0px; border-bottom-width:0px; line-height:10px; padding:0px 10px 0px 0px;">
									Length of time to display each image in seconds
								</td>
								<?php if ( ( ! empty( $_POST['save'] ) ) && ( floatval( $_POST[$this->_var . '-sleep'] ) <= 0 ) ) : ?>
									<td style="margin:0px; border-bottom-width:0px; line-height:10px; text-align:center; padding:3px; background-color:red;">
								<?php else: ?>
									<td style="margin:0px; border-bottom-width:0px; line-height:10px; text-align:center; padding:0px;">
								<?php endif; ?>
									<?php $this->_addTextBox( 'sleep', array( 'size' => '3', 'maxlength' => '5' ) ); ?>
								</td>
							</tr>
							<tr>
								<td style="margin:0px; border-bottom-width:0px; line-height:10px; padding:0px 10px 0px 0px;">
									Length of time to fade each image in seconds
								</td>
								<?php if ( ( ! empty( $_POST['save'] ) ) && ( floatval( $_POST[$this->_var . '-fade'] ) <= 0 ) ) : ?>
									<td style="margin:0px; border-bottom-width:0px; line-height:10px; text-align:center; padding:3px; background-color:red;">
								<?php else: ?>
									<td style="margin:0px; border-bottom-width:0px; line-height:10px; text-align:center; padding:0px;">
								<?php endif; ?>
									<?php $this->_addTextBox( 'fade', array( 'size' => '3', 'maxlength' => '5' ) ); ?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<br />
			
			<p class="submit"><?php $this->_addSubmit( 'save', 'Save' ); ?></p>
			<?php $this->_addHiddenNoSave( 'action', 'save' ); ?>
			<?php $this->_addUsedInputs(); ?>
			<?php wp_nonce_field( $this->_var . '-nonce' ); ?>
		</form>
	</div>
<?php
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
			$options['name'] = $var;
			
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
		
		function _fadeImages() {
			require_once( $GLOBALS['ithemes_theme_path'] . '/lib/iThemesFileUtility.php' );
			
			
			$files = array();
			
			foreach ( (array) $this->_options['image_ids'] as $id ) {
				if ( wp_attachment_is_image( $id ) ) {
					$post = get_post( $id );
					
					$file = get_attached_file( $id );
					$data = iThemesFileUtility::resize_image( $file, $this->_options['width'], $this->_options['height'], true );
					
					if ( ! is_array( $data ) && is_wp_error( $data ) )
						echo "<!-- Resize Error: " . $data->get_error_message() . " -->";
					else
						$files[] = $data['url'];
				}
			}
			
			if ( 0 === count( $files ) ) {
				if ( $dir = @opendir( TEMPLATEPATH . '/images/random/' ) ) {
					while ( ( $file = readdir( $dir ) ) !== false )
						if ( is_file( TEMPLATEPATH . '/images/random/' . $file ) && ( preg_match( '/gif$|jpg$|jpeg$|png$/i', $file ) ) )
							$files[] = get_bloginfo( 'template_directory' ) . '/images/random/' . $file;
					
					closedir( $dir );
				}
			}
			
			if ( ( '1' == $this->_options['enable_fade'] ) && ( count( $files ) > 1 ) ) {
				if ( 'ordered' == $this->_options['fade_sort'] )
					usort( $files, array( &$this, _sortFiles ) );
				else
					shuffle( $files );
				
				$list = '';
				
				foreach ( (array) $files as $file ) {
					if ( ! empty( $list ) )
						$list .= ",\n";
					
					if ( ! empty( $this->_options['link'] ) )
						$list .= "{src: '$file', href: '" . $this->_options['link'] . "'}";
					else
						$list .= "{src: '$file'}";
				}
				
?>
	<style type="text/css">
		#<?php echo $this->_options['id_name']; ?> {
			width: <?php echo $this->_options['width']; ?>px;
			height: <?php echo $this->_options['height']; ?>px;
			text-align: left;
		}
	</style>
	
	<script type='text/javascript'>
		jQuery(function($) {$(document).ready(function() {
			$('#<?php echo $this->_options['id_name']; ?>').crossSlide(
				{sleep: <?php echo $this->_options['sleep']; ?>, fade: <?php echo $this->_options['fade']; ?>, debug: true},
				[
				<?php echo "$list\n"; ?>
				]
			);
		});});
	</script>
<?
				
			}
			else {
				shuffle( $files );
				
?>
	<style type="text/css">
		#<?php echo $this->_options['id_name']; ?> {
			width: <?php echo $this->_options['width']; ?>px;
			height: <?php echo $this->_options['height']; ?>px;
		}
	</style>
	
	<script type='text/javascript'>
		jQuery(function($) {$(document).ready(function() {
			$('#<?php echo $this->_options['id_name']; ?>').html('<a href="<?php echo $this->_options['link']; ?>"><img src="<?php echo $files[0]; ?>" /></a>');
		});});
	</script>
<?php
				
			}
		}
		
		function get_random_image() {
			require_once( $GLOBALS['ithemes_theme_path'] . '/lib/iThemesFileUtility.php' );
			
			
			$files = array();
			
			foreach ( (array) $this->_options['image_ids'] as $id ) {
				if ( wp_attachment_is_image( $id ) ) {
					$post = get_post( $id );
					
					$file = get_attached_file( $id );
					$data = iThemesFileUtility::resize_image( $file, $this->_options['width'], $this->_options['height'], true );
					
					if ( ! is_array( $data ) && is_wp_error( $data ) )
						echo "<!-- Resize Error: " . $data->get_error_message() . " -->";
					else
						$files[] = $data['url'];
				}
			}
			
			if ( 0 === count( $files ) ) {
				if ( $dir = @opendir( TEMPLATEPATH . '/images/random/' ) ) {
					while ( ( $file = readdir( $dir ) ) !== false )
						if ( is_file( TEMPLATEPATH . '/images/random/' . $file ) && ( preg_match( '/gif$|jpg$|jpeg$|png$/i', $file ) ) )
							$files[] = get_bloginfo( 'template_directory' ) . '/images/random/' . $file;
					
					closedir( $dir );
				}
			}
			
			shuffle( $files );
			
			return $files[0];
		}
		
		function _sortFiles( $a, $b ) {
			if ( is_array( $a ) ) {
				$a = basename( $a['name'] );
				$b = basename( $b['name'] );
			}
			else {
				$a = preg_replace( '/-\d+x\d+\./', '.', $a );
				$b = preg_replace( '/-\d+x\d+\./', '.', $b );
				
				$a = basename( $a );
				$b = basename( $b );
			}
			
			return strnatcasecmp( $a, $b );
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
		
		
		// Utility Functions //////////////////////////
		
		function _initializeImages() {
			if ( $dir = @opendir( TEMPLATEPATH . '/images/random/' ) ) {
				require_once( $GLOBALS['ithemes_theme_path'] . '/lib/iThemesFileUtility.php' );
				
				if ( ! ( ( $uploads = wp_upload_dir() ) && false === $uploads['error'] ) )
					return new WP_Error( 'upload_dir_failure', 'Unable to load images into the uploads directory: ' . $uploads['error'] );
				
				
				$ids;
				
				while ( ( $file = readdir( $dir ) ) !== false ) {
					if ( is_file( TEMPLATEPATH . '/images/random/' . $file ) && ( preg_match( '/gif$|jpg$|jpeg$|png$/i', $file ) ) ) {
						$filename = wp_unique_filename( $uploads['path'], basename( $file ) );
						
						// Move the file to the uploads dir
						$new_file = $uploads['path'] . "/$filename";
						if ( false === copy( TEMPLATEPATH . '/images/random/' . $file, $new_file ) ) {
							closedir( $dir );
							return new WP_Error( 'copy_file_failure', 'The theme images were unable to be loaded into the uploads directory' );
						}
						
						// Set correct file permissions
						$stat = stat( dirname( $new_file ));
						$perms = $stat['mode'] & 0000666;
						@chmod( $new_file, $perms );
						
						// Compute the URL
						$url = $uploads['url'] . "/$filename";
						
						
						$wp_filetype = wp_check_filetype( $file );
						$type = $wp_filetype['type'];
						
						
						$file_obj['url'] = $url;
						$file_obj['type'] = $type;
						$file_obj['file'] = $new_file;
						
						
						$title = preg_replace( '/\.[^.]+$/', '', basename( $file ) );
						$content = '';
						
						require_once( ABSPATH . 'wp-admin/includes/image.php' );
						
						// use image exif/iptc data for title and caption defaults if possible
						if ( $image_meta = @wp_read_image_metadata( $new_file ) ) {
							if ( trim( $image_meta['title'] ) )
								$title = $image_meta['title'];
							if ( trim( $image_meta['caption'] ) )
								$content = $image_meta['caption'];
						}
						
						// Construct the attachment array
						$attachment = array(
							'post_mime_type' => $type,
							'guid' => $url,
							'post_title' => $title,
							'post_content' => $content
						);
						
						// Save the data
						$id = wp_insert_attachment( $attachment, $new_file );
						if ( ! is_wp_error( $id ) ) {
							wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $new_file ) );
						}
						
						
						$ids[] = $id;
					}
				}
				
				closedir( $dir );
				
				
				$this->_options['image_ids'] = $ids;
				$this->_save();
			}
		}
	}
}

?>
