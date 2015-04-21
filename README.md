# medvidio-wordpress-plugin
Wordpress plugin for accessing video from Apollo / Vostok

##Installation

Clone this repository into your Wordpress plugin directory so you have:

```
.../wp-content/plugins/medvidio-wordpress-plugin
```

Go to the Wordpress Dashboard and activate the plugin.
This creates the ```wp_medvidio_videos``` database table for storing the data needed to retrieve the videos that you are going to refer to in your Worpress content.

##Configuration

Create a new record in wp_options with ```option_name = "medvidio_jwplayer_license_key"``` and ```option_value``` equal to the licence key for your JWPlayer.

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
 
