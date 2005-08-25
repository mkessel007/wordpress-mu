<?php 
@header('Content-type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
if (!isset($_GET["page"])) require_once('admin.php'); ?>
<?php get_admin_page_title(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php bloginfo('name') ?> &rsaquo; <?php echo $title; ?> &#8212; WordPress</title>
<link rel="stylesheet" href="<?php echo get_settings('siteurl') ?>/wp-admin/wp-admin.css?version=<?php bloginfo('version'); ?>" type="text/css" />
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_settings('blog_charset'); ?>" />

<script type="text/javascript">
//<![CDATA[

function addLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      oldonload();
      func();
    }
  }
}

<?php if ( isset($xfn) ) : ?>

function GetElementsWithClassName(elementName, className) {
	var allElements = document.getElementsByTagName(elementName);
	var elemColl = new Array();
	for (i = 0; i < allElements.length; i++) {
		if (allElements[i].className == className) {
			elemColl[elemColl.length] = allElements[i];
		}
	}
	return elemColl;
}

function meChecked() {
  var undefined;
  var eMe = document.getElementById('me');
  if (eMe == undefined) return false;
  else return eMe.checked;
}

function upit() {
	var isMe = meChecked(); //document.getElementById('me').checked;
	var inputColl = GetElementsWithClassName('input', 'valinp');
	var results = document.getElementById('rel');
	var linkText, linkUrl, inputs = '';
	for (i = 0; i < inputColl.length; i++) {
		 inputColl[i].disabled = isMe;
		 inputColl[i].parentNode.className = isMe ? 'disabled' : '';
		 if (!isMe && inputColl[i].checked && inputColl[i].value != '') {
			inputs += inputColl[i].value + ' ';
				}
		 }
	inputs = inputs.substr(0,inputs.length - 1);
	if (isMe) inputs='me';
	results.value = inputs;
	}

function blurry() {
	if (!document.getElementById) return;

	var aInputs = document.getElementsByTagName('input');

	for (var i = 0; i < aInputs.length; i++) {		
		 aInputs[i].onclick = aInputs[i].onkeyup = upit;
	}
}

addLoadEvent(blurry);
<?php endif; ?>
//]]>
</script>
<script type="text/javascript" src="fat.js"></script>
<?php if ( isset( $editing ) ) : ?>
<?php if ( 'true' == get_user_option('rich_editing') ) :?>
<script type="text/javascript" src="tinymce/tiny_mce_src.js"></script>
<script type="text/javascript">
tinyMCE.init({
	mode : "specific_textareas",
	textarea_trigger : "title",
	width : "100%",
	theme : "advanced",
	theme_advanced_buttons1 : "bold,italic,strikethrough,separator,bullist,numlist,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,image,emotions,separator,undo,redo,code",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_path_location : "bottom",
	theme_advanced_resizing : true,
	theme_advanced_resize_horizontal : false,
	entity_encoding : "raw",
	extended_valid_elements : "a[id|href|title|onclick],img[class|src|alt|title|width|height|align]",
	plugins : "emotions"
	<?php do_action('mce_options'); ?>
});
</script>
<?php endif; ?>
<script type="text/javascript" src="dbx.js"></script>
<script type="text/javascript" src="dbx-key.js"></script>

<?php if ( current_user_can('manage_categories') ) : ?>
<script type="text/javascript" src="tw-sack.js"></script>
<script type="text/javascript">
var ajaxCat = new sack();
var newcat;
 
function newCatAddIn() {
	var ajaxcat = document.createElement('p');
	ajaxcat.id = 'ajaxcat';

	newcat = document.createElement('input');
	newcat.type = 'text';
	newcat.name = 'newcat';
	newcat.id = 'newcat';
	newcat.size = '16';
	newcat.setAttribute('autocomplete', 'off');
	newcat.setAttribute('onkeypress', 'return ajaxNewCatKeyPress(event);');

	var newcatSub = document.createElement('input');
	newcatSub.type = 'button';
	newcatSub.name = 'Button';
	newcatSub.value = '+';
	newcatSub.setAttribute('onclick', 'ajaxNewCat();');

	var searchResult = document.createElement( 'div' );
	searchResult.type = 'div';
	searchResult.name = 'searchresults';
	searchResult.id = 'searchresults';
	searchResult.style.display = 'none';
	searchResult.style.overflow = 'auto';
	searchResult.style.border = '1px solid #ccc';
	searchResult.style.background = '#eee';


	ajaxcat.appendChild(newcat);
	ajaxcat.appendChild(newcatSub);
	ajaxcat.appendChild(searchResult);
	document.getElementById('categorychecklist').parentNode.appendChild(ajaxcat);
}

addLoadEvent(newCatAddIn);

function getResponseElement() {
	var p = document.getElementById('ajaxcatresponse');
	if (!p) {
		p = document.createElement('p');
		document.getElementById('categorydiv').appendChild(p);
		p.id = 'ajaxcatresponse';
	}
	return p;
}

function newCatLoading() {
	var p = getResponseElement();
	p.innerHTML = 'Sending Data...';
}

function newCatLoaded() {
	var p = getResponseElement();
	p.innerHTML = 'Data Sent...';
}

function newCatInteractive() {
	var p = getResponseElement();
	p.innerHTML = 'Processing Data...';
}

function newCatCompletion() {
	var p = getResponseElement();
	var id = ajaxCat.response;
	if ( id == '-1' ) {
		p.innerHTML = "You don't have permission to do that.";
		return;
	}
	if ( id == '0' ) {
		p.innerHTML = "That category name is invalid.  Try something else.";
		return;
	}
	p.parentNode.removeChild(p);
	var exists = document.getElementById('category-' + id);
	if (exists) {
		exists.checked = 'checked';
		exists.parentNode.setAttribute('id', 'new-category-' + id);
		var nowClass = exists.parentNode.getAttribute('class');
		exists.parentNode.setAttribute('class', nowClass + ' fade');
		Fat.fade_all();
		exists.parentNode.setAttribute('class', nowClass);
	} else {
		var catDiv = document.getElementById('categorychecklist');
		var newLabel = document.createElement('label');
		catDiv.insertBefore(newLabel, catDiv.firstChild);
		newLabel.setAttribute('for', 'category-' + id);
		newLabel.setAttribute('id', 'new-category-' + id);
		newLabel.setAttribute('class', 'selectit fade');

		var newCheck = document.createElement('input');
		newLabel.appendChild(newCheck);
		newCheck.value = id;
		newCheck.type = 'checkbox';
		newCheck.checked = 'checked';
		newCheck.name = 'post_category[]';
		newCheck.id = 'category-' + id;

		var newLabelText = document.createTextNode(' ' + newcat.value);
		newLabel.appendChild(newLabelText);
		Fat.fade_all();
		newLabel.setAttribute('class', 'selectit');
	}
	newcat.value = '';
}

function ajaxNewCatKeyPress(e) {
	if (!e) {
		if (window.event) {
			e = window.event;
		} else {
			return;
		}
	}
	if (e.keyCode == 13) {
		ajaxNewCat();
		e.returnValue = false;
		e.cancelBubble = true;
		return false;
	}
}

function ajaxNewCat() {
	var newcat = document.getElementById('newcat');
	var catString = 'ajaxnewcat=' + newcat.value;
	ajaxCat.requestFile = 'edit-form-ajax-cat.php';
	ajaxCat.method = 'GET';
	ajaxCat.onLoading = newCatLoading;
	ajaxCat.onLoaded = newCatLoaded;
	ajaxCat.onInteractive = newCatInteractive;
	ajaxCat.onCompletion = newCatCompletion;
	ajaxCat.runAJAX(catString);
}
</script>
<?php endif; ?>

<?php endif; ?>
<script type="text/javascript" src="tw-sack.js"></script>
<script type="text/javascript">
var ajaxFeedback = new sack();

function show_feedback_form() {
	var feedbackform = document.getElementById( 'feedbackform' );
	feedbackform.style.display='';
}
function hide_feedback_form() {
	var feedbackform = document.getElementById( 'feedbackform' );
	feedbackform.style.display='none';
}
function toggle_feedback_form() {
	var feedbackform = document.getElementById( 'feedbackform' );
	if( feedbackform.style.display == 'table' ) {
		feedbackform.style.display='none';
	} else {
		feedbackform.style.display='table';
	}
}
function feedbackLoading() {
	var p = document.getElementById( 'feedbackstatus' );
	p.innerHTML = 'Sending Feedback...';
}

function feedbackLoaded() {
	var p = document.getElementById( 'feedbackstatus' );
	p.innerHTML = 'Feedback Sent. Thank you for your help!';
}


function newfeedback() {
	var form = document.getElementById( 'wpmufeedbackform' );
	//alert( form.feedbackproblem.value );
	var feedback = 'user_login=' + form.user_login.value + '&host=' + form.host.value + '&browser=' + form.browser.value + '&req=' + form.page.value + '&feedback=' + form.feedbackproblem.value;
	ajaxFeedback.requestFile = 'wpmu-feedback.php';
	ajaxFeedback.method = 'GET';
	ajaxFeedback.onLoading = feedbackLoading;
	ajaxFeedback.onLoaded = feedbackLoaded;
	//ajaxFeedback.onInteractive = newCatInteractive;
	//ajaxFeedback.onCompletion = feedbackCompletion;
	ajaxFeedback.runAJAX(feedback);
	return false;
}

</script>

<?php do_action('admin_head'); ?>
</head>
<body>

<div id='feedbackform' style='display: none; position: absolute; top: 50px; right: 10px; height:200px; width: 400px; background: #eee; border: 1px solid #333;'>
	<div style='padding-left: 5px;background: #dfe8f1;'>
	<span><a style='text-decoration: none' href="javascript: hide_feedback_form()">X</a></span><span style='padding-left: 150px; text-align: center'><strong>Feedback</strong></span>
	</div>
	<form id='wpmufeedbackform' action='wpmu-feedback.php' method='post'>
	<input type='hidden' name='user_login' value='<?php echo $current_user->data->user_login ?>'>
	<input type='hidden' name='host' value='<?php echo $_SERVER["HTTP_HOST"] ?>'>
	<input type='hidden' name='browser' value='<?php echo $_SERVER["HTTP_USER_AGENT"] ?>'>
	<input type='hidden' name='page' value='<?php echo $_SERVER["REQUEST_URI"] ?>'>
	<table>
	<tr><th align='left' valign='top'>From:</td><td valign='top'><?php echo $current_user->data->user_login ?></td></tr>
	<tr><th align='left' valign='top'>Host:</td><td valign='top'><?php echo $_SERVER["HTTP_HOST"] ?></td></tr>
	<tr><th align='left' valign='top'>Browser:</td><td valign='top'><?php echo $_SERVER["HTTP_USER_AGENT"] ?></td></tr>
	<tr><th align='left' valign='top'>Page:</td><td valign='top'><?php echo $_SERVER["REQUEST_URI"] ?></td></tr>
	<tr><th align='left' valign='top'>Problem:</td><td valign='top'><textarea name='feedbackproblem' rows='5' cols='40'></textarea></td></tr>
	<tr>
	<td align='right'><input value='Submit' type='button' onclick='javascript: return newfeedback()'></td>
	<td align='right' id='feedbackstatus' valign='top'></td>
	</tr>
	</table>
	</form>
</div>

<div id="wphead">
<h1><?php echo wptexturize(get_settings(('blogname'))); ?> <span>(<a href="<?php echo get_settings('home') . '/'; ?>"><?php _e('View site') ?> &raquo;</a>)</span></h1>
</div>

<div id="user_info"><p><?php printf(__('Howdy, <strong>%s</strong>.'), $user_identity) ?> [<a href="<?php echo get_settings('siteurl')
	 ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account') ?>"><?php _e('Sign Out'); ?></a>, <a href="profile.php"><?php _e('My Account'); ?></a>, <a href="javascript: toggle_feedback_form()"><?php _e( "Feedback" ) ?>] </p></div>

<?php
require(ABSPATH . '/wp-admin/menu-header.php');

if ( $parent_file == 'options-general.php' ) {
	require(ABSPATH . '/wp-admin/options-head.php');
}
?>
