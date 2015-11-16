<?php

class GraphiteBackup {

	public function execute( $targetInput ) {
		$this->echoLine( "Getting $targetInput" );

		$data = $this->getDataForTarget( $targetInput );

		if ( empty( $data ) ) {
			$this->echoLine( "Empty target, no backup made." );

			return;
		}

		foreach ( $data as $targetData ) {
			$metric = $targetData['target'];
			$this->echoLine( "Backing up $metric" );
			$dataPoints = $targetData['datapoints'];
			$file = $this->getDataPath( $metric );

			// Note: Loading this could start using lots of memory?
			$currentFileContents = @file_get_contents( $file );
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
					$this->echoLine( "Failed to write to file." );
					return;
				}
			}


			$this->echoLine( "$dataPointsAdded new points, $dataPointsSkipped skipped points." );
		}

		$this->echoLine( "Done." );
	}

	private function echoLine( $line ) {
		echo $line . "\n";
	}

	private function getDataPath( $metric ) {
		return $this->getDataDirectory() . $metric . '.txt';
	}

	private function getDataDirectory() {
		return  __DIR__ .
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
