<?php

/*
    Plugin Name: Q2A Easy Ask
    Plugin URI:
    Plugin Description: Easy Ask Form for Q2A.
    Plugin Version: 1.0
    Plugin Date: 2018-01-11
    Plugin Author: 38qa.net
    Plugin Author URI:
    Plugin License: GPLv2
    Plugin Minimum Question2Answer Version: 1.7
    Plugin Update Check URI:
*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
    header('Location: ../../');
    exit;
}

define( 'QEA_DIR', dirname( __FILE__ ) );
define( 'QEA_FOLDER', basename( dirname( __FILE__ ) ) );

// page
qa_register_plugin_module('page', 'qa-easy-ask-page.php', 'qa_easy_ask_page', 'Easy Ask Page');

// layer
qa_register_plugin_layer('qa-easy-ask-layer.php','Easy Ask Layer');

/*
    Omit PHP closing tag to help avoid accidental output
*/
