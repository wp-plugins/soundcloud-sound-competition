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

function get_remixcomp_admin_header() {
     $return_string =   '<h1 style="margin-bottom:5px;">Sound Contest</h1>'.
                        '<div style="padding: 10px; background: #EFEFEF; border: 1px solid #ccc; width: 500px">'.
                        '<p class="description" style="margin-bottom:0px;">Hosted within WordPress, SoundCloud integrated - Version 0.9.2</p>'.
                        '<p class="description" style="margin-top:0px;">Made by Kenneth Berentzen, more info about this plugin -> <a href="http://webhjelpen.no/wordpress-plugin/" target="new">Webhjelpen.no</a></p>'.
                        '<iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.facebook.com%2FWebHjelpen&amp;send=false&amp;layout=button_count&amp;width=450&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:21px;" allowTransparency="true"></iframe>'.
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
        $return_string = 'PGJyPjxkaXYgc3R5bGU9ImZvbnQtc2l6ZToxMHB4O21hcmdpbjowcHg7cGFkZGluZzowcHg7Ij5Qb3dlcmVkIGJ5IDxhIGhyZWY9Imh0dHA6Ly9rZW5yZWNvcmRzLmNvbSIgdGFyZ2V0PSJuZXciPktlbiBSZWNvcmRzPC9hPiAmIzg1OTQ7IEdldCB5b3VyIDxhIGhyZWY9Imh0dHA6Ly93ZWJoamVscGVuLm5vL3dvcmRwcmVzcy1wbHVnaW4vc291bmRjbG91ZC1zb3VuZC1jb21wZXRpdGlvbi8iIHRhcmdldD0ibmV3Ij5Tb3VuZENsb3VkIFNvdW5kIENvbnRlc3QgUGx1Z2luIGZvciBXb3JkUHJlc3M8L2E+IDwvZGl2Pjxicj48YnI+'; 
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