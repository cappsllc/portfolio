<?php get_header(); ?>

<div id="content" class="inner clearfix">
<div id="inner">

    	<?php if (have_posts()) : while (have_posts()) : the_post(); // the loop ?>
	
	<!--post title as a link-->
	<h1 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h1>

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

	<?php else : // do not delete ?>

	<h3>Page Not Found</h3>
    <p>We're sorry, but the page you are looking for isn't here.</p>
    <p>Try searching for the page you are looking for or using the navigation in the header or sidebar</p>

	<?php endif; // do not delete ?>
	
</div>
<!--include sidebar-->
<?php include(TEMPLATEPATH."/r_sidebar.php"); ?>
</div>

<?php include(TEMPLATEPATH."/tabber.php"); ?>

<!--include footer-->
<?php get_footer(); ?>