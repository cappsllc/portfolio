<?php global $wp_theme_options; ?>
<div style="clear: both;"></div>
<div id="footer">

<?php wp_page_menu('menu_class=footernav&depth=1'); ?>

<a href="http://icar.ithemes.com" title="Car WordPress Themes" >iCar Theme</a> &bull; Powered by <a href="http://wordpress.org/">WordPress</a> 
  
<?php wp_footer(); //we need this for plugins ?>
</div><!--end #footer-->

</div>
<?php do_action('it_footer'); ?>
</body>
</html>
