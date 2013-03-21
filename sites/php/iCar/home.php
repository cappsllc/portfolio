<?php get_header(); ?>

<div id="content">
<div id="home">

	<div id="feature-image" style="float:left;"></div>
	<div id="feature" class="clearfix">
		<h3><?php echo $wp_theme_options['homepage_title'] ?></h3>
		<?php echo stripslashes(nl2br(nls2p($wp_theme_options['homepage_copy']))); ?>
	</div>
    
    <div id="boxes" class="clearfix">
        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Homepage Bottom Left') ) : ?>
        <div id="boxleft">
        <h2>SUVs</h2>
        <img src="<?php bloginfo('template_url'); ?>/images/car1.jpg" alt="<?php bloginfo('name'); ?>" />
        <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Mauris ullamcorper nibh a est. Donec et tortor. 
        Pellentesque fermentum imperdiet urna. Donec lectus. Curabitur commodo mauris vel nisi. <a href="#">Continue reading &raquo;</a></p>
        </div>
        <?php endif; ?>
        
        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Homepage Bottom Middle') ) : ?>
        <div id="boxmiddle">
        <h2>Sports</h2>
        <img src="<?php bloginfo('template_url'); ?>/images/car2.jpg" alt="<?php bloginfo('name'); ?>" />
        <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Mauris ullamcorper nibh a est. Donec et tortor. 
        Pellentesque fermentum imperdiet urna. Donec lectus. Curabitur commodo mauris vel nisi. <a href="#">Continue reading &raquo;</a></p>
        </div>
        <?php endif; ?>
        
        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Homepage Bottom Right') ) : ?>
        <div id="boxright">
        <h2>Classics</h2>
        <img src="<?php bloginfo('template_url'); ?>/images/car3.jpg" alt="<?php bloginfo('name'); ?>" />
        <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Mauris ullamcorper nibh a est. Donec et tortor. 
        Pellentesque fermentum imperdiet urna. Donec lectus. Curabitur commodo mauris vel nisi. <a href="#">Continue reading &raquo;</a></p>
        </div>
        <?php endif; ?>
    </div>
    
</div>
</div>

<!--include sidebar-->
<?php include(TEMPLATEPATH."/tabber.php"); ?>

<!--include footer-->
<?php get_footer(); ?>

