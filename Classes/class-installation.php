<?php
/*
Plugin Name: SoundCloud Sound Competition
Plugin URI: http://webhjelpen.no/wordpress-plugin/soundcloud-sound-contest/
Description: Host your own Sound Contest integrated with SoundCloud, users connect easy with SoundCloud to choose track to add to your competition. Everything within your WordPress web site.
Author: Kenneth Berentzen
Author URI: http://webhjelpen.no/wordpress-plugin/
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

function remixcomp_install() {
      
    global $wpdb;
        
    $table = $wpdb->prefix."ken_remixcomp_users";
    
    $structure = "CREATE TABLE $table (
        rcu_id bigint(20) NOT NULL AUTO_INCREMENT,
    	rcu_email text NULL,    
        rcu_sc_id bigint(20) NULL,
	rcu_sc_kind text NULL,
	rcu_sc_permalink text NULL,
	rcu_sc_username text NULL,
	rcu_sc_uri text NULL,
	rcu_sc_permalink_url text NULL,
	rcu_sc_avatar_url text NULL,
        rcu_sc_country text NULL,
        rcu_sc_full_name text NULL, 
	rcu_sc_description longtext NULL,
	rcu_sc_city text NULL,
	rcu_sc_discogs_name text NULL,
	rcu_sc_myspace_name text NULL,
	rcu_sc_website text NULL,
	rcu_sc_website_title text NULL, 
	rcu_sc_online int NULL, 
	rcu_sc_track_count int NULL, 
        rcu_sc_playlist_count int NULL, 
        rcu_sc_public_favorites_count int NULL, 
        rcu_sc_followers_count int NULL, 
        rcu_sc_followings_count int NULL, 
        rcu_sc_plan text NULL, 
        rcu_sc_private_tracks_count int NULL, 
        rcu_sc_private_playlists_count int NULL, 
        rcu_sc_primary_email_confirmed int NULL, 
        rcu_created_date datetime NULL,
        rcu_created_by text NULL,
        rcu_modified_date datetime NULL,
        rcu_modified_by text NULL,
	UNIQUE KEY rcu_id (rcu_id)
    );";
        
    $wpdb->query($structure);
    
    $table = $wpdb->prefix."ken_remixcomp_entrees";
    
    $structure = "CREATE TABLE $table (
        rce_id bigint(20) NOT NULL AUTO_INCREMENT,
        rce_rcu_id bigint(20) NULL, 
        rce_remix text NULL,
	rce_remix_status text NULL,
        rce_sct_kind text NULL,
        rce_sct_id text NULL,
        rce_sct_user_id text NULL,
        rce_sct_duration text NULL,
        rce_sct_original_content_size text NULL,
        rce_sct_sharing text NULL,
        rce_sct_tag_list text NULL,
        rce_sct_permalink text NULL,
        rce_sct_downloadable text NULL,
        rce_sct_purchase_url text NULL,
        rce_sct_label_id text NULL,
        rce_sct_purchase_title text NULL,
        rce_sct_genre text NULL,
        rce_sct_title text NULL,
        rce_sct_description longtext NULL,
        rce_sct_label_name text NULL,
        rce_sct_release text NULL,
        rce_sct_track_type text NULL,
        rce_sct_original_format text NULL,
        rce_sct_license text NULL,
        rce_sct_uri text NULL,
        rce_sct_user_playback_count int NULL,
        rce_sct_user_favorite text NULL,
        rce_sct_permalink_url text NULL,
        rce_sct_artwork_url text NULL,
        rce_sct_waveform_url text NULL,
        rce_sct_stream_url text NULL,
        rce_sct_download_url text NULL,
        rce_sct_playback_count int NULL,
        rce_sct_download_count int NULL,
        rce_sct_favoritings_count int NULL,
        rce_sct_comment_count int NULL,
        rce_sct_attachments_uri text NULL,
        rce_sct_secret_token text NULL,
        rce_sct_secret_uri text NULL,
	rce_vote_count int NOT NULL default '0', 
	rce_created_date datetime NOT NULL,
        rce_created_by VARCHAR(100) NOT NULL,
        rce_modified_date datetime NULL,
        rce_modified_by VARCHAR(100) NULL,
	UNIQUE KEY rce_id (rce_id)
    );";
        
    $wpdb->query($structure);

	$table = $wpdb->prefix."ken_remixcomp_voting";
        $structure = "CREATE TABLE $table (
        rcv_id bigint(20) NOT NULL AUTO_INCREMENT,
	rcv_rce_id bigint(20) NOT NULL, 
        rcv_remix text NULL,
        rcv_rate int NULL,
	rcv_ip text NULL,
	rcv_session text NULL,
	rcv_user_agent text NULL,
	rcv_created_date datetime NOT NULL,
        rcv_created_by VARCHAR(100) NOT NULL,
	UNIQUE KEY rcv_id (rcv_id)
    );";
        
    $wpdb->query($structure);

}
