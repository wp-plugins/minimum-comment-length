<?php
/*
Plugin Name: Minimum Comment Length
Version: 0.5
Plugin URI: http://yoast.com/wordpress/minimum-comment-length/
Description: Check the comment for a set minimum length and disapprove it if it's too short.
Author: Joost de Valk
Author URI: http://yoast.com/

Copyright 2008 Joost de Valk (email: joost@joostdevalk.nl)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if ( ! class_exists( 'MinComLength_Admin' ) ) {

	class MinComLength_Admin {
		
		function add_config_page() {
			global $wpdb;
			if ( function_exists('add_submenu_page') ) {
				add_options_page('Min Comment Length Configuration', 'Min Comment Length', 10, basename(__FILE__), array('MinComLength_Admin','config_page'));
			}
		}
		
		function config_page() {
			// Set some defaults if no settings are set yet
			$options['mincomlength'] = 15;
			$options['mincomlengtherror'] = "Error: Your comment is too short. Please try to say something useful.";
			add_option('MinComLengthOptions', $options);
			
			// Overwrite defaults with saved settings
			if ( isset($_POST['submit']) ) {
				if (!current_user_can('manage_options')) die(__('You cannot edit the Minimum Comment Length options.'));
				check_admin_referer('mincomlength-config');

				if (isset($_POST['mincomlength']) && $_POST['mincomlength'] != "" && is_numeric($options['mincomlength'])) 
					$options['mincomlength'] = $_POST['mincomlength'];

				if (isset($_POST['mincomlengtherror']) && $_POST['mincomlengtherror'] != "") 
					$options['mincomlengtherror'] = $_POST['mincomlengtherror'];

				update_option('MinComLengthOptions', $options);
			}
			
			$options = get_option('MinComLengthOptions');			
			?>
			<div class="wrap">
				<h2>Minimum Comment Length options</h2>
				<form action="" method="post" id="mincomlength-conf">
					<?php
					if ( function_exists('wp_nonce_field') )
						wp_nonce_field('mincomlength-config');
					?>
					<table class="form-table" style="width: 100%;">
						<tr valign="top">
							<th scrope="row">
								<label for="mincomlength">Minimum comment length:</label>
							</th>
							<td>
								<input type="text" value="<?php echo $options['mincomlength']; ?>" name="mincomlength" id="mincomlength" size="4"/>
							</td>
						</tr>
						<tr valign="top">
							<th scrope="row">
								<label for="mincomlengtherror">Error message:</label>
							</th>
							<td>
								<input type="text" value="<?php echo $options['mincomlengtherror']; ?>" name="mincomlengtherror" id="mincomlengtherror" size="50"/>
							</td>
						</tr>

					</table>
					<p class="submit"><input type="submit" name="submit" value="Update Settings &raquo;" /></p>
				</form>
			</div>
<?php		}	
	}
}

function check_comment_length($commentdata) {
	$options = get_option('mincomlength');
	if (!isset($options['mincomlength']) || $options['mincomlength'] == "" || !is_numeric($options['mincomlength']))
		$options['mincomlength'] = 15;

	if (!isset($options['mincomlengtherror']) || $options['mincomlengtherror'] == "")
		$options['mincomlengtherror'] = "Error: Your comment is too short. Please try to say something useful.";
	
	if (strlen($commentdata['comment_content']) < $options['mincomlength']) {
		wp_die( __($options['mincomlengtherror']) );
	} else {
		return $commentdata;
	}
}

add_filter('preprocess_comment','check_comment_length');
add_action('admin_menu', array('MinComLength_Admin','add_config_page'));
?>