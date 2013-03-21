<?php global $wp_theme_options; ?>
<div id="jtabber">
	<div id="nav" class="clearfix"><!--The tab links-->
		<a href="#" title="rec"><span>New Inventory</span></a>
		<a href="#" title="notes"><span>Pre-Owned</span></a>
	</div>

	<div id="rec" class="hiddencontent">
	    <?php $my_query = "cat=" . $wp_theme_options['pop_cat'] . "&showposts=4"; $my_query = new WP_Query($my_query); ?>
	    <?php while ($my_query->have_posts()) : $my_query->the_post(); ?>
 	    <h5><a href="<?php the_permalink(); ?>" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h5>
        <p><?php the_time('F j, Y') ?><br />
        by: <?php the_author_link(); ?> &bull; <?php the_category(', ') ?></p>
	    <?php endwhile; ?>
	</div>

	<div id="pop" class="hiddencontent">

	    <?php $my_query = "showposts=4"; $my_query = new WP_Query($my_query); ?>
	    <?php while ($my_query->have_posts()) : $my_query->the_post(); ?>
 	    <h5><a href="<?php the_permalink(); ?>" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h5>
        <p><?php the_time('F j, Y') ?><br />
        by: <?php the_author_link(); ?> &bull; <?php the_category(', ') ?></p>
	    <?php endwhile; ?>
	</div><!--tab 1 end -->

	<div id="notes" class="hiddencontent">
	    <?php $my_query = "cat=" . $wp_theme_options['notes_cat'] . "&showposts=4"; $my_query = new WP_Query($my_query); ?>
	    <?php while ($my_query->have_posts()) : $my_query->the_post(); ?>
 	    <h5><a href="<?php the_permalink(); ?>" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h5>
        <p><?php the_time('F j, Y') ?><br />
        by: <?php the_author_link(); ?> &bull; <?php the_category(', ') ?></p>
	    <?php endwhile; ?>
	</div>
</div><!--#jtabber end-->
	
<div id="tabber_sidebar">
	<div id="iconlinks">
		<?php if ($wp_theme_options['appointment_link']) { ?><a id="schedule" href="<?php echo $wp_theme_options['appointment_link']; ?>">Schedule Service</a><?php } ?>
		<?php if ($wp_theme_options['openings_link']) { ?><a id="checklocal" href="<?php echo $wp_theme_options['openings_link']; ?>">Check Local Events</a><?php } ?>
		<?php if ($wp_theme_options['list_link']) { ?><a id="download" href="<?php echo $wp_theme_options['list_link']; ?>">Download Brochures</a><?php } ?>
		<a id="subscribe" href="<?php if($wp_theme_options['feedburner_url']) echo $wp_theme_options['feedburner_url']; else bloginfo('rss2_url'); ?>">Subscribe to Our Blog</a>
	</div>
<ul>
<?php if ( !function_exists('dynamic_sidebar')
        || !dynamic_sidebar('Tabber Sidebar') ) : ?>

    
<?php endif; ?>
</ul>
</div>