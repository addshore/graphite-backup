<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = new Symfony\Component\Console\Application( 'graphite-backup' );

$app->addCommands( array(
	new GraphiteBackup\Console\Command\BackupCommand(),
) );

$app->setDefaultCommand( 'backup' );
$app->run();
