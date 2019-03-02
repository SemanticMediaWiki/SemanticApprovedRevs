<?php

namespace SMW\ApprovedRevs\PropertyAnnotators;

use SMW\DIProperty;
use SMW\SemanticData;
use SMWDITime as DITime;;
use SMW\ApprovedRevs\DatabaseLogReader;
use SMW\ApprovedRevs\PropertyRegistry;
use User;

/**
 * @private
 *
 * @license GNU GPL v2+
 */
class ApprovedDatePropertyAnnotator {

	/**
	 * @var DatabaseLogReader
	 */
	private $databaseLogReader;

	/**
	 * @var Integer|null
	 */
	private $approvedDate;

	/**
	 * @param DatabaseLogReader $databaseLogReader
	 */
	public function __construct( DatabaseLogReader $databaseLogReader ) {
		$this->databaseLogReader = $databaseLogReader;
	}

	/**
	 * @since 1.0
	 *
	 * @param integer $approvedDate
	 */
	public function setApprovedDate( $approvedDate ) {
		$this->approvedDate = $approvedDate;
	}

	/**
	 * @since 1.0
	 *
	 * @return DIProperty
	 */
	public function newDIProperty() {
		return new DIProperty( PropertyRegistry::SAR_PROP_APPROVED_DATE );
	}

	/**
	 * @since 1.0
	 *
	 * @param SemanticData $semanticData
	 */
	public function addAnnotation( SemanticData $semanticData ) {

		if ( $this->approvedDate === null ) {
			$this->approvedDate = $this->databaseLogReader->getDateOfLogEntry(
				$semanticData->getSubject()->getTitle(),
				'approval'
			);
		}

		$property = $this->newDIProperty();
		$dataItem = $this->newDITime();
		$semanticData->removeProperty( $property );

		if ( $dataItem ) {
			$semanticData->addPropertyObjectValue( $property, $dataItem );
		}
	}

	private function newDITime() {

		if ( $this->approvedDate === null || is_bool( $this->approvedDate ) ) {
			return;
		}

		$date = $this->approvedDate;

		return new DITime(
			DITime::CM_GREGORIAN,
			$date->format( 'Y' ),
			$date->format( 'm' ),
			$date->format( 'd' ),
			$date->format( 'H' ),
			$date->format( 'i' )
		);
	}

}
