<?php
// -- BEGIN LICENSE BLOCK ----------------------------------
//
// This file is a plugin for Dotclear 2.
// 
// Copyright (c) 2013 FredM
// Licensed under the GPL version 2.0 license.
// A copy of this license is available in LICENSE file or at
// http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
//
// -- END LICENSE BLOCK ------------------------------------
if (!defined('DC_CONTEXT_ADMIN')) { return; }

$page_title = __('GalleryInsert');

#Settings
$s =& $core->blog->settings->galleryinsert;

# Init var
$p_url = 'plugin.php?p='.basename(dirname(__FILE__));

# Saving configurations
if (isset($_POST['save']))
{
	$s->put('galleryinsert_enabled',!empty($_POST['galleryinsert_enabled']));
	$s->put('divbox_enabled',!empty($_POST['divbox_enabled']));
	$s->put('carousel_enabled',!empty($_POST['carousel_enabled']));
	$s->put('galleria_enabled',!empty($_POST['galleria_enabled']));
	$s->put('galleria_width',$_POST['galleria_width']);
	$s->put('galleria_height',$_POST['galleria_height']);
	$s->put('galleria_th_size',$_POST['galleria_th_size']);
	$s->put('meta_string',$_POST['meta_string']);
	$s->put('style_string',base64_encode($_POST['style_string']));
	$s->put('jgallery_enabled',!empty($_POST['jgallery_enabled']));

	http::redirect($p_url.'&config=1');
}

//if (isset($_POST['privatepicture_save']))
//{
//	$s->put('privatepicture_enabled',!empty($_POST['privatepicture_enabled']));
//	//$s->put('privatepicture_password',md5($_POST['privatepicture_password']));
//	$s->put('privatepicture_password',$_POST['privatepicture_password']);
//	
//	http::redirect($p_url.'&config=1');
//}


?>

<html>
<head>
  <title><?php echo $page_title; ?></title>
</head>
<body>
<?php

	echo dcPage::breadcrumb(
		array(
			html::escapeHTML($core->blog->name) => '',
			'<span class="page-title">'.$page_title.'</span>' => ''
		));

# Updating posts (5 by 5)
if (isset($_REQUEST['doupdate'])) {

	$params = array();
	$params['post_type']='';
	$params['sql'] = "AND (post_content LIKE '%::gallery%' OR post_excerpt LIKE '%::gallery%') ";
	$start = $_REQUEST['start'];
	$lim = 5;
	$params['limit'] = array($start,$lim);
	
	$postcount = $core->blog->getPosts($params,true)->f(0);
	if ($start<$postcount) {
		//echo '<p class="clear form-note info">'.sprintf(__('Updating posts %d to %d.'),$start+1,$start+$lim).'</p>';
		echo '<p class="clear form-note info">'.sprintf(__('Updating posts %d on %d.'),$start+1,$postcount).'</p>';
	
		$post = $core->blog->getPosts($params);
		while ($post->fetch()) {
			$cur = $core->con->openCursor($core->prefix.'post');
			$cur->cat_id =             $post->cat_id;
			$cur->post_dt =            $post->post_dt;
			$cur->post_format =        $post->post_format;
			$cur->post_password =      $post->post_password;
			$cur->post_url =           $post->post_url;
			$cur->post_lang =          $post->post_lang;
			$cur->post_title =         $post->post_title;
			$cur->post_excerpt =       $post->post_excerpt;
			$cur->post_excerpt_xhtml = $post->post_excerpt_xhtml;
			$cur->post_content =       $post->post_content;
			$cur->post_content_xhtml = $post->post_content_xhtml;
			$cur->post_notes =         $post->post_notes;
			$cur->post_status =        $post->post_status;
			$cur->post_selected =      $post->post_selected;
			$cur->post_open_comment =  $post->post_open_comment;
			$cur->post_open_tb =       $post->post_open_tb;
			$core->callBehavior('adminBeforePostUpdate',$cur,$post->post_id);
			$core->blog->updPost($post->post_id,$cur);
			$core->callBehavior('adminAfterPostUpdate',$cur,$post->post_id);
		}
	
		$new_url = $p_url.'&doupdate=1&start='.($start+$lim);
		echo
		'<script type="text/javascript">'."\n".
		"//<![CDATA\n".
		"window.location = '".$new_url."'\n".
		"//]]>\n".
		'</script>'.
		'<noscript><p><a href="'.html::escapeURL($new_url).'">'.__('next').'</a></p></noscript>';
	} else {
		echo '<p class="success">'.__('Update done').'</p>';
	}
}

# Messages
if (!empty($_REQUEST['config'])) {
  dcPage::success(__('Configuration successfully updated'));
}

?>

<?php 

# Baseline
//echo '<h2>'.html::escapeHTML($core->blog->name).' &rsaquo; GalleryInsert v'.$core->plugins->moduleInfo('GalleryInsert','version').'</h2>';

# Activation box
echo '<div>'.
	'<form action="'.$p_url.'" method="post" id="modal-form">'.
	'<div class="fieldset"><h4>'.__('Activation').'</h4>'.
		'<p><label class="classic" for="galleryinsert_enabled">'.
		form::checkbox('galleryinsert_enabled','1',$s->galleryinsert_enabled).
		__('Enable GalleryInsert').'</label></p>'.
		'<p><label style="margin-left: 30px;" class="classic" for="divbox_enabled">'.
		form::checkbox('divbox_enabled','1',$s->divbox_enabled).
		__('Enable Divbox').'</label></p>'.
		'<p><label style="margin-left: 30px;" class="classic" for="carousel_enabled">'.
		form::checkbox('carousel_enabled','1',$s->carousel_enabled).
		__('Enable Carousel').'</label></p>'.
		'<p><label style="margin-left: 30px;" class="classic" for="jgallery_enabled">'.
		form::checkbox('jgallery_enabled','1',$s->jgallery_enabled).
		__('Enable jgallery').'</label></p>'.
		'<p><label style="margin-left: 30px;" class="classic" for="galleria_enabled">'.
		form::checkbox('galleria_enabled','1',$s->galleria_enabled).
		__('Enable Galleria').'</label></p>'.
		'<p>'.
		'<label style="margin-left: 60px;" class="classic" for="galleria_width">'.
		__('Galleria Width')." ".form::field('galleria_width',5,20,$s->galleria_width).'</label>'.
		'<label class="classic" for="galleria_height">'.
		__('Galleria Height')." ".form::field('galleria_height',5,20,$s->galleria_height).'</label>'.
		'<label class="classic" for="galleria_th_size">'.
		__('Galleria Thumbnail Size')." ".form::field('galleria_th_size',5,20,$s->galleria_th_size).'</label>'.
		'<a href="#" onclick="$(\'#galleria_help\').toggle(\'fast\');return false;">+ '.__('Help').'</a>'.
		'</p>'.
		'<p class="clear form-note info" id="galleria_help" style="display:none">'.
		__('Galleria Width is given in percentage (good for responsive design) or in pixel. Ex : 100% or 500.').'<BR>'.
		__('Galleria Height is given in pixel or in width ratio (good for responsive design). Ex : 450 or 0.5625.').'<BR>'.
		__('Galleria Thumbnail Size is given in pixel. Ex : 70.').
		'</p>'.
		'<p>'.
		'<label class="classic" for="meta_string">'.
		__('Metadata string')." ".
		form::field('meta_string',70,255,$s->meta_string).
		'</label>'.
		'<a href="#" onclick="$(\'#meta_string_help\').toggle(\'fast\');return false;">+ '.__('Help').'</a>'.
		'</p>'.
		'<p class="clear form-note info" id="meta_string_help" style="display:none">'.
		__('The metadata string is used to add picture metadata (if available) after the picture title.').'<BR>'.
		__('Possible insertions are :').'<BR>'.
		'- %Title%<BR>- %Description%<BR>- %Location%<BR>- %DateTimeOriginal%<BR>- %Make%<BR>- %Model%<BR>- %Lens%<BR>- %ExposureProgram%<BR>- %Exposure%<BR>- %FNumber%<BR>- %ISOSpeedRatings%<BR>- %FocalLength%<BR>- %ExposureBiasValue%<BR>- %MeteringMode%'.
		'</p>'.
		'<p><label class="classic" for="style_string">'.
		__('Style string')."<BR>".
		form::textarea('style_string',100,5,html::escapeHTML(base64_decode($s->style_string))).
		'</label></p>'.
	'<p class="clear"><input type="submit" name="save" value="'.__('Save configuration').'" />'.$core->formNonce().'</p>'.
	'</div>'.
'</form>'.
'</div>';


# Private pictures box
//echo '<div>'.
//	'<form action="'.$p_url.'" method="post" id="modal-form">'.
//	'<fieldset><legend>'.__('Private picture').'</legend>'.
//		'<p><label class="classic" for="privatepicture_enabled">'.
//		form::checkbox('privatepicture_enabled','1',$s->privatepicture_enabled).
//		__('Enable private pictures').'</label></p>'.
//		'<p><label class="classic" for="privatepicture_password">'.
//		__('Password for private pictures')." ".
//		form::field('privatepicture_password',20,255,$s->privatepicture_password).
//		'</label></p>'.
//	'<p class="clear"><input type="submit" name="privatepicture_save" value="'.__('Save configuration').'" />'.$core->formNonce().'</p>'.
//	'</fieldset>'.
//'</form>'.
//'</div>';


# Liste les posts contenant une galerie
$params = array();
$params['post_type']='';
$params['sql'] = "AND (post_content LIKE '%::gallery%' OR post_excerpt LIKE '%::gallery%') ";

$post = $core->blog->getPosts($params);

$postliste = '';
$postfound=false;
while ($post->fetch()) {
		$postfound=true;
		//$postliste .= "Post " . $post->post_id . " : <a target='_blank' href='".$post->getURL()."'>" . $post->post_title . '</a>'."<BR />\n";
		$postliste .= ucfirst($post->post_type) . " " . $post->post_id . " : <a target='_blank' href='".$post->getURL()."'>" . $post->post_title . '</a>'."<BR />\n";
}

# List of posts box
echo '<div>'.
	'<form action="'.$p_url.'" method="post" id="modal-form">'.
	'<div class="fieldset"><h4>'.__('List of posts with gallery').'</h4>'.
		'<p>';
		echo $postliste;
echo '</p>';

if (!$postfound) {
	echo '<p class="clear form-note warning">'. __('No post with gallery found') . '</p>';
} else {
	echo '<p>'.form::hidden(array('type'),'modal').'</p>'.
	'<p class="clear"><input type="submit" name="doupdate" value="'.__('Update gallery in posts').'" />'.$core->formNonce().form::hidden('start','0').'</p>'.
	'<p class="clear form-note info">'.__('Update of posts can be useful after a plugin upgrade, it will regenerate the gallery in all posts.<br />It is recommended to make a backup of your database before... Just in case !').'</p>';
}
echo '</div>'.
'</form>'.
'</div>';

?>

<?php dcPage::helpBlock('galleryinserthelp');?>

</body>
</html>