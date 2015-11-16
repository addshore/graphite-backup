<?php

require_once __DIR__ . '/GraphiteBackup.php';

if( !array_key_exists( 1, $argv ) ) {
	die( "You must provide at least one target parameter!" );
}

$params = $argv;
unset( $params[0] );

$app = new GraphiteBackup();
$app->execute( $params );