<?php
/*
Plugin Name: SoundCloud Sound Competition
Plugin URI: http://lightdigitalmedia.com/wordpress-plugins/soundcloud-sound-competition/
Description: Host your own Sound Contest integrated with SoundCloud, users connect easy with SoundCloud to choose track to add to your competition. Everything within your WordPress web site.
Author: Kenneth Berentzen
Author URI: http://lightdigitalmedia.com/
License: Copyright 2014  Kenneth Berentzen  (email : post@lightdigitalmedia.com)
*/

require_once( MYPLUGINNAME_PATH.'API/Facebook/FacebookSession.php' );
require_once( MYPLUGINNAME_PATH.'API/Facebook/FacebookRedirectLoginHelper.php' );
require_once( MYPLUGINNAME_PATH.'API/Facebook/FacebookRequest.php' );
require_once( MYPLUGINNAME_PATH.'API/Facebook/FacebookResponse.php' );
require_once( MYPLUGINNAME_PATH.'API/Facebook/FacebookSDKException.php' );
require_once( MYPLUGINNAME_PATH.'API/Facebook/FacebookRequestException.php' );
require_once( MYPLUGINNAME_PATH.'API/Facebook/FacebookOtherException.php' );
require_once( MYPLUGINNAME_PATH.'API/Facebook/FacebookAuthorizationException.php' );
require_once( MYPLUGINNAME_PATH.'API/Facebook/GraphObject.php' );
require_once( MYPLUGINNAME_PATH.'API/Facebook/GraphSessionInfo.php' );

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookOtherException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphSessionInfo;


function remixcomp_remixers( $atts, $remixer_id ) {
	extract( shortcode_atts( array(
		'type' => null
	), $atts ) );
        //Variables start
        global $wpdb;

        $wpdb->show_errors();
        //$wpdb->hide_errors();   
        !is_array(get_option('ken_remixcomp_settings')) ? "" : extract(get_option('ken_remixcomp_settings')); 

        /***********************************************************************
                                    fb voting
        ***********************************************************************/
        if ( $kenrmx_voting_type == "Facebook_voting" ) {
            //init_fb_session(); //Gammel SDK
            
            // start session
            session_start();

            FacebookSession::setDefaultApplication($kenrmx_facebook_app_id,$kenrmx_facebook_app_secret);

            // login helper with redirect_uri
            $helper = new FacebookRedirectLoginHelper( ''.get_full_url_to_competition().'/' );
             
            // see if a existing session exists
            if ( isset( $_SESSION ) && isset( $_SESSION['fb_token'] ) ) {
                // create new session from saved access_token
                $session = new FacebookSession( $_SESSION['fb_token'] );
                // validate the access_token to make sure it's still valid
                try {
                    if ( !$session->validate() ) {
                        $session = null;
                    }
                } 
                catch ( Exception $e ) {
                    // catch any exceptions
                    $session = null;
                }
            } 
            else {
                // no session exists
                try {
                    $session = $helper->getSessionFromRedirect();
                } 
                catch( FacebookRequestException $ex ) {
                    // When Facebook returns an error
                    echo $ex->message;
                } 
                catch( Exception $ex ) {
                    // When validation fails or other local issues
                    echo $ex->message;
                }
            }

            // see if we have a session
            if ( isset( $session ) ) {
                
                $_SESSION['fb_token'] = $session->getToken();               // save the session
                $session = new FacebookSession( $session->getToken() );     // create a session using saved token or the new one we generated at login
                $request = new FacebookRequest( $session, 'GET', '/me' );   // graph api request for user data
                $response = $request->execute();
                $graphObject = $response->getGraphObject()->asArray();      // get response
                // print profile data
                //echo '<pre>' . print_r( $graphObject, 1 ) . '</pre>';
                // print logout url using session and redirect_uri (logout.php page should destroy the session)
                //echo '<a href="' . $helper->getLogoutUrl( $session, 'http://kenrecords.com/' ) . '">Logout</a>';
                //Insert data to db
                set_fb_voters_add($graphObject[id], $graphObject[email], $graphObject[first_name], $graphObject[gender], 
                  $graphObject[last_name], $graphObject[link], $graphObject[locale], $graphObject[name], 
                  $graphObject[timezone], $graphObject[updated_time], $graphObject[verified]);
                $fb_session_logged_in = true;
            } else {
                // show login url
                //echo '<a href="' . $helper->getLoginUrl( array( 'email', 'user_friends' ) ) . '">Login</a>';
                $fb_session_logged_in = false;
                $fb_login_to_vote_url = $helper->getLoginUrl( array( 'email', 'user_friends' ) );
            }

        }
        /************************* fb voting ****************************/

        $the_url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]; 
        $session = session_id();
        $session_ip = get_real_IP_address();
        $session_user_agent = $_SERVER['HTTP_USER_AGENT'];

        //Variables
        if($remixer_id!=null){
            //echo("HAr verdi!!");
            $remix_id = $remixer_id;
        }
        else {
            //echo("HAr ikke verdi!!");
            $remix_id = urldecode(get_query_var('rmxid'));    
        }
        $voting_rmx = urldecode(get_query_var('voting_rmx'));
        $sort_id = urldecode(get_query_var('sortid'));
        $remix_db_slug = $atts['type'];
        
        
        if ($sort_id != null) {

            if ($sort_id != null && $sort_id == 1) {
                $_SESSION['sort'] = "1";
            }
            else {
                $_SESSION['sort'] = "2";
            }
        }    
        
        //Hvis voting_rmx så skal låten registreres hvis den ikke har blitt votet før på den brukeren.
        //------------------------------------------------------------------------------------------
        if ($voting_rmx) {
            if( check_has_visitor_voted_by_session($voting_rmx,$remix_db_slug,$session) ) { 
                if ( $kenrmx_voting_type == "Facebook_voting" ) { 
                    set_visitor_voted_fb($voting_rmx, $remix_db_slug, $session_ip, $session, $session_user_agent, $graphObject[id]); 
                } 
                else {
                    set_visitor_voted($voting_rmx, $remix_db_slug, $session_ip, $session, $session_user_agent);     
                }    
            } //End sjekk om har votet
        } //End voting_rmx if
        
        echo("<br><link rel='stylesheet' href='".plugins_url('soundcloud-sound-competition/css/style.css')."' />");
        //echo($session_ip."-".$session."<br>");
        
        echo("<div style=\"float:right;\"><a id=\"ken-remix-comp-upload\" href='$kenrmx_wpsc_connect_page_url'>Upload</a> <a id=\"ken-remix-comp-info\" href='$kenrmx_wpsc_more_info_url'>Info</a></div><div id='ken-remix-comp-clear'></div>");
        
        //Hvis det kommer en rmx id så skal låten vises
        //------------------------------------------------------------------------------------------
        if ($remix_id ) {
            //Dokumentasjon http://codex.wordpress.org/Function_Reference/add_query_arg
            //http://codex.wordpress.org/Function_Reference/get_query_var
            //http://wordpress.stackexchange.com/questions/31821/pretty-url-with-add-query-var
            $base_url = $the_url;
            $base_url_voting = $the_url;  
            $base_url_remixers = $the_url;    //Getting current url
            $params3 = array( 'rmxid' ); 
            $params4 = array( 'voting_rmx' );
            //$params5 = array( 'sortid' ); 
            $base_url_voting = remove_query_arg( $params3, $base_url_voting ); 
            $base_url_remixers = remove_query_arg( $params4, $base_url_remixers ); 
            //$base_url_sortid = remove_query_arg( $params5, $base_url_sortid );  
            $base_url = remove_query_arg( $params3, $base_url ); 
            $base_url = remove_query_arg( $params4, $base_url ); 
            //$base_url = remove_query_arg( $params5, $base_url );  
            /***************************************************************************************
                                            LIST ONE
            ****************************************************************************************/
            // This query selects all contracts that are published
            $sql2 = "
                SELECT * FROM ".$wpdb->prefix."ken_remixcomp_entrees
                JOIN ".$wpdb->prefix."ken_remixcomp_users ON rcu_id = rce_rcu_id  
                WHERE rce_remix='".$remix_db_slug."' 
                AND rce_id=".$remix_id.";
            ";
            $results2 = $wpdb->get_results($sql2);                                              // Run our query, getting results as an object

            if (!empty($results2)) {                                                            // If the query returned something
                foreach ($results2 as $result2) {                                               // Loop though our results!	

                    if ( $kenrmx_voting_type == "Facebook_voting" && !$fb_session_logged_in ) {
                        $vote_sql = null;
                    }
                    else if ( $kenrmx_voting_type == "Facebook_voting" && $fb_session_logged_in ) { 
                        $vote_sql = "SELECT *                                                       
                            FROM ".$wpdb->prefix."ken_remixcomp_voting
                            WHERE rcv_rce_id='".$result2->rce_id."'
                            AND rcv_fb_userid='".$graphObject[id]."'
                            LIMIT 1
                        ";  
                    } 
                    else {
                        $vote_sql = "SELECT *                                                       
                            FROM ".$wpdb->prefix."ken_remixcomp_voting 
                            WHERE rcv_rce_id='".$result2->rce_id."'
                            AND rcv_session='".$session."'
                            LIMIT 1
                        ";   
                    }
                                                                         
                    //Check if user voted
                    $vote_results = $wpdb->get_results($vote_sql);
            
                    $current_rmx_url = $the_url;                                                //Getting current url
                    $params2 = array( 'rmxid' => $result2->rce_id );                            //Making parameter
                    $current_rmx_url = add_query_arg( $params2, $current_rmx_url );             //Adding url parameter

                    $social_url_sound = $kenrmx_wpsc_entrees_page_url."/".get_name_permalink_to_a_sound()."/".$result2->rce_id;

                    require( MYPLUGINNAME_PATH.'view/print_one.php' );
                    
                }//end foreach
            }//end if sql res
                
        echo("<a href='".$base_url."'><h2>");   
        _e("All entrees", "soundcloud-sound-competition");   
        echo("</h2></a>");      
        
        }//End if remix_id
        
        echo("<div style=\"float:right;\"><a id=\"ken-remix-comp-latest\" href='".add_query_arg( array( 'sortid' => 1 ), $base_url )."'>Latest uploads</a> <a id=\"ken-remix-comp-rated\" href='".add_query_arg( array( 'sortid' => 2 ), $base_url )."'>Highest rated</a></div><div id=\"ken-remix-comp-clear\"></div>");
        
        /***************************************************************************************
                                        LIST ALL
        ****************************************************************************************/
        //Sort
        if ($_SESSION['sort'] == 1) {
            $sort_query = "ORDER BY rce_id DESC";
        }
        else {
            $sort_query = "ORDER BY rce_vote_count DESC";
        }
            
	   // This query selects all contracts that are published
	   $sql = "SELECT * FROM ".$wpdb->prefix."ken_remixcomp_entrees
            JOIN ".$wpdb->prefix."ken_remixcomp_users ON rcu_id = rce_rcu_id
            WHERE rce_remix='".$remix_db_slug."' ".$sort_query.";";
        
        $results = $wpdb->get_results($sql);  // Run our query, getting results as an object

        if (!empty($results)) {                 // If the query returned something
            foreach ($results as $result) {     // Loop though our results!	
                
                if ( $kenrmx_voting_type == "Facebook_voting" && !$fb_session_logged_in ) {
                    $vote_sql_all = null;
                }
                else if ( $kenrmx_voting_type == "Facebook_voting" && $fb_session_logged_in ) { 
                    $vote_sql_all = "SELECT *                                                       
                        FROM ".$wpdb->prefix."ken_remixcomp_voting 
                        WHERE rcv_rce_id='".$result->rce_id."'
                        AND rcv_fb_userid='".$graphObject[id]."'
                        LIMIT 1
                    "; 
                } 
                else {
                    $vote_sql_all = "SELECT *                                                       
                        FROM ".$wpdb->prefix."ken_remixcomp_voting 
                        WHERE rcv_rce_id='".$result->rce_id."'
                        AND rcv_session='".$session."'
                        LIMIT 1
                    "; 
                }
                                                                //Check if user voted
                $vote_results_all = $wpdb->get_results($vote_sql_all);
                
                $params = array( 'rmxid' => $result->rce_id );                              //Making parameter
                $base_url_remixers = add_query_arg( $params, $base_url_remixers );          //Adding url parameter
                
                if( $kenrmx_wpsc_preview_type == "Sound_list" )  {
                    require( MYPLUGINNAME_PATH.'view/print_all_sound.php' );    
                }
                else  {
                    require( MYPLUGINNAME_PATH.'view/print_all.php' );
                }
                
            } //Close loop
	} //Close if return somthing

        ?>  
            <!-- Google +1 -->
            <script type="text/javascript">
            (function() {
                var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                po.src = 'https://apis.google.com/js/plusone.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
            })();
            </script>

            <!-- Facebook -->
            <script>(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
            fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));</script>
            <div id='ken-remix-comp-clear'></div>
        <?php 
        if( !soundcloud_sound_competition_ch_l() ): 
            echo( get_remixcomp_st() );
        endif;

} //End function


//Definer post variable
function rmxid_queryvars($public_query_vars) {
    global $wpdb;
    $public_query_vars[] = 'rmxid';
    $public_query_vars[] = 'voting_rmx';
    $public_query_vars[] = 'sortid';
    return $public_query_vars;
}
add_filter('query_vars', 'rmxid_queryvars');

