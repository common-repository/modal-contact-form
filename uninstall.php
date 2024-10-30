<?php
// If uninstall not called from WordPress exit
if(!defined('WP_UNINSTALL_PLUGIN'))
exit();
// Delete option from options table
delete_option( 'lb_modal_contact_form_options' );
delete_option( 'lb_modal_contact_form_version' );
?>