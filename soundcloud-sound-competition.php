<?php
/*
Plugin Name: SoundCloud Sound Competition
Plugin URI: http://webhjelpen.no/wordpress-plugin/soundcloud-sound-competition/
Description: Host your own Sound Contest integrated with SoundCloud, users connect easy with SoundCloud to choose track to add to your competition. Everything within your WordPress web site.
Version: 0.9.2
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



global $wpdb;

//Set plugin local path
define( 'MYPLUGINNAME_PATH', plugin_dir_path(__FILE__) );

//Add class functions
require_once('Classes/class-helper-functions.php');
require_once('Classes/class-installation.php');
require_once('Classes/class-sc-connect.php');
require_once('Classes/class-remixers.php');
require_once('Classes/class-admin-remixers.php');
require_once('Classes/class-admin-settings.php');

//Add actions
add_action('activate_soundcloud-sound-competition/soundcloud-sound-competition.php', 'remixcomp_install');
add_action('admin_menu', 'remixcomp_admin_actions');
add_action('init', 'init_sessions');

//Add shortcodes
add_shortcode( 'soundcomp-add', 'remixcomp_sc_connect' );
add_shortcode( 'soundcomp-entrees', 'remixcomp_remixers' );
