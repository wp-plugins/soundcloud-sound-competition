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

//Listing page admin
function remixcomp_list_partisipants() {
	if (!current_user_can(10))  { //Admin users
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
    global $wpdb;

_e(get_remixcomp_admin_header());

_e("<br><strong>Sound contests</strong>: ");

$wpdb->show_errors(); 
// Give wpdb with global scope
global $wpdb;

!is_array(get_option('ken_remixcomp_settings')) ? "" : extract(get_option('ken_remixcomp_settings'));

if($_GET['set_star'] && preg_match('/^\d\d*$/', $_GET['set_star']) ){
    //Update star
    $wpdb->update( 
            $wpdb->prefix."ken_remixcomp_entrees" , 
            array( 
                    'rce_remix_status' => 'star'
            ), 
            array (
                'rce_id' => $_GET['set_star']
            )
    );
}

if($_GET['remove_star'] && preg_match('/^\d\d*$/', $_GET['remove_star']) ){
    //Remove star
    $wpdb->update( 
            $wpdb->prefix."ken_remixcomp_entrees" , 
            array( 
                    'rce_remix_status' => null
            ), 
            array (
                'rce_id' => $_GET['remove_star']
            )
    );
}

/***************************************************************************************
			  LIST ALL ADMIN
****************************************************************************************/

        $sql_all = "SELECT rce_remix, count(*) as teller 
            FROM ".$wpdb->prefix."ken_remixcomp_entrees
            group by rce_remix;";
	$all_results = $wpdb->get_results($sql_all);  // Run our query, getting results as an object
	
        if (!empty($all_results)) { // If the query returned something
            foreach ($all_results as $all_result) {  // Loop though our results!
                
                if( ($kenrmx_sc_remix_type == $all_result->rce_remix && $_GET['rmx_slug'] == $kenrmx_sc_remix_type) ||
                    ($kenrmx_sc_remix_type == $all_result->rce_remix && $_GET['rmx_slug'] == null) ||
                    ($_GET['rmx_slug'] == $all_result->rce_remix && $_GET['rmx_slug'] != $kenrmx_sc_remix_type) ){
                    _e("".$all_result->rce_remix." ( ".$all_result->teller." participants )  ");
                }
                else {
                    _e("<a href='?page=".$_GET['page']."&rmx_slug=".$all_result->rce_remix."'>".$all_result->rce_remix."</a> ( ".$all_result->teller." participants )  ");
                }
                
            }
        }    

        if( $_GET['rmx_slug'] ){
            $remix_db_slug = $_GET['rmx_slug'];
        }
        else {
            $remix_db_slug = $kenrmx_sc_remix_type;
        }
	
        // This query selects 
	$sql_star = "
            SELECT * FROM ".$wpdb->prefix."ken_remixcomp_entrees
            JOIN ".$wpdb->prefix."ken_remixcomp_users ON rcu_id = rce_rcu_id 
            WHERE rce_remix_status = 'star' 
            AND rce_remix='".$remix_db_slug."' 
            ORDER BY rce_id DESC;";
	$star_results = $wpdb->get_results($sql_star);  // Run our query, getting results as an object
        // This query selects 
	$sql = "
            SELECT * FROM ".$wpdb->prefix."ken_remixcomp_entrees
            JOIN ".$wpdb->prefix."ken_remixcomp_users ON rcu_id = rce_rcu_id 
            WHERE (rce_remix_status is null OR rce_remix_status != 'star')
            AND rce_remix='".$remix_db_slug."' 
            ORDER BY rce_id DESC;";
	$results = $wpdb->get_results($sql);  // Run our query, getting results as an object
	
        //_e("<br><br>".$sql_star."<br><br>");
        //_e($sql."<br><br>");
        
	?>

	<table cellspacing="0"><tr><th>
	
	<table class="widefat post fixed" cellspacing="0">
		<thead>
			<tr>
				<th class="manage-column" width="10" scope="col">ID</th>
				<th class="manage-column" width="10" scope="col">Star</th>
				<th class="manage-column" width="100" scope="col">Name</th>
				<th class="manage-column" width="50" scope="col">Track name</th>
				<th class="manage-column" width="100" scope="col">Email</th>
				<th class="manage-column" width="10" scope="col">Votes</th>
				<th class="manage-column" width="50" scope="col">SC Date</th>
			</tr>
		</thead>
		<tbody>	
	<?php
		if (!empty($star_results)) { // If the query returned something
			foreach ($star_results as $star_result) {  // Loop though our results!
				$star_result->rce_created_date = date("j F, Y (H:i)",strtotime($star_result->rce_created_date));  // Format the date
				
	?>
				<tr id="rcp-id-<?php echo $star_result->rce_id; ?>" valign="top">
					<th scope="row" style="font-weight:normal"><?php echo esc_attr($star_result->rce_id); ?></th>
                                        <th scope="row" style="font-weight:normal"><a href="?page=<?php _e($_GET['page']); ?>&rmx_slug=<?php _e(urlencode($remix_db_slug)); ?>&remove_star=<?php _e($star_result->rce_id); ?>"><img src="<?php _e( plugins_url('soundcloud-sound-competition/images/star_full.png') ); ?>" border=0></a></th>
					<th scope="row" style="font-weight:bold"><a href="<?php echo esc_attr($star_result->rcu_sc_permalink_url ); ?>" target="new"><?php echo esc_attr($star_result->rcu_sc_username); ?></a></th>
					<th scope="row" style="font-weight:normal"><object height="18" width="100%"> <param name="movie" value="http://player.soundcloud.com/player.swf?url=<?php echo esc_attr($star_result->rce_sct_secret_uri); ?>&amp;auto_play=false&amp;player_type=tiny&amp;font=Arial&amp;color=000000"></param> <param name="allowscriptaccess" value="always"></param> <param name="wmode" value="transparent"></param><embed wmode="transparent" allowscriptaccess="always" height="18" src="http://player.soundcloud.com/player.swf?url=<?php echo esc_attr($star_result->rce_sct_secret_uri); ?>&amp;auto_play=false&amp;player_type=tiny&amp;font=Arial&amp;color=000000" type="application/x-shockwave-flash" width="100%"></embed> </object>   </th>
					<th scope="row" style="font-weight:normal"><?php echo esc_attr($star_result->rcu_email); ?></th>
					<th scope="row" style="font-weight:normal"><?php echo esc_attr($star_result->rce_vote_count); ?></th>
					<th scope="row" style="font-weight:normal"><?php echo esc_attr($star_result->rce_created_date ); ?></th>
				</tr>
	<?php
			} //Close loop
		} //Close if return somthing
	?>				

	<?php
		if (!empty($results)) { // If the query returned something
			foreach ($results as $result) {  // Loop though our results!
				$result->rce_created_date = date("j F, Y (H:i)",strtotime($result->rce_created_date));  // Format the date
				
	?>
				<tr id="rcp-id-<?php echo $result->rce_id; ?>" valign="top">
					<th scope="row" style="font-weight:normal"><?php echo esc_attr($result->rce_id); ?></th>
                                        <th scope="row" style="font-weight:normal"><a href="?page=<?php _e($_GET['page']); ?>&rmx_slug=<?php _e(urlencode($remix_db_slug)); ?>&set_star=<?php _e($result->rce_id); ?>"><img src="<?php _e( plugins_url('soundcloud-sound-competition/images/star_empty.png') ); ?>" border=0></a></th>
					<th scope="row" style="font-weight:bold"><a href="<?php echo esc_attr($result->rcu_sc_permalink_url ); ?>" target="new"><?php echo esc_attr($result->rcu_sc_username); ?></a></th>
					<th scope="row" style="font-weight:normal"><object height="18" width="100%"> <param name="movie" value="http://player.soundcloud.com/player.swf?url=<?php echo esc_attr($result->rce_sct_secret_uri); ?>&amp;auto_play=false&amp;player_type=tiny&amp;font=Arial&amp;color=000000"></param> <param name="allowscriptaccess" value="always"></param> <param name="wmode" value="transparent"></param><embed wmode="transparent" allowscriptaccess="always" height="18" src="http://player.soundcloud.com/player.swf?url=<?php echo esc_attr($result->rce_sct_secret_uri); ?>&amp;auto_play=false&amp;player_type=tiny&amp;font=Arial&amp;color=000000" type="application/x-shockwave-flash" width="100%"></embed> </object>   </th>
					<th scope="row" style="font-weight:normal"><?php echo esc_attr($result->rcu_email); ?></th>
					<th scope="row" style="font-weight:normal"><?php echo esc_attr($result->rce_vote_count); ?></th>
					<th scope="row" style="font-weight:normal"><?php echo esc_attr($result->rce_created_date ); ?></th>
				</tr>
	<?php
			} //Close loop
		} //Close if return somthing
	?>				
		</tbody>
	</table>
	
	</th><th width="25"></th></tr></table>

<?php
    
}