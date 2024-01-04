<?php
/**
* Plugin Name: PermaHammer
* Plugin URI: https://github.com/FreshyMichael/PermaHammer/
* Description: Pesky permalink errors be gone - automatically re-saves permalinks on end-user 404
* Version: 1.0.0
* Author: FreshySites
* Author URI: https://freshysites.com/
* License: GNU v3.0
*/

if (!defined('ABSPATH')) {
    die();
}

/* PermaHammer Start */
//______________________________________________________________________________

add_action('template_redirect', 'permaHammer_flush_rewrite_rules_on_404');

function permaHammer_flush_rewrite_rules_on_404() {
    if (is_404()) {
        // Get The URL of the 404 page
        $requested_url = esc_url((isset($_SERVER['HTTPS']) ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");

        // Get the Home URL
        $siteHomeUrl = get_home_url();

        // Get the site Admin Email
        $adminEmail = get_bloginfo('admin_email');

        // Resave Permalinks / Flush Rewrite Rules
        flush_rewrite_rules();

        // Error log that the workaround has been triggered, and include the 404'd URL
        error_log('Internal Page 404 Error encountered, flush_rewrite_rules action triggered for url:' . $requested_url);

        wp_mail($adminEmail, $siteHomeUrl . ' has encountered an Internal 404 Error', $requested_url . ' on ' . $siteHomeUrl . ' triggered the workaround in place. Please visit internal pages and confirm working');
    }
}
