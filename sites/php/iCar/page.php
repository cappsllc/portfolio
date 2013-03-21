<?php get_header(); ?>

<div id="content" class="inner clearfix">
<div id="inner">
		
	<?php if (have_posts()) : while (have_posts()) : the_post(); // the loop ?>


	<!--post title-->
	<h1 id="post-<?php the_ID(); ?>"><?php the_title(); ?></h1>

	<!--post text with the read more link-->
	<?php the_content(); ?>
	
	<!--for paginate posts-->
	<?php link_pages('<p><strong>Pages:</strong> ', '</p>', 'number'); ?>
	
    <?php //comments_template(); // uncomment this if you want to include comments template ?>
	
	<?php endwhile; // end of one post ?>
	<?php else : // do not delete ?>
	
	<h3>Page Not Found</h3>
    <p>We're sorry, but the page you are looking for isn't here.</p>
    <p>Try searching for the page you are looking for or using the navigation in the header or sidebar</p>

    <?php endif; // do not delete ?>
	
</div>
<!--include sidebar-->
<?php include(TEMPLATEPATH."/r_sidebar_page.php"); ?>
</div>

<?php include(TEMPLATEPATH."/tabber.php"); ?>

<!--include footer-->
<?php get_footer(); ?>