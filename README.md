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

Install wp-db-table-editor to more easily add records to the video database table (optional):
```
$ cd .../wp-content/plugins
$ git clone https://github.com/AccelerationNet/wp-db-table-editor.git
```

Go to the Wordpress Dashboard and activate the plugin(s).
This creates the ```wp_medvidio_videos``` database table for storing the data needed to retrieve the videos to which you are going to refer in your Wordpress content.

##Configuration

Create a new record in wp_options with ```option_name = "medvidio_jwplayer_license_key"``` and ```option_value``` equal to the license key for your JWPlayer.

NB: you must have the 'Enterprise' version of JWPlayer in order to stream videos from medvid.io.

##Inserting Videos

Add the data describing the video to the ```wp_medvidio_videos``` database table:

You need to specify the medvidio video 'Id' which you can get from the medvidio API.

You must specify the private and public keys associated with the medvidio 'application' that created the video.
These are available from your medvidio account dashboard at https://mercury.reeldx.com/#/applications (click on the little pencil to the right).

You can optionally specify a Description for the video.
This description will be inserted immediately after the player in the Wordpress document.

To insert the video into a Wordpress document, use a Wordpress 'shortcode' in your post like:

```
[medvidio id=53]
```

By default the created player will have the following values:
- Height: 270
- Width: 480
- Aspectratio: ""

You can specify height / width / aspectratio as well:

```
[medvidio id=xx height=480 width=720 aspectratio=16:9] 
```
Note that when specifying an aspectratio value the height value will be ignored even if one is provided.

To explicitly exclude the height or width value you can define them as "" values:

```
[medvidio id=xx height="" width=100% aspectratio=16:9] 
```

The video will be inserted with the optional description following.

##Improvements

- Create the *medvidio_jwplayer_license_key* wp_option database record option automatically on plugin installation
- have license key entry on admin / config screen
- have apollo server address on admin / config screen
 
## Testing

Download the Wordpress VM from Bitnami: https://bitnami.com/stack/wordpress/virtual-machine#virtualbox .
Be sure to choose the VirtualBox version of the VM rather than the VMWare version.
Unzip the VM that you downloaded.

In VirtualBox, do *File->Import* and specify the .ovf file in the un-zipped VM 

Once the VM has started it will tell you the IP address that it has been assigned.
You can go to a browser in your *host* and enter that IP in order to see the Wordpress application.
If you go to ```<thatIP>/wp-admin``` , then you can give user/bitnami in order to login in to Wordpress and see the Dashboard.

First, we need to enable sshd so that we can more easily paste commands into the VM.
Log in to the VM in the VirtualBox window using bitnami/bitnami (unless you have already changed the password, of course).
Then do:

```
$ sudo mv /etc/init/ssh.conf.back /etc/init/ssh.conf
$ sudo start ssh
```

Now you can ssh in to the VM from the host machine with:

```
$ ssh bitnami@<thatIP>
```

and you should be able to paste the commands into the ssh session using your host's normal copy and paste features.

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

Next, install the DB editor plugin:

```
$ cd /opt/bitnami/apps/wordpress/htdocs/wp-content/plugins
$ git clone --recursive https://github.com/AccelerationNet/wp-db-table-editor.git
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
Find *DB-table-editor* and click *Activate* .
On the left, click *Settings->MedVidioClient* .
See that there are no entries in the *Registered ... content* table.

In the VM start the mysql cli:

```
$ mysql -u root -p
```

Note that the password should be *bitnami*.

Now you need to add one record to the database:

```
mysql> insert into bitnami_wordpress.wp_options (option_name, option_value) values ('medvidio_jwplayer_license_key', '<key>');
Query OK, 1 row affected (0.00 sec)
```

where you replace <key> with the JW Player Enterprise key.

Go back to the Wordpress Dashboard and click *DB Table Editor->Medvidio Videos* on the left.
Click the *New* button and enter then description and (Apollo) id for the video that you want to add, along with the application name and the public and secret keys associated with the application / user that has view rights to that video.
Once the values are entered, click the *Save 5 Changes* button along the top.
Note the value in the id column for the record that you just added.
That is the id value that you need to use in the shortcode as described below.

Now you are ready to add a Wordpress post with a shortcode specifying this video. 
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

You can also include the *height* / *width* / *aspectratio* values:

```
[medvidio id=xx height=480] 
```

```
[medvidio id=xx height=480 width=720] 
```
```
[medvidio id=xx height=480 width=720 aspectratio=16:9] 
```
Note that by including the *aspectratio* value the *height* value will be ignored

You can also explicitly exclude the *height* or *width* values:

```
[medvidio id=xx height=""] 
```
```
[medvidio id=xx height="" width=""] 
```




