<?php
/*
Plugin Name: WP Link To Us
Plugin URI: http://wplinktous.com/
Description: Linking to you should be easy!
Version: 2.0
Author: Xpancom - Mitch Moccia
Author URI: http://xpancom.com
License: GPL
*/
?>
<?php
/* Runs when plugin is activated */
register_activation_hook( __FILE__, 'create_wplinktous_table' );

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'wplinktous_remove' );

function create_wplinktous_table (){
	global $table_prefix, $wpdb;

	$wplinktous_data = $table_prefix . "wplinktous";

	if($wpdb->get_var("show tables like '$wplinktous_data'") != $wplinktous_data) {
		$sql0  = "CREATE TABLE `". $wplinktous_data . "` ( ";
		$sql0 .= "`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
		`addgraphicstitle` VARCHAR( 250 ) NOT NULL ,
		`addgraphicsimage` VARCHAR( 250 ) NOT NULL ,
		`addgraphicsurl` VARCHAR( 250 ) NOT NULL ,
		`addgraphicstarget` INT( 2 ) NOT NULL DEFAULT  '1',
		`addtexttext` VARCHAR( 255 ) NOT NULL ,
		`active` INT( 2 ) NOT NULL DEFAULT  '1',
		`fieldtype` ENUM(  'addgraphics',  'addtext' ) NOT NULL ,
		`titleofpage` VARCHAR( 255 ) NOT NULL ,
		`introtext` VARCHAR( 500 ) NOT NULL ,
		`footertext` VARCHAR( 500 ) NOT NULL ,
		PRIMARY KEY (  `id` )
		) ENGINE = MYISAM";
		//require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
		require_once ABSPATH.'wp-admin/includes/upgrade.php';
		dbDelta($sql0);
		
			$insert = "INSERT INTO " . $wplinktous_data .
            " (fieldtype, titleofpage, introtext, footertext) " .
            "VALUES ('','Link To Us Now!','If you have a Website and want to link to us, we have made it super easy. Instructions: Use the one click button below to instantly copy the raw HTML code to your clipboard. Then place the code in your Web pages and emails to easily link to us. Thank you.','We appreciate you taking the time to link to us. Please abide by the terms.')";
		$results = $wpdb->query( $insert );
	}
	

	$imgdir = category_images_base_dir();
	if (empty($imgdir)){
		echo 'There was a problem creating the image upload directory';
		exit;
	}
}

function wplinktous_remove() {
	delete_option('wplinktous_data');
}

function sanitize($in) {
	return addslashes(htmlspecialchars(strip_tags(trim($in))));
}

function category_images_base_dir()
{
    // Where should the dir be? Get the base WP uploads dir
    $wp_upload_dir = wp_upload_dir();
    $base_dir = $wp_upload_dir[ 'basedir' ];
    // Append our subdir
    $dir = $base_dir . '/wplinktous';
    // Does the dir exist? (If not, then make it)
    if ( ! file_exists( $dir ) ) {
        mkdir( $dir ); 
    }
    // Now return it
    return $dir;
}

add_shortcode("wplinktous", "wp_link_to_us_page");

function get_blog_path(){

	//$pathis = bloginfo('url');
	$pathis = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	return $pathis;
}

function wp_link_to_us_page(){

	$theURL = 'theURL';
		global $table_prefix, $wpdb;

	$wplinktous_data = $table_prefix . "wplinktous";

		echo '
	<script type="text/javascript" src="'.plugins_url( 'js/jquery-1.4.3.min.js', __FILE__ ).'"></script>		
	<script type="text/javascript" src="'.plugins_url( 'js/jquery.zclip.min.js', __FILE__ ).'"></script>	
	';
	
	echo '<style>
div.try 
.try #text-to-copy { border:4px solid #666; margin-top:0; }';
//echo 'what is next ?';
	//echo "<h4>If you have a Website and want to link to $theURL, we have made  it super easy.</h4>";

		
		foreach($wpdb->get_results("SELECT * FROM $wplinktous_data WHERE active = 1;") as $key => $row) {
		echo '.try #copy-button'.$row->id.' { width:110px; height:50px; background:url('.plugins_url( 'images/button.png', __FILE__ ).') no-repeat 0 0; display:block; overflow:hidden; color:#fff; text-decoration:none; text-indent:30px; line-height:43px; vertical-align:middle; margin:0; font-size: 16px; }';
		}
		
			echo '</style>';
		// each column in your row will be accessible like this
	//echo "<h4>Instructions: Use the one click button below to instantly copy the raw HTML code to your clipboard. Then place the code in your Web pages and emails to easily link to us. Thank you for your interest in $theURL</h4>";
	foreach($wpdb->get_results("SELECT * FROM $wplinktous_data WHERE active = 1 LIMIT 1;") as $key => $row) {

		echo "<h4>$row->titleofpage</h4>";
		
		echo "<h4>$row->introtext</h4>"; 
	}

	
	echo '<h3>Graphical Links</h3>';
	echo '<div class="try">';
	echo '
	<table class="contacts" cellspacing="5" summary="Contacts template">

	<tr class="contactDept">		
		<th>Asset</th>
		<th>Raw HTML Code</th>
		</tr>';
		
			global $table_prefix, $wpdb;

	$wplinktous_data = $table_prefix . "wplinktous";
		
		foreach($wpdb->get_results("SELECT * FROM $wplinktous_data WHERE active = 1 AND fieldtype='addgraphics';") as $key => $row) {
		// each column in your row will be accessible like this
		$addgraphicsurlcl = stripslashes($row->addgraphicsurl);
		$titleofgraphiccl = stripslashes($row->addgraphicstitle);
		$addgraphicsimagecl = stripslashes($row->addgraphicsimage);
		
		if ($row->addgraphicstarget == '0')	{
			$selecttargetcl = '_blank';
		}else{
			$selecttargetcl = '_self';
		}
		
		$blogurlwp = get_blog_path();	
		$blogurlwpbase = str_replace("wp-link-to-us/", "", $blogurlwp);
		//echo "The path is ".$blogurlwpbase."End of";
		//exit;
		
		echo '
		<tr>
		<td class="contact" width="50%"><a href="'.$addgraphicsurlcl.'" target="_blank"><img width="250" src="http://'.$blogurlwpbase.$row->addgraphicsimage.'"></a><br><strong>'.$row->addgraphicstitle.'</strong></td>
		<td class="contact" width="50%">';
				
		echo '
		<textarea rows="3" cols="30"><a href="'.$addgraphicsurlcl.'" title="'.$titleofgraphiccl.'" target="'.$selecttargetcl.'"><img src="http://'.$blogurlwpbase.$addgraphicsimagecl.'" alt="'.$titleofgraphiccl.'">
		</textarea>

		<a id="copy-button'.$row->id.'" href="#">Copy</a>  
		</td>
		</tr>';
		
		}

		
	echo '</table>';
	
	echo '<h3>Text Links</h3>';
	
	echo '
		<table class="contacts" cellspacing="5" summary="Contacts template">

	<tr class="contactDept">		
		<th>Asset</th>
		<th>Raw HTML Code</th>
		</tr>';
		
			global $table_prefix, $wpdb;

	$wplinktous_data = $table_prefix . "wplinktous";
		
		foreach($wpdb->get_results("SELECT * FROM $wplinktous_data WHERE active = 1 AND fieldtype='addtext';") as $key => $row) {
		// each column in your row will be accessible like this
		$addtexttextcl = stripslashes($row->addtexttext);
		echo "
		<tr>
		<td class=\"contact\" width=\"50%\">$addtexttextcl</td>
		<td class=\"contact\" width=\"50%\">

		<textarea rows=\"3\" cols=\"30\">$addtexttextcl</textarea>

		<a id=\"copy-button$row->id\" href=\"#\">Copy</a>
		</td>
		</tr>";
		
		}
		
		echo '</table>';
		echo '</div>';
		
		//echo '<h4>We appreciate you taking the time to link to us. Please abide by the terms.</h4>';
		foreach($wpdb->get_results("SELECT * FROM $wplinktous_data WHERE active = 1 LIMIT 1;") as $key => $row) {
		echo "<h4>$row->footertext</h4>";
		
		echo '<script type="text/javascript">
$(document).ready(function(){';

			$blogurlwp = get_blog_path();	
			$blogurlwpbase = str_replace("wp-link-to-us/", "", $blogurlwp);
			$flashpath = 'http://'.$blogurlwpbase.'wp-content/plugins/wp-link-to-us/js/ZeroClipboard.swf';

			foreach($wpdb->get_results("SELECT * FROM $wplinktous_data WHERE active = 1;") as $key => $row) {

				echo "$(\"#copy-button$row->id\").zclip({
					path: \"$flashpath\",
					copy: function(){return $(this).prev().val();}
				});";
	}

echo '
});
</script>';
	}
}

define ("MAX_SIZE","100"); 

if ( is_admin() ){

/* Call the html code */
add_action('admin_menu', 'wplinktous_admin_menu');

function wplinktous_admin_menu() {
	add_options_page('WP Link To Us', 'WP Link To Us', 'administrator',
	'WPLinkToUs', 'wplinktous_html_page');
	}
		
	wp_enqueue_script('jquery.tools.min', plugins_url( 'js/jquery.tools.min.js', __FILE__ ));
	wp_enqueue_style('tabs', plugins_url( 'css/tabs.css', __FILE__ ));
	wp_enqueue_script('jquery.zclip.min.js', plugins_url( 'js/jquery.zclip.min.js', __FILE__ ));
}

function wplinktous_html_page() {
?>
<style>
	
/* tab pane styling */
.panes div {
	display:none;		
	padding:15px 10px;
	border:1px solid #999;
	border-top:0;
	
	font-size:14px;
	background-color:#fff;
}

	</style>
	<body>
	<h1>WP Link To Us</h1>
	<h3><em>Linking to you should be easy!</em></h3>
	<p>
	<form method="post" action="<?php echo $PHP_SELF;?>" enctype="multipart/form-data" name="newad" id="newad">
	
	<?		
		//print_r($_POST);
 		if ($_POST['submit'] == 'Save Graphic'){
 		
 		$image=$_FILES['image']['name'];
		//if it is not empty
		if ($image) 
		{
			//get the original name of the file from the clients machine
			$filename = stripslashes($_FILES['image']['name']);
			
			$path_parts = pathinfo($filename);
			$extension = $path_parts['extension'];			
			
			$extension = strtolower($extension);
			//if it is not a known extension, we will suppose it is an error and will not  upload the file,  
			//otherwise we will do more tests
			if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif")) 
			{
			//print error message
			echo '<h1>Unknown extension!</h1>';
			$errors=1;
			}
			else
			{
			//get the size of the image in bytes
			//$_FILES['image']['tmp_name'] is the temporary filename of the file
			//in which the uploaded file was stored on the server
			$size=filesize($_FILES['image']['tmp_name']);
			
			/*
			//compare the size with the maxim size we defined and print error if bigger
			if ($size > MAX_SIZE*1024)
			{
			echo '<h1>You have exceeded the size limit!</h1>';
			$errors=1;
			}
			*/
    
			//we will give an unique name, for example the time in unix time format
			$image_name=time().'.'.$extension;
			//the new name will be containing the full path where will be stored (images folder)
			$fullpath = dirname(__FILE__);
			$newname=$fullpath."/images/".$image_name;
			
			$upload_dir = wp_upload_dir();
			$newname = $upload_dir['basedir']."/wplinktous/".$image_name;
			
			$recordname = "/wp-content/uploads/wplinktous/".$image_name;
			
			//echo 'name is '.$newname;
			//we verify if the image has been uploaded, and print error instead
			$copied = copy($_FILES['image']['tmp_name'], $newname);
				if (!$copied) 
				{
				echo '<h1>Copy unsuccessfull!</h1>';
				$errors=1;
				}
			}
		}
 		
	 		global $wpdb;
		
			$dbfields['addgraphicstitle'] = $_POST['titleofgraphic'];
			$dbfields['addgraphicsimage'] = $recordname;
			$dbfields['addgraphicsurl'] = $_POST['graphicurl'];
			$dbfields['addgraphicstarget'] = $_POST['selecttarget'];

			$result = $wpdb->insert( 'wp_wplinktous', $dbfields);
			
			$_POST['res'] = 'Success. Your graphic has been saved.';		
		}
		
		if ($_POST['submit'] == 'Edit Graphic'){
 		//echo('Im here');
 		//exit;
 		$image=$_FILES['image']['name'];
 		$editid = $_POST['editid'];
		//if it is not empty
		if ($image) 
		{
			//get the original name of the file from the clients machine
			$filename = stripslashes($_FILES['image']['name']);
			//get the extension of the file in a lower case format

			$path_parts = pathinfo($filename);
			$extension = $path_parts['extension'];
			$extension = strtolower($extension);
			//if it is not a known extension, we will suppose it is an error and will not  upload the file,  
			//otherwise we will do more tests
			if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif")) 
			{
			//print error message
			echo '<h1>Unknown extension!</h1>';
			$errors=1;
			}
			else
			{
			//get the size of the image in bytes
			//$_FILES['image']['tmp_name'] is the temporary filename of the file
			//in which the uploaded file was stored on the server
			$size=filesize($_FILES['image']['tmp_name']);
			/*
			//compare the size with the maxim size we defined and print error if bigger
			if ($size > MAX_SIZE*1024)
			{
			echo '<h1>You have exceeded the size limit!</h1>';
			$errors=1;
			}
			*/
			//we will give an unique name, for example the time in unix time format
			$image_name=time().'.'.$extension;
			//the new name will be containing the full path where will be stored (images folder)
			$fullpath = dirname(__FILE__);
			$newname=$fullpath."/images/".$image_name;
			$recordname = plugins_url('images/'.$image_name, __FILE__ );
			//echo 'name is '.$newname;
			//we verify if the image has been uploaded, and print error instead
			
			$upload_dir = wp_upload_dir();
			$newname = $upload_dir['basedir']."/wplinktous/".$image_name;			 
			$recordname = "/wp-content/uploads/wplinktous/".$image_name;
						
			$copied = copy($_FILES['image']['tmp_name'], $newname);
				if (!$copied) 
				{
				echo '<h1>Copy unsuccessfull!</h1>';
				$errors=1;
				}
			}
		}
 		
	 		global $wpdb;
		
			$dbfields['addgraphicstitle'] = $_POST['titleofgraphic'];
			if ($recordname == ''){
			$dbfields['addgraphicsimage'] = $_POST['addgraphicsimageold'];
			}else{
			$dbfields['addgraphicsimage'] = $recordname;
			}
			
			$dbfields['addgraphicsurl'] = $_POST['graphicurl'];
			$dbfields['addgraphicstarget'] = $_POST['selecttarget'];

			$result = $wpdb->update( 'wp_wplinktous', $dbfields,
			
			array(
					'id' => $editid
				)
			);		
			
			$_POST['editimage'] = '';
			$_POST['res'] = 'Your changes have been saved.';	
		}

		if ($_POST['submit'] == 'Save Text'){
 		
	 		global $wpdb;
		
			$dbfields['addtexttext'] = $_POST['addtexttext'];			
		    $dbfields['fieldtype'] = 'addtext';		
		    	
			$result = $wpdb->insert( 'wp_wplinktous', $dbfields);	
			$_POST['res'] = 'Success. Your text has been saved.';					
		}
		
		if ($_POST['submit'] == 'Edit Text'){

	 		global $wpdb;
			
			$edittextid = $_POST['edittextid'];
			$dbfields['addtexttext'] = $_POST['addtexttext'];			

			$result = $wpdb->update( 'wp_wplinktous', $dbfields,
			
			array(
					'id' => $edittextid
				)
			);	
			$_POST['res'] = 'The asset has been updated';				
		}
		
		if ($_POST['submit'] == 'Save Settings'){
			
	 		global $wpdb;
			
			$dbfields['titleofpage'] = $_POST['titleofpage'];			
			$dbfields['introtext'] = $_POST['introtext'];			
			$dbfields['footertext'] = $_POST['footertext'];	

			$result = $wpdb->update( 'wp_wplinktous', $dbfields, array(
					'active' => 1
				));				

			$_POST['res'] = 'The settings have been updated';				
		}
		
		if ($_POST['deleteimage'] != ''){
			global $wpdb;
 			$delid = $_POST['deleteimage'];
			$wpdb->update( 'wp_wplinktous', 
				array( 
					'active' => '0'
				),
				array(
					'id' => $delid
				)
				);
				
				$_POST['res'] = 'The asset has been deleted';
			}
		
		if ($_POST['deletetext'] != ''){
			global $wpdb;
 			$delid = $_POST['deletetext'];
			$wpdb->update( 'wp_wplinktous', 
				array( 
					'active' => '0'
				),
				array(
					'id' => $delid
				)
				);
				
				$_POST['res'] = 'The asset has been deleted';
			}
			
	
	if ($_POST['editimage'] != ''){ 

	global $table_prefix, $wpdb;

	$wplinktous_data = $table_prefix . "wplinktous";

//$editid = $_POST['editimage'];

$editid = sanitize($_POST['editimage']);
$editid = mysql_real_escape_string($editid);

$resedit = $wpdb->get_row("SELECT * FROM $wplinktous_data WHERE id = $editid;");
$addgraphicsurlcl = stripslashes($resedit->addgraphicsurl);
?>
<p><table border="0">
	<tr>
		<td><h3>Title (Title of graphic, 'a' tag title attribute and 'img' tag alt.)</h3></td>
	</tr>
	<tr>
	<td>
		<input type="text" id="titleofgraphic" name="titleofgraphic" value="<?=$resedit->addgraphicstitle;?>" size="50">
	</td>
	</tr>
	<tr>
		<td><h3>Select a graphic to upload</h3></td>
	</tr>
	<tr>
	<td>
	    <img width="120" height="120" src="<?=$resedit->addgraphicsimage;?>"><br>
		<input type="file" name="image" size="60" value="<?=$resedit->addgraphicsimage;?>">
	</td>
	</tr>
	<tr>
		<td><h3>Enter the URL of where the graphic should point to when</h3></td>
	</tr>
	<tr>
	<td>
		<textarea rows="3" cols="30"><?=$addgraphicsurlcl;?></textarea>		
	</td>
	</tr>
	<tr>
		<td><h3>Select target</h3></td>
	</tr>
	<tr>
	<td>
		<input type="radio" id="selecttarget" name="selecttarget" value="_blank" size="25">Same window&nbsp;&nbsp;
		<input type="radio" id="selecttarget" name="selecttarget" value="_self" size="25" checked>New window	
	</td>
	</tr>
	<tr>
    	<td>&nbsp;</td>
  	</tr>
	<tr>
	<td>
	<input type="hidden" name="editid" value="<?=$editid;?>">
	<input type="hidden" name="addgraphicsimageold" value="<?=$resedit->addgraphicsimage;?>">
	<input type="submit" value="Edit Graphic" name="submit"/></td>
	</tr>
	</table>
<?
}else if ($_POST['edittext'] != ''){
	global $table_prefix, $wpdb;
	
	$wplinktous_data = $table_prefix . "wplinktous";
	
	$edittextid = sanitize($_POST['edittext']);
	$edittextid = mysql_real_escape_string($editid);

$resedit = $wpdb->get_row("SELECT * FROM $wplinktous_data WHERE id = $edittextid;");
?>
<h3>Enter and style text that your visitors can use to link to you</h3>
	<table border="0">
	<tr>
    <td>
    <textarea rows="8" cols="50" name="addtexttext">
	<?=$resedit->addtexttext;?></textarea>
    </td>
    <td>&nbsp;</td>
    <td align="left" valign="top">
    
	This is text that the visitors can<br> place in their member areas<br> to link to you.
	
    </td>
  	</tr>
  	<tr>
    	<td>&nbsp;</td>
  	</tr>
  	<tr>
    	<td>
		<input type="hidden" name="edittextid" value="<?=$edittextid;?>">
    	<input type="submit" value="Edit Text" name="submit"/></td>
  	</tr>
	</table>
<?
}else{
?>	
<h2><font color="red"><?=$_POST['res']?></font></h2>
	<!-- the tabs -->
<ul class="tabs">
	<li><a href="#tab-1">Add Graphics</a></li>
	<li><a href="#tab-2">Add Text</a></li>
	<li><a href="#tab-3">My Assets</a></li>
	<li><a href="#tab-4">Settings</a></li>
</ul>

<!-- tab "panes" -->
<div class="panes">
	<!-- start Add Graphics page -->	
	<div id="tabs-1">	
	<p><table border="0">
	<tr>
		<td><h3>Title (Title of graphic, 'a' tag title attribute and 'img' tag alt.)</h3></td>
	</tr>
	<tr>
	<td>
		<input type="text" id="titleofgraphic" name="titleofgraphic" placeholder="Enter some descriptive text" size="50">
	</td>
	</tr>
	<tr>
		<td><h3>Select a graphic to upload</h3></td>
	</tr>
	<tr>
	<td>
		<input type="file" name="image" size="60">
	</td>
	</tr>
	<tr>
		<td><h3>Enter the URL of where the graphic should point to when</h3></td>
	</tr>
	<tr>
	<td>
		<input type="text" id="graphicurl" name="graphicurl" placeholder="the URL" size="50">
	</td>
	</tr>
	<tr>
		<td><h3>Select target</h3></td>
	</tr>
	<tr>
	<td>
		<input type="radio" id="selecttarget" name="selecttarget" value="_blank" size="25">Same window&nbsp;&nbsp;
		<input type="radio" id="selecttarget" name="selecttarget" value="_self" size="25" checked>New window	
	</td>
	</tr>
	<tr>
    	<td>&nbsp;</td>
  	</tr>
	<tr>
	<td>
	<input type="hidden" name="savechanges" value="addgraphicstab">
	<input type="submit" value="Save Graphic" name="submit" /></td>
	</tr>
	</table>
	</div>
	<!-- end Add Graphics page -->	
	<!-- start Add Text page -->	
	<div id="tabs-2">
	<h3>Enter and style text that your visitors can use to link to you</h3>
	<table border="0">
	<tr>
    <td>
    <textarea rows="8" cols="50" name="addtexttext"><a href="<?=bloginfo('url');?>" target="_blank">Click Here</a></textarea>
    </td>
    <td>&nbsp;</td>
    <td align="left" valign="top">
    
	This is the text your visitors can<br /> place in their Website to link to you.<br />
    The HTML to create a clickable link looks like this:<br />
    &lt;a href="http://enter your url here"&gt;Click Here&lt;/a&gt;
    </td>
  	</tr>
  	<tr>
    	<td>&nbsp;</td>
  	</tr>
  	<tr>
    	<td><input type="submit" value="Save Text" name="submit"/></td>
  	</tr>
	</table>
	</div>
	<!-- end Add Text page -->	
	
	<!-- start My Assets page -->
	<div id="tabs-3">
	
	<?
		global $table_prefix, $wpdb;
	
	$wplinktous_data = $table_prefix . "wplinktous";	
	
		echo '<style>
			div.try 
			.try #text-to-copy { border:4px solid #666; margin-top:0; }';
			
		foreach($wpdb->get_results("SELECT * FROM $wplinktous_data WHERE active = 1;") as $key => $row) {
		// each column in your row will be accessible like this
		
		echo '.try #copy-button'.$row->id.' { width:110px; height:50px; background:url('.plugins_url('images/button.png', __FILE__ ).') no-repeat 0 0; display:block; overflow:hidden; color:#fff; text-decoration:none; text-indent:30px; line-height:43px; vertical-align:middle; margin:0; font-size: 16px; }';
		}
		
		echo '</style>';
	?>
	

	<h3>Graphical Links</h3>
	<table class="contacts" cellspacing="5" summary="Contacts template">
<div class="try">
	<tr class="contactDept">
		<th>Edit</th>
		<th>Delete</th>
		<th>Asset</th>
		<th>Raw HTML Code</th>
		</tr> 
		
		<?
				global $table_prefix, $wpdb;
	
	$wplinktous_data = $table_prefix . "wplinktous";	
	
		
		foreach($wpdb->get_results("SELECT * FROM $wplinktous_data WHERE active = 1 AND fieldtype='addgraphics';") as $key => $row) {
		// each column in your row will be accessible like this
			$addgraphicsurlcl = stripslashes($row->addgraphicsurl);
			$titleofgraphiccl = stripslashes($row->addgraphicstitle);
				$addgraphicsimagecl = stripslashes($row->addgraphicsimage);
				//echo $addgraphicsimagecl;
				//exit;
		
		if ($row->addgraphicstarget == '0')	{
			$selecttargetcl = '_blank';
		}else{
			$selecttargetcl = '_self';
		}
		?>
		
		<tr>
		
		<td class="contact" width="18%"><input type="image" src="<?=plugins_url('images/edit.gif', __FILE__ );?>" alt="Edit" name="editimage" value="<?=$row->id;?>"></td>
		<td class="contact" width="17%"><input type="image" src="<?=plugins_url('images/delete_icon.png', __FILE__ );?>" alt="Delete" name="deleteimage" value="<?=$row->id;?>"></td>
		<td class="contact" width="25%"><img width="200" src="<?=bloginfo('url');?><?=$row->addgraphicsimage;?>"><br><?=$row->addgraphicstitle;?></td>
		<td class="contact" width="50%"><textarea rows="3" cols="30"><a href="<?=$addgraphicsurlcl;?>" title="<?=$titleofgraphiccl;?>" target="<?=$selecttargetcl;?>"><img src="<?=bloginfo('url');?><?=$addgraphicsimagecl;?>" alt="<?=$titleofgraphiccl;?>"></a></textarea></td>
		</tr>
		<?
		}
		?>
		</div>
	</table>
	
	<h3>Text links</h3>
	<table class="contacts" cellspacing="5" summary="Contacts template">

	<tr class="contactDept">
		<th>Edit</th>
		<th>Delete</th>
		<th>Asset</th>
		<th>Raw HTML Code</th>
		</tr> 
	
	<?
				global $table_prefix, $wpdb;
	
	$wplinktous_data = $table_prefix . "wplinktous";
		
		foreach($wpdb->get_results("SELECT * FROM $wplinktous_data WHERE active = 1 AND fieldtype='addtext';") as $key => $row) {
		// each column in your row will be accessible like this
		?>
		<tr>
		<td class="contact" width="18%"><input type="image" src="<?=plugins_url('images/edit.gif', __FILE__ );?>" alt="Edit" name="edittext" value="<?=$row->id;?>"></td>
		<td class="contact" width="17%"><input type="image" src="<?=plugins_url('images/delete_icon.png', __FILE__ );?>" alt="Delete" name="deletetext" value="<?=$row->id;?>"></td>
		<td class="contact" width="25%"><?=stripslashes($row->addtexttext);?></td>
		<td class="contact" width="50%"><textarea rows="3" cols="30"><?=stripslashes($row->addtexttext);?></textarea></td>
		</tr>
		<?
		}

		?>
	</table>

	</div>
	<!-- end My Assets page -->

	<!-- start Settings page -->
	<div id="tabs-4">
	<font color="red">Ready to go live? Simply paste the following shortcode into any page in your Wordpress site:<br />
    <h2>[wplinktous]</h2>The fields below will show on your external WP Link To Us page.</font></p>
	<?
		global $table_prefix, $wpdb;
	
	$wplinktous_data = $table_prefix . "wplinktous";
		
		foreach($wpdb->get_results("SELECT * FROM $wplinktous_data WHERE active = 1 LIMIT 1;") as $key => $row) {
		// each column in your row will be accessible like this
		?>
	<p><table border="0">
 <tr>
    <td><h3>Title of page</h3></td>
  </tr>
  <tr>
    <td>
      <input type="text" name="titleofpage" value="<?=$row->titleofpage;?>" size="25">
    </td>
  </tr>
  <tr>
    <td><h3>Introduction text (appears at the top of your WP Link To Us page)</h3></td>
  </tr>
  <tr>
    <td>
      <textarea rows="8" cols="50" name="introtext"><?=$row->introtext;?></textarea>
    </td>
  </tr>
  <tr>
    <td><h3>Footer text (appears below of your WP Link To Us page)</h3></td>
  </tr>
  <tr>
    <td>
      <textarea rows="8" cols="50" name="footertext"><?=$row->footertext;?></textarea>
    </td>
  </tr>
   <tr>
    <td><input type="submit" name="submit" value="Save Settings"></td>
  </tr>
</table>
<?
}
?>
	</div>
	<!-- end Settings page -->
</div>

</p>
<script type="text/javascript">
function submitform()
{
//alert("Going to submit form");
return confirm("Do you really want to delete this asset ?")
    document.newad.submit();
}
</script>
</form>
<!-- This JavaScript snippet activates those tabs -->
<script>

// perform JavaScript after the document is scriptable.
$(function() {
	// setup ul.tabs to work as tabs for each div directly under div.panes
	$("ul.tabs").tabs("div.panes > div");
});
</script>
<?
		global $table_prefix, $wpdb;
	
	$wplinktous_data = $table_prefix . "wplinktous";
foreach($wpdb->get_results("SELECT * FROM $wplinktous_data WHERE active = 1 LIMIT 1;") as $key => $row) {
		
		echo '<script type="text/javascript">
$(document).ready(function(){';
			
			$blogurlwp = get_blog_path();	
			$blogurlwpbase = str_replace("wp-link-to-us/", "", $blogurlwp);
			$flashpath = 'http://'.$blogurlwpbase.'wp-content/plugins/wp-link-to-us/js/ZeroClipboard.swf';			

			foreach($wpdb->get_results("SELECT * FROM $wplinktous_data WHERE active = 1;") as $key => $row) {

				echo "$(\"#copy-button$row->id\").zclip({
					path: \"$flashpath\",
					copy: function(){return $(this).prev().val();}
				});";
	}

echo '
});
</script>';
	}
}

?>
<?
}
?>