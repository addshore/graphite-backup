<?php

require_once __DIR__ . '/GraphiteBackup.php';

if( !array_key_exists( 1, $argv ) ) {
	die( "You must provide a target parameter!" );
}

$app = new GraphiteBackup();
$app->execute( $argv[1] );