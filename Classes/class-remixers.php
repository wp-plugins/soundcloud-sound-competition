<?php
/*
Plugin Name: SoundCloud Sound Competition
Plugin URI: http://lightdigitalmedia.com/wordpress-plugins/soundcloud-sound-competition/
Description: Host your own Sound Contest integrated with SoundCloud, users connect easy with SoundCloud to choose track to add to your competition. Everything within your WordPress web site.
Author: Kenneth Berentzen
Author URI: http://lightdigitalmedia.com/
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

//Special get ip funksjon
function get_real_IP_address() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) { //check ip from share internet 
        $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { //to check ip is pass from proxy
        $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else {
        $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

//Definer post variable
function rmxid_queryvars($public_query_vars) {
    global $wpdb;
    $public_query_vars[] = 'rmxid';
    $public_query_vars[] = 'voting_rmx';
    $public_query_vars[] = 'sortid';
    return $public_query_vars;
}
add_filter('query_vars', 'rmxid_queryvars');

/* 
//For å få det til å funke med følgende url http://kenrecords.com/remixers/99 istedet for http://kenrecords.com/remixers/?rmxid=99
//Utskrift av siden du er på $pagename = get_query_var('pagename');
function add_rewrite_rules($aRules) {
    $aNewRules = array('remixers/([^/]+)/?$' => 'index.php?pagename=remixers&rmxid=$matches[1]');
    $aRules = $aNewRules + $aRules;
    return $aRules;
}
add_filter('rewrite_rules_array', 'add_rewrite_rules');
*/



function remixcomp_remixers( $atts ) {
	extract( shortcode_atts( array(
		'type' => null
	), $atts ) );

        //Variables start
        global $wpdb;
        //$wpdb->show_errors();
        $wpdb->hide_errors();   
        !is_array(get_option('ken_remixcomp_settings')) ? "" : extract(get_option('ken_remixcomp_settings')); 
        $the_url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]; 
        $session = session_id();
        $session_ip = get_real_IP_address();
        $session_user_agent = $_SERVER['HTTP_USER_AGENT'];
        
        echo("<br><link rel='stylesheet' href='".plugins_url('soundcloud-sound-competition/css/style.css')."' />");
        //echo($session_ip."-".$session."<br>");
        
        //Variables
        $remix_id = urldecode(get_query_var('rmxid'));
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
            $vote_sql_sjekk = "SELECT *                                                       
                FROM ".$wpdb->prefix."ken_remixcomp_voting 
                WHERE rcv_remix='".$remix_db_slug."'
                AND rcv_rce_id='".$voting_rmx."' 
                AND rcv_session='".$session."'
                LIMIT 1
            "; 
            $vote_sjekk_results = $wpdb->get_results($vote_sql_sjekk);
            
            if( preg_match('/^\d\d*$/', $voting_rmx) && empty($vote_sjekk_results) ) { 
                
                //Insert session vote
                $wpdb->insert( 
                        $wpdb->prefix."ken_remixcomp_voting" , 
                        array( 
                                'rcv_rce_id' => $voting_rmx, 
                                'rcv_remix' => $remix_db_slug, 
                                'rcv_rate' => 1, 
                                'rcv_ip' => $session_ip, 
                                'rcv_session' => $session, 
                                'rcv_user_agent' => $session_user_agent, 
                                'rcv_created_date' => date("Y-m-d H:i:s"), 
                                'rcv_created_by' => 'Web'
                        )
                );
                
                //Update count
                $wpdb->query( 
                        $wpdb->prepare( 
                                "
                                UPDATE ".$wpdb->prefix."ken_remixcomp_entrees 
                                SET rce_vote_count = rce_vote_count + 1
                                WHERE rce_id = %d
                                ",
                                $remix_id
                        )
                );

                
            } //End sjekk om har votet
            
            
        } //End voting_rmx if
        
        
        
        _e("<div style=\"float:right;\"><a id=\"ken_upload\" href='$kenrmx_wpsc_connect_page_url'>Upload</a> <a id=\"ken_info\" href='$kenrmx_wpsc_more_info_url'>Info</a></div><div id=\"clear\">");
        
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

                    $vote_sql = "SELECT *                                                       
                        FROM ".$wpdb->prefix."ken_remixcomp_voting 
                        WHERE rcv_rce_id='".$result2->rce_id."'
                        AND rcv_session='".$session."'
                        LIMIT 1
                    ";                                                                 //Check if user voted
                    $vote_results = $wpdb->get_results($vote_sql);
            
                    $current_rmx_url = $the_url;                                                //Getting current url
                    $params2 = array( 'rmxid' => $result2->rce_id );                            //Making parameter
                    $current_rmx_url = add_query_arg( $params2, $current_rmx_url );             //Adding url parameter
                    
                    require( MYPLUGINNAME_PATH.'view/print_one.php' );
                    
                }//end foreach
            }//end if sql res
                
        echo("<a href='".$base_url."'><h2>All entrees</h2></a>");      
        
        }//End if remix_id
        
        _e("<div style=\"float:right;\"><a id=\"ken_latest\" href='".add_query_arg( array( 'sortid' => 1 ), $base_url )."'>Latest uploads</a> <a id=\"ken_rated\" href='".add_query_arg( array( 'sortid' => 2 ), $base_url )."'>Highest rated</a></div><div id=\"clear\">");
        
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
                
                $vote_sql_all = "SELECT *                                                       
                    FROM ".$wpdb->prefix."ken_remixcomp_voting 
                    WHERE rcv_rce_id='".$result->rce_id."'
                    AND rcv_session='".$session."'
                    LIMIT 1
                ";                                                                 //Check if user voted
                $vote_results_all = $wpdb->get_results($vote_sql_all);
                
                $params = array( 'rmxid' => $result->rce_id );                              //Making parameter
                $base_url_remixers = add_query_arg( $params, $base_url_remixers );          //Adding url parameter
                
                if( $kenrmx_wpsc_preview_type == "Sound_list" )  {
                    require( MYPLUGINNAME_PATH.'view/print_all2.php' );    
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
            <div id='clear'></div>
        <?php
        echo( get_remixcomp_stamper() );
        
} //End function

