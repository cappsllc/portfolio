<div id="r_sidebar">

<!--sidebar.php-->
<ul>
<?php if ( !function_exists('dynamic_sidebar')
        || !dynamic_sidebar('Post Sidebar') ) : ?>

<!--recent posts-->
<li>
	<h2 class="posts">Recent Posts</h2>
	<ul>
	<?php get_archives('postbypost', 10); ?>
	</ul>
</li>

<!--list of categories, order by name, without children categories, no number of articles per category-->
<li>	
	<h2 class="categories">Categories</h2>			
	<ul>
	<?php wp_list_categories('orderby=name&title_li'); ?>
	</ul>
</li>

<!--archives ordered per month-->
<li>
    <h2 class="archives">Archives</h2>
	<ul>
	<?php wp_get_archives('type=monthly'); ?>
	</ul>
</li>

<!--links or blogroll-->
<li>
    <h2 class="links">Links</h2>
	<ul><?php get_links(-1, '<li>', '</li>', ' - '); ?></ul>
</li>
			
<!--feeds-->
<li>
    <h2 class="feeds">Feeds</h2>
	<ul>
    <li><a href="<?php bloginfo('rss2_url');  ?>">Subscribe to RSS Feed</a></li>
    <li><a href="<?php bloginfo('comments_rss2_url');  ?>">Subscribe to Comments</a></li>
    </ul>
</li>

<!--sidebar.php end-->

<?php endif; ?>
</ul>
</div>