<?php

namespace SMW\ApprovedRevs\PropertyAnnotators;

use SMW\DIProperty;
use SMW\SemanticData;
use SMWDIBlob as DIBlob;
use SMW\ApprovedRevs\DatabaseLogReader;
use SMW\ApprovedRevs\PropertyRegistry;

/**
 * @private
 *
 * @license GNU GPL v2+
 */
class ApprovedStatusPropertyAnnotator {

	/**
	 * @var DatabaseLogReader
	 */
	private $databaseLogReader;

	/**
	 * @var Integer|null
	 */
	private $approvedStatus;

	/**
	 * @param DatabaseLogReader $databaseLogReader
	 */
	public function __construct( DatabaseLogReader $databaseLogReader ) {
		$this->databaseLogReader = $databaseLogReader;
	}

	/**
	 * @since 1.0
	 *
	 * @param string $approvedStatus
	 */
	public function setApprovedStatus( $approvedStatus ) {
		$this->approvedStatus = $approvedStatus;
	}

	/**
	 * @since 1.0
	 *
	 * @return DIProperty
	 */
	public function newDIProperty() {
		return new DIProperty( PropertyRegistry::SAR_PROP_APPROVED_STATUS );
	}

	/**
	 * @since 1.0
	 *
	 * @param SemanticData $semanticData
	 */
	public function addAnnotation( SemanticData $semanticData ) {

		if ( $this->approvedStatus === null ) {
			$this->approvedStatus = $this->databaseLogReader->getStatusOfLogEntry(
				$semanticData->getSubject()->getTitle(),
				'approval'
			);
		}

		$property = $this->newDIProperty();
		$semanticData->removeProperty( $property );

		if ( is_string( $this->approvedStatus ) && $this->approvedStatus !== '' ) {
			$semanticData->addPropertyObjectValue(
				$property,
				new DIBlob( $this->approvedStatus )
			);
		}
	}

}
