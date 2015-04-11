<?php
/*
Plugin Name: MedVid.io client
Plugin URI: http://github.com/ReelDx/wordpress-client
Description: Retrieves videos from MedVid.io 
Author: Greg Zuro <greg@zuro.net>
Version: 1.0
Author URI: http://github.com/gregzuro
*/

add_action('admin_menu', 'medvidio_client_admin_actions');
function medvidio_client_admin_actions() {
	add_options_page('MedVidioClient', 'MedVidioClient', 'manage_options', __FILE__, 'medvidio_client_admin');
}

function medvidio_client_admin()
{
?>
	<div class="wrap">
	<h4>Registered MedVid.io content:</h4>
	<table class="widefat">
	<thead>
	<tr>
	<th> Video Id </th>
	<th> Application </th>
	<th> Public Key </th>
	<th> Secret Key </th>
	</tr>
	</thead>
	<tfoot>
	<tr>
	<th> Id </th>
	<th> Application </th>
	<th> Public Key </th>
	<th> Secret Key </th>
	</tr>
	</tfoot>
	<tbody>
<?php
	global $wpdb;

	$medvidio_videos = $wpdb->get_results(
		"
		SELECT mv_video_id, mv_application, mv_public_key, mv_secret_key
		from wp_medvidio_videos
		"
	);
?>
<?php
	foreach ( $medvidio_videos as $video) {
?>
		<tr>
<?php
		echo "<td>".$video->mv_video_id."</td>";
		echo "<td>".$video->mv_application."</td>";
		echo "<td>".$video->mv_public_key."</td>";
		echo "<td>".$video->mv_secret_key."</td>";
?>
		</tr>
<?php
	}
?>
	</tbody>
	</table>
	</div>
<?php
}

register_activation_hook( __FILE__, 'medvidio_client_db_install');
//register_activation_hook( __FILE__, 'db_install_data');
global $medvidio_client_db_version;
$medvidio_client_db_version = '1.0';

function medvidio_client_db_install() {
	global $wpdb;
	global $medvidio_client_db_version;
	$installed_version = get_option( "medvidio_client_db_version");

	if ( $installed_version != $medvidio_client_db_version ) {

		$table_name = $wpdb->prefix . 'medvidio_videos';

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			description text NOT NULL,
			mv_video_id int NOT NULL,
			mv_application tinytext NOT NULL,
			mv_public_key tinytext NOT NULL,
			mv_secret_key tinytext NOT NULL,
			UNIQUE KEY id (id)
			) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $sql );

		update_option( 'medvidio_client_db_version', $medvidio_client_db_version) ;
			
		}
			
	}

function medvidio_client_db_check() {

	global $medvidio_client_db_version;
	if ( get_site_option( 'medvidio_client_db_version' ) != $medvidio_client_db_version ) {
		medvidio_client_install();
	}
}
add_action( 'plugins_loaded', 'medvidio_client_db_check()');

?>
