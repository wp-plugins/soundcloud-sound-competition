<?php
/*
Plugin Name: SoundCloud Sound Competition
Plugin URI: http://webhjelpen.no/wordpress-plugin/soundcloud-sound-competition/
Description: Host your own Sound Contest integrated with SoundCloud, users connect easy with SoundCloud to choose track to add to your competition. Everything within your WordPress web site.
Author: Kenneth Berentzen
Author URI: http://webhjelpen.no/wordpress-plugin
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

// [remixcomp-connect type="foo-value"]
function remixcomp_sc_connect( $atts ) {
	extract( shortcode_atts( array(
		'type' => null
	), $atts ) );

    //Initiation
    global $wpdb;
    $wpdb->hide_errors();
    require( MYPLUGINNAME_PATH.'API/Soundcloud.php' );
    !is_array(get_option('ken_remixcomp_settings')) ? "" : extract(get_option('ken_remixcomp_settings'));

    //Variables
    $par_redirectUrl = $kenrmx_sc_redirect_uri;
    $par_clientId = $kenrmx_sc_client_id;
    $par_clientSecret = $kenrmx_sc_client_secret;
    $par_development = false;
    $par_regexp = "/^[^0-9][A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";
    $par_error = "";
    $par_return = "";
    $par_db_ok = 0;
    $par_track_id_uploaded = 0;

    $soundcloud = new Services_Soundcloud($par_clientId,$par_clientSecret,$par_redirectUrl );
    $soundcloud->setDevelopment($par_development);

    $authURL = $soundcloud->getAuthorizeUrl();        

    //Kill session if exit
    if ( htmlentities($_GET['exit']) == 't' ) {
        //Disconnect from SC
        session_destroy();
        unset($_SESSION['sc_token']);
    }
    else if( $_GET['code'] || isset($_SESSION['sc_token']) ) {
        //Try to connect to SC
        try {
            //Check if session is set if not set access token to session
            if (!isset($_SESSION['sc_token'])){
                $response = $soundcloud->accessToken($_GET['code']);
                $_SESSION['sc_token'] = $response['access_token'];
            }
            //If session is already set access token from session to sc api
            else {
                $soundcloud->setAccessToken($_SESSION['sc_token']);
            }
        } catch (Services_Soundcloud_Invalid_Http_Response_Code_Exception $e) {
            exit("Err: ".$e->getMessage());
        }
    }


    $par_return = "<link rel='stylesheet' href='".plugins_url('soundcloud-sound-competition/css/style.css')."' />";

    //Ikke koblet til SC
    if (!isset($_SESSION['sc_token'])){
        $par_return = $par_return."<center><a id=\"ken_connect\" href='$authURL'>Connect to SoundCloud</a></center><br>";
    }
    //Koblet til SC
    else {
        //Try to get user connected to from SC
        try {

            $me = json_decode($soundcloud->get('me'), true);
            $par_return = $par_return."".$me[username]." logged in, <a href='?exit=t'>logout</a>.<br><br>";

            //print_r($me);//Printer ut alle mulighetene man kan lagre av en bruker.

            $tracks = json_decode($soundcloud->get('tracks',array('user_id'=> $me['id'])) , true);
            //$results = $tracks; // The array we're working with

            //  After post track
            //--------------------------------------
            if ( htmlentities($_POST['trackid']) ) {

                //Check email
                if ( preg_match($par_regexp, $_POST['email']) ) {
                    //Input to DB
                    //Find correct track
                    foreach( $tracks as $track ) {
                        if($track['id']==$_POST['trackid']) {

                            //print_r($track); //Printer ut alle mulighetene man kan lagre av tracks.
                            //Get user id from db if 0 user does not exist
                            $user_id = get_id_by_sc_user_id($me[id]);

                            //Bruker tabellen
                            //----------------
                            if( $user_id != 0 ){
                                //update
                                $wpdb->update( 
                                    $wpdb->prefix."ken_remixcomp_users" , 
                                    array( 
                                        'rcu_email' => $_POST['email'], 
                                        'rcu_sc_id' => $me[id], 
                                        'rcu_sc_kind' => $me[kind], 
                                        'rcu_sc_permalink' => $me[permalink], 
                                        'rcu_sc_username' => $me[username], 
                                        'rcu_sc_uri' => $me[uri], 
                                        'rcu_sc_permalink_url' => $me[permalink_url], 
                                        'rcu_sc_avatar_url' => $me[avatar_url], 
                                        'rcu_sc_country' => $me[country], 
                                        'rcu_sc_full_name' => $me[full_name], 
                                        'rcu_sc_description' => $me[description], 
                                        'rcu_sc_city' => $me[city], 
                                        'rcu_sc_discogs_name' => $me[discogs_name], 
                                        'rcu_sc_myspace_name' => $me[myspace_name], 
                                        'rcu_sc_website' => $me[website], 
                                        'rcu_sc_website_title' => $me[website_title], 
                                        'rcu_sc_online' => $me[online], 
                                        'rcu_sc_track_count' => $me[track_count], 
                                        'rcu_sc_playlist_count' => $me[playlist_count], 
                                        'rcu_sc_public_favorites_count' => $me[public_favorites_count], 
                                        'rcu_sc_followers_count' => $me[followers_count], 
                                        'rcu_sc_followings_count' => $me[followings_count], 
                                        'rcu_sc_plan' => $me[plan], 
                                        'rcu_sc_private_tracks_count' => $me[private_tracks_count], 
                                        'rcu_sc_private_playlists_count' => $me[private_playlists_count], 
                                        'rcu_sc_primary_email_confirmed' => $me[primary_email_confirmed], 
                                        'rcu_modified_date' => date("Y-m-d H:i:s"), 
                                        'rcu_modified_by' => $me[username]
                                    ), 
                                    array ( 'rcu_id' => $user_id )
                                ); 

                            }
                            else {
                                //Insert
                                $wpdb->insert( 
                                    $wpdb->prefix."ken_remixcomp_users" , 
                                    array( 
                                        'rcu_email' => $_POST['email'], 
                                        'rcu_sc_id' => $me[id], 
                                        'rcu_sc_kind' => $me[kind], 
                                        'rcu_sc_permalink' => $me[permalink], 
                                        'rcu_sc_username' => $me[username], 
                                        'rcu_sc_uri' => $me[uri], 
                                        'rcu_sc_permalink_url' => $me[permalink_url], 
                                        'rcu_sc_avatar_url' => $me[avatar_url], 
                                        'rcu_sc_country' => $me[country], 
                                        'rcu_sc_full_name' => $me[full_name], 
                                        'rcu_sc_description' => $me[description], 
                                        'rcu_sc_city' => $me[city], 
                                        'rcu_sc_discogs_name' => $me[discogs_name], 
                                        'rcu_sc_myspace_name' => $me[myspace_name], 
                                        'rcu_sc_website' => $me[website], 
                                        'rcu_sc_website_title' => $me[website_title], 
                                        'rcu_sc_online' => $me[online], 
                                        'rcu_sc_track_count' => $me[track_count], 
                                        'rcu_sc_playlist_count' => $me[playlist_count], 
                                        'rcu_sc_public_favorites_count' => $me[public_favorites_count], 
                                        'rcu_sc_followers_count' => $me[followers_count], 
                                        'rcu_sc_followings_count' => $me[followings_count], 
                                        'rcu_sc_plan' => $me[plan], 
                                        'rcu_sc_private_tracks_count' => $me[private_tracks_count], 
                                        'rcu_sc_private_playlists_count' => $me[private_playlists_count], 
                                        'rcu_sc_primary_email_confirmed' => $me[primary_email_confirmed], 
                                        'rcu_created_date' => date("Y-m-d H:i:s"), 
                                        'rcu_created_by' => $me[username], 
                                        'rcu_modified_date' => date("Y-m-d H:i:s"), 
                                        'rcu_modified_by' => $me[username]
                                    )
                                ); 
                                $user_id = $wpdb->insert_id;
                            }

                            //Insert to db
                            $wpdb->insert( 
                                $wpdb->prefix."ken_remixcomp_entrees" , 
                                array( 
                                    'rce_rcu_id' => $user_id, 
                                    'rce_remix' => $kenrmx_sc_remix_type, 
                                    'rce_sct_kind' => $track['kind'], 
                                    'rce_sct_id' => $track['id'], 
                                    'rce_sct_user_id' => $track['user_id'], 
                                    'rce_sct_duration' => $track['duration'], 
                                    'rce_sct_original_content_size' => $track['original_content_size'], 
                                    'rce_sct_sharing' => $track['sharing'], 
                                    'rce_sct_tag_list' => $track['tag_list'], 
                                    'rce_sct_permalink' => $track['permalink'], 
                                    'rce_sct_downloadable' => $track['downloadable'], 
                                    'rce_sct_purchase_url' => $track['purchase_url'], 
                                    'rce_sct_label_id' => $track['label_id'], 
                                    'rce_sct_purchase_title' => $track['purchase_title'], 
                                    'rce_sct_genre' => $track['genre'], 
                                    'rce_sct_title' => $track['title'], 
                                    'rce_sct_description' => $track['description'], 
                                    'rce_sct_label_name' => $track['label_name'], 
                                    'rce_sct_release' => $track['release'], 
                                    'rce_sct_track_type' => $track['track_type'], 
                                    'rce_sct_original_format' => $track['original_format'], 
                                    'rce_sct_license' => $track['license'], 
                                    'rce_sct_uri' => $track['uri'], 
                                    'rce_sct_user_playback_count' => $track['user_playback_count'], 
                                    'rce_sct_user_favorite' => $track['user_favorite'], 
                                    'rce_sct_permalink_url' => $track['permalink_url'], 
                                    'rce_sct_artwork_url' => $track['artwork_url'], 
                                    'rce_sct_waveform_url' => $track['waveform_url'], 
                                    'rce_sct_stream_url' => $track['stream_url'], 
                                    'rce_sct_download_url' => $track['download_url'], 
                                    'rce_sct_playback_count' => $track['playback_count'], 
                                    'rce_sct_download_count' => $track['download_count'], 
                                    'rce_sct_favoritings_count' => $track['favoritings_count'], 
                                    'rce_sct_comment_count' => $track['comment_count'], 
                                    'rce_sct_attachments_uri' => $track['attachments_uri'],
                                    'rce_sct_secret_token' => $track['secret_token'], 
                                    'rce_sct_secret_uri' => $track['secret_uri'], 
                                    'rce_vote_count' => 0, 
                                    'rce_created_date' => date("Y-m-d H:i:s"), 
                                    'rce_created_by' => $me[username], 
                                    'rce_modified_date' => date("Y-m-d H:i:s"), 
                                    'rce_modified_by' => $me[username]
                                )
                            );
                            $par_track_id_uploaded = $wpdb->insert_id;
                            $par_db_ok = 1;
                        } //end if track id is post track id
                    }
                } else {
                    $par_error = "<div id='ken_nb_red'>Email not valid, please correct your email</div><br>";
                }

            }

            if ($par_db_ok == 1) {
                $par_return = $par_return."<p>Your track is uploaded! Check out your track <br><a href='".$kenrmx_wpsc_entrees_page_url."?rmxid=".$par_track_id_uploaded."'>".$kenrmx_wpsc_entrees_page_url."?rmxid=".$par_track_id_uploaded."</a></p>";
            }
            else {
                //Utskrift av form
                $par_return = $par_return."<form name='input' action='' method='POST'>";
                $par_return = $par_return."<b>Your email</b> <font size='1'>(enter the remix-alert newsletter, we will contact you here if you win!)</font><br><input type='text' value='".htmlentities($_POST['email'])."' name='email' class='text'><br><br>";
                $par_return = $par_return."<b>Select track</b><br><select class='text' name='trackid'>";
                foreach( $tracks as $track ) {
                    $par_return = $par_return."<option class='text' value='".$track['id']."'>".$track['title']."</option>";
                }
                $par_return = $par_return."</select><br><br>";
                if($par_error != ""){
                    $par_return = $par_return.$par_error;
                }
                $par_return = $par_return."<input type='submit' value='Submit' class='submit' />";
                $par_return = $par_return."</form>";
                //print_r($tracks);//Printer ut alle mulighetene man har og kan lagre av en låt.                
            }


        } catch (Services_Soundcloud_Invalid_Http_Response_Code_Exception $e) {
            //Disconnect from SC
            session_destroy();
            unset($_SESSION['sc_token']);
            _e("<div id='ken_nb_red'>Please refreash your browser</div><br>");
            //exit("<div id='ken_nb_red'>Please refreash your browser</div><br>".$e->getMessage());
        }

    }
    return "{$par_return}";
}
