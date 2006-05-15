<?php
// This array constructs the admin menu bar.
//
// Menu item name
// The minimum level the user needs to access the item: between 0 and 10
// The URL of the item's file

$menu_perms = get_site_option( "menu_items" );
if( is_array( $menu_perms ) == false )
	$menu_perms = array();

$menu[0] = array(__('Dashboard'), 'read', 'index.php');
$menu[5] = array(__('Write'), 'edit_posts', 'post-new.php');
$menu[10] = array(__('Manage'), 'edit_posts', 'edit.php');
$menu[20] = array(__('Links'), 'manage_links', 'link-manager.php');
$menu[25] = array(__('Presentation'), 'switch_themes', 'themes.php');
if( $menu_perms[ 'plugins' ] == 1 )
	$menu[30] = array(__('Plugins'), 'activate_plugins', 'plugins.php');
if ( current_user_can('edit_users') )
	$menu[35] = array(__('Users'), 'read', 'profile.php');
else
	$menu[35] = array(__('Profile'), 'read', 'profile.php');
$menu[40] = array(__('Options'), 'manage_options', 'options-general.php');
$menu[45] = array(__('Import'), 'import', 'import.php');

$submenu['post-new.php'][5] = array(__('Write Post'), 'edit_posts', 'post-new.php');
$submenu['post-new.php'][10] = array(__('Write Page'), 'edit_pages', 'page-new.php');

$submenu['edit.php'][5] = array(__('Posts'), 'edit_posts', 'edit.php');
$submenu['edit.php'][10] = array(__('Pages'), 'edit_pages', 'edit-pages.php');
$submenu['edit.php'][15] = array(__('Categories'), 'manage_categories', 'categories.php');
$submenu['edit.php'][20] = array(__('Comments'), 'edit_posts', 'edit-comments.php');
$awaiting_mod = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '0'");
$submenu['edit.php'][25] = array(sprintf(__("Awaiting Moderation (%s)"), $awaiting_mod), 'edit_posts', 'moderation.php');
#$submenu['edit.php'][30] = array(__('Files'), 'edit_files', 'templates.php');
/*
$invites_left = get_usermeta( $user_ID, 'invites_left' );
$submenu['edit.php'][35] = array(sprintf(__("Invites (%s)"), $invites_left ), 'edit_posts', 'invites.php'); // TODO: put somewhere else.
*/

$submenu['link-manager.php'][5] = array(__('Manage Links'), 'manage_links', 'link-manager.php');
$submenu['link-manager.php'][10] = array(__('Add Link'), 'manage_links', 'link-add.php');
$submenu['link-manager.php'][15] = array(__('Link Categories'), 'manage_links', 'link-categories.php');
$submenu['link-manager.php'][20] = array(__('Import Links'), 'manage_links', 'link-import.php');

$submenu['profile.php'][5] = array(__('Your Profile'), 'read', 'profile.php');
$submenu['profile.php'][10] = array(__('Authors &amp; Users'), 'edit_users', 'users.php');

$submenu['options-general.php'][10] = array(__('General'), 'manage_options', 'options-general.php');
$submenu['options-general.php'][15] = array(__('Writing'), 'manage_options', 'options-writing.php');
$submenu['options-general.php'][20] = array(__('Reading'), 'manage_options', 'options-reading.php');
$submenu['options-general.php'][25] = array(__('Discussion'), 'manage_options', 'options-discussion.php');

$submenu['themes.php'][5] = array(__('Themes'), 'switch_themes', 'themes.php');

// Loop over submenus and remove pages for which the user does not have privs.
foreach ($submenu as $parent => $sub) {
	foreach ($sub as $index => $data) {
		if ( ! current_user_can($data[1]) ) {
			$menu_nopriv[$data[2]] = true;
			unset($submenu[$parent][$index]);
		}
	}
	
	if ( empty($submenu[$parent]) )
		unset($submenu[$parent]);
}

// Loop over the top-level menu.
// Remove menus that have no accessible submenus and require privs that the user does not have.
// Menus for which the original parent is not acessible due to lack of privs will have the next
// submenu in line be assigned as the new menu parent. 
foreach ( $menu as $id => $data ) {
	// If submenu is empty...
	if ( empty($submenu[$data[2]]) ) {
		// And user doesn't have privs, remove menu.
		if ( ! current_user_can($data[1]) ) {
			$menu_nopriv[$data[2]] = true;
			unset($menu[$id]);
		}
	} else {
		$subs = $submenu[$data[2]];
		$first_sub = array_shift($subs);
		$old_parent = $data[2];
		$new_parent = $first_sub[2];
		// If the first submenu is not the same as the assigned parent,
		// make the first submenu the new parent.
		if ( $new_parent != $old_parent ) {
			$real_parent_file[$old_parent] = $new_parent;
			$menu[$id][2] = $new_parent;
			
			foreach ($submenu[$old_parent] as $index => $data) {
				$submenu[$new_parent][$index] = $submenu[$old_parent][$index];
				unset($submenu[$old_parent][$index]);
			}
			unset($submenu[$old_parent]);	
		}
	}
}

get_currentuserinfo();
if( is_site_admin() ) {
	$menu[1] = array(__('Site Admin'), '10', 'wpmu-admin.php' );
	$submenu[ 'wpmu-admin.php' ][5] = array( 'Blogs', '10', 'wpmu-blogs.php' );
	$submenu[ 'wpmu-admin.php' ][10] = array( 'Users', '10', 'wpmu-users.php' );
	$submenu[ 'wpmu-admin.php' ][15] = array( 'Feeds', '10', 'wpmu-feeds.php' );
	$submenu[ 'wpmu-admin.php' ][20] = array( 'Themes', '10', 'wpmu-themes.php' );
	$submenu[ 'wpmu-admin.php' ][25] = array( 'Options', '10', 'wpmu-options.php' );
	$submenu[ 'wpmu-admin.php' ][30] = array( 'Upgrade', '10', 'wpmu-upgrade-site.php' );
}

// Create list of page plugin hook names.
foreach ($menu as $menu_page) {
	$admin_page_hooks[$menu_page[2]] = sanitize_title($menu_page[0]);
}

do_action('admin_menu', '');
ksort($menu); // make it all pretty

if (! user_can_access_admin_page()) {
	global $wpdb;
	// find the blog of this user first
	$primary_blog = $wpdb->get_var( "SELECT meta_value FROM {$wpdb->usermeta} WHERE user_id = '$user_ID' AND meta_key = 'primary_blog'" );
	$url = $wpdb->get_row("SELECT domain, path FROM $wpdb->blogs WHERE 
blog_id = $primary_blog");
	header( 'Location: http://' . $url->domain . $url->path . 'wp-admin/' );
	exit;
	die( __('You do not have sufficient permissions to access this page.') );
}

?>
