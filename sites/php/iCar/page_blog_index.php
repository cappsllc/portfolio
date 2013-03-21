<?php
/*
Template Name: Blog Index Template
*/
?>
<?php get_header(); ?>

<div id="content" class="inner clearfix">
<div id="inner">

    
  <h4>Blog</h4>

<?php
$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query('showposts=5'.'&paged='.$paged);
?>
<?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
<?php global $more; $more = 0; ?>

		
	<!--post title as a link-->
	<h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h3>

    <!--post meta info-->
	<div class="meta-top">
        <?php the_time('F jS, Y') ?> <!-- the timestamp -->&bull;
        By: <?php the_author_link(); ?> <!-- The author's name as a link to his archive -->&bull;
        <?php the_category(', ') ?> <!-- list of categories, seperated by commas, linked to corresponding category archives -->
    </div>

	<!--post text with the read more link-->
	<?php the_content('<div class="post-more">Read the rest of this entry &raquo;</div>'); ?>

	<!--post meta info-->
	<div class="meta-bottom">
        <span class="meta-more"><a href="<?php the_permalink(); ?>">Continue Reading</a></span><!-- the permalink -->
        <span class="meta-comments"><a href="<?php the_permalink(); ?>#comments"><?php comments_number('Comments(0)', 'Comments(1)', 'Comments(%)'); ?></a></span> <!-- comment number as link to post comments -->
        <span class="meta-category"><?php the_category(', ') ?></span> <!-- list of categories, seperated by commas, linked to corresponding category archives -->
    </div>
	
	<?php endwhile; // end of one post ?>

    <!-- Previous/Next page navigation -->
    <div class="page-nav">
	    <div class="nav-previous"><?php previous_posts_link('&laquo; Previous Page') ?></div>
	    <div class="nav-next"><?php next_posts_link('Next Page &raquo;') ?></div>
    </div>    

<?php $wp_query = null; $wp_query = $temp;?>
	
</div>
<!--include sidebar-->
<?php include(TEMPLATEPATH."/r_sidebar.php"); ?>
</div>

<?php include(TEMPLATEPATH."/tabber.php"); ?>

<!--include footer-->
<?php get_footer(); ?>