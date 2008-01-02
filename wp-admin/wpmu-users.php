<?php
require_once('admin.php');

$title = __('WordPress MU &rsaquo; Admin &rsaquo; Users');
$parent_file = 'wpmu-admin.php';
wp_enqueue_script( 'listman' );
require_once('admin-header.php');

if( is_site_admin() == false ) {
	wp_die( __('<p>You do not have permission to access this page.</p>') );
}

if ( $_GET['updated'] == 'true' ) {
	?>
	<div id="message" class="updated fade"><p>
		<?php
		switch ($_GET['action']) {
			case 'delete':
				_e('User deleted !');
			break;
			case 'all_spam':
				_e('Users mark as spam !');
			break;
			case 'all_delete':
				_e('Users deleted !');
			break;
			case 'add':
				_e('User added !');
			break;
			default:
				_e('Options saved !');
			break;
		}
		?>
	</p></div>
	<?php
}
?>

<div class="wrap">
	<?php
	$apage = isset( $_GET['apage'] ) ? intval( $_GET['apage'] ) : 1;
	$num = isset( $_GET['num'] ) ? intval( $_GET['num'] ) : 15;

	$query = "SELECT * FROM {$wpdb->users}";
	
	if( !empty($_GET['s']) ) {
		$search = '%' . trim(addslashes($_GET['s'])) . '%';
		$query .= " WHERE user_login LIKE '$search' OR user_email LIKE '$search'";
	}
	
	if( !isset($_GET['sortby']) ) {
		$_GET['sortby'] = 'id';
	}
	
	if( $_GET['sortby'] == 'email' ) {
		$query .= ' ORDER BY user_email ';
	} elseif( $_GET['sortby'] == 'id' ) {
		$query .= ' ORDER BY ID ';
	} elseif( $_GET['sortby'] == 'login' ) {
		$query .= ' ORDER BY user_login ';
	} elseif( $_GET['sortby'] == 'name' ) {
		$query .= ' ORDER BY display_name ';
	} elseif( $_GET['sortby'] == 'registered' ) {
		$query .= ' ORDER BY user_registered ';
	}
	
	$query .= ( $_GET['order'] == 'DESC' ) ? 'DESC' : 'ASC';

	if( !empty($_GET['s'])) {
		$user_list = $wpdb->get_results( $query, ARRAY_A );
		$total = count($user_list);	
	} else {
		$total = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->users}");
	}
	
	$query .= " LIMIT " . intval( ( $apage - 1 ) * $num) . ", " . intval( $num );
	
	$user_list = $wpdb->get_results( $query, ARRAY_A );

	// Pagination
	$user_navigation = paginate_links( array(
		'base' => add_query_arg( 'apage', '%#%' ),
		'format' => '',
		'total' => ceil($total / $num),
		'current' => $apage
	));
	?>
	<h2><?php _e("Users"); ?></h2>
	
	<div style="float:right; padding:0 20px; margin-top:20px;"> 
		<?php if ( $user_navigation ) echo "<p class='pagenav'>$user_navigation</p>"; ?>
	</div>
	
	<form action="wpmu-users.php" method="get" id="searchform"> 
		<fieldset> 
			<legend><?php _e('Search Users&hellip;') ?></legend> 
			<input type="text" name="s" value="<?php if (isset($_GET['s'])) echo stripslashes(wp_specialchars($_GET['s'], 1)); ?>" size="17" /> 
		</fieldset>			
		<input class="button" id="post-query-submit" type="submit" name="submit" value="<?php _e('Search') ?>"  /> 
	</form>

	<br style="clear:both;" />
	
	<?php if( isset($_GET['s']) && $_GET['s'] != '' ) : ?>
		<p><a href="wpmu-blogs.php?action=blogs&amp;s=<?php echo stripslashes(wp_specialchars($_GET['s'], 1)); ?>"><?php _e('Search Blogs:') ?> <strong><?php echo stripslashes(wp_specialchars($_GET['s'], 1)) ?></strong></a></p>
	<?php endif; ?>

	<?php
	// define the columns to display, the syntax is 'internal name' => 'display name'
	$posts_columns = array(
		'id'         => __('ID'),
		'login'      => __('Login'),
		'email'     => __('Email'),
		'name'       => __('Name'),
		'registered' => __('Registered'),
		'blogs'      => __('Blogs')
	);
	$posts_columns = apply_filters('manage_posts_columns', $posts_columns);

	// you can not edit these at the moment
	$posts_columns['control_edit']   = '';
	$posts_columns['control_delete'] = '';

	?>
	<script type="text/javascript">
		<!--
		var checkflag = "false";
		function check_all_rows() {
			field = document.formlist;
			if (checkflag == "false") {
				for (i = 0; i < field.length; i++) {
					if( field[i].name == 'allusers[]' ) {
						field[i].checked = true;
					}
				}
				checkflag = "true";
				return "<?php _e('Uncheck All') ?>"; 
			} else {
				for (i = 0; i < field.length; i++) {
					if( field[i].name == 'allusers[]' ) {
						field[i].checked = false;
					}
				}
				checkflag = "false";
				return "<?php _e('Check All') ?>"; 
			}
		}
		//  -->
	</script>

	<form name="formlist" action='wpmu-edit.php?action=allusers' method='post'>
		<table class="widefat" cellpadding="3" cellspacing="3">
			<thead>
			<tr>
				<?php foreach( (array) $posts_columns as $column_id => $column_display_name) { ?>
					<th scope="col">
						<?php if( $column_id == 'blogs' ) {
							_e('Blogs');
						} else { ?>
							<a href="wpmu-users.php?sortby=<?php echo $column_id ?>&amp;<?php if( $_GET['sortby'] == $column_id ) { if( $_GET['order'] == 'DESC' ) { echo "order=ASC&amp;" ; } else { echo "order=DESC&amp;"; } } ?>apage=<?php echo $apage ?>"><?php echo $column_display_name; ?></a>
						<?php } ?>
					</th>
				<?php } ?>
			</tr>
			</thead>
			<tbody id="the-list">
			<?php if ($user_list) {
				$bgcolor = '';
				foreach ( (array) $user_list as $user) { 
					$class = ('alternate' == $class) ? '' : 'alternate';
					?>
					
					<tr class="<?php echo $class; ?>">
					<?php
					foreach( (array) $posts_columns as $column_name=>$column_display_name) :
						switch($column_name) {
							case 'id': ?>
								<th scope="row"><input type='checkbox' id='user_<?php echo $user['ID'] ?>' name='allusers[]' value='<?php echo $user['ID'] ?>' /> <label for='user_<?php echo $user['ID'] ?>'><?php echo $user['ID'] ?></label></th>
							<?php
							break;

							case 'login': ?>
								<td><label for='user_<?php echo $user['ID'] ?>'><?php echo $user['user_login'] ?></label></td>
							<?php
							break;

							case 'name': ?>
								<td><?php echo $user['display_name'] ?></td>
							<?php
							break;

							case 'email': ?>
								<td><?php echo $user['user_email'] ?></td>
							<?php
							break;

							case 'registered': ?>
								<td><?php echo mysql2date(__('Y-m-d \<\b\r \/\> g:i:s a'), $user['user_registered']); ?></td>
							<?php
							break;

							case 'blogs': 
								$blogs = get_blogs_of_user( $user['ID'], true );
								?>
								<td>
									<?php
									if( is_array( $blogs ) ) {
										foreach ( (array) $blogs as $key => $val ) {
											echo '<a href="wpmu-blogs.php?action=editblog&amp;id=' . $val->userblog_id . '">' . str_replace( '.' . $current_site->domain, '', $val->domain . $val->path ) . '</a> (<a '; 
											if( get_blog_status( $val->userblog_id, 'spam' ) == 1 )
												echo 'style="background-color: #f66" ';
											echo 'target="_new" href="http://'.$val->domain . $val->path.'">' . __('View') . '</a>)<br />'; 
										}
									}
									?>
								</td>
							<?php
							break;

							case 'control_edit': ?>
								<td><a href="user-edit.php?user_id=<?php echo $user['ID']; ?>" class="edit"><?php _e('Edit'); ?></a></td>
							<?php
							break;

							case 'control_delete': ?>
								<td><a href="wpmu-edit.php?action=confirm&amp;action2=deleteuser&amp;msg=<?php echo urlencode( __("You are about to delete this user.") ); ?>&amp;id=<?php echo $user['ID']; ?>&amp;redirect=<?php echo wpmu_admin_redirect_url(); ?>" class="delete" onclick="return deleteSomething( 'user', <?php echo $user['ID']; ?>, '<?php echo js_escape(sprintf(__("You are about to delete this user '%s'.\n'OK' to delete, 'Cancel' to stop."), $user['user_login'])); ?>' );"><?php _e('Delete'); ?></a></td>
							<?php
							break;

							default: ?>
								<td><?php do_action('manage_users_custom_column', $column_name, $user['ID']); ?></td>
							<?php
							break;
						}
					endforeach
					?>
					</tr> 
					<?php
				}
			} else {
			?>
				<tr style='background-color: <?php echo $bgcolor; ?>'> 
					<td colspan="<?php echo (int) count($posts_columns); ?>"><?php _e('No users found.') ?></td> 
				</tr> 
				<?php
			} // end if ($users)
			?> 
			</tbody>
		</table>
		
		<div style="float:right; padding:0 20px; margin-top:20px;"> 
			<?php if ( $user_navigation ) echo "<p class='pagenav'>$user_navigation</p>"; ?>
		</div>
	
		<p><input class="button" type="button" value="<?php _e('Check All') ?>" onclick="this.value=check_all_rows()" /></p>
		
		<h3><?php _e('Selected Users:') ?></h3>
		<ul style="list-style:none;">
			<li><input type='radio' name='userfunction' id='delete' value='delete' /> <label for='delete'><?php _e('Delete') ?></label></li>
			<li><input type='radio' name='userfunction' id='spam' value='spam' /> <label for='spam'><?php _e('Mark as Spammers') ?></label></li>
			<li><input type='radio' name='userfunction' id='notspam' value='notspam' /> <label for='spam'><?php _e('Not Spam') ?></label></li> 
		</ul>
		
		<p class="submit" style="width: 220px">
			<?php wp_nonce_field( "allusers" ); ?>
			<input type='hidden' name='action' value='allusers' />
			<input class="button" type='submit' value='<?php _e('Apply Changes') ?>' /></p>
	</form>
</div>

<?php
if( apply_filters('show_adduser_fields', true) ) :
?>
<div class="wrap">
	<form action="wpmu-edit.php?action=adduser" method="post">
		<h2><?php _e('Add User') ?></h2>
		
		<table cellpadding="3" cellspacing="3">
			<tr>
				<th style="text-align:center;" scope='row'><?php _e('Username') ?></th>
				<td><input type="text" name="user[username]" /></td>
			</tr>
			<tr>
				<th style="text-align:center;" scope='row'><?php _e('Email') ?></th>
				<td><input type="text" name="user[email]" /></td>
			</tr>
			<tr><td colspan='2'><?php _e('Username and password will be mailed to the above email address.') ?></td></tr>
		</table>
		<p>
			<?php wp_nonce_field('add-user') ?>
			<input class="button" type="submit" name="Add user" value="<?php _e('Add user') ?>" /></p>
	</form>
</div>
<?php endif; ?>

<?php include('admin-footer.php'); ?>
