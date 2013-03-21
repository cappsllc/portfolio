<?php global $wp_theme_options; // at the top because we use the variables multiple times in this file ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">

<!--The Title-->
<title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' :: '; } ?><?php bloginfo('name'); ?></title>

<!--The Favicon-->
<link rel="shortcut icon" href="<?php bloginfo('template_url'); ?>/images/favicon.ico" type="image/x-icon" />

<!--The Meta Info-->
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<?php wp_meta(); //we need this for plugins ?>

<!--The Stylesheets-->
<style type="text/css" media="screen">
    @import url( <?php bloginfo('stylesheet_url'); ?> );
    @import url(<?php bloginfo('stylesheet_directory'); ?>/css/dropdown.css);
    @import url(<?php bloginfo('stylesheet_directory'); ?>/css/jtabber.css);
    @import url(<?php bloginfo('stylesheet_directory'); ?>/css/custom.css);
</style>
<!--The Internet Explorer Specific Stuff-->
<!--[if lte IE 7]>
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/css/lte-ie7.css" type="text/css" media="screen" />
<![endif]-->
<!--[if lt IE 7]>
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/css/lt-ie7.css" type="text/css" media="screen" />
    <script src="<?php bloginfo('template_url'); ?>/js/dropdown.js" type="text/javascript"></script>
<![endif]-->
<?php 
///////////////////////begin the insertion of the sidebar extend code
if(!is_front_page()) : ?>
<style type="text/css" media="screen">
#container { background: url(<?php bloginfo('stylesheet_directory'); ?>/images/container-bg-sidebar.gif) top center repeat-y; }
</style>
<?php endif; 
///////////////////////end the insertion of the sidebar extend code 
?>

<!--The RSS and Pingback-->
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php if($wp_theme_options['feedburner_url']) echo $wp_theme_options['feedburner_url']; else bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php wp_head(); //we need this for plugins ?>
</head>

<body>

<div id="container">

<div id="header" class="clearfix">
	<div id="title"><a href="<?php bloginfo('url'); ?>"><?php bloginfo('title'); ?></a></div>
    <div id="desc"><?php bloginfo('description'); ?></div>
    <div id="search">
    <form method="get" id="searchform" action="<?php bloginfo('home'); ?>/">
    <input type="text" value="search..." name="s" id="s" onfocus="if (this.value == 'search...') {this.value = '';}" onblur="if (this.value == '') {this.value = 'search...';}" />
    </form>
    </div>
</div>

<?php wp_page_menu(); //takes default arguments from functions.php?>