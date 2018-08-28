<?php

namespace McGuffin;

if ( ! defined('ABSPATH') ) {
	die('FU!');
}

function __autoload( $class ) {
	if ( strpos( $class, 'McGuffin\\' ) === false ) {
		// not our plugin.
		return;
	}
	$ds = DIRECTORY_SEPARATOR;
	$file = get_template_directory() . $ds . 'include' . $ds . str_replace( '\\', $ds, $class ) . '.php';

	if ( file_exists( $file ) ) {
		require_once $file;
	} else {
		throw new \Exception( sprintf( 'Class `%s` could not be loaded. File `%s` not found.', $class, $file ) );
	}
}


spl_autoload_register( 'McGuffin\__autoload' );
