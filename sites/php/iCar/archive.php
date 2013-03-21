<?php get_header(); ?>

<div id="content" class="inner clearfix">
<div id="inner">

    
    <?php if (have_posts()) : // the loop ?>

    <h4><?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>

    <?php /* If this is a category archive */ if (is_category()) { ?>				
        Archive for <?php echo single_cat_title(); ?>
		
 	<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		Archive for <?php the_time('F jS, Y'); ?>
		
    <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		Archive for <?php the_time('F, Y'); ?>

    <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		Archive for <?php the_time('Y'); ?>
		
    <?php /* If this is a search */ } elseif (is_search()) { ?>
		Search Results
		
	<?php /* If this is an author archive */ } elseif (is_author()) { ?>
	    Author Archive

    <?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		Blog Archives

	<?php } //do not delete ?>
    
    </h4>

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