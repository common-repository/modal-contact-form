<?php
/*
Plugin Name: Modal Contact Form
Plugin URI: http://www.lucbianco.com/modal-contact-form-installation/
Description: Provide a shortcode which will be replaced by either a button able to open a modal contact form, or by a classic contact form inside a page
Author: Luc Bianco
Author URI: http://www.lucbianco.com
Version: 1.8.1
License: GPLv2 or later
*/

/* ================================================================================  
Copyright 2015-2016 Luc BIANCO (email: luc.bianco@free.fr)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

If you want a written copy of the GNU General Public License,
write to the Free Software Foundation, Inc., 
51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
================================================================================ */

// Global variable for version number 
global $lb_modal_contact_form_version;
$lb_modal_contact_form_version = '1.8.1';


// Enqueue javascript and styles for frontend 
add_action( 'wp_enqueue_scripts', 'lb_modal_contact_form_init');
function lb_modal_contact_form_init() {
global $lb_modal_contact_form_version;
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-slider' );
	wp_enqueue_script( 'jquery-touch-punch');
	wp_enqueue_script( 'lb_modal_contact_form_js',                                    
						plugins_url( '/js/lb-modal-contact-form.js', __FILE__ ),   
						array(
							'jquery',                                       
							'jquery-ui-slider',
							'jquery-touch-punch'			
						),
						"$lb_modal_contact_form_version");
	
	// Check CSS option in plugin admin settings 
	$options = get_option('lb_modal_contact_form_options');
	$css = $options['css'];
	
	if ('on' == $css) {
		wp_register_style(
			'lb_modal_contact_form_css',                                       
			plugins_url( '/lb-modal-contact-form.css', __FILE__ )     
		);
		
		wp_enqueue_style( 'lb_modal_contact_form_css' );
	} else {
		wp_register_style(
			'lb_modal_contact_form_custom_css',                                       
			plugins_url( '/lb-modal-contact-form-custom.css', __FILE__ )     
		);

		wp_enqueue_style( 'lb_modal_contact_form_custom_css' );
	}
	
	// Send some parameters to javascript
	wp_localize_script('lb_modal_contact_form_js', 'lb_modal_contact_form_params', array(
		'antispam_message' => __('Drag cursor to the right', 'lb-modal-contact-form'),
		'send_button' => __('SEND', 'lb-modal-contact-form')
	));	
}

// Load languages translations
add_action('plugins_loaded', 'lb_modal_contact_form_load_translations');
function lb_modal_contact_form_load_translations() {
	//if (!is_admin()) {
		load_plugin_textdomain('lb-modal-contact-form', false, 'modal-contact-form/languages');
	//}
}
// Register shortcode i.e [insert-modal-contact-form]
add_shortcode( 'insert-modal-contact-form', 'lb_insert_modal_contact_form' );


// Set default parameters at plugin activation if options does not exist already (i.e from a previous activation)
register_activation_hook( __FILE__, 'lb_modal_contact_form_activate');
function lb_modal_contact_form_activate() {
   global $lb_modal_contact_form_version;
   $version = get_option('lb_modal_contact_form_version');
   
	update_option('lb_modal_contact_form_version', $lb_modal_contact_form_version );

	$options = get_option('lb_modal_contact_form_options');

	// Check if it is a first install or if there are some settings to keep
	if (!isset($options['email']) || ("" == $options['email'])) {$email = 'yourmail@example.com';} else {$email = $options['email'];}
	if (!isset($options['modal'])) {$modal = 'on';} else {$modal = $options['modal'];}
	if (!isset($options['css'])) {$css = 'on';} else {$css = $options['css'];}
	if (!isset($options['file'])) {$file = 'off';} else {$file = $options['file'];}
	if (!isset($options['customerphone'])) {$customerphone = 'on';} else {$customerphone = $options['customerphone'];}
	if (!isset($options['customeremail'])) {$customeremail = 'on';} else {$customeremail = $options['customeremail'];}
	if (!isset($options['autoreply'])) {$autoreply = 'off';} else {$autoreply = $options['autoreply'];}
	if (isset($options['autoreply_sentence'])) {$autoreply_sentence = $options['autoreply_sentence'];} else {$autoreply_sentence = "";}

	$lb_modal_contact_form_myplugin_options = array(
		'css' => $css,
		'modal' => $modal,
		'email' => $email,
		'file' => $file,
		'customerphone' => $customerphone,
		'customeremail' => $customeremail,
		'autoreply' => $autoreply,
		'autoreply_sentence' => $autoreply_sentence
	);
	update_option( 'lb_modal_contact_form_options', $lb_modal_contact_form_myplugin_options );

}


// Manage the case of a plugin update (activation hook is not fired in case of plugin update)
function lb_modal_contact_form_check_version() {
    $version = get_option('lb_modal_contact_form_version');
	global $lb_modal_contact_form_version;
	
	// Is it a update or a re-activation
    if ( $version != $lb_modal_contact_form_version ) {
       lb_modal_contact_form_activate();
    }
}
add_action( 'plugins_loaded', 'lb_modal_contact_form_check_version' );

// Create a custom menu for plugin
add_action('admin_menu', 'lb_modal_contact_form_create_menu');
add_action('admin_init', 'lb_modal_contact_form_settings' );

// Add a new menu page
function lb_modal_contact_form_create_menu() {
	add_menu_page('Contact Modal Form Settings', __('Contact Modal Form Settings','lb-modal-contact-form'), 'manage_options', 'lb_modal_contact_form', 'lb_modal_contact_form_settings_page');
}

// Register and define the settings
function lb_modal_contact_form_settings() {
	register_setting('lb_modal_contact_form_options', 'lb_modal_contact_form_options', 'lb_modal_contact_form_validate_options');
	add_settings_section('lb_modal_contact_form_main', __('General settings','lb-modal-contact-form'), 'lb_modal_contact_form_section_text', 'lb_modal_contact_form'	);
	add_settings_field('lb_modal_contact_form_email', __('Contact email','lb-modal-contact-form'), 'lb_modal_contact_form_settings_email', 'lb_modal_contact_form', 'lb_modal_contact_form_main'	);
	add_settings_field('lb_modal_contact_form_file', __('Allow attachments','lb-modal-contact-form'), 'lb_modal_contact_form_settings_file', 'lb_modal_contact_form', 'lb_modal_contact_form_main');
	add_settings_field('lb_modal_contact_form_css', __('Default CSS','lb-modal-contact-form'), 'lb_modal_contact_form_settings_css', 'lb_modal_contact_form', 'lb_modal_contact_form_main'	);
	add_settings_field('lb_modal_contact_form_modal', __('Modal window','lb-modal-contact-form'), 'lb_modal_contact_form_settings_modal', 'lb_modal_contact_form', 'lb_modal_contact_form_main'	);
	add_settings_field('lb_modal_contact_form_customerphone', __('Phone field','lb-modal-contact-form'), 'lb_modal_contact_form_settings_customerphone', 'lb_modal_contact_form', 'lb_modal_contact_form_main'	);
	add_settings_field('lb_modal_contact_form_customeremail', __('Email field','lb-modal-contact-form'), 'lb_modal_contact_form_settings_customeremail', 'lb_modal_contact_form', 'lb_modal_contact_form_main'	);
	add_settings_field('lb_modal_contact_form_autoreply', __('Autoreply','lb-modal-contact-form'), 'lb_modal_contact_form_settings_autoreply', 'lb_modal_contact_form', 'lb_modal_contact_form_main'	);
	add_settings_section('lb_modal_contact_form_donate', '', 'lb_modal_contact_form_section_donate', 'lb_modal_contact_form_donate'	);	
	}

function lb_modal_contact_form_settings_page() {
 	?>
	<div class="wrap">
	<h2><?php _e('Contact Modal form settings','lb-modal-contact-form') ?></h2>
	<form method="post" action="options.php">
	
	<?php
	settings_fields('lb_modal_contact_form_options');
	do_settings_sections('lb_modal_contact_form');
	submit_button(); 
	do_settings_sections('lb_modal_contact_form_donate');
	?>
	</form>
	</div>
<?php
}



// Display settings sections
function lb_modal_contact_form_section_text() {
	echo '<p>'.__('Please provide following information:','lb-modal-contact-form').'</p>';
	echo '<ul><li>'.__('One email address to receive messages','lb-modal-contact-form').'</li>';
	echo '<li>'.__('Choose if you allow an attached file to your contact form','lb-modal-contact-form').'</li>';
	echo '<li>'.__('Choose if you want to use your own CSS formatting or default one','lb-modal-contact-form').'</li>';
	echo '<li>'.__('Choose if you want to display contact form in a modal window (default) or embedded','lb-modal-contact-form').'</li>';
	echo '<li>'.__('Choose if you want to enable phone and email fields on contact form','lb-modal-contact-form').'</li>';
	echo '<li>'.__('In case you are out of office, choose if you want to mention it','lb-modal-contact-form').'</li>';
	echo '</ul>';
}

function lb_modal_contact_form_settings_email() {
	$options = get_option('lb_modal_contact_form_options');
	$email = $options['email'];
	echo "<input id='email' name='lb_modal_contact_form_options[email]' type='text'  size='35' value='$email' />";
}

function lb_modal_contact_form_settings_css() {
	$options = get_option('lb_modal_contact_form_options');
	$css = $options['css'];
	echo "<input type='checkbox' name='lb_modal_contact_form_options[css]' ".checked($css, 'on' , false)."/>";	
}

function lb_modal_contact_form_settings_modal() {
	$options = get_option('lb_modal_contact_form_options');
	$modal = $options['modal'];
	echo "
	<label for='modal_on'><input type='radio' id='modal_on' name='lb_modal_contact_form_options[modal]' value='on' ".checked($modal, 'on' , false)."/>".__('On','lb-modal-contact-form')."</label><br><label for='modal_off'><input type='radio' id='modal_off' name='lb_modal_contact_form_options[modal]' value='off' ".checked($modal, 'off' , false)."/>".__('Off','lb-modal-contact-form')."</label>";
}

function lb_modal_contact_form_settings_customerphone() {
	$options = get_option('lb_modal_contact_form_options');
	$customerphone = $options['customerphone'];
	echo "
	<label for='customerphone_enabled'><input type='radio' id='customerphone_enabled' name='lb_modal_contact_form_options[customerphone]' value='on' ".checked($customerphone, 'on' , false)."/>".__('On','lb-modal-contact-form')."</label><br><label for='customerphone_disabled'><input type='radio' id='customerphone_disabled' name='lb_modal_contact_form_options[customerphone]' value='off' ".checked($customerphone, 'off' , false)."/>".__('Off','lb-modal-contact-form')."</label>";
}

function lb_modal_contact_form_settings_customeremail() {
	$options = get_option('lb_modal_contact_form_options');
	$customeremail = $options['customeremail'];
	echo "
	<label for='customeremail_enabled'><input type='radio' id='customeremail_enabled' name='lb_modal_contact_form_options[customeremail]' value='on' ".checked($customeremail, 'on' , false)."/>".__('On','lb-modal-contact-form')."</label><br><label for='customeremail_disabled'><input type='radio' id='customeremail_disabled' name='lb_modal_contact_form_options[customeremail]' value='off' ".checked($customeremail, 'off' , false)."/>".__('Off','lb-modal-contact-form')."</label>";
}

function lb_modal_contact_form_settings_file() {
	$options = get_option('lb_modal_contact_form_options');
	$file = $options['file'];
	echo "<input type='checkbox' name='lb_modal_contact_form_options[file]' ".checked($file, 'on' , false)."/>";	
}

function lb_modal_contact_form_settings_autoreply() {
	$options = get_option('lb_modal_contact_form_options');
	$autoreply = $options['autoreply'];
	echo "<input type='checkbox' name='lb_modal_contact_form_options[autoreply]' ".checked($autoreply, 'on' , false)."/>";
	$autoreply_sentence = $options['autoreply_sentence'];
	echo "<input id='autoreply_sentence' name='lb_modal_contact_form_options[autoreply_sentence]' type='text'  size='100' value='$autoreply_sentence' placeholder='".esc_attr__('Enter here your autoreply message','lb-modal-contact-form')."' />";
}

function lb_modal_contact_form_validate_options($input) {
	$valid = array();
	
	$valid['email'] = sanitize_email($input['email']);
	$valid['autoreply_sentence'] = sanitize_text_field($input['autoreply_sentence']);
	if (isset($input['css'])) {$valid['css'] = $input['css'];} else { $valid['css'] = 'no';}
	if (isset($input['modal'])) {$valid['modal'] = $input['modal'];} else { $valid['modal'] = 'off';}
	if (isset($input['file'])) {$valid['file'] = $input['file'];} else { $valid['file'] = 'no';}
	if (isset($input['autoreply'])) {$valid['autoreply'] = $input['autoreply'];} else { $valid['autoreply'] = 'no';}
    if (isset($input['customerphone'])) {$valid['customerphone'] = $input['customerphone'];} else { $valid['customerphone'] = 'on';}
	if (isset($input['customeremail'])) {$valid['customeremail'] = $input['customeremail'];} else { $valid['customeremail'] = 'on';} 
	return $valid;
}

// Display settings sections
function lb_modal_contact_form_section_donate() {
?>
	
            <h2><?php _e('If you like the plugin, you can buy me a coffee :-)','lb-modal-contact-form') ?> </h2>
            <p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=UGNC8G9TBNJAJ" title="Donate" target="_blank"><img src="<?php echo(plugins_url( '/images/btn_donateCC_LG.gif', __FILE__ )) ?>" alt="Donate" title="Donate" /></a></p>
         
<?php
}

// Callback function for the [insert-modal-contact-form] shortcode
function lb_insert_modal_contact_form( $attr, $content ) {
	// Get email address stored in plugin settings 
	$options = get_option('lb_modal_contact_form_options');
	$tosend = $options['email'];
	$file = $options['file'];
	$modal = $options['modal'];
	$customerphone = $options['customerphone'];
	$customeremail = $options['customeremail'];
	$autoreply = $options['autoreply'];
	$autoreply_sentence = $options['autoreply_sentence'];
	
	if ('on' == $modal) {
	// Contact button and modal windows with contact form 
	$form = '	
	<a href="#lb-modal-contact-form-overlay" id="lb-modal-contact-form-open"><label  class="lb-modal-contact-form-btn" for="lb-modal-contact-form-modalCheck">'.__('CONTACT','lb-modal-contact-form').'</label></a>
	
    <div id="lb-modal-contact-form-overlay">
	
        <div class="lb-modal-contact-form-modal-content">
         
			<div id="lb-modal-contact-form-window">
				<div class="lb-modal-contact-form-top">
					<h2>'.__('CONTACT US','lb-modal-contact-form').'</h2>
					<a href="#"><label class="btn-close" for="lb-modal-contact-form-modalCheck" >×</label></a>
				 </div>
				<div class="lb-modal-contact-form-main">
					<form class="lb-modal-contact-form" action="" method="POST" enctype="multipart/form-data">
						<p>
							<label for="lb-modal-contact-form-contact">'.__('Name','lb-modal-contact-form').'</label>
							<input type="text" name="contact" id="lb-modal-contact-form-contact" placeholder="Mr John Foo" required />
						</p>
						';
					if ('on' == $customerphone) {
					$form .= '	
						<p>
							<label for="lb-modal-contact-form-tel">'.__('Phone number','lb-modal-contact-form').'</label>
							<input type="tel" name="tel" id="lb-modal-contact-form-tel" placeholder="06xxxxxxxx" required/>
						</p>
					';
					}
					if ('on' == $customeremail) {
					$form .= '	
						<p>
							<label for="lb-modal-contact-form-mail">'.__('Email','lb-modal-contact-form').'</label>
							<input type="email" name="mail" id="lb-modal-contact-form-mail" placeholder="john.foo@example.com" required/>
						</p>
					';	
					}
			$form .= '	<p>
							<label for="lb-modal-contact-form-message">'.__('Message','lb-modal-contact-form').'</label>
							<textarea name="message" id="lb-modal-contact-form-message" placeholder="'.__('Your message','lb-modal-contact-form').'" required/></textarea>
						</p>
						';
						
						
						
	if ('on' == $file) {
	$form .= '
						<p>
							<label for="lb-modal-contact-form-file">'.__('Attachment','lb-modal-contact-form').'</label>
							<input type="file" name="lb-modal-contact-form-file" id="lb-modal-contact-form-file"  multiple="false" />
						</p>
						';
	}
	if ('on' == $customeremail) {
	$form .= '	
							<p>
								<input type="checkbox" name="copy" id="lb-modal-contact-form-copy" checked/></textarea>
								<label for="lb-modal-contact-form-copy">'.__('Receive a copy','lb-modal-contact-form').'</label>
							</p>
							';	
					}
					$form .= '	
						</form>
						<div id="lb-modal-contact-form-sent" hidden>Message sent</div>
				</div>
			</div>
		</div>
	</div>
		';
	
	} elseif ('off' == $modal) {
	
	
	$form = '	
	<div class="lb-modal-contact-form-modal-content">
	     <div id="lb-modal-contact-form-window">
				<div class="lb-modal-contact-form-top">
					<h2>'.__('CONTACT US','lb-modal-contact-form').'</h2>
				 </div>
				<div class="lb-modal-contact-form-main">
					<form class="lb-modal-contact-form" action="" method="POST" enctype="multipart/form-data">
						<p>
							<label for="lb-modal-contact-form-contact">'.__('Name','lb-modal-contact-form').'</label>
							<input type="text" name="contact" id="lb-modal-contact-form-contact" placeholder="Mr John Foo" required />
						</p>
						';
					if ('on' == $customerphone) {
					$form .= '	
						<p>
							<label for="lb-modal-contact-form-tel">'.__('Phone number','lb-modal-contact-form').'</label>
							<input type="tel" name="tel" id="lb-modal-contact-form-tel" placeholder="06xxxxxxxx" required/>
						</p>
					';
					}
					if ('on' == $customeremail) {
					$form .= '	
						<p>
							<label for="lb-modal-contact-form-mail">'.__('Email','lb-modal-contact-form').'</label>
							<input type="email" name="mail" id="lb-modal-contact-form-mail" placeholder="john.foo@example.com" required/>
						</p>
					';	
					}
			$form .= '	<p>
							<label for="lb-modal-contact-form-message">'.__('Message','lb-modal-contact-form').'</label>
							<textarea name="message" id="lb-modal-contact-form-message" placeholder="'.__('Your message','lb-modal-contact-form').'" required/></textarea>
						</p>
						';	
						
						
						
						
	if ('on' == $file) {
	$form .= '
						<p>
							<label for="lb-modal-contact-form-file">'.__('Attachment','lb-modal-contact-form').'</label>
							<input type="file" name="lb-modal-contact-form-file" id="lb-modal-contact-form-file"  multiple="false" />
						</p>
						';
	}
	if ('on' == $customeremail) {
	$form .= '	
							<p>
								<input type="checkbox" name="copy" id="lb-modal-contact-form-copy" checked/></textarea>
								<label for="lb-modal-contact-form-copy">'.__('Receive a copy','lb-modal-contact-form').'</label>
							</p>
							';
							}
     $form .= '							
						</form>
						<div id="lb-modal-contact-form-sent" hidden>Message sent</div>
				</div>
		</div>
	</div>
	';
	
	
	
	}
	
			
	// Sanitize data from POST and send email 
	if( isset($_POST['submit']) ) {
		$name = stripslashes_deep(sanitize_text_field($_POST['contact']));
		$tel = stripslashes_deep(sanitize_text_field($_POST['tel'])); 
		$email = stripslashes_deep(sanitize_email($_POST['mail']));
		$message = stripslashes_deep(sanitize_text_field($_POST['message']));
		
		$copy = stripslashes_deep(sanitize_text_field($_POST['copy']));
		$_POST = array(); // Avoid multiple emails sending in case of several shortcodes in same page
		$subject = __('Contact from site','lb-modal-contact-form').' '.get_bloginfo('name');
		if (("" != $tel) && ("" != $email)){
			$content = __('You have received following message from','lb-modal-contact-form').' '.$name."\n\n".__('Email','lb-modal-contact-form').' '.$email."\n\n".__('Phone number','lb-modal-contact-form').' '.$tel."\n\n".__('The message is', 'lb-modal-contact-form').': '.$message;
		} elseif (("" != $tel) && ("" == $email)) {
			$content = __('You have received following message from','lb-modal-contact-form').' '.$name."\n\n".__('Phone number','lb-modal-contact-form').' '.$tel."\n\n".__('The message is', 'lb-modal-contact-form').': '.$message;
		} elseif (("" == $tel) && ("" != $email)) {
			$content = __('You have received following message from','lb-modal-contact-form').' '.$name."\n\n".__('Email','lb-modal-contact-form').' '.$email."\n\n".__('The message is', 'lb-modal-contact-form').': '.$message;
		} else {
			$content = __('You have received following message from','lb-modal-contact-form').' '.$name."\n\n".__('The message is', 'lb-modal-contact-form').': '.$message;
		}
		
		// Function needed to handle file upload
		if ( !function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		// Overrides allowed file extensions and try to upload file if extension is allowed
		$uploadedfile = $_FILES['lb-modal-contact-form-file'];
		$upload_overrides = array(
                'test_form' => false,
                'mimes'     => array(
										'jpg|jpeg|jpe'    => 'image/jpeg',
										'pdf'             => 'application/pdf',
										'zip'             => 'application/zip',
										'png'             => 'image/png',
										'txt'             => 'text/plain',
										'doc'             => 'application/msword',
										'docx'            => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
										'xlsx'            => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
										'xla|xls|xlt|xlw' => 'application/vnd.ms-excel',
					)
				);
		$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

		// All fields are mandatory
		if (!empty($name) and !empty($message)) {
			
			// Handles case where attachment option is enabled
			if ('on' == $file) {
				// Check if attachment file exists and is valid
				if ( $movefile && !isset( $movefile['error'] ) ) {
				
					$attachments = $movefile['file'];
					
					// Send email to address stored in plugin settings 
					wp_mail($tosend, $subject, $content, '', $attachments);
					
					// Send mail copy confirmation to sender in case copy option is set 
					if (('on' == $copy) AND ($email != '')) {
						if (('on' == $autoreply) AND ($autoreply_sentence != '')) {
							$content = $autoreply_sentence."\n\n"."_____________________________"."\n\n";
							$content .= __('You have sent the following message', 'lb-modal-contact-form').': '."\n\n".$message."\n\n";
						} else {
							$content = __('You have sent the following message', 'lb-modal-contact-form').': '."\n\n".$message;
						}
						wp_mail( $email, $subject, $content, '', $attachments  );
					}
					
					// Remove temp file
					@unlink($attachments);	
					
					// Display a success alert window
					echo "<script type='text/javascript'>alert('".__('Message has been sent', 'lb-modal-contact-form')."');</script>";
					
				// In case file is not valid send mail anyway without attachment
				} else {
				
					wp_mail($tosend, $subject, $content);
					
					// Display a success alert window
					echo "<script type='text/javascript'>alert('".__('Message has been sent', 'lb-modal-contact-form')."');</script>";
					
					// Send mail copy confirmation to sender in case copy option is set 
					if (('on' == $copy) AND ($email != '')) {
						if (('on' == $autoreply) AND ($autoreply_sentence != '')) {
							$content = $autoreply_sentence."\n\n"."_____________________________"."\n\n";
							$content .= __('You have sent the following message', 'lb-modal-contact-form').': '."\n\n".$message."\n\n";
						} else {
							$content = __('You have sent the following message', 'lb-modal-contact-form').': '."\n\n".$message;
						}
						wp_mail( $email, $subject, $content  );
					}
				}
			// Handles case where attachment option is disabled
			} else { 
			
					// Send email to address stored in plugin settings 
					wp_mail($tosend, $subject, $content);
					
					// Send mail copy confirmation to sender in case copy option is set 
					if (('on' == $copy) AND ($email != '')) {
						if (('on' == $autoreply) AND ($autoreply_sentence != '')) {
							$content = $autoreply_sentence."\n\n"."_____________________________"."\n\n";
							$content .= __('You have sent the following message', 'lb-modal-contact-form').': '."\n\n".$message."\n\n";
						} else {
							$content = __('You have sent the following message', 'lb-modal-contact-form').': '."\n\n".$message;
						}
						wp_mail( $email, $subject, $content  );
					}
					
					// Display a success alert window
					echo "<script type='text/javascript'>alert('".__('Message has been sent', 'lb-modal-contact-form')."');</script>";
			}

			// Redirect page after submit, for instance to avoid multiple submission warning in case of refresh
			// Note that wp_redirect cannot be used here because headers already sent
			echo "<script type='text/javascript'>window.location=document.location.pathname;</script>";
		}
	
	}
	
	// Replace shortcode by the contact button
	return $form;
	
}

?>