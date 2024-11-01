<?php
/*

Plugin Name:  Simple Sugarsync Upload Form

Plugin URI:   http://cdsincdesign.com/simple-sugarsync-upload/

Description:  Use the shortcode [simple-wp-sugarsync] in any page to insert a SugarSync file upload form.

Version:      1.2.0

Author:       Creative Design Solutions

Author URI:   http://cdsincdesign.com/

*/

/*

Copyright (C) 2012 Steven Whitney(at)cdsincdesign.com

This program is free software: you can redistribute it and/or modify

it under the terms of the GNU General Public License as published by

the Free Software Foundation, either version 3 of the License, or

(at your option) any later version.

This program is distributed in the hope that it will be useful,

but WITHOUT ANY WARRANTY; without even the implied warranty of

MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the

GNU General Public License for more details.

You should have received a copy of the GNU General Public License

along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/
if  (!in_array ( 'curl' , get_loaded_extensions() ) || !function_exists ('curl_init')) {
	define ('C_MISSING',true);
}else{
	define ('C_MISSING',false);
}

if (!class_exists('SugarSync'))require_once (dirname(__FILE__).'/classes/sugarsyncClass.php');

if(!function_exists('formatBytes')){
	function formatBytes($bytes, $precision = 2) { 
		$units = array('B', 'KB', 'MB', 'GB', 'TB'); 
		
		$bytes = max($bytes, 0); 
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
		$pow = min($pow, count($units) - 1); 
		
		// Uncomment one of the following alternatives
		$bytes /= pow(1024, $pow);
		// $bytes /= (1 << (10 * $pow)); 
		
		return round($bytes, $precision) . ' ' . $units[$pow]; 
	}
}

function sixtyfourDecode($str){
	$rt = base64_decode($str);
	return $rt;
}

function showSugarSync(){
	
	$wp_ssync_allow_ext = get_option( 'wp_ssync_allow_ext' );
	
	$wp_ssync_username = get_option('wp_ssync_username');
	
	$wp_ssync_password = sixtyfourDecode(get_option( 'wp_ssync_password' ));
	
	$wp_ssync_key = get_option( 'wp_ssync_key' );
	
	$wp_ssync_secret = get_option( 'wp_ssync_secret' );
	
	$wp_ssync_show_form = get_option( 'wp_ssync_show_form' );
	
	$wp_ssync_delete_file = get_option( 'wp_ssync_delete_file' );
	
	$wp_ssync_thank_message = stripslashes(get_option( 'wp_ssync_thank_message' ));
	
	$wp_ssync_path = get_option( 'wp_ssync_path' );

	$wp_ssync_temp_path = get_option( 'wp_ssync_temp_path' );
	
	
	echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/simple-sugarsync-upload/css/wp-ssync-style.css" />' . "\n";

	echo '<div class="wp-ssync">';

	$showform = true;

	try {

		if ($wp_ssync_allow_ext == '')

			throw new Exception(__('Need to configure allowed file extensions!',simpleSsyncUpload));

		if (C_MISSING)

			throw new Exception(__('This plugin requires CURL to be enabled!',simpleSsyncUpload));

	} catch(Exception $e) {

    		echo '<span id="syntax_error">'.__('Error:',simpleSsyncUpload). ' ' . htmlspecialchars($e->getMessage()) . '</span>';

		$showform = false;

	}
	
	if ($_POST['gogogadget']) {

	    try {
			
			$ssync = new SugarSync($wp_ssync_username,$wp_ssync_password,$wp_ssync_key,$wp_ssync_secret);		

			} catch(Exception $e) {
	
				echo '<span id="syntax_error">'.__('Error:',simpleDbUpload). ' ' . htmlspecialchars($e->getMessage()) . '</span>';
	
				$showform = false;
	
			}
			
			try {

	$allowedExtensions = split("[ ]+", $wp_ssync_allow_ext);

	  foreach ($_FILES as $file) { 

	    if ($file['tmp_name'] > '') { 

	    $file['name'] = str_replace(' ', '%20', $file['name']);

	      if (!in_array(end(explode(".", strtolower($file['name']))), $allowedExtensions)) { 

			$ext = implode(", ", $allowedExtensions);
			
			throw new Exception(__('Allowed file extensions: ',simpleDbUpload).''.$ext);			
	      } 

	    } 

	  } 

	        // Rename uploaded file to reflect original name

	        if ($_FILES['file']['error'] !== UPLOAD_ERR_OK)

	            throw new Exception(__('File was not uploaded from your computer.',simpleDbUpload));

			if (!file_exists($wp_ssync_temp_path))

			{

	        if (!mkdir($wp_ssync_temp_path))

	            throw new Exception(__('Cannot create temporary directory!',simpleDbUpload));

			}

	        if ($_FILES['file']['name'] === "")

	            throw new Exception(__('File name not supplied by the browser.',simpleDbUpload));

			//$new_file_name = str_replace(' ', '_', $new_file_name);

			$new_file_name = explode(".",$file['name']);

		    $tmpFile = $wp_ssync_temp_path.'/'.str_replace("/\0", '_', $new_file_name[0]) . "_" . date("Y-m-d").".".str_replace("/\0", '_', $new_file_name[1]);

	    	if (!move_uploaded_file($_FILES['file']['tmp_name'], $tmpFile))

	        	throw new Exception(__('Cannot rename uploaded file!',simpleDbUpload));

	       	// Upload

			$chunks = explode("/",$tmpFile);

			for($i = 0; $i < count($chunks); $i++){

				$c = $i;

			}
			
			try{
				$ssyncUser = $ssync->user();
				$ssyncFolder = trim($wp_ssync_path,'/');
				$ssync->mkdir($ssyncFolder,$ssyncUser->magicBriefcase);
	
				$ssync->chdir($ssyncFolder,$ssyncUser->magicBriefcase);
				
				$upupandaway = $ssync->upload($tmpFile);
			} catch(Exception $e) {
				throw new Exception(__('ERROR! Upload Failed. ',simpleDbUpload) . ' ' . html_entity_decode($e->getMessage()));
			}

			echo '<span id="sucess">'.$wp_ssync_thank_message.'</span>';

			if($wp_ssync_show_form == "True"){
				$showform = true;
			}else{
				$showform = false;
			}
			
			if($wp_ssync_delete_file == "True"){
				$delete_file = true;
			}else{
				$delete_file = false;
			}

	    } catch(Exception $e) {

		    	echo '<span id="syntax_error">'.__('Error: ',simpleDbUpload) . ' ' . html_entity_decode($e->getMessage()) . '</span>';

			$showform = true;

			$delete_file = true;

	    }		

	    // Clean up

	if($delete_file == true) {

	    	if (isset($tmpFile) && file_exists($tmpFile))

	        	unlink($tmpFile);

		}

	}

	if($showform == true) {
		?>
          <form method="POST" enctype="multipart/form-data">
               <input type="hidden" name="gogogadget" value="1"/>
               <input class="input_form" size="34" type="file" name="file" />
               <input id="submit_button" type="submit" value="<?php _e('Submit',simpleDbUpload); ?>" />
          </form>
          <?php
	}

	echo "</div>";
}

function wp_simple_sugarsync_settings(){
	if(C_MISSING){
	?>
	<div class="error">
		<p>
			<strong><?php _e('This plugin will not work without CURL enabled.', simpleSsyncUpload ); ?></strong>
		</p>
	</div>
	<?php	
     }
	?>
     <div class="wrap">
     <h2><?php _e('Wordpress Sugarsync Upload Form',simpleSsyncUpload);?></h2>
     <p><?php _e('This plugin will create a folder in your Sugarsync account and allow public uploads.',simpleSsyncUpload);?></p>
     <p><a href="https://www.sugarsync.com/referral?rf=6kesipz0rw5z&utm_source=txemail&utm_medium=email&utm_campaign=referral" target="_blank"><?php _e('Need a Sugarsync account? Please use this link so we both get some extra space.',simpleSsyncUpload);?></a></p>
     <?php
	if( $_POST[ "wp_ssync_submit_hidden" ] == 'Y' ) {
	
		// Save the posted value in the database
		update_option( 'wp_ssync_username', $_POST[ 'wp_ssync_username' ] );
		
		update_option( 'wp_ssync_password', base64_encode($_POST[ 'wp_ssync_password' ] ));
		
		update_option( 'wp_ssync_path', $_POST[ 'wp_ssync_path' ] );
		
		update_option( 'wp_ssync_temp_path', $_POST[ 'wp_ssync_temp_path' ] );
		
		update_option( 'wp_ssync_allow_ext', $_POST[ 'wp_ssync_allow_ext' ] );
		
		update_option( 'wp_ssync_thank_message', $_POST[ 'wp_ssync_thank_message' ] );
		
		update_option( 'wp_ssync_show_form', $_POST[ 'wp_ssync_show_form' ] );
		
		update_option( 'wp_ssync_delete_file', $_POST[ 'wp_ssync_delete_file' ] );
		
		if($_POST['wp_ssync_menu_pref']=='settings_menu' and get_option('wp_ssync_menu_pref')!='settings_menu'){
			update_option('wp_ssync_menu_pref',$_POST['wp_ssync_menu_pref']);
			?>
			<script type="text/javascript">
			<!--
			window.location = "<?php echo admin_url();?>"
			//-->
			</script>
			<?php
		}elseif($_POST['wp_ssync_menu_pref']!='settings_menu' and get_option('wp_ssync_menu_pref')!='main'){
			update_option('wp_ssync_menu_pref','main');
			?>
			<script type="text/javascript">
			<!--
			window.location = "<?php echo admin_url();?>"
			//-->
			</script>
			<?php
		}
		
		if (trim($_POST['wp_ssync_reset_confirm']) == trim($_POST['ssyncreset'])){
			
			wp_simple_sugarsync_deactivate();
			
			register_wp_simple_sugarsync_settings();
			
			$reset = true;
			?>
			<div class="updated">
				<p><strong>All options have been reset!</strong></p>
			</div>
			<?php
		}
		
		// Put an options updated message on the screen
		if (!$db_error && !$reset) {
			?>
			<div class="updated">
				<p>
					<strong><?php _e('Options saved.', simpleSsyncUpload ); ?></strong>
				</p>
			</div>
			<?php
		}
	}
	?>
	<p>
	<?php
	if(!$reset and !C_MISSING){
		try{
			$ssync = new SugarSync(get_option('wp_ssync_username'),sixtyfourDecode(get_option( 'wp_ssync_password' )),get_option( 'wp_ssync_key' ),get_option( 'wp_ssync_secret' ));
			$ssyncUser = $ssync->user();
			$sugarsyncfreespase=(float)$ssyncUser->quota->limit-(float)$ssyncUser->quota->usage;
			_e('SugarSync accout space: ',simpleSsyncUpload);
			echo '<strong>'.formatBytes((float)$ssyncUser->quota->usage).'</strong> ';
			_e('used',simpleSsyncUpload);
			echo ' | ';
			echo '<strong>'.formatBytes($sugarsyncfreespase).'</strong> ';
			_e('free',simpleSsyncUpload);
		} catch(Exception $e) {
			echo '<div class="error">'.__('Error:',simpleSsyncUpload). ' Invalid Username or Password</div>';
		}
	}
	?>
     </p>
  <?php $sure = rand(10000,99999);?>
  <form name="wp_ssync_form" method="POST" action="">
    <table class="form-table">
      <tr>
      	<th scope="row"><p><?php _e('Keep Simple SugarSync in settings menu.',simpleSsyncUpload);?></p></th>
          <td>
               <input type="checkbox" name="wp_ssync_menu_pref" value="settings_menu" <?php if(get_option('wp_ssync_menu_pref')=='settings_menu')echo 'checked';?> />
          </td>
      </tr>
      <tr>
        <th scope="row"><p><?php _e('Sugarsync Email Address.',simpleSsyncUpload);?></p></th>
        <td><input type="text" size="60" name="wp_ssync_username" value="<?php echo get_option( 'wp_ssync_username' ); ?>" /></td>
      </tr>
      <tr>
        <th scope="row"><p><?php _e('Sugarsync Password.',simpleSsyncUpload);?></p></th>
        <td><input type="password" size="60" name="wp_ssync_password" value="<?php echo sixtyfourDecode(get_option( 'wp_ssync_password' )); ?>" /></td>
      </tr>
      <tr>
        <th scope="row"><p><?php _e('Path in sugarsync folder.',simpleSsyncUpload);?></p></th>
        <td><input type="text" size="60" name="wp_ssync_path" value="<?php echo get_option( 'wp_ssync_path' ); ?>" />
          <br />
          <label for="inputid"><?php _e('All files/folders will be located in your SugarSync Magic Briefcase.',simpleSsyncUpload);?></label></td>
      </tr>
      <tr>
        <th scope="row"><p><?php _e('Temporary path on server. Files get saved here if the Sugarsync server is down.',simpleSsyncUpload);?></p></th>
        <td><input type="text" size="60" name="wp_ssync_temp_path" value="<?php echo get_option( 'wp_ssync_temp_path' ); ?>" />
          <br />
          <label for="inputid"><strong><?php _e('Default Location:',simpleSsyncUpload);?></strong> <?php $upload_dir = wp_upload_dir(); echo $upload_dir['basedir'].'/simple_sugarsync'; ?></label></td>
      </tr>
      <tr>
        <th scope="row"><p><?php _e('Allowed file extensions, separated by spaces.',simpleSsyncUpload);?> <strong>(<?php _e('Required',simpleSsyncUpload);?>)</strong></p></th>
        <td><input type="text" size="60" name="wp_ssync_allow_ext" value="<?php echo get_option( 'wp_ssync_allow_ext' ); ?>" />
        <br />
          <label for="inputid"><strong><?php _e('Example:',simpleSsyncUpload);?></strong> doc docx gif jpg jpeg pdf png psd tif tiff</label></td>
      </tr>
      <tr>
        <th scope="row"><p><?php _e('Message displayed after uploading a file.',simpleSsyncUpload);?></p></th>
        <td><input type="text" size="60" name="wp_ssync_thank_message" value="<?php echo stripslashes(get_option( 'wp_ssync_thank_message' )); ?>" /></td>
      </tr>
      <tr>
        <th scope="row"><p><?php _e('Show upload form again after upload?',simpleSsyncUpload);?></p></th>
          <td><select name="wp_ssync_show_form">
               <option value="True" <? if(get_option('wp_ssync_show_form') == "True"){?>selected="selected"<? }?>><?php _e('True',simpleSsyncUpload);?></option>
               <option value="False" <? if(get_option('wp_ssync_show_form') == "False"){?>selected="selected"<? }?>><?php _e('False',simpleSsyncUpload);?></option>
               </select>
          </td>
      </tr>
      <tr>
        <th scope="row"><p><?php _e('Delete local file after upload to sugarsync?',simpleSsyncUpload);?></p></th>
          <td><select name="wp_ssync_delete_file">
               <option value="True" <? if(get_option('wp_ssync_delete_file')== "True"){?>selected="selected"<? }?>><?php _e('True',simpleSsyncUpload);?></option>
               <option value="False" <? if(get_option('wp_ssync_delete_file')== "False"){?>selected="selected"<? }?>><?php _e('False',simpleSsyncUpload);?></option>
               </select>
          </td>
      </tr>
      <tr>
        <th scope="row"><p><?php _e('RESET SETTINGS.',simpleSsyncUpload);?></p></th>
        <td><input type="text" size="60" name="ssyncreset" autocomplete="off" value="" />
          <br />
          <label for="inputid"><?php _e('PLEASE TYPE THE FOLLOWING NUMBERS:',simpleSsyncUpload);?> <? echo $sure;?></label></td>
      </tr>
      <tr>
        <th scope="row" style="width:255px;">
        	<input type="hidden" name="wp_ssync_submit_hidden" value="Y" />
          <input type="hidden" name="wp_ssync_reset_confirm" value="<? echo $sure;?>" />
          <input type="submit" class="button" style="line-height:15px;" value="<?php _e('Save options',simpleSsyncUpload); ?>" />
          <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DV48838KHA4QU" target="_blank"><img style="margin-bottom:-7px;height:23px;" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" alt="" /></a> </th>
        <td></td>
      </tr>
    </table>
  </form>
  <br />
  <a href="https://www.sugarsync.com/referral?rf=6kesipz0rw5z&utm_source=txemail&utm_medium=email&utm_campaign=referral" target="_blank"><?php _e('Need a Sugarsync account? Please use this link so we both get some extra space.',simpleSsyncUpload);?></a> <br />
  <br />
  <br />
  <br />
  <a href="http://www.sugarsync.com/" target="_blank" style="color:#EEE;">Need a Sugarsync account and don't free space? Use this link.</a> </div>
<?php
}
	// Version Check
	function wpssync_get_version() {
		if ( ! function_exists( 'get_plugins' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
		$plugin_file = basename( ( __FILE__ ) );
		return $plugin_folder[$plugin_file]['Version'];
	}
	
	// shortcode

	function shortcode_wp_simple_sugarsync( $atts, $content = NULL ) {

		// Hackis way to show my shortcode at the right place

		ob_start();

		showSugarSync();

		$output_string=ob_get_contents();

		ob_end_clean();

		return $output_string;

	}

	function wp_ssync_create_menu() {
		if(get_option('wp_ssync_menu_pref')=='main'):
		//create new top-level menu
			add_menu_page('Simple SugarSync', 'Upload', 'administrator', __FILE__, 'wp_simple_sugarsync_settings',plugins_url('/images/sugarsync-icon.png', __FILE__));
		else:
		//create options page
			add_options_page('Simple SugarSync', 'Simple SugarSync', 'administrator', __FILE__, 'wp_simple_sugarsync_settings');
		endif;
		//call register settings function
		add_action( 'admin_init', 'register_wp_simple_sugarsync_settings' );

	}

	function wp_simple_sugarsync_deactivate(){

		remove_shortcode( 'simple-wp-sugarsync' );
	
		delete_option( 'wp_ssync_username' );
		
		delete_option( 'wp_ssync_password' );
	
		delete_option( 'wp_ssync_path' );
	
		delete_option( 'wp_ssync_temp_path' );
	
		delete_option( 'wp_ssync_allow_ext' );
		
		delete_option( 'wp_ssync_thank_message' );
		
		delete_option( 'wp_ssync_show_form' );
		
		delete_option( 'wp_ssync_delete_file' );
	
		delete_option( 'wp_ssync_key' );
	
		delete_option( 'wp_ssync_secret' );			
		
		delete_option( 'wp_ssync_version_number' );
		
		delete_option( 'wp_ssync_menu_pref' );

	}

	function register_wp_simple_sugarsync_settings() {

		//register our settings

		register_setting( 'wp_ssync-settings-group', 'wp_ssync_username' );
		
		register_setting( 'wp_ssync-settings-group', 'wp_ssync_password' );

		register_setting( 'wp_ssync-settings-group', 'wp_ssync_path' );

		register_setting( 'wp_ssync-settings-group', 'wp_ssync_temp_path' );

		register_setting( 'wp_ssync-settings-group', 'wp_ssync_allow_ext' );
		
		register_setting( 'wp_ssync-settings-group', 'wp_ssync_thank_message' );
		
		register_setting( 'wp_ssync-settings-group', 'wp_ssync_show_form' );
		
		register_setting( 'wp_ssync-settings-group', 'wp_ssync_delete_file' );

		register_setting( 'wp_ssync-settings-group', 'wp_ssync_key' );

		register_setting( 'wp_ssync-settings-group', 'wp_ssync_secret' );
		
		register_setting( 'wp_ssync-settings-group', 'wp_ssync_version_number');
		
		register_setting( 'wp_ssync-settings-group', 'wp_ssync_menu_pref');
		
		$wp_ssync_key = sixtyfourDecode('TVRjMk1EYzBPREV6TWpRNU1UazVNemd3TXpr');
		
		$wp_ssync_secret = sixtyfourDecode('WkRCaU1HRXdNRFJqWlRneU5EaG1OV0psTVRBek9UYzBaR1UwT1dFM01UUQ');
		
		update_option('wp_ssync_key', $wp_ssync_key);
		
		update_option( 'wp_ssync_secret', $wp_ssync_secret);
		
		if(!get_option('wp_ssync_show_form')){
			update_option('wp_ssync_show_form',"False");
		}

		if(!get_option('wp_ssync_temp_path') || get_option('wp_ssync_temp_path') == ''){
			$upload_dir = wp_upload_dir();
			update_option( 'wp_ssync_temp_path', $upload_dir['basedir'].'/simple_sugarsync' );
		}
		
		if(!get_option('wp_ssync_menu_pref')){
			update_option('wp_ssync_menu_pref','settings_menu');
		}

		/*if(substr(get_option('wpsync_version_number'),0,-2) != '1.0'){
			wp_simple_sugarsync_deactivate();
			update_option('wp_ssync_version_number',wpdb_get_version());
			register_wp_simple_sugarsync_settings();
		}*/
		
		update_option('wp_ssync_version_number',wpssync_get_version());
	}

	function wp_ssync_PluginInit(){
	  	//load_plugin_textdomain( 'simpleSsyncUpload', PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)),dirname(plugin_basename(__FILE__)).'/languages');
		load_plugin_textdomain('simpleSsyncUpload', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	// Start this plugin once all other plugins are fully loaded

	//add_action( 'plugins_loaded', 'SimpleSugarSync');

	add_shortcode( 'simple-wp-sugarsync', 'shortcode_wp_simple_sugarsync' );

	add_action('admin_menu', 'wp_ssync_create_menu');

	//add_action( 'init', 'wp_ssync_PluginInit' );

	register_deactivation_hook( __FILE__, 'wp_simple_sugarsync_deactivate' );
?>