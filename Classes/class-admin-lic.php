<?php
/*
Plugin Name: SoundCloud Sound Competition
Plugin URI: http://lightdigitalmedia.com/wordpress-plugins/soundcloud-sound-competition/
Description: Host your own Sound Contest integrated with SoundCloud, users connect easy with SoundCloud to choose track to add to your competition. Everything within your WordPress web site.
Author: Kenneth Berentzen
Author URI: http://lightdigitalmedia.com/
License: Copyright 2012  Kenneth Berentzen  (email : post@lightdigitalmedia.com)

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

function soundcloud_sound_competition_ch_l() {

    $store_url = 'http://lightdigitalmedia.com';
    $item_name = 'Soundcloud Sound Competition';
    $license = get_option( 'soundcloud_sound_competition_license_key' );
        
    $api_params = array( 
        'edd_action' => 'check_license', 
        'license' => $license, 
        'item_name' => urlencode( $item_name ) 
    );
    
    $response = wp_remote_get( add_query_arg( $api_params, $store_url ), array( 'timeout' => 15, 'sslverify' => false ) );

    if ( is_wp_error( $response ) )
        return false;

    $license_data = json_decode( wp_remote_retrieve_body( $response ) );

    if( $license_data->license == 'valid' ) {
        //echo 'valid'; exit;
        // this license is still valid
        return true;
    } else {
        //echo 'invalid'; exit;
        // this license is no longer valid
        return false;
    }
}

function ssc_remixcomp_license_page() {
    $license    = get_option( 'soundcloud_sound_competition_license_key' );
    $status     = get_option( 'soundcloud_sound_competition_license_status' );
    _e(get_remixcomp_admin_header()); 
    ?>
        <h2><?php _e('Plugin License Options', 'soundcloud-sound-competition'); ?></h2>
        <form method="post" action="options.php">
 
            <?php settings_fields('ssc_license'); ?>
 
            <table class="form-table">
                <tbody>
                    <tr valign="top">   
                        <th scope="row" valign="top">
                            <?php _e('License Key', 'soundcloud-sound-competition'); ?>
                        </th>
                        <td>
                            <input id="soundcloud_sound_competition_license_key" name="soundcloud_sound_competition_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
                            <label class="description" for="soundcloud_sound_competition_license_key"><?php _e('Enter your license key', 'soundcloud-sound-competition'); ?></label>
                        </td>
                    </tr>
                    <?php if( false !== $license ) { ?>
                        <tr valign="top">   
                            <th scope="row" valign="top">
                                <?php _e('Activate License', 'soundcloud-sound-competition'); ?>
                            </th>
                            <td>
                                <?php if( $status !== false && $status == 'valid' ) { ?>
                                    <span style="color:green;"><?php _e('active','soundcloud-sound-competition'); ?></span>
                                <?php } else {
                                    wp_nonce_field( 'ssc_nonce', 'ssc_nonce' ); ?>
                                    <input type="submit" class="button-secondary" name="edd_license_activate" value="<?php _e('Activate License','soundcloud-sound-competition'); ?>"/>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>    
            <?php submit_button(); ?>
 
        </form>
    <?php
}
 
function soundcloud_sound_competition_register_option() {
    // creates our settings in the options table
    register_setting('ssc_license', 'soundcloud_sound_competition_license_key', 'ssc_sanitize_license' );
}
add_action('admin_init', 'soundcloud_sound_competition_register_option');
 
function ssc_sanitize_license( $new ) {
    $old = get_option( 'soundcloud_sound_competition_license_key' );
    if( $old && $old != $new ) {
        delete_option( 'soundcloud_sound_competition_license_status' ); // new license has been entered, so must reactivate
    }
    return $new;
}

//Soundcloud license catcher
function soundcloud_sound_competition_activate_license() {
 
    // listen for our activate button to be clicked
    if( isset( $_POST['edd_license_activate'] ) ) {
 
        // run a quick security check 
        if( ! check_admin_referer( 'ssc_nonce', 'ssc_nonce' ) )   
            return; // get out if we didn't click the Activate button
 
        // retrieve the license from the database
        //$license = trim( get_option( 'edd_sample_license_key' ) );
        $license = trim( get_option( 'soundcloud_sound_competition_license_key' ) );
 
        // data to send in our API request
        $api_params = array( 
            'edd_action'=> 'activate_license', 
            'license'   => $license, 
            'item_name' => urlencode( 'Soundcloud Sound Competition' ) // the name of our product in EDD
        );
 
        // Call the custom API.
        $response = wp_remote_get( add_query_arg( $api_params, 'http://lightdigitalmedia.com' ) );
 
        // make sure the response came back okay
        if ( is_wp_error( $response ) )
            return false;
 
        // decode the license data
        $license_data = json_decode( wp_remote_retrieve_body( $response ) );
 
        // $license_data->license will be either "active" or "inactive"
 
        update_option( 'soundcloud_sound_competition_license_status', $license_data->license );
 
    }
}
add_action('admin_init', 'soundcloud_sound_competition_activate_license');

