<?php

/*
Copyright 2008 iThemes (email: support@ithemes.com)

Written by Chris Jean
Version 1.3.0

Version History
	1.0.1 - 2008-09-30
	1.0.2 - 2008-10-03
	1.0.3 - 2008-11-02
		Fixed file path to url conversion issues
	1.0.4 - 2008-11-03
		Added check for WP_Error on image resize
	1.0.5 - 2008-11-07
		Fixed bug in resizeImage function
	1.0.6 - 2008-11-08
		Fixed Windows compatibility bug in resizeImage function
	1.0.7 - 2008-11-08
		Sped up _getResizedImageFilePath code
	1.0.8 - 2008-11-10
		Cleaned up output
	1.1.0 - 2008-11-11
		Replaced WP resize calls with internal code to provide expandable resized images
	1.1.1 - 2008-11-13
		Compatibility fixes for both Windows and PHP4
		Added backwards compatibility calls for use in older themes
	1.1.2 - 2008-11-25
		Switched references to get_option( 'home' ) to get_option( 'siteurl' )
	1.2.0 - 2008-12-02
		Added get_url_from_file and get_file_attachment functions
		Allow 0 values in width and height for resize_image in order to resize on just one dimension
	1.3.0 - 2008-12-16
		Added ability to resize animated GIF files.
		Added delete_file_attachment and is_animated_gif functions.
*/


if ( !class_exists( 'iThemesFileUtility' ) ) {
	class iThemesFileUtility {
		// For backwards compatibility
		function uploadFile( $file_id ) {
			return iThemesFileUtility::upload_file( $file_id );
		}
		
		function upload_file( $file_id ) {
			$overrides = array( 'test_form' => false );
			$file = wp_handle_upload( $_FILES[$file_id], $overrides );
			
			if ( isset( $file['error'] ) )
				return new WP_Error( 'upload_error', $file['error'] );
			
			$url = $file['url'];
			$type = $file['type'];
			$file = $file['file'];
			$title = preg_replace( '/\.[^.]+$/', '', basename( $file ) );
			$content = '';
			
			if ( $image_meta = @wp_read_image_metadata( $file ) ) {
				if ( trim( $image_meta['title'] ) )
					$title = $image_meta['title'];
				if ( trim( $image_meta['caption'] ) )
					$content = $image_meta['caption'];
			}
			
			$attachment = array(
				'post_mime_type'	=> $type,
				'guid'				=> $url,
				'post_title'		=> $title,
				'post_content'		=> $content
			);
			
			$id = wp_insert_attachment( $attachment, $file );
			if ( !is_wp_error( $id ) )
				wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );
			
			
			$data = array(
				'id'		=> $id,
				'url'		=> $url,
				'type'		=> $type,
				'file'		=> $file,
				'title'		=> $title,
				'caption'	=> $content
			);
			
			return $data;
		}
		
		// For backwards compatibility. Note how the default crop changed from false to true in the new version
		function resizeImage( $file, $max_w, $max_h, $crop = false, $suffix = null, $dest_path = null, $jpeg_quality = 90 ) {
			return iThemesFileUtility::resize_image( $file, $max_w, $max_h, $crop, $suffix, $dest_path, $jpeg_quality );
		}
		
		function resize_image( $file, $max_w = 0, $max_h = 0, $crop = true, $suffix = null, $dest_path = null, $jpeg_quality = 90 ) {
			if ( is_numeric( $file ) ) {
				$file_info = iThemesFileUtility::get_file_attachment( $file );
				
				if ( false === $file_info )
					return new WP_Error( 'error_loading_image_attachment', "Could not find requested file attachment ($file)" );
				
				$file = $file_info['file'];
			}
			
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			
			$image = wp_load_image( $file );
			if ( ! is_resource( $image ) )
				return new WP_Error( 'error_loading_image', $image );
			
			list( $orig_w, $orig_h, $orig_type ) = getimagesize( $file );
			$dims = iThemesFileUtility::_image_resize_dimensions( $orig_w, $orig_h, $max_w, $max_h, $crop );
			if ( ! $dims )
				return new WP_Error( 'error_resizing_image', "Could not resize image" );
			list( $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h ) = $dims;
			
			if ( ( $src_w == $dst_w ) && ( $src_h == $dst_h ) )
				return array( 'file' => $file, 'url' => iThemesFileUtility::get_url_from_file( $file ), 'name' => basename( $file ) );
			
			if ( ! $suffix )
				$suffix = "resized-image-${dst_w}x${dst_h}";
			
			$info = pathinfo( $file );
			$dir = $info['dirname'];
			$ext = $info['extension'];
			$name = basename( $file, ".${ext}" );
			
			if ( ! is_null( $dest_path ) && $_dest_path = realpath( $dest_path ) )
				$dir = $_dest_path;
			$destfilename = "${dir}/${name}-${suffix}.${ext}";
			
			
			if ( file_exists( $destfilename ) ) {
				if ( filemtime( $file ) > filemtime( $destfilename ) )
					unlink( $destfilename );
				else
					return array( 'file' => $destfilename, 'url' => iThemesFileUtility::get_url_from_file( $destfilename ) );
			}
			
			
			// ImageMagick cannot resize animated PNG files yet, so this only works for
			// animated GIF files.
			$animated = false;
			if ( iThemesFileUtility::is_animated_gif( $file ) ) {
				$coalescefilename = "${dir}/${name}-coalesced-file.${ext}";
				
				if ( ! file_exists( $coalescefilename ) )
					system( "convert $file -coalesce $coalescefilename" );
				
				if ( file_exists( $coalescefilename ) ) {
					system( "convert -crop ${src_w}x${src_h}+${src_x}+${src_y}! $coalescefilename $destfilename" );
					
					if ( file_exists( $destfilename ) ) {
						system( "mogrify -resize ${dst_w}x${dst_h} $destfilename" );
						system( "convert -layers optimize $destfilename" );
						
						$animated = true;
					}
				}
			}
			
			
			if ( ! $animated ) {
				$newimage = imagecreatetruecolor( $dst_w, $dst_h );
				
				// preserve PNG transparency
				if ( IMAGETYPE_PNG == $orig_type && function_exists( 'imagealphablending' ) && function_exists( 'imagesavealpha' ) ) {
					imagealphablending( $newimage, false );
					imagesavealpha( $newimage, true );
				}
				
				imagecopyresampled( $newimage, $image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h );
				
				// we don't need the original in memory anymore
				if ( $orig_type == IMAGETYPE_GIF ) {
					if ( ! imagegif( $newimage, $destfilename ) )
						return new WP_Error( 'resize_path_invalid', __( 'Resize path invalid' ) );
				}
				elseif ( $orig_type == IMAGETYPE_PNG ) {
					if ( ! imagepng( $newimage, $destfilename ) )
						return new WP_Error( 'resize_path_invalid', __( 'Resize path invalid' ) );
				}
				else {
					// all other formats are converted to jpg
					$destfilename = "{$dir}/{$name}-{$suffix}.jpg";
					if ( ! imagejpeg( $newimage, $destfilename, apply_filters( 'jpeg_quality', $jpeg_quality ) ) )
						return new WP_Error( 'resize_path_invalid', __( 'Resize path invalid' ) );
				}
				
				imagedestroy( $newimage );
			}
			
			imagedestroy( $image );
			
			
			// Set correct file permissions
			$stat = stat( dirname( $destfilename ) );
			$perms = $stat['mode'] & 0000666; //same permissions as parent folder, strip off the executable bits
			@ chmod( $destfilename, $perms );
			
			
			return array( 'file' => $destfilename, 'url' => iThemesFileUtility::get_url_from_file( $destfilename ), 'name' => basename( $destfilename ) );
		}
		
		// Customized image_resize_dimensions() from 2.6.3 wp-admin/includes/media.php (cheanged to resize to fill on crop)
		function _image_resize_dimensions( $orig_w, $orig_h, $dest_w = 0, $dest_h = 0, $crop = false ) {
			if ( ( $orig_w <= 0 ) || ( $orig_h <= 0 ) )
				return new WP_Error ( 'error_resizing_image', "Supplied invalid original dimensions ($orig_w, $orig_h)" );
			if ( ( $dest_w < 0 ) || ( $dest_h < 0 ) )
				return new WP_Error ( 'error_resizing_image', "Supplied invalid destination dimentions ($dest_w, $dest_h)" );
			
			
			if ( ( $dest_w == 0 ) || ( $dest_h == 0 ) )
				$crop = false;
			
			
			$new_w = $dest_w;
			$new_h = $dest_h;
			
			$s_x = 0;
			$s_y = 0;
			
			$crop_w = $orig_w;
			$crop_h = $orig_h;
			
			
			if ( $crop ) {
				$cur_ratio = $orig_w / $orig_h;
				$new_ratio = $dest_w / $dest_h;
				
				if ( $cur_ratio > $new_ratio ) {
					$crop_w = floor( $orig_w / ( ( $dest_h / $orig_h ) / ( $dest_w / $orig_w ) ) );
					$s_x = floor( ( $orig_w - $crop_w ) / 2 );
				}
				elseif ( $new_ratio > $cur_ratio ) {
					$crop_h = floor( $orig_h / ( ( $dest_w / $orig_w ) / ( $dest_h / $orig_h ) ) );
					$s_y = floor( ( $orig_h - $crop_h ) / 2 );
				}
			}
			else
				list( $new_w, $new_h ) = wp_constrain_dimensions( $orig_w, $orig_h, $dest_w, $dest_h );
			
			
			return array( 0, 0, $s_x, $s_y, $new_w, $new_h, $crop_w, $crop_h );
		}
		
		function getURLFromFile( $file ) {
			return iThemesFileUtility::get_url_from_file( $file );
		}
		
		function get_url_from_file( $file ) {
			return get_option( 'siteurl' ) . str_replace( '\\', '/', str_replace( rtrim( ABSPATH, '\\\/' ), '', $file ) );
		}
		
		function getFileAttachment( $id ) {
			return iThemesFileUtility::get_file_attachment( $id );
		}
		
		function get_file_attachment( $id ) {
			if ( wp_attachment_is_image( $id ) ) {
				$post = get_post( $id );
				
				$file = array();
				$file['ID'] = $id;
				$file['file'] = get_attached_file( $id );
				$file['url'] = wp_get_attachment_url( $id );
				$file['title'] = $post->post_title;
				$file['name'] = basename( get_attached_file( $id ) );
				
				return $file;
			}
			
			return false;
		}
		
		function deleteFileAttachment( $id ) {
			return iThemesFileUtility::delete_file_attachment( $id );
		}
		
		function delete_file_attachment( $id ) {
			if ( wp_attachment_is_image( $id ) ) {
				$file = get_attached_file( $id );
				
				$info = pathinfo( $file );
				$ext = $info['extension'];
				$name = basename( $file, ".$ext" );
				
				
				if ( $dir = opendir( dirname( $file ) ) ) {
					while ( false !== ( $filename = readdir( $dir ) ) ) {
						if ( preg_match( "/^$name-resized-image-\d+x\d+\.$ext$/", $filename ) )
							unlink( dirname( $file ) . '/' . $filename );
						elseif ( "$name-coalesced-file.$ext" === $filename )
							unlink( dirname( $file ) . '/' . $filename );
					}
					
					closedir( $dir );
				}
				
				unlink( $file );
				
				
				return true;
			}
			
			return false;
		}
		
		// Can only detect animated GIF files, which is fine because ImageMagick doesn't seem
		// to be able to resize animated PNG (MNG) files yet.
		function is_animated_gif( $file ) {
			$filecontents=file_get_contents($file);
			
			$str_loc=0;
			$count=0;
			while ($count < 2) # There is no point in continuing after we find a 2nd frame
			{
				$where1=strpos($filecontents,"\x00\x21\xF9\x04",$str_loc);
				if ($where1 === FALSE)
				{
					break;
				}
				else
				{
					$str_loc=$where1+1;
					$where2=strpos($filecontents,"\x00\x2C",$str_loc);
					if ($where2 === FALSE)
					{
						break;
					}
					else
					{
						if ($where1+8 == $where2)
						{
							$count++;
						}
						$str_loc=$where2+1;
					}
				}
			}
			
			if ($count > 1)
				return(true);
			return(false);
		}
	}
}

?>