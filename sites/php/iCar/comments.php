<?php // Do not delete these lines
	if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

        if (!empty($post->post_password)) { // if there's a password
            if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
				?>
				
				<p class="nocomments">This post is password protected. Enter the password to view comments.<p>
				
				<?php
				return;
            }
        }

		/* This variable is for alternating comment background */
		$oddcomment = 'odd';
        $trackback_check = "true";
?>

<!-- You can start editing here. -->

<div id="comments">

	<?php if ($comments) : ?>

	<h4 class="comments">Comments</h4>

	<!--the bgein of one comment-->
	<?php foreach ($comments as $comment) : ?>
    <?php $comment_type = get_comment_type(); ?>
    <?php if($comment_type == 'comment') { ?>

		<div class="<?php if($oddcomment) { echo $oddcomment; } else echo 'even'; ?>" id="comment-<?php comment_ID() ?>">
			
            <div class="comment-head">
            By <?php comment_author_link() ?> on <a href="#comment-<?php comment_ID() ?>" title=""><?php comment_date('F jS, Y') ?> at <?php comment_time() ?></a> <?php edit_comment_link('e','',''); ?>
			</div>
            
            <?php if ($comment->comment_approved == '0') : ?>
			<em>Your comment is awaiting moderation.</em><br />
			<?php endif; ?>

			<?php comment_text() ?>
            
		</div>

	<?php /* Changes every other comment to a different class */	
		if ('odd' == $oddcomment) $oddcomment = '';
		else $oddcomment = 'odd';
	?>

    <?php } /* End of is_comment statement */ ?>
	<?php endforeach; /* end for each comment */ ?>
    
    <?php foreach ($comments as $comment) : ?>
    <?php $comment_type = get_comment_type(); ?>
    <?php if($comment_type != 'comment') { ?>
        
        <?php if ($trackback_check == "true") { // some sloppy php to get the <h4></h4> and <ul> to display ?>
        <h4>Trackbacks</h4>
        <ul>
        <?php } //end trackback_check if ?>
        
        <li id="comment-<?php comment_ID() ?>">
			<strong><?php comment_author_link() ?></strong>
		</li>
        <?php $trackback_check = "false"; ?>

    <?php } ?>
    <?php endforeach; ?>
    
        <?php if ($trackback_check == "false") { // if there was at lease 1 trackback, then close the ul ?>
        </ul>
        <?php } //end trackback_check if ?>

	<?php else : // this is displayed if there are no comments so far ?>

  	<?php if ('open' == $post->comment_status) : //If comments are open, but there are no comments ?>
		
	<?php else : // comments are closed ?>
	
	<p>Comments are closed.</p>

	<?php endif; ?>
	<?php endif; ?>

<div id="respond" class="postspace2">&nbsp;</div>

<?php if ('open' == $post->comment_status) : ?>

	<h4 class="respond">Leave a Comment</h4>

	<?php if ( get_option('comment_registration') && !$user_ID ) : ?>

	<p>You must be <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php the_permalink(); ?>">logged in</a> to post a comment.</p>

	<?php else : ?>

<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

	<?php if ( $user_ID ) : ?>
	<p>Logged in as <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="Log out of this account">Logout &raquo;</a></p>

	<?php else : ?>

	<p><input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
	<label for="author"><small>Name <?php if ($req) echo "(required)"; ?></small></label></p>

	<p><input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
	<label for="email"><small>Mail (will not be published) <?php if ($req) echo "(required)"; ?></small></label></p>

	<p><input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
	<label for="url"><small>Website</small></label></p>

	<?php endif; ?>

	<p><textarea name="comment" id="comment" cols="42" rows="10" tabindex="4"></textarea></p>

	<p><input name="submit" type="submit" id="submit" tabindex="5" value="Submit Comment" />
	<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" /></p>
	
	<?php do_action('comment_form', $post->ID); ?>

</form>

<?php endif; // If registration required and not logged in ?>

<?php endif; // if you delete this the sky will fall on your head ?>

</div> <!--end comments div-->
