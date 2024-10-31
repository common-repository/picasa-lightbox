<?php
/*
Plugin Name: 	WP Picasa LightBox
Plugin URI: 	http://bogde.ro/computers/picasa-lightbox.htm
Description:	This plugin allows you to easily add your Picasa photos to your posts. It requires a <a href="http://zeo.unic.net.my/notes/lightbox2-for-wordpress/" title="get Lightbox">Lightbox plugin</a> to work.
Author: 			Bogdan Necula
Version: 			0.2.2b
Author URI: 	http://bogde.ro/

Copyright (c) 2008

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
*/

define('PLB_PLUGIN_NAME', 'PicasaLightBox');
define('PLB_FILE', basename(__FILE__));
define('PLB_DIR', dirname(__FILE__));
define('PLB_PATH', PLB_DIR.'/'.PLB_FILE);
define('PLB_ADMIN_URL', $_SERVER[PHP_SELF]."?page=".basename(PLB_DIR).'/'.PLB_FILE);
define('PLB_DISPLAY_NAME', 'Picasa LightBox');
define('PLB_DEFAULT_SERVER', 'http://picasaweb.google.com');
define('PLB_MSG_NOPHOTOS', '<strong>Sorry, no photos found!</strong>');
define('PLB_MSG_NOALBUMS', '<strong>Sorry, no albums found!</strong> If you are sure your settings are correct, cURL may have been disabled by your hosting provider.');

//
// extract the Picasa albums from Google: title, description, thumbnail and slug
//
function getAlbums($baseurl, $user, $access = 'public') {
	require_once(PLB_DIR.'/rss-functions-mod.php');
	$feedURL = $baseurl.'/data/feed/base/user/'.$user.'?kind=album&alt=rss&hl=en_US&access='.$access;
	define('MAGPIE_CACHE_ON', null);
	$feedContent = fetch_rss_mod($feedURL);
	
	// try to get the content for three more times in case the above call failed
	for ($i = 0; $i < 3; $i++) {
		if ((!is_array($feedContent->items)) || (sizeof($feedContent->items) == 0)) {
			$feedContent = fetch_rss_mod($feedURL);
		} else {
			break;
		}
	}
	
	$albums = array();
	$i = 0;
	if (is_array($feedContent->items)) {
		foreach ($feedContent->items as $item) { 
			$row = array();		
			$row['id'] = $i;
			$row['timestamp'] = $item['date_timestamp'];
			$row['title'] = $item['title'];
			$row['description'] = $item['media']['group_description'];
			$row['thumbnail'] = $item['media']['group_thumbnail@url'];
			
			$link = $item['link'];
			$pieces = explode("/", $link);			
			$row['slug'] = $pieces[sizeof($pieces) - 1];
		
			$albums[$i] = $row;
			$i++;
		}
	}
	return $albums;
}

//
// extract the photos from a Picasa album: title, description, thumbnails, url
//
function getPicasaPhotos($baseurl, $user, $album, $access = 'public') {
	require_once(PLB_DIR.'/rss-functions-mod.php');
	$feedURL = $baseurl.'/data/feed/api/user/'.$user.'/album/'.$album.'?kind=photo&alt=rss';
	//echo $feedURL."<br>";
	define('MAGPIE_CACHE_ON', null);
	$feedContent = fetch_rss_mod($feedURL);

	// try to get the content for three more times in case the above call failed
	for ($i = 0; $i < 3; $i++) {
		if ((!is_array($feedContent->items)) || (sizeof($feedContent->items) == 0)) {
			$feedContent = fetch_rss_mod($feedURL);
		} else {
			break;
		}
	}

	$photos = array();
	$i = 0;
	if (is_array($feedContent->items)) {
		foreach ($feedContent->items as $item) { 
			$row = array();		
			$row['id'] = $i;			
			$row['gid'] = $item['gphoto']['id'];
			$row['title'] = $item['title'];
			$row['description'] = $item['description'];
			$row['thumbnail72'] = $item['media']['group_thumbnail@url'];
			$row['thumbnail144'] = $item['media']['group_thumbnail#2@url'];
			$row['thumbnail288'] = $item['media']['group_thumbnail#3@url'];
			$row['url'] = $item['enclosure@url'];		
			$photos[$i] = $row;
			$i++;
		}
	}
	return $photos;
}

class PicasaLightBox {
	//
	// register hooks and add the actions and filters
	//
	function bootstrap() {
		// add the installation and uninstallation hooks
		register_activation_hook(PLB_PATH, array(PLB_PLUGIN_NAME, 'activate'));
		register_deactivation_hook(PLB_PATH, array(PLB_PLUGIN_NAME, 'deactivate'));

		// add the actions and filters
		add_action('admin_menu', array(PLB_PLUGIN_NAME, 'initAdminMenu'));
	} 	
	
	//
	// add the Picasa LightBox options page
	//
	function initAdminMenu() {
		add_options_page(PLB_DISPLAY_NAME, PLB_DISPLAY_NAME, 6, __FILE__, array(PLB_PLUGIN_NAME, 'getOptionsMenu'));
	} 	
	
	//
	// generates the options menu and sets Picasa LighBox options in WordPress
	//
	function getOptionsMenu() {
		// Start the cache
		ob_start();

		// validate inputs
		if ($_REQUEST['plbAction'] == 'updateOptions') {
			$pieces = explode("/", $_REQUEST['picasaServer']);
            
			// send user back to the form if there are validation errors
			if ($pieces[0] != "http:" || !strlen($pieces[2]) || strlen($pieces[3])) {
				$message = "Invalid URL for Picasa Server.";
			}            
			// otherwise save the options
			else {
				update_option('plb_picasa_server', $pieces[0] . "//" . $pieces[2]);
				update_option('plb_picasa_user', $_REQUEST['picasaUser']); 
				update_option('plb_thumb_size', $_REQUEST['thumbSize']);				
				$message = "Options saved.";
			}
		}

		// get option values
		$picasaServer	= get_option('plb_picasa_server');
		$picasaUser 	= get_option('plb_picasa_user');
		$thumbSize 		= get_option('plb_thumb_size');

		// get the options page and display it
		require(PLB_DIR.'/options.php');
		$optionsPage = ob_get_contents();
		ob_end_clean();
		echo $optionsPage;
	}   	
  
  //
  // create the Picasa Photos tab
  //
	function wp_upload_tabs($array) {
		$args = array();
		$args['album'] = '';
		$tab = array(
			'PLB' => array('Picasa Photos', 'upload_files', array(&$this, 'photosTab'), array(1, 1), $args)
		);	
		return array_merge($array, $tab);
	}
    
 	//
 	// add the new tab
 	//
	function addPhotosTab() {
		add_filter('wp_upload_tabs', array(&$this, 'wp_upload_tabs'));
	}
    
  //
  // this funtion generates the tab content
  //
	function photosTab() {
		$perpage = 10;
		$tags = $_REQUEST['tags'];
		$usecache = ! (isset($_REQUEST['refresh']) && $_REQUEST['refresh']);
		$albums = getAlbums(get_option('plb_picasa_server'), get_option('plb_picasa_user'));       
		include(PLB_DIR.'/picasa-photos-tab.php');
	}

	// 
	// install
	//
	function activate() {
		update_option('plb_picasa_server', PLB_DEFAULT_SERVER);
		update_option('plb_thumb_size', '144');			
	}
      
	// 
	// cleanup -- delete the settings
	//
	function deactivate() {
		delete_option('plb_picasa_server');
		delete_option('plb_thumb_size');
		delete_option('plb_picasa_user');
	}

	function PicasaLightBox() {
		// init
		PicasaLightBox::bootstrap();
		// add Picasa Photos Tab
		add_action('load-upload.php', array(&$this, 'addPhotosTab'));		
	}
}

$PicasaLightBox =& new PicasaLightBox();
?>