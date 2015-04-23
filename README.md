# medvidio-wordpress-plugin
Wordpress plugin for accessing video from Apollo / Vostok

##Installation

Clone this repository into your Wordpress plugin directory so you have:

```
.../wp-content/plugins/medvidio-wordpress-plugin
```

By doing:

```
$ cd .../wp-content/plugins
$ git clone https://github.com/ReelDx/medvidio-wordpress-plugin.git
```

Go to the Wordpress Dashboard and activate the plugin.
This creates the ```wp_medvidio_videos``` database table for storing the data needed to retrieve the videos to which you are going to refer in your Worpress content.

##Configuration

Create a new record in wp_options with ```option_name = "medvidio_jwplayer_license_key"``` and ```option_value``` equal to the license key for your JWPlayer.

NB: you must have the 'Enterprise' version of JWPlayer in order to stream videos from medvid.io.

##Inserting Videos

Add the data describing the video to the ```wp_medvidio_videos``` database table:

You need to specify the medvidio video 'Id' which you can get from the medvidio API.

You must specify the private and public keys associated with the medvidio 'application' that created the video.
These are available from your medvidio account dashboard at https://mercury.reeldx.com/#/applications (click on the little pencil to the right).

You must also specify the width and height of the player during playback.

You can optionally specify a Description for the video.
This description will be inserted immediately after the player in the Worpress document.

To insert the video into a Wordpress document, use a Wordpress 'shortcode' in your post like:

```
[medvidio id=53]
```

The video will be inserted with the optional description following.

##Improvements

- Create the *medvidio_jwplayer_license_key* option automatically on plugin installation
- have license key entry on admin / config screen
- additional player parameters in admin screen
- allow editing and adding records to the ```wp_medvidio_videos``` database table on the admin screen
 
## Testing

Download the Wordpress VM from Bitnami: https://bitnami.com/stack/wordpress/virtual-machine#virtualbox .
Unzip the VM that you downloaded.
In VirtualBox, do *File->Import* and specify the .ovf file in the un-zipped VM 

Once the VM has started it will tell you the IP address that it has been assigned.
You can go to a browser in your *host* and enter that IP in order to see the Wordpress application.
If you go to ```<thatIP>/wp-admin``` , then you can give user/bitnami in order to login in to Wordpress and see the Dashboard.

Go to the VM console and do:

```
$ sudo apt-get update
$ sudo apt-get -y install git
```

Now that you have git installed, you can:

```
$ cd /opt/bitnami/apps/wordpress/htdocs/wp-content/plugins
$ git clone https://github.com/ReelDx/medvidio-wordpress-plugin.git
```

You now need to install the JW Player.
In the VM:

```
$ cd /opt/bitnami/apps/wordpress/htdocs/wp-content
$ wget https://account.jwplayer.com/static/download/jwplayer-6.12.zip
$ unzip jwplayer-6.12.zip
$ rm jwplayer-6.12.zip
```

Now go back to the Wordpress Dashboard and click on *Plugins* on the left side.
Find *MedVid.io client* and click *Activate* .
On the left, click *Settings->MedVidioClient* .
See that there are no entries in the *Registered ... content* table.

In the VM start the mysql cli:

```
$ mysql -u root -p
```

Note that the password should be *bitnami*.

Now you need to add two records to the database:

```
mysql> insert into bitnami_wordpress.wp_options (option_name, option_value) values ('medvidio_jwplayer_license_key', '<key>');
Query OK, 1 row affected (0.00 sec)
mysql> insert into bitnami_wordpress.wp_medvidio_videos (description, mv_video_id, mv_application, mv_public_key, mv_secret_key, height, width) values ('<desc>', '<video_id>', '<application>', '<public_key>', '<secret_key>', '<height>', '<width>');
Query OK, 1 row affected (0.00 sec)
```

where you replace all the values ('<...>') with appropriate ones for a video that you have added to (prod) Apollo.

Go back to the Wordpress Dashboard and the settings for the MedVidio plugin (on the left, click *Settings->MedVidioClient*) where you should now see the record that you just added.
Note the value under *WP Id*. 
That is the id value that you need to use in the shortcode as described below.

Now you are ready to add a worpress post with a shortcode specifying this video. 
In the Wordpress Dashboard, click *Posts->Add New* .
Enter a title, then some text in the main entry box below.
Include a shortcode referencing your video like:

```
Here's some text.

[medvidio id=xx]

Some additional text.
```

where xx is the *Wp Id* that you noted earlier.

Now click the *Preview* button toward the upper right and you should see the post with the included video on a new browser page.






