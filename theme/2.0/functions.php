<?php
/**
 * _s functions and definitions.
 *
 * @link https://codex.wordpress.org/Functions_File_Explained
 *
 * @package _s
 */

namespace McGuffin;


/**
 *	Register Autoloader
 */
require_once get_template_directory() . '/include/autoload.php';

/**
 * Custom template tags for this theme.
 */
require_once get_template_directory() . '/include/template-tags.php';


Theme::instance();
