<?php
/*
Plugin Name: MedVid.io client
Plugin URI: http://github.com/ReelDx/wordpress-client
Description: Retrieves videos from MedVid.io 
Author: Greg Zuro <greg@zuro.net>
Version: 1.0
Author URI: http://github.com/gregzuro
*/

require 'vendor/autoload.php';


function medvidio_client_get( $atts ) {
	$a = shortcode_atts( array('id' => 0,), $atts);
	$id = $a['id'] ;
	if ($id == 0) {
		return "[ MedVid.io: No Id specified ]";
	}

	global $wpdb;
	$table_name = $wpdb->prefix . 'medvidio_videos';
	$video = $wpdb->get_results(
		"
		SELECT mv_video_id, description, mv_application, mv_public_key, mv_secret_key 
		FROM $table_name
		WHERE id = $id
		"
	);

//	$keys = array('1' => 'my_key', '2' => 'my_key2');

	$payload = array(
		"aud" => $video[0]->mv_public_key,
		"iat" => time(),
		"exp" => time()+20000,
		"sub" => $video[0]->mv_application
		);
	$token = JWT::encode($payload, $video[0]->mv_secret_key, 'HS256');

//	return "The public key is: " . $video[0]->mv_public_key . " !";

	$service_url = "https://apollo.reeldx.com/api/v1/video/" . $video[0]->mv_video_id;
	$curl = curl_init($service_url);
    curl_setopt($curl, CURLOPT_TIMEOUT, 5);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    	'Accept: application/json',
    	'Authorization: Bearer ' . $token
    	));


    $curl_response = curl_exec($curl);
	$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);

	if ($httpcode != "200") {
		return "[ MedVid.io: error retrieving: " . $httpcode . " ]";
	}
	else {
		return "[ MedVid.io: curl result is: " . $curl_response . " ]";
	}


}
add_shortcode( 'medvidio', 'medvidio_client_get');

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
	<th> WP Id </th>
	<th> Video Id </th>
	<th> User Id </th>
	<th> Public Key </th>
	<th> Secret Key </th>
	</tr>
	</thead>
	<tfoot>
	<tr>
	<th> WP Id </th>
	<th> Video Id </th>
	<th> User Id </th>
	<th> Public Key </th>
	<th> Secret Key </th>
	</tr>
	</tfoot>
	<tbody>
<?php
	global $wpdb;

	$table_name = $wpdb->prefix . 'medvidio_videos';
	$medvidio_videos = $wpdb->get_results(
		"
		SELECT id, mv_video_id, mv_application, mv_public_key, mv_secret_key
		FROM $table_name
		"
	);
?>
<?php
	foreach ( $medvidio_videos as $video) {
?>
		<tr>
<?php
		echo "<td>".$video->id."</td>";
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
$medvidio_client_db_version = '1.2';

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
