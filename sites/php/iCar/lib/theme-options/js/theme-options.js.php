<?php
	if ( defined( 'ABSPATH' ) )
	    require_once( ABSPATH . 'wp-load.php' );
	else {
		if ( file_exists( '../../../../wp-load.php' ) )
			require_once('../../../../wp-load.php');
		elseif ( file_exists( '../../../../../../wp-load.php' ) )
			require_once('../../../../../../wp-load.php');
		else
			die( 'Fatal Error: Could not locate wp-load.php' );
	}
	
	
	$abspath = ABSPATH;
	
	if ( preg_match( '/^[a-zA-Z]:/', $abspath ) )
		$abspath = preg_replace( '|\/$|', '\\', $abspath );
	
	$url = get_option( 'siteurl' ) . '/' . str_replace( '\\', '/', str_replace( $abspath, '', dirname( __FILE__ ) ) );
	
?>
(function($) {
	function refreshBackgroundOptions() {
		if($("#background_option").attr("value") == 'default') {
			$("#background_color_options").slideUp();
			$("#background_image_options").slideUp();
			
			$("#background_preview").css("background-color", $("#default_option_background_color").attr("value"));
			$("#background_preview").css("background-image", "url(" + $("#default_option_background_image").attr("value") + ")");
			$("#background_preview").css("background-repeat", $("#default_option_background_repeat").attr("value"));
			$("#background_preview").css("background-position", $("#default_option_background_position").attr("value"));
		}
		else if($("#background_option").attr("value") == 'custom_image') {
			$("#background_color_options").slideDown();
			$("#background_image_options").slideDown();
			
			$("#background_preview").css("background-color", $("#background_color").attr("value"));
			$("#background_preview").css("background-image", "url(" + $("#background_image").attr("value") + ")");
			$("#background_preview").css("background-repeat", $("#background_repeat").attr("value"));
			$("#background_preview").css("background-position", $("#background_position").attr("value"));
		}
		else if($("#background_option").attr("value") == 'custom_color') {
			$("#background_color_options").slideDown();
			$("#background_image_options").slideUp();
			
			$("#background_preview").css("background-color", $("#background_color").attr("value"));
			$("#background_preview").css("background-image", "none");
			$("#background_preview").css("background-repeat", $("#background_repeat").attr("value"));
			$("#background_preview").css("background-position", $("#background_position").attr("value"));
		}
	}
	
	function backgroundPreviewToggle(show) {
		if(($("#background_preview").css("display") == 'none') || (show == "show")) {
			$("#background_preview").slideDown();
			$("#smaller_background_preview").show();
			$("#larger_background_preview").show();
			
			$("#show_hide_background_preview").html("Hide Background Preview");
		}
		else {
			$("#background_preview").slideUp();
			$("#smaller_background_preview").hide();
			$("#larger_background_preview").hide();
			
			$("#show_hide_background_preview").html("Show Background Preview");
		}
	}
	
	function colorPickerToggle(show) {
		if(show == "show") {
			$("#cp1_ColorPickerWrapper").fadeIn("fast");
			cp1.show();
			
			$("#show_background_color_picker").attr("value", "Hide Picker");
		}
		else if(show == "hide") {
			cp1.hide();
			$("#cp1_ColorPickerWrapper").fadeOut("fast");
			
			$("#show_background_color_picker").attr("value", "Show Picker");
		}
		else if(($("#cp1_ColorPickerWrapper").css("display") == 'none')) {
			$("#cp1_ColorPickerWrapper").fadeIn("fast");
			cp1.show();
			
			$("#show_background_color_picker").attr("value", "Hide Picker");
		}
		else {
			cp1.hide();
			$("#cp1_ColorPickerWrapper").fadeOut("fast");
			
			$("#show_background_color_picker").attr("value", "Show Picker");
		}
	}
	
	$(document).ready(
		function(){
			var position = $("#background_color").position();
			var height = $("#background_color").outerHeight();
			
			$("#cp1_ColorPickerWrapper").css("left", position.left).css("top", position.top + parseInt(height) + 5);
			
			cp1 = new Refresh.Web.ColorPicker('cp1', {startHex: $("#background_color").attr("value").substr(1), startMode: 's', clientFilesPath: '<?php echo $url; ?>/colorpicker/images/'});
			cp1.hide();
			
			$("#background_color_options").hide();
			$("#background_image_options").hide();
			
			refreshBackgroundOptions();
			
			$("#background_option").change(
				function(e) {
					colorPickerToggle("hide");
					refreshBackgroundOptions();
					backgroundPreviewToggle("show");
				}
			);
			
			$("#background_repeat").change(
				function(e) {
					$("#background_preview").css("background-repeat", $(this).attr("value"));
				}
			);
			
			$("#background_position").change(
				function(e) {
					$("#background_preview").css("background-position", $(this).attr("value"));
				}
			);
			
			
			$("#show_hide_background_preview").click(
				function(e) {
					backgroundPreviewToggle();
				}
			);
			
			
			$("#larger_background_preview").click(
				function(e) {
					$("#background_preview").animate({width: parseInt($("#background_preview").css("width")) + 30, height: parseInt($("#background_preview").css("height")) + 30}, "fast");
					
					$("#background_preview_width").attr("value", $("#background_preview").css("width"));
					$("#background_preview_height").attr("value", $("#background_preview").css("height"));
				}
			);
			
			
			$("#smaller_background_preview").click(
				function(e) {
					if((parseInt($("#background_preview").css("width")) >= 300) && (parseInt($("#background_preview").css("height")) >= 300)) {
						$("#background_preview").animate({width: parseInt($("#background_preview").css("width")) - 30, height: parseInt($("#background_preview").css("height")) - 30}, "fast");
						
						$("#background_preview_width").attr("value", $("#background_preview").css("width"));
						$("#background_preview_height").attr("value", $("#background_preview").css("height"));
					}
				}
			);
			
			
			$("#show_background_color_picker,#cp1_hide_div").click(
				function(e) {
					colorPickerToggle();
				}
			);
		}
	);
})(jQuery);
