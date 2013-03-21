<?php
//This file is only included if we are using a verison
//of WordPress prior to 2.7

//Legacy Comments (commented out for now)
//add_filter('comments_template','legacy_comments');
function legacy_comments($file) {
	if(!function_exists('wp_list_comments')) {
		$file = TEMPLATEPATH.'/legacy/comments.php';
	}
	return $file;
}
//post_class function
function post_class( $class = '', $post_id = null ) {
	// Separates classes with a single space, collates classes for post DIV
	echo 'class="' . join( ' ', get_post_class( $class, $post_id ) ) . '"';
}
//get_post_class function
function get_post_class( $class = '', $post_id = null ) {
	$post = get_post($post_id);
	$classes = array();
	$classes[] = $post->post_type;
	// sticky for Sticky Posts
	if ( is_sticky($post->ID) && is_home())
		$classes[] = 'sticky';
	// hentry for hAtom compliace
	$classes[] = 'hentry';
	// Categories
	foreach ( (array) get_the_category($post->ID) as $cat ) {
		if ( empty($cat->slug ) )
			continue;
		$classes[] = 'category-' . $cat->slug;
	}
	// Tags
	foreach ( (array) get_the_tags($post->ID) as $tag ) {
		if ( empty($tag->slug ) )
			continue;
		$classes[] = 'tag-' . $tag->slug;
	}
	if ( !empty($class) ) {
		if ( !is_array( $class ) )
			$class = preg_split('#\s+#', $class);
		$classes = array_merge($classes, $class);
	}
	return apply_filters('post_class', $classes, $class, $post_id);
}
//a function to get basic functionality for a wp_page_menu function
function wp_page_menu( $args = array() ) {
	$defaults = array('sort_column' => 'post_title', 'menu_class' => 'menu', 'echo' => true, 'link_before' => '', 'link_after' => '');
	$args = wp_parse_args( $args, $defaults );
	$args = apply_filters( 'wp_page_menu_args', $args );

	$menu = '';

	$list_args = $args;

	// Show Home in the menu
	if ( isset($args['show_home']) && ! empty($args['show_home']) ) {
		if ( true === $args['show_home'] || '1' === $args['show_home'] || 1 === $args['show_home'] )
			$text = __('Home');
		else
			$text = $args['show_home'];
		$class = '';
		if ( is_front_page() && !is_paged() )
			$class = 'class="current_page_item"';
		$menu .= '<li ' . $class . '><a href="' . get_option('home') . '">' . $link_before . $text . $link_after . '</a></li>';
		// If the front page is a page, add it to the exclude list
		if (get_option('show_on_front') == 'page') {
			if ( !empty( $list_args['exclude'] ) ) {
				$list_args['exclude'] .= ',';
			} else {
				$list_args['exclude'] = '';
			}
			$list_args['exclude'] .= get_option('page_on_front');
		}
	}

	$list_args['echo'] = false;
	$list_args['title_li'] = '';
	$menu .= str_replace( array( "\r", "\n", "\t" ), '', wp_list_pages($list_args) );

	if ( $menu )
		$menu = '<ul>' . $menu . '</ul>';

	$menu = '<div class="' . $args['menu_class'] . '">' . $menu . "</div>\n";
	$menu = apply_filters( 'wp_page_menu', $menu, $args );
	if ( $args['echo'] )
		echo $menu;
	else
		return $menu;
}
function is_sticky() {
	return FALSE;
}

//if prior to 2.5
if(!function_exists('is_front_page')) {
function is_front_page() { //make it equivalent to is_home()
	is_home();
}	
}
?>