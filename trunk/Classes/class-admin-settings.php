<?php
/*
Plugin Name: SoundCloud Sound Competition
Plugin URI: http://webhjelpen.no/wordpress-plugins/host-soundcloud-sound-contest-in-wordpress/
Description: Host your own Sound Contest integrated with SoundCloud, users connect easy with SoundCloud to choose track to add to your competition. Everything within your WordPress web site.
Author: Kenneth Berentzen
Author URI: http://webhjelpen.no/wordpress-plugins/
License: Copyright 2012  Kenneth Berentzen  (email : berentzen@gmail.com)

		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License, version 2, as
		published by the Free Software Foundation.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program; if not, write to the Free Software
		Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//Admin Settings
function remixcomp_settings() {
	if (!current_user_can(10))  { //Admin users
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
    global $wpdb;

    _e(get_remixcomp_admin_header()); ?>

    <?php

    if($_POST['kenrmx_sc_redirect_uri']) {
            //Setting variables
            $ken_settings = array('kenrmx_sc_redirect_uri' => $_POST['kenrmx_sc_redirect_uri'], 
                                            'kenrmx_sc_client_id' => $_POST['kenrmx_sc_client_id'], 
                                            'kenrmx_sc_client_secret' => $_POST['kenrmx_sc_client_secret'], 
                                            'kenrmx_sc_remix_type' => $_POST['kenrmx_sc_remix_type'], 
                                            'kenrmx_wpsc_connect_page_url' => $_POST['kenrmx_wpsc_connect_page_url'], 
                                            'kenrmx_wpsc_entrees_page_url' => $_POST['kenrmx_wpsc_entrees_page_url'], 
                                            'kenrmx_wpsc_more_info_url' => $_POST['kenrmx_wpsc_more_info_url'], 
                                            'kenrmx_facebook_width' => $_POST['kenrmx_facebook_width'], 
                                            'kenrmx_facebook_comments' => $_POST['kenrmx_facebook_comments']
                                            );
            update_option('ken_remixcomp_settings', $ken_settings);
    }

    !is_array(get_option('ken_remixcomp_settings')) ? "" : extract(get_option('ken_remixcomp_settings'));

    ?>
    <h3>Soundcloud Account Details</h3>
    <form action="?page=<?php echo $_GET['page']; ?>" method="POST">
    <strong style="color:#999;">Soundcloud Redirect URI:</strong><br>
    <input type="text" size="80" name="kenrmx_sc_redirect_uri" value="<?php echo $kenrmx_sc_redirect_uri; ?>"/><br><br>
    <strong style="color:#999;">Soundcloud Client ID:</strong><br>
    <input type="text" size="80" name="kenrmx_sc_client_id" value="<?php echo $kenrmx_sc_client_id; ?>"/><br><br>
    <strong style="color:#999;">Soundcloud Client Secret:</strong><br>
    <input type="text" size="80" name="kenrmx_sc_client_secret" value="<?php echo $kenrmx_sc_client_secret; ?>"/><br><br>
    <strong style="color:#999;">Comp db name:</strong> <font color="green">Example -> mycomp1</font> <div style="color:#ccc;">(If you wanto make a new remix competition this is the new slug for registration in the database)</div>
    <input type="text" size="80" name="kenrmx_sc_remix_type" value="<?php echo $kenrmx_sc_remix_type; ?>"/><br><br>
    <input type='submit' value='Save settings'/><br><br>
    
    <h3>Current Contest Page</h3>
    <strong style="color:#999;">WordPress SoundCloud Connect Page URL:</strong><br>       
    <input type="text" size="80" name="kenrmx_wpsc_connect_page_url" value="<?php echo $kenrmx_wpsc_connect_page_url; ?>"/><br>
    <font color="#ccc">Place the shortcode below on this page (the page/url just above this text)</font><br>
    <input type="text" size="40" onclick="this.focus();this.select()" readonly="readonly" value="[soundcomp-add]"/><br><br>
    
    <strong style="color:#999;">WordPress List Entrees Page URL:</strong><br>
    <input type="text" size="80" name="kenrmx_wpsc_entrees_page_url" value="<?php echo $kenrmx_wpsc_entrees_page_url; ?>"/><br>
    <font color="#ccc">Place the shortcode below on this page (the page/url just above this text).</font><br>
    <input type="text" size="40" onclick="this.focus();this.select()" readonly="readonly" value="[soundcomp-entrees type='<?php echo $kenrmx_sc_remix_type; ?>']"/><br><br>
    
    <strong style="color:#999;">More information Page/Post URL:</strong> <br>
    <input type="text" size="80" name="kenrmx_wpsc_more_info_url" value="<?php echo $kenrmx_wpsc_more_info_url; ?>"/><br><br>
    
    <h3>Contest List Entrees</h3>
    <strong style="color:#999;">Facebook comments box width:</strong><br>
    <input type="text" size="80" name="kenrmx_facebook_width" value="<?php echo $kenrmx_facebook_width; ?>"/><br><br>
    <strong style="color:#999;">Facebook comments amount:</strong> <div style="color:#999;">(how many comments to show)</div>
    <input type="text" size="80" name="kenrmx_facebook_comments" value="<?php echo $kenrmx_facebook_comments; ?>"/><br><br>

    <input type='submit' value='Save settings'/><br><br>


    </form>    
    <?php
    
}

