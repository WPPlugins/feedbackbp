<?php

/*
Plugin Name: Feedback by Paragraph
Plugin URI: http://www.andydickinson.net/feedback-by-paragraph-plugin/
Author: Andy Dickinson
Author URI: http://andydickinson.net
Description:Displays a pop up comment on each paragraph. Designed to provide student feedback.
Version: 0.6

Copyright 2009  Andy Dickinson  (email : judgemonkey@mac.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


	//add_filter('the_content', 'FBP_content');
	add_filter('the_content', 'FBP_preFlight');
	add_filter('comments_array', 'FBPfiltercomments', 1);
	add_filter('get_comments_number', 'FBPfiltercommentcount');
	add_filter('comments_template', 'FBPcommentsmessage');

	function tb_enqueue() {
   	 wp_enqueue_script('jquery');
   	 wp_enqueue_script('thickbox');
   	 wp_enqueue_style('thickbox');
	}

//Checks user levels based on the admin options. 

function FBP_preFlight ($content) {
global $post; 
global $wpdb;
global $current_user;
global $user_level;

( !function_exists( get_currentuserinfo()) )   ;

$BY=$post->post_author;
$fbpOptions = get_option('feedbackBP');
$fbpOptsee =$fbpOptions['whocanseecomments'];
$fbpOptdo =$fbpOptions['whocancomment'];
$fbpRPcomm = $fbpOptions['respectpostcomment'];
$BY=$post->post_author;

if (($fbpRPcomm == '1') && ($post->comment_status=='closed')) {

return $content;

}

if ($fbpOptsee == 'all') {
$content=FBP_content($content,$fbpOptdo);
return $content;
}elseif (($fbpOptsee == 'LoggedIn') && (is_user_logged_in() )) {

$content=FBP_content($content,$fbpOptdo);
return $content;

}elseif (($fbpOptsee == 'AuthorAdmin') && (current_user_can('manage_options') || $BY == $current_user->ID)) {

$content=FBP_content($content,$fbpOptdo);
return $content;

}elseif ($fbpOptsee == 'Admin' && current_user_can('manage_options') ) {

$content=FBP_content($content,$fbpOptdo);
return $content;

}else {
return $content;
}

}


function trythis() {
global $comments;



$trythis='<ol>';

foreach ($comments as $comment) :
						if (get_comment_type() == "comment")
					{ 					
							$trythis.= '<li id="comment-'.comment_ID().'" >';
							$trythis.= comment_author_link().'<br />';
							$trythis.= comment_text();
							$trythis.='</li>';
						} 
 endforeach;
				$trythis.='</ol>';


return $trythis;
}


//START OF THE PLUGIN OPTIONS PAGE STUFF

//functions to handle the options pages begin below
//they are based on http://planetozh.com/blog/2009/05/handling-plugins-options-in-wordpress-28-with-register_setting/


// Init plugin options to white list our options
function feedbackBPoptions_init(){
	register_setting( 'feedbackBPoptions_options', 'feedbackBP', 'feedbackBPoptions_validate' );
	
}

// Add menu page
function feedbackBPoptions_add_page() {
	add_options_page('feedbackBP Options', 'feedbackBP Options', 'manage_options', 'feedbackBP', 'feedbackBPoptions_do_page');
}

// Draw the menu page itself
function feedbackBPoptions_do_page() {
	?>
<div class="wrap">
		<h2>Feedback By Paragraph Options</h2>
		<form method="post" action="options.php">
			<?php settings_fields('feedbackBPoptions_options'); ?>
			<?php $options = get_option('feedbackBP'); ?>
 
 				<table class="form-table">
					<tr valign="top">
					<th scope="row">Display Comment message</th>
						<td><input name="feedbackBP[commentmessage]" type="checkbox" value="1" <?php checked('1', $options['commentmessage']); ?> /></td>
						<td><em> Uncheck this to remove the "Paragrah comments will not be displayed below" message above the comment form</em></td>
					</tr>
					<th scope="row">Respect the post comment setting</th>
						<td><input name="feedbackBP[respectpostcomment]" type="checkbox" value="1" <?php checked('1', $options['respectpostcomment']); ?> /></td>
						<td><em>Turn off feedbackBP on posts where the comments are closed by the individual post settings.</em></td>
					</tr>
					<tr valign="top">
					<th scope="row">Who can comment using feedbackBP</th>
						<td><select name="feedbackBP[whocancomment]">
								<option value="all" <?php echo ($options['whocancomment'] == "all") ? 'selected="selected"' : ''; ?> >Everyone</option>
								<option value="LoggedIn" <?php echo ($options['whocancomment'] == "LoggedIn") ? 'selected="selected"' : ''; ?> >Logged in users</option>
								<option value="AuthorAdmin" <?php echo ($options['whocancomment'] == "AuthorAdmin") ? 'selected="selected"' : ''; ?> >Just the Author and Admin</option>
								<option value="Admin" <?php echo ($options['whocancomment'] == "Admin") ? 'selected="selected"' : ''; ?>>Just the Blog Admin</option>

						</select></td>
					</tr>
						<tr valign="top">
						<th scope="row">Who can see the comments</th>
						<td><select name="feedbackBP[whocanseecomments]">
								<option value="all" <?php echo ($options['whocanseecomments'] == "all") ? 'selected="selected"' : ''; ?> >Everyone</option>
								<option value="LoggedIn" <?php echo ($options['whocanseecomments'] == "LoggedIn") ? 'selected="selected"' : ''; ?> >Logged in users</option>
								<option value="AuthorAdmin" <?php echo ($options['whocanseecomments'] == "AuthorAdmin") ? 'selected="selected"' : ''; ?> >Just the Author and Admin</option>
								<option value="Admin" <?php echo ($options['whocanseecomments'] == "Admin") ? 'selected="selected"' : ''; ?>>Just the Blog Admin</option>
						</select></td>
					</tr>
				</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
	<?php	
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function feedbackBPoptions_validate($input) {
	// Our first value is either 0 or 1
	//$input['commentmessage'] = ( $input['commentmessage'] == 1 ? 1 : 0 );
	
	
	return $input;
}

//END OF THE OPTIONS PAGE


//START OF THE FEEDBACK BY PARAGRAPH GUBBINS PROPER
function FBPcommentsmessage () {
$fbpOptions = get_option('feedbackBP');

if ($fbpOptions['commentmessage'] == '1') {

echo '<div id="FBPcommentmessage"><p>Paragrah comments will not be displayed below</p></div>';}
return false;
}

function FBPcss() {
 	echo "\n".'<link href="'.get_option('siteurl').'/wp-content/plugins/feedbackbp/FBPstyles.css " type="text/css" rel="stylesheet" media="screen"/>'."\n" ;
}

function FBPfiltercomments ($commentarray) {


$comments = array_filter($commentarray,"stripFBPcomments");
return $comments;

}

function FBPfiltercommentcount ($count) {
global $id;
    if (empty($id)) { return $count; }
    $comments = get_approved_comments((int)$id);
   
$comments = array_filter($comments,"stripFBPcomments");
return sizeof($comments);

}

function stripFBPcomments($var) {
    if ($var->comment_type == 'trackback' || $var->comment_type == 'pingback'|| $var->comment_type == ''|| $var->comment_type == 'comment') { return true; }
    return false;
}


function FBP_content($content,$whocando) {
global $post; 
global $wpdb;
global $current_user;


// This bit will throw back the orginal content. Put in for those themes that display the whole post on a page
if(!is_single()){

return $content;

}
  ( !function_exists( get_currentuserinfo()) )   ;

$fbpOptions = get_option('feedbackBP');



$BY=$post->post_author;
$ID = $post->ID;

// need to think about this to make sure that we can get any image divs in here as a page element
$content=do_shortcode( $content );
//$content = str_replace("\r\n","",$content); 
$ptag=preg_split("#</p[^>]*>#U",$content,0, PREG_SPLIT_NO_EMPTY); // Find the <p> tags in the content

$i = 0;
foreach ($ptag as $ptagchange) {
//Not sure why I need to do this bit but there seemed to be an extra link added. Couldnt work out why. May be to do with where the link gets added or the way I'm splitting
//My knowledge of regex and preg_split is already at its maximum so I can live with this till someone points out the reason
if ($ptagchange == $ptag[0]) {
$stuff.= $ptag[0];
}else{
$FBPcc = FBPcommentcount($ID,$i);
$FBPcomms = FBPcommentlist($ID,$i);
$FBPlink="<a href=\"#TB_inline?height=400&width=350&inlineId=popupcomments-".$i." \" class=\"thickbox spch-bub-inside\"><em>".$FBPcc."</em></a>";

if ($whocando == 'all') {
$FBPform=FBPcommentForm($ID,$i); 

}elseif (($whocando == 'LoggedIn') && (is_user_logged_in() )) {

$FBPform=FBPcommentForm($ID,$i); 

}
elseif (($whocando == 'AuthorAdmin') && (current_user_can('manage_options') || $BY == $current_user->ID)) {

$FBPform=FBPcommentForm($ID,$i); 

}elseif ($whocando == 'Admin' && current_user_can('manage_options') ) {

$FBPform=FBPcommentForm($ID,$i); 

}else {

$FBPform='<span class="FBPnocomment">You are not allowed to comment at this level</span>';

}

$stuff.=$FBPlink.'<div id="popupcomments-'.$i.'" style="display:none;visibility:hidden"><div class="FBPcontent"><H2> Comments </h2>'.$FBPcomms.''.$FBPform.'</div> </div></p>'.$ptagchange;


$i++;
}
}


$content = $stuff;


return $content;
echo trythis();
}


function FBPcommenttrial($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; ?>
   <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
     <div id="comment-<?php comment_ID(); ?>">
      <div class="comment-author vcard">
         <?php echo get_avatar($comment,$size='48',$default='<path_to_url>' ); ?>

         <?php printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>'), get_comment_author_link()) ?>
      </div>
      <?php if ($comment->comment_approved == '0') : ?>
         <em><?php _e('Your comment is awaiting moderation.') ?></em>
         <br />
      <?php endif; ?>

      <div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)'),'  ','') ?></div>

      <?php comment_text() ?>

      <div class="reply">
         <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
      </div>
     </div>
<?php
        }


function FBPcommentForm($ID,$i) {
global $current_user;


(!function_exists( get_currentuserinfo()));

$form_action    = get_permalink();// send to itself and grab the variables in another function
$author_default = $current_user->display_name;
$email_default  =$current_user->user_email;

if (get_option('comment_moderation')==1) {

$mod='0';

}else{ 

$mod='1';

}

$FBPformcode='<div class="FBPcommentform">
     
   <form action="'.$form_action.'" method="post" enctype="multipart/form-data" style="text-align: left">';
   
if (is_user_logged_in() ){
 
  $FBPformcode .='<p>'.$current_user->display_name.'</p>';
	
}else{   
  
 $FBPformcode .=   '<p><input type="text" name="author" id="author" value="'.$author_default.'" size="22" /> <label for="author"><small>Your name*</small></label></p>
    <p><input type="text" name="email" id="email" value="'.$email_default.'" size="22" /> <label for="email"><small>Your e-mail*</small></label></p>';
   
}   

$FBPformcode.='<p><label for="FBPcomment">Leave a comment</label></p>
	<p><textarea name="comment" id="comment" cols="100%" rows="10" tabindex="4"></textarea></p>
	<p><input name="submit" type="submit" id="submit" tabindex="5" value="Submit Comment" />';
	
if (is_user_logged_in() ){
 
  $FBPformcode .='<input type="hidden" name="author" id="author" value="'.$current_user->display_name.'" />
    <input type="hidden" name="email" id="email" value="'.$current_user->user_email.'" />';
	
}  	
	
$FBPformcode .='<input type="hidden" name="comment_post_ID" value="'.$ID.'"id="comment_post_ID" />
	<input type="hidden" name="comment_type" value="feedbackbypar'.$i.'" id="comment_type" />
	<input type="hidden" name="comment_approved" value ="'.$mod.'" id="comment_approved" />
	<input type="hidden" name="comment_form_submitted" value="1">
   
   </form></div>';

return $FBPformcode;
}

function FBPcommentcount($FBPpostID, $FBPparID){
global $wpdb;
global $post;
global $current_user;

$r=$FBPpostID;
$w=$FBPparID;

$ccQuery=$wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_post_ID=$r AND comment_type='feedbackbypar$w' AND comment_approved=1");

    	return $ccQuery;
	
}

function FBPcommentlist($FBPpostID, $FBPparID){
global $wpdb;
global $post;
global $current_user;

  ( !function_exists( get_currentuserinfo()) )   ;

$r=$FBPpostID;
$w=$FBPparID;
$tempcommlist=' ';



$commentlist = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID=$r AND comment_type='feedbackbypar$w' AND comment_approved=1");

if (empty($commentlist)) {

return "<span class='FBPnocomment'> No Comments </span>";

}else {

$tempcommlist ='<div class="FBPcomments"> <ol>';

foreach ($commentlist as $comm) {

$tempcommlist .= '<li><p class="FBPauthor">'.$comm->comment_author.'</p><p class="FBPcommentcontent">'.$comm->comment_content.'</p></li>';
}

$tempcommlist .='</ol></div>';

return $tempcommlist;
}

}


function comment_form_process() {

	global $wpdb;
	global $post;
	global $current_user;
	
 if ( !isset($_POST['comment_form_submitted']) ) return;
 $header_location = get_permalink();	
 $author  = ( isset($_POST['author']) )  ? trim(strip_tags($_POST['author'])) : null;
 $email   = ( isset($_POST['email']) )   ? trim(strip_tags($_POST['email'])) : null;
 $message = ( isset($_POST['comment']) ) ? trim(strip_tags($_POST['comment'])) : null;
 $comment_type = ( isset($_POST['comment_type']) ) ? trim($_POST['comment_type']) : null;
 $comment_post_ID = ( isset($_POST['comment_post_ID']) ) ? trim($_POST['comment_post_ID']) : null;
 $comment_moderate = ( isset($_POST['comment_approved']) ) ? trim($_POST['comment_approved']) : null;





$wpdb->query( $wpdb->prepare( "INSERT INTO $wpdb->comments(comment_author, comment_author_email,comment_content, comment_post_ID, comment_type,comment_approved )VALUES ( %s, %s, %s, %d, %s,%d )", $author, $email, $message,$comment_post_ID, $comment_type,$comment_moderate  ) );
header('Location: ' . $_SERVER['HTTP_REFERER']);

  exit();

}


function tb_inject() {

echo "\n" .'<!-- You have the Feedback plugin activated so if you have other plugins that use thickbox and you are getting odd problems it may be because there is more than one plugin calling all the javascript -->'."\n";

}

function _load_thkBox_images() {

// Fixes the issue with the WP core reference of the close and load images
// that are set to to be relative to the images making it bloody difficult to call.
// code from http://www.PhoenixHomes.com/tech/thickbox-content

		echo "\n" . '<script type="text/javascript">tb_pathToImage = "' . get_option('siteurl') . '/wp-includes/js/thickbox/loadingAnimation.gif";tb_closeImage = "' . get_option('siteurl') . '/wp-includes/js/thickbox/tb-close.png";</script>'. "\n";			
	}

add_action('admin_init', 'feedbackBPoptions_init' );
add_action('admin_menu', 'feedbackBPoptions_add_page');
add_action('init', 'comment_form_process',1);
add_action('wp_head', 'tb_enqueue', 1);
add_action('wp_head', 'tb_inject', 10);
add_action('wp_head', 'FBPcss');
add_action( 'wp_footer', '_load_thkBox_images', 11 );


?>