<?php

namespace SMW\ApprovedRevs\PropertyAnnotators;

use MediaWiki\Title\Title;
use MediaWiki\User\UserIdentity;
use SMW\ApprovedRevs\DatabaseLogReader;
use SMW\ApprovedRevs\PropertyRegistry;
use SMW\DIProperty;
use SMW\DIWikiPage;
use SMW\SemanticData;

/**
 * @private
 *
 * @license GPL-2.0-or-later
 */
class ApprovedByPropertyAnnotator {

	/**
	 * @var DatabaseLogReader
	 */
	private $databaseLogReader;

	/**
	 * @var UserIdentity|false|null
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
	 * @param UserIdentity|false|null $approvedBy
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

	/**
	 * @return DIWikiPage|null
	 */
	private function newDIWikiPage() {
		if ( !$this->approvedBy instanceof UserIdentity ) {
			return;
		}

		$userPage = Title::makeTitle( NS_USER, $this->approvedBy->getName() );

		if ( $userPage instanceof Title ) {
			return DIWikiPage::newFromTitle( $userPage );
		}
	}

}
