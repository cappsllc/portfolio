<?php
	if ( defined( 'ABSPATH' ) )
	    require_once( ABSPATH . 'wp-load.php' );
	else {
		if ( file_exists( '../../../../../wp-load.php' ) )
			require_once('../../../../../wp-load.php');
		elseif ( file_exists( '../../../../wp-load.php' ) )
			require_once('../../../../wp-load.php');
		elseif ( file_exists( '../../../wp-load.php' ) )
			require_once('../../../wp-load.php');
		elseif ( file_exists( '../../wp-load.php' ) )
			require_once('../../wp-load.php');
		elseif ( file_exists( '../wp-load.php' ) )
			require_once('../wp-load.php');
		else
			die( 'Fatal Error: Could not locate wp-load.php' );
	}
	
	// Flash often fails to send cookies with the POST or upload, so we need to pass it in GET or POST instead
	if ( is_ssl() && empty($_COOKIE[SECURE_AUTH_COOKIE]) && !empty($_REQUEST['auth_cookie']) )
		$_COOKIE[SECURE_AUTH_COOKIE] = $_REQUEST['auth_cookie'];
	elseif ( empty($_COOKIE[AUTH_COOKIE]) && !empty($_REQUEST['auth_cookie']) )
		$_COOKIE[AUTH_COOKIE] = $_REQUEST['auth_cookie'];
	
	unset($current_user);
	
	require_once( ABSPATH . 'wp-admin/admin.php' );
	
	
	if ( !current_user_can('upload_files') ) {
		header('Content-Type: text/plain; charset=' . get_option('blog_charset'));
		wp_die(__('You do not have permission to upload files.'));
	}
	
	
	if ( ! function_exists( 'ithemes_file_upload_handler' ) ) {
		function ithemes_file_upload_handler() {
		
		if ( !empty( $_POST['upload'] ) ) {
			$elements = array();
			
			foreach ( (array) explode( ',', $_POST['update'] ) as $update ) {
				list ( $id, $file_field, $type, $field ) = explode( ':', $update );
				$elements[] = compact( 'id', 'file_field', 'type', 'field' );
			}
			
			global $ithemes_theme_url;
			
			require_once( '../iThemesFileUtility.php' );
			
			$file = iThemesFileUtility::uploadFile( 'imageFile' );
			
			if ( is_wp_error( $file ) ) {
				echo '<div id="media-upload-error">' . wp_specialchars( $file->get_error_message() ) . '</div>';
				exit;
			}
			
			$html = '<a href="' . $file['url'] . '" target="viewImage"><img src="' . $thumbnail['url'] . '" /></a>';
			
?>
	<script type="text/javascript">
		/* <![CDATA[ */
		var win = window.dialogArguments || opener || parent || top;
		
		<?php foreach ( (array) $elements as $element ) : ?>
			var var_<?php echo $element['id']; ?> = win.document.getElementById('<?php echo $element['id']; ?>');
			
			<?php if ( 'attribute' == $element['type'] ) : ?>
				jQuery(var_<?php echo $element['id']; ?>).attr('<?php echo $element['field']; ?>', '<?php echo $file[$element['file_field']]; ?>');
			<?php elseif ( 'css' == $element['type'] ) : ?>
				jQuery(var_<?php echo $element['id']; ?>).css('<?php echo $element['field']; ?>', 'url(<?php echo $file[$element['file_field']]; ?>)');
			<?php elseif ( 'html' == $element['type'] ) : ?>
				jQuery(var_<?php echo $element['id']; ?>).html('<?php echo $file[$element['file_field']]; ?>');
			<?php endif; ?>
		<?php endforeach; ?>
		
		win.tb_remove();
		/* ]]> */
	</script>
<?php
				
				exit;
			}
			
?>
	<div class="wrap">
		<table class="form-table">
			<tr>
				<td>
					<form enctype="multipart/form-data" method="post" action="#">
						<?php echo wp_nonce_field( 'upload-file-nonce' ); ?>
						Select a file: <input type="file" name="imageFile" /><br /><br />
						<input type="submit" name="upload" value="Upload" />
						<input type="hidden" name="update" value="<?php echo $_REQUEST['update']; ?>" />
					</form>
				</td>
			</tr>
		</table>
	</div>
<?php
			
		}
	}
	
	wp_iframe( 'ithemes_file_upload_handler' );
?>