<?php

require ('wp-config.php');

require_once( ABSPATH . WPINC . '/registration.php');

do_action("signup_header");
if( $current_blog->domain != $current_site->domain ) {
	header( "Location: http://" . $current_site->domain . $current_site->path . "wp-signup.php" );
	die();
}

get_header();
?>
<div id="content" class="widecolumn">
<style type="text/css">
form { margin-top: 2em; }
#submit, #blog_title, #user_email {
	width: 90%;
	font-size: 24px;
}
.error {
	background-color: #f66;
}
</style>
<?php
function show_blog_form($blog_id = '', $blog_title = '', $errors = '') {
	global $current_site;

	// Blog name/Username
	if ( $errors->get_error_message('blog_id') )
		print '<tr class="error">';
	else
		print '<tr>';

	if( constant( "VHOST" ) == 'no' )
		echo '<th valign="top">' . __('Blog Name:') . '</th><td>';
	else
		echo '<th valign="top">' . __('Blog Domain:') . '</th><td>';

	if ( $errmsg = $errors->get_error_message('blog_id') ) {
		?><p><strong><?php echo $errmsg ?></strong></p><?php
	}
	if( constant( "VHOST" ) == 'no' ) {
		print '<span style="font-size: 20px">' . $current_site->domain . $current_site->path . '</span><input name="blog_id" type="text" id="blog_id" value="'.$blog_id.'" maxlength="50" style="width:40%; text-align: left; font-size: 20px;" /><br />';
	} else {
		print '<input name="blog_id" type="text" id="blog_id" value="'.$blog_id.'" maxlength="50" style="width:40%; text-align: right; font-size: 20px;" /><span style="font-size: 20px">' . $current_site->domain . $current_site->path . '</span><br />';
	}
	if ( !is_user_logged_in() ) {
		print '(<strong>Your address will be ';
		if( constant( "VHOST" ) == 'no' ) {
			print $current_site->domain . $current_site->path . 'blogname';
		} else {
			print 'domain.' . $current_site->domain . $current_site->path;
		}
		print '.</strong> Must be at least 4 characters, letters and numbers only. It cannot be changed so choose carefully!)</td> </tr>';
	}

	// Blog Title
	if ( $errors->get_error_message('blog_title')) {
		print '<tr class="error">';
	} else {
		print '<tr>';
	}
?><th valign="top" width="120">Blog Title:</th><td><?php

	if ( $errmsg = $errors->get_error_message('blog_title') ) {
?><p><strong><?php echo $errmsg ?></strong></p><?php
	}
	print '<input name="blog_title" type="text" id="blog_title" value="'.wp_specialchars($blog_title, 1).'" /></td>
		</tr>';
?>
<tr>
<th scope="row"  valign="top">Privacy:</th>
<td><label><input type="checkbox" name="blog_public" value="1" checked="checked" /> <?php _e('I would like my blog to appear in search engines like Google and Technorati, and in public listings around this site.'); ?></label></td>
</tr>
<?php
}

function validate_blog_form() {
	if ( is_user_logged_in() )
		$user = wp_get_current_user();
	else
		$user = '';

	$result = wpmu_validate_blog_signup($_POST['blog_id'], $_POST['blog_title'], $user);	

	return $result;
}

function show_user_form($user_name = '', $user_email = '', $errors = '') {
	// Blog name/Username
	if ( $errors->get_error_message('user_name') ) {
		print '<tr class="error">';
	} else {
		print '<tr>';
	}

	echo '<th valign="top">' . __('Username:') . '</th><td>';

	if ( $errmsg = $errors->get_error_message('user_name') ) {
		?><p><strong><?php echo $errmsg ?></strong></p><?php
	}

	print '<input name="user_name" type="text" id="user_name" value="'.$user_name.'" maxlength="50" style="width:50%; font-size: 30px;" /><br />';
	print '(Must be at least 4 characters, letters and numbers only.)</td> </tr>';

	// User Email
	if ( $errors->get_error_message('user_email') ) {
		print '<tr class="error">';
	} else {
		print '<tr>';
	}
?><th valign="top">Email&nbsp;Address:</th><td><?php

	if ( $errmsg = $errors->get_error_message('user_email') ) {
?><p><strong><?php echo $errmsg ?></strong></p><?php
	}
	print '
	<input name="user_email" type="text" id="user_email" value="'.wp_specialchars($user_email, 1).'" maxlength="200" /><br /> (We&#8217;ll send your password to this address, so <strong>triple-check it</strong>.)</td>
	</tr>';

	if ( $errmsg = $errors->get_error_message('generic') )
		print '<tr class="error"> <th colspan="2">'.$errmsg.'</th> </tr>';
}

function validate_user_form() {
	$result = wpmu_validate_user_signup($_POST['user_name'], $_POST['user_email']);	

	return $result;
}

function signup_another_blog($blog_id = '', $blog_title = '', $errors = '') {
	global $current_user, $wpdb, $domain, $current_site;

	if ( ! is_wp_error($errors) )
		$errors = new WP_Error();

	echo '<h2>' . sprintf( __('Get <em>another</em> %s blog in seconds'), $current_site->site_name ) . '</h2>';

	if ( $errors->get_error_code() ) {
		print "<p>There was a problem, please correct the form below and try again.</p>";
	}

?>
<p>Welcome back, <?php echo $current_user->display_name; ?>. By filling out the form below, you can <strong>add another blog to your account</strong>. There is no limit to the number of blogs you can have, so create to your heart's content, but blog responsibly.</p>
<p>Here are the blogs you already have:</p>
<ul>
<?php 
	$blogs = get_blogs_of_user($current_user->ID);

	if ( ! empty($blogs) ) foreach ( $blogs as $blog ) {
		echo "<li><a href='" . $blog->domain . $blog->path . "'>" . $blog->domain . $blog->path . "</a></li>";
	}
?>
</ul>
<p><?php _e("If you&#8217;re not going to use a great blog domain, leave it for a new user. Now have at it!") ?></p>
<form name="setupform" id="setupform" method="post" action="wp-signup.php">
<input type="hidden" name="stage" value="gimmeanotherblog">
<table border="0" width="100%" cellpadding="9">
<?php
	show_blog_form($blog_id, $blog_title, $errors);
?>
<tr>
<th scope="row"  valign="top">&nbsp;</th>
<td><input id="submit" type="submit" name="Submit" class="submit" value="Create Blog &raquo;" /></td>
</tr>
</table>
</form>
<?php
}

function validate_another_blog_signup() {
	global $current_user;

	$result = validate_blog_form();
	extract($result);
	
	if ( $errors->get_error_code() ) {
		signup_another_blog($blog_id, $blog_title, $errors);
		return;
	}
		
	$public = (int) $_POST['blog_public'];
	$meta = array ('lang_id' => 'en', 'public' => $public);

	wpmu_create_blog($domain, $path, $blog_title, $current_user->id, $meta);
	confirm_another_blog_signup($domain, $path, $blog_title, $current_user->user_login, $current_user->user_email, $meta);		
}

function confirm_another_blog_signup($domain, $path, $blog_title, $user_name, $user_email, $meta) {
?>
<h2><?php printf(__('%s Is Yours'), $domain.$path ) ?></h2>
<p><?php printf(__('<a href="http://%1$s">http://%2$s</a> is your new blog.  <a href="%3$s">Login</a> as "%4$s" using your existing password.'), $domain.$path, $domain.$path, "http://" . $domain.$path . "wp-login.php", $user_name) ?></p>
<?php
	do_action('signup_finished');
}

function signup_user($user_name = '', $user_email = '', $errors = '') {
	global $current_site;

	if ( ! is_wp_error($errors) )
		$errors = new WP_Error();
?>	
<h2><?php printf( __('Get your own %s account in seconds'), $current_site->site_name ) ?></h2>
<p>Fill out this one-step form and you'll be blogging seconds later!</p>
<form name="setupform" id="setupform" method="post" action="wp-signup.php">
<input type="hidden" name="stage" value="validate-user-signup">
<table border="0" width="100%" cellpadding="9">
<?php show_user_form($user_name, $user_email, $errors); ?>
<th scope="row"  valign="top">&nbsp;</th>
<td>
<p>
<input id="signupblog" type="radio" name="signup_for" value="blog" checked="checked" />
<label for="signupblog">Gimme a blog! (Like username.<?php echo $current_site->domain ?>)</label>
<br />
<input id="signupuser" type="radio" name="signup_for" value="user" />
<label for="signupuser">Just a username, please.</label>
</p>
</td>
</tr>
<tr>
<th scope="row"  valign="top">&nbsp;</th>
<td><input id="submit" type="submit" name="Submit" class="submit" value="Next &raquo;" /></td>
</tr>
</table>
</form>
<?php

}

function validate_user_signup() {
	$result = validate_user_form();
	extract($result);
	
	if ( $errors->get_error_code() ) {
		signup_user($user_name, $user_email, $errors);
		return;	
	}

	if ( 'blog' == $_POST['signup_for'] ) {
		signup_blog($user_name, $user_email);
		return;
	}

	wpmu_signup_user($user_name, $user_email);

	confirm_user_signup($user_name, $user_email);
}

function confirm_user_signup($user_name, $user_email) {
?>
<h2><?php printf(__('%s Is Your New Username'), $user_name) ?></h2>
<p><?php _e('But, before you can start using your new username, <strong>you must activate it</strong>.') ?></p>
<p><?php printf(__('Check your inbox at <strong>%1$s</strong> and click the link given.  '),  $user_email) ?></p>
<p><?php _e('If you do not activate your username within two days, you will have to sign up again.'); ?></p>
<?php
}

function signup_blog($user_name = '', $user_email = '', $blog_id = '', $blog_title = '', $errors = '') {
	if ( ! is_wp_error($errors) )
		$errors = new WP_Error();

	if ( empty($blog_id) )
		$blog_id = $user_name;
?>	
<form name="setupform" id="setupform" method="post" action="wp-signup.php">
<input type="hidden" name="stage" value="validate-blog-signup">
<input type="hidden" name="user_name" value="<?php echo $user_name ?>">
<input type="hidden" name="user_email" value="<?php echo $user_email ?>">
<table border="0" width="100%" cellpadding="9">
<?php show_blog_form($blog_id, $blog_title, $errors); ?>
<tr>
<th scope="row"  valign="top">&nbsp;</th>
<td><input id="submit" type="submit" name="Submit" class="submit" value="Signup &raquo;" /></td>
</tr>
</table>
</form>
<?php
}

function validate_blog_signup() {
	// Re-validate user info.
	$result = wpmu_validate_user_signup($_POST['user_name'], $_POST['user_email']);
	extract($result);

	if ( $errors->get_error_code() ) {
		signup_user($user_name, $user_email, $errors);
		return;
	}
	
	$result = wpmu_validate_blog_signup($_POST['blog_id'], $_POST['blog_title']);
	extract($result);

	if ( $errors->get_error_code() ) {
		signup_blog($user_name, $user_email, $blog_id, $blog_title, $errors);
		return;
	}

	$public = (int) $_POST['blog_public'];
	$meta = array ('lang_id' => 'en', 'public' => $public);
         
	wpmu_signup_blog($domain, $path, $blog_title, $user_name, $user_email, $meta);

	confirm_blog_signup($domain, $path, $blog_title, $user_name, $user_email, $meta);
}

function confirm_blog_signup($domain, $path, $blog_title, $user_name, $user_email, $meta) {
?>
<h2><?php printf(__('%s Is Yours'), $domain.$path) ?></h2>
<p><?php _e('But, before you can start using your blog, <strong>you must activate it</strong>.') ?></p>
<p><?php printf(__('Check your inbox at <strong>%1$s</strong> and click the link given.  '),  $user_email) ?></p>
<p><?php _e('If you do not activate your blog within two days, you will have to sign up again.'); ?></p>
<?php
	do_action('signup_finished');
}

// Main
$blog_id = isset($_GET['new']) ? strtolower(preg_replace('/^-|-$|[^-a-zA-Z0-9]/', '', $_GET['new'])) : null;
if( $_POST['blog_public'] != 1 )
	$_POST['blog_public'] = 0;
	
switch ($_POST['stage']) {
	case 'validate-user-signup' :
		validate_user_signup();
		break;
	case 'validate-blog-signup':
		validate_blog_signup();
		break;
	case 'gimmeanotherblog':
		validate_another_blog_signup();
		break;
	default :
		if ( is_user_logged_in() )
			signup_another_blog($blog_id);
		else
			signup_user( $blog_id );

		if ($blog_id) {
?><p><em>The blog you were looking for, <strong><?php echo $blog_id ?>.<?php echo $current_site->domain ?></strong> doesn't exist but you can create it now!</em></p><?php
		}
		break;
}
?>
</div>

<?php get_footer(); ?>
