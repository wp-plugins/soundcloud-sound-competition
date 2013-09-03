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

function get_remixcomp_admin_header() {
     $return_string =   '<h1 style="margin-bottom:5px;">Sound Contest</h1>'.
                        '<div style="padding: 10px; background: #EFEFEF; border: 1px solid #ccc; width: 500px">'.
                        '<p class="description" style="margin-bottom:0px;">Hosted within WordPress, SoundCloud integrated</p>'.
                        '<p class="description" style="margin-top:0px;">Made by Kenneth Berentzen, more info about this plugin -> <a href="http://lightdigitalmedia.com/wordpress-plugins/soundcloud-sound-competition/" target="new">LightDigitalMedia.com</a></p>'.
                        '<br>If you like and use this plugin, please support it by rating it on <a href="http://wordpress.org/plugins/soundcloud-sound-competition/" target="new">Wordpress</a><br>or by <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=U7ZQC4QH7MVP2" target="new">donating a few cappuchinos</a> so i will keep going :) - Or both! '.
                        '</div>';       
     return "{$return_string}";
} 

//Enable sessions
function init_sessions() {
    if (!session_id()) {
        session_start();
    }
}

//Admin meny hirarkiet setter opp
function remixcomp_admin_actions() {
	add_menu_page('RemixComp', 'Sound Contest', 10, 'soundcloud-sound-competition', 'remixcomp_list_partisipants');
        add_submenu_page( 'soundcloud-sound-competition', 'Settings', 'Settings', 10, 'remixcomp-settings','remixcomp_settings'); 
}

function get_remixcomp_stamper() {
        //base64_encode base64_decode
        $return_string = 'PGRpdiBzdHlsZT0iZm9udC1zaXplOjEwcHg7bWFyZ2luOjBweDtwYWRkaW5nOjBweDsiPlBvd2VyZWQgYnkgPGEgaHJlZj0iaHR0cDovL2tlbnJlY29yZHMuY29tIiB0YXJnZXQ9Im5ldyI+S2VuIFJlY29yZHM8L2E+IOKGkiBHZXQgeW91ciA8YSBocmVmPSJodHRwOi8vbGlnaHRkaWdpdGFsbWVkaWEuY29tL3dvcmRwcmVzcy1wbHVnaW5zL3NvdW5kY2xvdWQtc291bmQtY29tcGV0aXRpb24vIiB0YXJnZXQ9Im5ldyI+U291bmRDbG91ZCBTb3VuZCBDb250ZXN0IFBsdWdpbiBmb3IgV29yZFByZXNzPC9hPiA8L2Rpdj4='; 
        $return_string = base64_decode($return_string);
        return "{$return_string}";
} 


function get_id_by_sc_user_id($sc_id) {
    global $wpdb;
    //Is digit
    if( preg_match('/^\d\d*$/', $sc_id) ) {
        
        $id=$wpdb->get_col( $wpdb->prepare( 
            "
            SELECT      rcu_id
            FROM        ".$wpdb->prefix."ken_remixcomp_users
            WHERE       rcu_sc_id = %d 
            LIMIT 1 ",
            $sc_id 
        ) ); 
        
        if ($id){
            return $id[0];
        }
        else {
            return 0;
        }
    }        
}
