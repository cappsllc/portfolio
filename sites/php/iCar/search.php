<?php get_header(); ?>

<div id="content" class="inner clearfix">
<div id="inner">

    
    <?php if (have_posts()) : // the loop ?>

    <h4>Search Results</h4>

    <?php while (have_posts()) : the_post(); // the loop ?>
    
    <!--post title as a link-->
	<h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h3>

    <!--optional excerpt or automatic excerpt of the post-->
	<?php the_excerpt(); //post excerpt ?>

	<!--post meta info-->
	<div class="meta-bottom">
           </div>

    <?php endwhile; //end one post ?>
    
    <!-- Previous/Next page navigation -->
    <div class="page-nav">
	    <div class="nav-previous"><?php previous_posts_link('&laquo; Previous Page') ?></div>
	    <div class="nav-next"><?php next_posts_link('Next Page &raquo;') ?></div>
    </div>   
                
	<?php else : //do not delete ?>

    <h3>Page Not Found</h3>
    <p>We're sorry, but the page you are looking for isn't here.</p>
    <p>Try searching for the page you are looking for or using the navigation in the header or sidebar</p>

	<?php endif; //do not delete ?>
		
	
</div>
<!--include sidebar-->
<?php include(TEMPLATEPATH."/r_sidebar.php"); ?>
</div>

<?php include(TEMPLATEPATH."/tabber.php"); ?>

<!--include footer-->
<?php get_footer(); ?>