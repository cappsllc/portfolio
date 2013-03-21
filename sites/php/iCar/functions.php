<?php
//Define the wp_content DIR for backward compatibility
if (!defined('WP_CONTENT_URL'))
	define('WP_CONTENT_URL', get_option('site_url').'/wp-content');
if (!defined('WP_CONTENT_DIR'))
	define('WP_CONTENT_DIR', ABSPATH.'/wp-content');
	
//Define the content width for images
$max_width = 596;
$GLOBALS['content_width'] = 596;

// Setting up our widgetized sidebars
if ( function_exists('register_sidebar') ) {
register_sidebar(array('name'=>'Homepage Bottom Left','before_widget' => '<div id="boxleft">','after_widget' => '</div>','before_title' => '<h2>','after_title' => '</h2>',));
register_sidebar(array('name'=>'Homepage Bottom Middle','before_widget' => '<div id="boxmiddle">','after_widget' => '</div>','before_title' => '<h2>','after_title' => '</h2>',));
register_sidebar(array('name'=>'Homepage Bottom Right','before_widget' => '<div id="boxright">','after_widget' => '</div>','before_title' => '<h2>','after_title' => '</h2>',));
register_sidebar(array('name'=>'Post Sidebar','before_widget' => '<li>','after_widget' => '</li>','before_title' => '<h2>','after_title' => '</h2>',));
register_sidebar(array('name'=>'Page Sidebar','before_widget' => '<li>','after_widget' => '</li>','before_title' => '<h2>','after_title' => '</h2>',));
register_sidebar(array('name'=>'Tabber Sidebar','before_widget' => '<li>','after_widget' => '</li>','before_title' => '<h2>','after_title' => '</h2>',));
}

//Call jTabber
add_action('wp_head','load_JS',12);
function load_JS() {
?>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jtabber.js"></script>
<script type="text/javascript">
jQuery(document).ready(function(){
		jQuery.jtabber({
			mainLinkTag: "#nav a", // much like a css selector, you must have a 'title' attribute that links to the div id name
			activeLinkClass: "selected", // class that is applied to the tab once it's clicked
			hiddenContentClass: "hiddencontent", // the class of the content you are hiding until the tab is clicked
			showDefaultTab: 1, // 1 will open the first tab, 2 will open the second etc.  null will open nothing by default
			showErrors: false, // true/false - if you want errors to be alerted to you
			effect: null, // null, 'slide' or 'fade' - do you want your content to fade in or slide in?
			effectSpeed: 'fast' // 'slow', 'medium' or 'fast' - the speed of the effect
		});
});
</script>
<?php
}

//Legacy Code (for backward compatibility)
if(!function_exists('wp_page_menu')) { //if pre-2.7
include(TEMPLATEPATH."/legacy/legacy.php"); }
    
//A function to include files throughout the theme
//It checks to see if the file exists first, so as to avoid error messages.
function get_template_file($filename) {
if (file_exists(TEMPLATEPATH."/$filename"))
include(TEMPLATEPATH."/$filename");
}

//Turns /n to <br /> and /n/n to </p><p>
function nls2p($str) {
	$str = str_replace('<p></p>', '', '<p>'
        . preg_replace('#([\r\n]\s*?[\r\n]){2,}#', '</p><p>', $str)
        . '</p>');
	return $str;
}

//A simple function to get data stored in a custom field
function get_custom_field($field) {
	global $post;
	$custom_field = get_post_meta($post->ID, $field, true);
	echo $custom_field;
}

//A function to get custom field image attachment...
//if there is none, do nothing.
function image_attachment($field) {
	global $post;
	$custom_field = get_post_meta($post->ID, $field, true);
	
	if($custom_field) { //if the user set a custom field
		echo '<img src="'.$custom_field.'" alt="'.get_the_title().'"/>';
	}
}

//wp_page_menu argument filter
add_filter('wp_page_menu_args','it_page_menu_args');
function it_page_menu_args($args) {
	global $wp_theme_options;
	$include = $wp_theme_options['include_pages'];
	$show_home = (in_array('home',(array)$include)) ? 1 : 0;
	$include_pages = implode(',',(array)$include);
	//define this theme's default arguments
	$custom_args = array('show_home' => $show_home, 'title_li' => '',
				'menu_class' => 'menu', 'echo' => true,
				'include' => $include_pages);
	//compare this theme's default arguments to arguments the user inputs
	//and give priority to the users arguments, if they conflict</p>i
	$our_args = wp_parse_args($args, $custom_args);
	return $our_args;
}
//wp_page_menu menu filter (to make class into id)
add_filter('wp_page_menu','it_page_menu');
function it_page_menu($menu) {
	$menu = str_replace('class','id',$menu);
	return $menu;
}

//Custom Header Image code
include(TEMPLATEPATH."/lib/custom-header.php");

//Theme Options code
include(TEMPLATEPATH."/lib/theme-options/theme-options.php");
$wp_theme_options = get_option('it-options');

//A function to add the custom body background image
add_action('wp_head','it_body_bg');
function it_body_bg() {
	global $wp_theme_options;
	
	$options = array( 'background_image', 'background_color', 'background_position', 'background_attachment', 'background_repeat' );
	
?>
	<style type="text/css">
		body {
			<?php if ( 'custom_color' == $wp_theme_options['background_option'] ) : ?>
				background-color: <?php echo $wp_theme_options['background_color']; ?>;
				background-image: none;
			<?php else : ?>
				<?php foreach ( (array) $options as $option ) : ?>
					<?php if ( ! empty( $wp_theme_options[$option] ) ) : ?>
						<?php if ( 'background_image' == $option ) : ?>
							<?php echo str_replace( '_', '-', $option ); ?>: url(<?php echo $wp_theme_options[$option]; ?>);
						<?php else : ?>
							<?php echo str_replace( '_', '-', $option ); ?>: <?php echo $wp_theme_options[$option]; ?>;
						<?php endif; ?>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>
		}
	</style>
<?php	
}

add_action( 'ithemes_load_plugins', 'ithemes_functions_after_init' );
function ithemes_functions_after_init() {
	//Include Tutorials Page
	include(TEMPLATEPATH."/lib/tutorials/tutorials.php");
	
	//Featured Image code
	include(TEMPLATEPATH."/lib/featured-images/featured-images.php");
	$GLOBALS['iThemesFeaturedImages'] =& new iThemesFeaturedImages( array( 'id_name' => 'feature-image', 'width' => '391', 'height' => '262', 'sleep' => 2, 'fade' => 1, 'modify_height' => true ) );
	
	//Contact Page Template code
	include(TEMPLATEPATH."/lib/contact-page-plugin/contact-page-plugin.php");
}

//A little SEO action
add_action('wp_meta','it_seo_options');
function it_seo_options() {
	//globalize variables
	global $post, $wp_theme_options;
	//build our excerpt
	$post_content = (strlen(strip_tags($post->post_content)) <= 300) ? strip_tags($post->post_content) : substr(strip_tags($post->post_content),0,300);
	$post_excerpt = ($post->post_excerpt) ? $post->post_excerpt : $post_content;
	//set the description
	$description = (is_home()) ? get_bloginfo('description') : $post_excerpt;
	//get the tags
	foreach((array)get_the_tags($post->ID) as $tag) { $post_tags .= ','. $tag->name; }
	$post_tags = substr($post_tags,1); //removing the first "," from the list
	
	//add the follow code to our meta section
	echo "\n".'<!--To follow, or not to follow-->'."\n";
	if(is_home() || is_single() || is_page()) echo '<meta name="robots" content="index,follow" />'."\n";
	elseif($wp_theme_options['cat_index'] != 'no' && is_category()) echo '<meta name="robots" content="index,follow" />'."\n";
	elseif(!is_home() && !is_single() && !is_page()) echo '<meta name="robots" content="noindex,follow" />'."\n";
	
	//add the description and keyword code to our meta section
	echo '<!--Add Description and Keywords-->'."\n";
	if($wp_theme_options['tag_as_keyword'] != 'no' && is_single() && $post_tags) echo '<meta name="keywords" content="'.$post_tags.'" />'."\n";
	if(is_home() || is_single() || is_page()) echo '<meta name="description" content="'.$description.'" />'."\n";
}

///Tracking/Analytics Code
function print_tracking() {
	global $wp_theme_options;
	echo stripslashes($wp_theme_options['tracking']);
}
if ($wp_theme_options['tracking_pos'] == "header")
	add_action('wp_head', 'print_tracking');
else
	add_action('it_footer', 'print_tracking');
?>