<?php
/*
Copyright 2008 iThemes (email: support@ithemes.com)

Written by Nathan Rice & Chris Jean

Version History
	1.0.1 - 2008-11-07
		Initial Release
	1.0.2 - 2008-11-17
		Added PHP4 object compatibility
	1.0.3 - 2008-11-24
		Fixed background uploader link in order to prevent XSS problems caused
		when the WordPress URL and the Blog URL addresses use different domains.
	1.0.4 - 2009-01-12
		Added wp_theme_var to allow for seperation from wp_theme_shortname.
*/

$GLOBALS['wp_theme_name']		= "iCar";
$GLOBALS['wp_theme_shortname']	= "it";
$GLOBALS['wp_theme_page_name']	= 'theme-options';
$GLOBALS['wp_tutorial_var'] = 'ic';

require_once( 'theme-options-framework.php' );

if ( ! class_exists( 'iThemesThemeOptions' ) && class_exists( 'iThemesThemeOptionsFramework' ) ) {
	class iThemesThemeOptions extends iThemesThemeOptionsFramework {
		function afterLoad() {
			if ( 'default' == $this->_options['background_option'] )
				foreach ( array( 'background_color', 'background_repeat', 'background_image', 'background_attachment', 'background_position' ) as $option )
					$this->_options[$option] = $this->force_defaults[$option];
		}
		
		function setDefaults() {
			$this->force_defaults['include_pages'] = array( 'home' );
			$this->force_defaults['tracking_pos'] = 'footer';
			$this->force_defaults['tag_as_keyword'] = 'yes';
			$this->force_defaults['cat_index'] = 'no';
		}
		
		function addScripts() {
			global $wp_scripts;
			
			
			$queue = array();
			
			foreach ( (array) $wp_scripts->queue as $item )
				if ( ! in_array( $item, array( 'page', 'editor', 'editor_functions', 'tiny_mce', 'media-upload', 'post' ) ) )
					$queue[] = $item;
			
			$wp_scripts->queue = $queue;
			
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'thickbox' );
			
			wp_enqueue_script( $this->_var . '-prototype', $this->_pluginURL . '/js/prototype.js' );
			wp_enqueue_script( $this->_var . '-color-methods', $this->_pluginURL . '/js/colorpicker/ColorMethods.js' );
			wp_enqueue_script( $this->_var . '-color-value-picker', $this->_pluginURL . '/js/colorpicker/ColorValuePicker.js' );
			wp_enqueue_script( $this->_var . '-slider', $this->_pluginURL . '/js/colorpicker/Slider.js' );
			wp_enqueue_script( $this->_var . '-color-picker', $this->_pluginURL . '/js/colorpicker/ColorPicker.js' );
			
			wp_enqueue_script( $this->_var . '-theme-options', $this->_pluginURL . '/js/theme-options.js.php' );
		}
		
		function addStyles() {
			wp_enqueue_style( 'thickbox' );
			
			wp_enqueue_style( $this->_var . '-theme-options', $this->_pluginURL . '/css/theme-options.css' );
		}
		
		function renderForm() {
			
?>

	<tr><th scope="row">Feedburner URL</th>
		<td>If you use a site like <a href="http://feedburner.com/">Feedburner</a> to handle your feeds, please enter your feed URL here (including http://):<br />
			<?php $this->_addTextBox('feedburner_url'); ?> (leave blank for none)<br />
		</td>
	</tr>
	
	<tr><th scope="row">Menu Builder</th>
		<td><div>Please select the pages you would like to <strong>INCLUDE</strong> in the Header Menus.</div>
			<table>
				<tr><th style="border:none; padding:0px;"><strong>Pages:</strong></th></tr>
				<tr><td style="border-bottom:none; vertical-align:top; padding:0px;"><?php $this->createMenuBuilderCheckboxes( 'include_pages', 'pages' ); ?></td>
				</tr>
			</table>
		</td>
	</tr>

	<tr><th scope="row">Homepage Title</th>
		<td>Type the title that you would like to use in the feature section beside the feature image:<br />
			<?php $this->_addTextBox('homepage_title'); ?><br />
		</td>
	</tr>
	
	<tr><th scope="row">Homepage Copy</th>
		<td>Type the text that you would like to use in the feature section beside the feature image:<br />
			<?php $this->_addTextArea( 'homepage_copy', array( 'rows' => '6', 'cols' => '50' ) ); ?><br />
		</td>
	</tr>
	
	<tr><th scope="row">Tabber Categories</th>
		<td>Please select the category you would like to use to populate the "New Inventory" tab:<br />
			<?php $this->_addCategoryDropDown( 'pop_cat' ); ?>
			<br /><br />
			Please select the category you would like to use to populate the "Pre-Owned Inventory" tab:<br />
			<?php $this->_addCategoryDropDown( 'notes_cat' ); ?>
		</td>
	</tr>
	
	<tr><th scope="row">Icon Links</th>
		<td><strong>Please paste the URLs for the pages that will be linked to under the tabbed section</strong>:<br /><br />
			"Schedule Service Appointment" link URL:<br />
			<?php $this->_addTextBox('appointment_link'); ?>(leave blank for none)<br /><br />
			"Check Our Inventory" URL:<br />
			<?php $this->_addTextBox('openings_link'); ?>(leave blank for none)<br /><br />
			"Download Brochures" URL:<br />
			<?php $this->_addTextBox('list_link'); ?>(leave blank for none)<br /><br />
		</td>
	</tr>
	
	<tr><th scope="row">Tracking Code</th>
		<td>If you use a tracking service like <a href="http://google.com/analytics">Google Analytics</a>, paste the tracking code in the box below:<br />
			(leave blank for none)<br />
			<?php $this->_addTextArea( 'tracking', array( 'rows' => '3', 'cols' => '50' ) ); ?><br />
			Does your tracking service go in the header or footer of the code?<br />
			<?php $this->_addDropDown( 'tracking_pos', array( 'footer' => 'Footer (default)', 'header' => 'Header' ) ); ?>
		</td>
	</tr>
	
	<tr><th scope="row">Search Engine Optimization</th>
		<td>
			Would You like to use post tags as <a href="http://en.wikipedia.org/wiki/Meta_element#The_keywords_attribute" target="_blank">META keywords</a> on single posts? (recommended)<br />
			<?php $this->_addDropDown( 'tag_as_keyword', array( 'yes' => 'Yes (default)', 'no' => 'No' ) ); ?><br />
			<strong>NOTE:</strong> By default, this theme uses either <a href="http://codex.wordpress.org/Template_Tags/the_excerpt_rss" target="_blank">the excerpt</a> on single posts or pages,<br />
			or the blog <a href="<?php bloginfo('wpurl'); ?>/wp-admin/options-general.php" target="_blank">tagline</a> on all other pages (home, archives, etc.) as the <a href="http://en.wikipedia.org/wiki/Meta_element#The_description_attribute" target="_blank">META description</a>.<br /><br />
			
			Would you like your category archives to be indexed by search engines? (<strong>not</strong> recommended)<br />
			<?php $this->_addDropDown( 'cat_index', array( 'no' => 'No (default)', 'yes' => 'Yes' ) ); ?><br />
			<strong>NOTE:</strong> No date based archives or search results will be indexed by Search Engines.
		</td>
	</tr>
<?php
		
	}
	
	function afterRenderForm() {
		
?>
	<div id="cp1_ColorPickerWrapper" style="padding:10px; border:1px solid black; position:absolute; z-index:10; background-color:white; display:none;">
		<table><tr>
			<td style="vertical-align:top;"><div id="cp1_ColorMap"></div></td>
			<td style="vertical-align:top;"><div id="cp1_ColorBar"></div></td>
			<td style="vertical-align:top;">
				<table>
					<tr><td colspan="3"><div id="cp1_Preview" style="background-color: #fff; width: 60px; height: 60px; padding: 0; margin: 0; border: solid 1px #000;"><br /></div></td></tr>
					<tr><td><input type="radio" id="cp1_HueRadio" name="cp1_Mode" value="0" /></td><td><label for="cp1_HueRadio">H:</label></td><td><input type="text" id="cp1_Hue" value="0" style="width: 40px;" /> &deg;</td></tr>
					<tr><td><input type="radio" id="cp1_SaturationRadio" name="cp1_Mode" value="1" /></td><td><label for="cp1_SaturationRadio">S:</label></td><td><input type="text" id="cp1_Saturation" value="100" style="width: 40px;" /> %</td></tr>
					<tr><td><input type="radio" id="cp1_BrightnessRadio" name="cp1_Mode" value="2" /></td><td><label for="cp1_BrightnessRadio">B:</label></td><td><input type="text" id="cp1_Brightness" value="100" style="width: 40px;" /> %</td></tr>
					<tr><td colspan="3" height="5"></td></tr>
					<tr><td><input type="radio" id="cp1_RedRadio" name="cp1_Mode" value="r" /></td><td><label for="cp1_RedRadio">R:</label></td><td><input type="text" id="cp1_Red" value="255" style="width: 40px;" /></td></tr>
					<tr><td><input type="radio" id="cp1_GreenRadio" name="cp1_Mode" value="g" /></td><td><label for="cp1_GreenRadio">G:</label></td><td><input type="text" id="cp1_Green" value="0" style="width: 40px;" /></td></tr>
					<tr><td><input type="radio" id="cp1_BlueRadio" name="cp1_Mode" value="b" /></td><td><label for="cp1_BlueRadio">B:</label></td><td><input type="text" id="cp1_Blue" value="0" style="width: 40px;" /></td></tr>
					<tr><td>#:</td><td colspan="2"><input type="text" id="cp1_Hex" value="FF0000" style="width: 60px;" /></td></tr>
				</table>
			</td>
		</tr></table>
		
		<a href="javascript:void(0);" style="float:right;" id="cp1_hide_div">save selection</a>
	</div>
	
	<div style="display:none;">
		<?php
			$images = array( 'rangearrows.gif', 'mappoint.gif', 'bar-saturation.png', 'bar-brightness.png', 'bar-blue-tl.png', 'bar-blue-tr.png', 'bar-blue-bl.png', 'bar-blue-br.png', 'bar-red-tl.png',
				'bar-red-tr.png', 'bar-red-bl.png', 'bar-red-br.png', 'bar-green-tl.png', 'bar-green-tr.png', 'bar-green-bl.png', 'bar-green-br.png', 'map-red-max.png', 'map-red-min.png',
				'map-green-max.png', 'map-green-min.png', 'map-blue-max.png', 'map-blue-min.png', 'map-saturation.png', 'map-saturation-overlay.png', 'map-brightness.png', 'map-hue.png' );
			
			foreach( (array) $images as $image )
				echo '<img src="' . $ithemes_theme_url . '/js/refresh_web/colorpicker/images/' . $image . "\" />\n";
		?>
	</div>
<?php
			
		}
		
		function createMenuBuilderCheckboxes( $var, $type ) {
			if ( empty( $this->_options[$var] ) )
				$this->_options[$var] = array();
			
			$options = array();
			
			if ( 'pages' == $type ) {
				$options['home'] = array( 'title' => "Home", 'depth' => 0 );
				$source_options = get_pages();
			}
			elseif ( 'categories' == $type )
				$source_options = get_categories('hide_empty=0&hierarchical=1');
			
			
			foreach ( (array) $source_options as $option ) {
				if ( 'pages' == $type ) {
					$parent = $option->post_parent;
					$title = $option->post_title;
					$id = $option->ID;
				}
				elseif ( 'categories' == $type ) {
					$parent = $option->category_parent;
					$title = $option->name;
					$id = $option->cat_ID;
				}
				
				if ( 0 == $parent )
					$options[$id] = array( 'title' => $title, 'depth' => 0 );
				else
					$options[$id] = array( 'title' => $title, 'depth' => ( $options[$parent]['depth'] + 1 ) );
			}
			
			foreach ( (array) $options as $id => $data ) {
				$attributes = array();
				$attributes['value'] = $id;
				
				if ( in_array( $id, $this->_options[$var] ) )
					$attributes['checked'] = 'checked';
				?>
					<div style="position:relative; left:<?php echo ( $data['depth'] * 15 ); ?>px;"><?php $this->_addMultiCheckBox( $var, $attributes ); ?> <?php echo $data['title']; ?></div>
				<?php
			}
		}
	}
}


if ( empty( $ithemes_theme_options ) )
	$GLOBALS['ithemes_theme_options'] =& new iThemesThemeOptions();

?>