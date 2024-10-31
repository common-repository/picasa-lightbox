<script type="text/javascript">

var lastPhoto = false;

function picasa_showOptions(id) {
	if (lastPhoto) picasa_hideOptions(lastPhoto)
	lastPhoto = id
	var div = document.getElementById('options-'+id);
	if (div) div.style.display='block';
	return false;
}

function picasa_hideOptions(id) {
	var div = document.getElementById('options-'+id);
	if (div) 
		div.style.display='none';
	var e = window.event;
	if (e) {
		e.cancelBubble = true;
		if (e.stopPropagation) e.stopPropagation();
	}
	return false;
}

function picasa_addPhoto(photoUrl, thumbUrl, photoTitle, photoDescription, postId) {
	var h =
		'<a href="' + photoUrl + '" rel="lightbox[post' + postId + ']" title="' + photoDescription + '">' +
    '<img src="' + thumbUrl + '" alt="' + photoTitle + '" /></a>';
        
	var win = window.opener ? window.opener : window.dialogArguments;
	if (!win) win = top;
	tinyMCE = win.tinyMCE;
	if (typeof tinyMCE != 'undefined' && tinyMCE.getInstanceById('content')) {
		tinyMCE.selectedInstance.getWin().focus();
		tinyMCE.execCommand('mceInsertContent', false, h);
	} else 
		win.edInsertContent(win.edCanvas, h);
	return false;
}
</script>

<style type="text/css">
#upload-files a.file-link {
    width:148px;
    height:148px;
}
.photo-options {
    position:absolute;
    top:4px;
    left:4px;
    width:138px;

    background:white;
    opacity:0.9;
    border:1px solid #ccc;
    font-size:10px;
    z-index:10;
    
    display:none;
}
.photo-options div.close {
    position:absolute;
    top:2px;
    right:2px;
}
.alignleft {
    position:relative;
}
#upload-content {
    padding-top:10px;
}
</style>

<form method="get" id="photos" action="upload.php">
<input type="hidden" name="tab" value="<?php echo $_GET['tab'];?>" />
<input type="hidden" name="post_id" value="<?php echo $_GET['post_id'];?>" />
<input type="hidden" name="action" value="<?php echo $_GET['action'];?>" />
<input type="hidden" name="style" value="<?php echo $_GET['style'];?>" />
<input type="hidden" name="_wpnonce" value="<?php echo $_GET['_wpnonce'];?>" />
<input type="hidden" name="ID" value="<?php echo $_GET['ID'];?>" />
</form>

<?php 
	if ($_GET['album'] != '') {
		$photos = getPicasaPhotos(get_option('plb_picasa_server'), get_option('plb_picasa_user'), $_GET['album']);	
		$thumbOption 	= 'thumbnail'.get_option('plb_thumb_size');
			
		if (count($photos) == 0) {
			echo PLB_MSG_NOPHOTOS;
		} else {
?>
			&nbsp;&nbsp;Current album: <b><?php echo $_GET['albumtitle']; ?></b> | <a href="upload.php?tab=<?php echo $_GET['tab'] ;?>&post_id=<?php echo $_GET['post_id'] ;?>&action=<?php echo $_GET['action']; ?>&style=<?php echo $_GET['style']; ?>&_wpnonce=<?php echo $_GET['_wpnonce']; ?>&ID=<?php echo $_GET['ID']; ?>&album=&refresh=refresh&paged">Back to albums</a><br />
			<ul id='upload-files'>
			<?php
			foreach($photos as $photo) {
?>
				<li id='picasa-photo-<?php echo $photo["id"]; ?>' class='alignleft'>
					<a id='file-link-3' href='<?php echo $photo["url"]."?imgmax=640"; ?>' title='<?php echo $photo["description"]; ?>' class='file-link image' onclick="return picasa_showOptions('<?php echo $photo['id']; ?>');">
					<img id='image<?php echo $photo["id"]; ?>' src='<?php echo $photo["thumbnail144"]; ?>' alt='<?php echo $photo["description"]; ?>' style="border: 0px" /></a>
					<div class='photo-options' id='options-<?php echo $photo["id"]; ?>'>
				
					<a href="<?php echo $photo['url'].'?imgmax=320'; ?>" onclick="return picasa_addPhoto('<?php echo $photo['url'].'?imgmax=320'; ?>', '<?php echo $photo[$thumbOption]; ?>', '<?php echo addslashes($photo['title']); ?>', '<?php if ($photo['description'] != '') echo addslashes($photo['description']); else echo addslashes($photo['title']); ?>', '<?php echo $_GET['post_id']; ?>')">Size: 320</a><br />
					<a href="<?php echo $photo['url'].'?imgmax=400'; ?>" onclick="return picasa_addPhoto('<?php echo $photo['url'].'?imgmax=400'; ?>', '<?php echo $photo[$thumbOption]; ?>', '<?php echo addslashes($photo['title']); ?>', '<?php if ($photo['description'] != '') echo addslashes($photo['description']); else echo addslashes($photo['title']); ?>', '<?php echo $_GET['post_id']; ?>')">Size: 400</a><br />
					<a href="<?php echo $photo['url'].'?imgmax=640'; ?>" onclick="return picasa_addPhoto('<?php echo $photo['url'].'?imgmax=640'; ?>', '<?php echo $photo[$thumbOption]; ?>', '<?php echo addslashes($photo['title']); ?>', '<?php if ($photo['description'] != '') echo addslashes($photo['description']); else echo addslashes($photo['title']); ?>', '<?php echo $_GET['post_id']; ?>')">Size: 640</a><br />
					<a href="<?php echo $photo['url'].'?imgmax=720'; ?>" onclick="return picasa_addPhoto('<?php echo $photo['url'].'?imgmax=720'; ?>', '<?php echo $photo[$thumbOption]; ?>', '<?php echo addslashes($photo['title']); ?>', '<?php if ($photo['description'] != '') echo addslashes($photo['description']); else echo addslashes($photo['title']); ?>', '<?php echo $_GET['post_id']; ?>')">Size: 720</a><br />
					<a href="<?php echo $photo['url'].'?imgmax=800'; ?>" onclick="return picasa_addPhoto('<?php echo $photo['url'].'?imgmax=800'; ?>', '<?php echo $photo[$thumbOption]; ?>', '<?php echo addslashes($photo['title']); ?>', '<?php if ($photo['description'] != '') echo addslashes($photo['description']); else echo addslashes($photo['title']); ?>', '<?php echo $_GET['post_id']; ?>')">Size: 800</a><br />
						    
    			<div class="close"><a href="#" onclick="return picasa_hideOptions(<?php echo $photo['id']?>);">[X]</a></div>
    			</div>
				</li>
<?php
			}
?>
			</ul>
<?php
		}
	} else {
			if (count($albums) == 0) {
			echo PLB_MSG_NOALBUMS;
		} else {
?>			
			<ul id='upload-files'>
<?php
			foreach($albums as $album) {
					$timestamp	= $album['timestamp'];
					$title 			= $album['title'];
					$thumbnail 	= $album['thumbnail'];
					$slug				= $album['slug'];
?>
			<li id='picasa-album-<?php echo $album['title']?>' class='alignleft'>
				<a href="upload.php?tab=<?php echo $_GET['tab'];?>&post_id=<?php echo $_GET['post_id'];?>&action=<?php echo $_GET['action'];?>&style=<?php echo $_GET['style'];?>&_wpnonce=<?php echo $_GET['_wpnonce'];?>&ID=<?php echo $_GET['ID'];?>&album=<?php echo $slug;?>&albumtitle=<?php echo $title;?>&refresh=refresh&paged">
				<img src="<?php echo $thumbnail;?>"></a><br><?php echo $title; ?>
			</li>
<?php 
			} 
?>
			</ul>
<?php
		}
	}
?>