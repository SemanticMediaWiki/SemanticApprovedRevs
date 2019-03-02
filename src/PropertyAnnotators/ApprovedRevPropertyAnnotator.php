<?php

namespace SMW\ApprovedRevs\PropertyAnnotators;

use SMW\DIProperty;
use SMW\SemanticData;
use SMWDINumber as DINumber;
use SMW\ApprovedRevs\ApprovedRevsFacade;
use SMW\ApprovedRevs\PropertyRegistry;

/**
 * @private
 *
 * @license GNU GPL v2+
 */
class ApprovedRevPropertyAnnotator {

	/**
	 * @var ApprovedRevsFacade
	 */
	private $approvedRevsFacade;

	/**
	 * @var Integer|null
	 */
	private $approvedRev;

	/**
	 * @param ApprovedRevsFacade $approvedRevsFacade
	 */
	public function __construct( ApprovedRevsFacade $approvedRevsFacade ) {
		$this->approvedRevsFacade = $approvedRevsFacade;
	}

	/**
	 * @since 1.0
	 *
	 * @param integer $approvedRev
	 */
	public function setApprovedRev( $approvedRev ) {
		$this->approvedRev = $approvedRev;
	}

	/**
	 * @since 1.0
	 *
	 * @return DIProperty
	 */
	public function newDIProperty() {
		return new DIProperty( PropertyRegistry::SAR_PROP_APPROVED_REV );
	}

	/**
	 * @since 1.0
	 *
	 * @param SemanticData $semanticData
	 */
	public function addAnnotation( SemanticData $semanticData ) {

		if ( $this->approvedRev === null ) {
			$this->approvedRev = $this->approvedRevsFacade->getApprovedRevID(
				$semanticData->getSubject()->getTitle()
			);
		}

		$property = $this->newDIProperty();
		$semanticData->removeProperty( $property );

		if ( is_numeric( $this->approvedRev ) ) {
			$semanticData->addPropertyObjectValue(
				$property,
				$this->newDINumber()
			);
		}
	}

	private function newDINumber() {
		return new DINumber( $this->approvedRev );
	}

}
