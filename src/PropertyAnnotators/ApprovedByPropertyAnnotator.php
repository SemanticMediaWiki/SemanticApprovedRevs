<?php

namespace SMW\ApprovedRevs\PropertyAnnotators;

use SMW\DIProperty;
use SMW\SemanticData;
use SMW\DIWikiPage;
use SMW\ApprovedRevs\DatabaseLogReader;
use SMW\ApprovedRevs\PropertyRegistry;
use User;
use Title;

/**
 * @private
 *
 * @license GNU GPL v2+
 */
class ApprovedByPropertyAnnotator {

	/**
	 * @var DatabaseLogReader
	 */
	private $databaseLogReader;

	/**
	 * @var Integer|null
	 */
	private $approvedBy;

	/**
	 * @param DatabaseLogReader $databaseLogReader
	 */
	public function __construct( DatabaseLogReader $databaseLogReader ) {
		$this->databaseLogReader = $databaseLogReader;
	}

	/**
	 * @since 1.0
	 *
	 * @param string $approvedBy
	 */
	public function setApprovedBy( $approvedBy ) {
		$this->approvedBy = $approvedBy;
	}

	/**
	 * @since 1.0
	 *
	 * @return DIProperty
	 */
	public function newDIProperty() {
		return new DIProperty( PropertyRegistry::SAR_PROP_APPROVED_BY );
	}

	/**
	 * @since 1.0
	 *
	 * @param SemanticData $semanticData
	 */
	public function addAnnotation( SemanticData $semanticData ) {

		if ( $this->approvedBy === null ) {
			$this->approvedBy = $this->databaseLogReader->getUserForLogEntry(
				$semanticData->getSubject()->getTitle(),
				'approval'
			);
		}

		$property = $this->newDIProperty();
		$dataItem = $this->newDIWikiPage();
		$semanticData->removeProperty( $property );

		if ( $dataItem ) {
			$semanticData->addPropertyObjectValue( $property, $dataItem );
		}
	}

	private function newDIWikiPage() {

		if ( !$this->approvedBy instanceof User ) {
			return;
		}

		$userPage = $this->approvedBy->getUserPage();

		if ( $userPage instanceof Title ) {
			return DIWikiPage::newFromTitle( $userPage );
		}
	}

}
