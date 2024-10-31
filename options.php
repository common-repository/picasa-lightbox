<?php
/**
 * Set options for Picasa LightBox.
 *
 * Copyright 2008 Bogdan Necula
 * 
 * This file is part of Picasa LightBox.
 *
 * Picasa LightBox is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * Picasa LightBox is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
 ?>
 
<div class="wrap">
	<h2><?php echo PLB_DISPLAY_NAME; ?></h2>
    
	<?php 
		if (strlen($message)) {
			echo '<div id="message" class="updated fade"><p><strong>'.$message.'</strong></p></div>';
			unset($message);
		} 
	?>

	<table border="0" cellpadding="2" cellspacing="2">
		<tr><td valign="top">    
    <h3>Options</h3>

    <form action="<?php echo PLB_ADMIN_URL; ?>" method="post">
    <input type="hidden" name="plbAction" value="updateOptions">
    
    <table border="0" cellspacing="3" cellpadding="3">   	
    <tr>
    <td>Your Picasa Server:</td>
    <td><input type="text" name="picasaServer" value="<?php echo $picasaServer ?>" size="30"></td>
    <td>The base URL of your Picasa server. If unsure type "http://picasaweb.google.com".</td>
    </tr>
    
    <tr>
    <td>Your Picasa Username:</td>
    <td><input type="text" name="picasaUser" value="<?php echo $picasaUser ?>" size="30"></td>
    <td>Your login name for Picasa.</td>
    </tr>
          
    <tr>
    <td>Thumbnail Size:</td>
    <td>
    	<input type="radio" name="thumbSize" value="72" <?php if ($thumbSize == 72) echo 'checked'; ?>>72px<br />
    	<input type="radio" name="thumbSize" value="144" <?php if ($thumbSize == 144) echo 'checked'; ?>>144px (recommended)<br />
    	<input type="radio" name="thumbSize" value="288" <?php if ($thumbSize == 288) echo 'checked'; ?>>288px
    </td>
    <td>Default size for the thumbnails.</td>
    </tr>     
    </table>

    <p><input type="submit" name="save" value="Save Options" /></p>
    </form>
  	</td><td valign="top">
			<div style="float:right; width:180px; background:#ccc; border:1px solid #999; padding:10px; font-size:0.9em; margin-left:10px; ">
				<h3>Support. Donate.</h3>
				<p>This plugin is freeware, and will always remain so.</p>
				<p>If you like this plugin, and wish to contribute to its development, consider making a donation.</p> 
				
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but04.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
				<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
				<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
				</form>

				<p>If you like this plugin, <b>don't forget to vote</b> on the <a href="http://wordpress.org/extend/plugins/picasa-lightbox/">official WP Plugins page</a>!</p>
			</div>
		</td></tr>
		</table>     
</div>