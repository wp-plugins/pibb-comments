<?php
/*
Plugin Name: Pibb Comments
Plugin URI: https://pibb.com/plugins
Description: Embedd a Pibb thread for commenting at the end of your blog posts.
Author: Janrain, Inc.
Author URI: http://www.janrain.com/
Version: 0.1
*/

/*  Copyright 2007 Janrain, Inc. (email : support@janrain.com)
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
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

require_once(ABSPATH . 'wp-admin/upgrade-functions.php');

function pibb_install () {
   global $wpdb;
   
   $table_name = $wpdb->prefix . "pibbthreads";
   if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
      $sql = "CREATE TABLE " . $table_name . " (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  post_id mediumint(9) NOT NULL,
	  thread_id VARCHAR(55) NOT NULL,
	  UNIQUE KEY id (id)
	);";
      dbDelta($sql);
   }
}

function pibb_ensure_topic( $post_id ) {
   global $wpdb;
  
   $table_name = $wpdb->prefix . "pibbthreads";
   $thread_id = $wpdb->get_var("SELECT thread_id FROM $table_name where post_id='$post_id'");
   if ($thread_id) {
     return $post_id;
   }
   
   $post = & get_post($post_id);

   $pibb_channel_name = get_option('pibb_channel_name');
   $pibb_api_key = get_option('pibb_api_key');

   if ($pibb_channel_name && $pibb_api_key) {
     /* Now we can fetch a url from the web */
     $url = "https://pibb.com/pibb/embed_thread?channel_name=" .
            urlencode($pibb_channel_name) . "&thread_name=" .
            urlencode($post->post_title) . "&user_key=" .
            urlencode($pibb_api_key);
  
     $cmdline = escapeshellcmd("/usr/bin/env curl --insecure " . $url);
     exec($cmdline, $results, $return);
     if(!$return) {
        $insert = "INSERT INTO " . $table_name .
                  " (post_id, thread_id) " .
                  "VALUES ('" . $post_id . "','" . $wpdb->escape($results[0]) . "')";
  
        $results = $wpdb->query( $insert );
     } else {
       trigger_error("curl result code [" . $return . "] [" . $results . "]", E_USER_ERROR);
     }
   }
   
   return $post_id;
}

function pibbify_content( $content ) {
    global $post;
    global $wpdb;

    // if not in entry context, no link, bail
    if ( ! is_single() ) {
        return $content;
    }

    $table_name = $wpdb->prefix . "pibbthreads";
    $post_id = $post->ID;
    $thread_id = $wpdb->get_var("SELECT thread_id FROM $table_name where post_id='$post_id'");
    if ($thread_id) {
      $suffix = "<div style=\"height: 400px;\" id=\"__pibb_thread\"></div><script src=\"https://pibb.com/widget/thread/" . urlencode($thread_id) . "\" type=\"text/javascript\"></script>";
      $content.= $suffix;
    }

    return $content;
}

add_action('activate_pibb-comments.php', 'pibb_install');
add_action('publish_post', 'pibb_ensure_topic');
add_action('the_content','pibbify_content');
add_action('admin_menu', 'pibb_rc_config_page');

function pibb_rc_config_page() {
  global $wpdb;
  if ( function_exists('add_submenu_page') )
    add_submenu_page('plugins.php', __('Pibb Configuration'), __('Pibb Configuration'),
                     'manage_options', __FILE__, 'pibb_rc_conf');
}


function pibb_rc_conf() {
  if ( isset($_POST['src_frmsubmit']) ) {
    if ( !current_user_can('manage_options'))
      die(__('Cheatin&#8217; uh?'));
      
    $pibb_channel_name = trim($_POST['pibb_channel_name']);
    $pibb_api_key = trim($_POST['pibb_api_key']);

    update_option('pibb_channel_name', $pibb_channel_name);
    update_option('pibb_api_key', $pibb_api_key);
  } else {
    $pibb_channel_name = get_option('pibb_channel_name');
    $pibb_api_key = get_option('pibb_api_key');
 }
?>
<div class="wrap">
<h2><?php _e('Pibb Comments Configuration'); ?></h2>
<form action="" method="post" style="margin: auto; width: 25em; ">
<input type="hidden" name="src_frmsubmit" value="1"/>
<h3><label>Pibb Channel Name:</label></h3>
<p>
  <input type="text" name="pibb_channel_name" value="<?php echo $pibb_channel_name; ?>"/>
</p>
<h3><label>Pibb API Key:</label></h3>
<p>
  <input type="text" name="pibb_api_key" value="<?php echo $pibb_api_key; ?>"/>
</p>
<p>
  <input type="submit"/>
</p>
</form>
</div>
<?php
}
?>
