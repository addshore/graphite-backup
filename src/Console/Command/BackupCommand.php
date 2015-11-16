<?php

namespace GraphiteBackup\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BackupCommand extends Command {

	protected function configure() {
		$this
			->setName( 'backup' )
			->setDescription( 'Back up a target into local files.' )
			->addArgument(
				'target',
				InputArgument::REQUIRED,
				'Which target do you want to backup?'
			);
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$targetInput = $input->getArgument( 'target' );
		$output->writeln( "Getting $targetInput" );

		$data = $this->getDataForTarget( $targetInput );

		if ( empty( $data ) ) {
			$output->writeln( "Empty target, no backup made." );

			return;
		}

		foreach ( $data as $targetData ) {
			$metric = $targetData['target'];
			$output->writeln( "Backing up $metric" );
			$dataPoints = $targetData['datapoints'];
			$file = $this->getDataPath( $metric );

			// Note: Loading this could start using lots of memory?
			$currentFileContents = file_get_contents( $file );
			if ( $currentFileContents === false ) {
				$currentFileContents = '';
			}

			$dataPointsAdded = 0;
			$dataPointsSkipped = 0;

			foreach ( $dataPoints as $dataPoint ) {
				list( $value, $timestamp ) = $dataPoint;
				$stringToAdd = $timestamp . ' ' . $value . "\n";

				// Don't write the line if we already have an exact duplicate
				if ( strpos( $currentFileContents, $stringToAdd ) !== false ) {
					$dataPointsSkipped++;
					continue;
				}

				// Write the metric to the file
				$success = file_put_contents( $file, $stringToAdd, FILE_APPEND );
				$dataPointsAdded++;
				if ( $success === false ) {
					$output->writeln( "Failed to write to file." );
					return;
				}
			}


			$output->writeln( "$dataPointsAdded new points, $dataPointsSkipped skipped points." );
		}

		$output->writeln( "Done." );
	}

	private function getDataPath( $metric ) {
		return $this->getDataDirectory() . $metric . '.txt';
	}

	private function getDataDirectory() {
		return dirname( dirname( dirname( __DIR__ ) ) ) .
		DIRECTORY_SEPARATOR .
		'data' .
		DIRECTORY_SEPARATOR;
	}

	private function getDataForTarget( $target ) {
		$json = file_get_contents( $this->getApiUrl( $target ) );

		return json_decode( $json, true );
	}

	private function getApiUrl( $target ) {
		return "https://graphite.wikimedia.org/render/?target=$target&format=json";
	}

}
