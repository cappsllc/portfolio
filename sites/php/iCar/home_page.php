<?php
/*
Template Name: Home Page Template
*/
?>
<?php get_header(); ?>

<div id="content">
<div id="home">

    <div id="feature" class="clearfix">
    
	<?php if (have_posts()) : while (have_posts()) : the_post(); // the loop ?>

    <img src="<?php echo get_post_meta($post->ID, "Side Photo", true); ?>" alt="<?php the_title(); ?>" />
	<!--post title as a link - uncomment to display the page title-->
	<!--<h3 id="post-<?php the_ID(); ?>"><?php the_title(); ?></h3>-->
    <?php the_content(); ?>
    
    <?php endwhile; endif; ?>
    
    </div>
 
   
    <div id="boxes" class="clearfix">
        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Homepage Bottom Left') ) : ?>
        <div id="boxleft">
        <h2>Who We Are</h2>
        <img src="<?php bloginfo('template_url'); ?>/images/featurebox1.jpg" alt="Feature 1" />
        <p><strong>Headline Goe here</strong><br />
        Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Mauris ullamcorper nibh a est. Donec et tortor. 
        Pellentesque fermentum imperdiet urna. Donec lectus. Curabitur commodo mauris vel nisi. <a href="#">Continue reading &raquo;</a></p>
        </div>
        <?php endif; ?>
        
        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Homepage Bottom Middle') ) : ?>
        <div id="boxmiddle">
        <h2>Solutions</h2>
        <img src="<?php bloginfo('template_url'); ?>/images/featurebox2.jpg" alt="Feature 2" />
        <p><strong>Headline Goe here</strong><br />
        Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Mauris ullamcorper nibh a est. Donec et tortor. 
        Pellentesque fermentum imperdiet urna. Donec lectus. Curabitur commodo mauris vel nisi. <a href="#">Continue reading &raquo;</a></p>
        </div>
        <?php endif; ?>
        
        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Homepage Bottom Right') ) : ?>
        <div id="boxright">
        <h2>Products</h2>
        <img src="<?php bloginfo('template_url'); ?>/images/featurebox3.jpg" alt="Feature 3" />
        <p><strong>Headline Goe here</strong><br />
        Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Mauris ullamcorper nibh a est. Donec et tortor. 
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