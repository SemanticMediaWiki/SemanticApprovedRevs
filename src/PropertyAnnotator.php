<?php

namespace SMW\ApprovedRevs;

use Psr\Log\LoggerAwareTrait;
use SMW\DIProperty;
use SMW\SemanticData;
use SMW\ApprovedRevs\PropertyAnnotators\ApprovedStatusPropertyAnnotator;
use SMW\ApprovedRevs\PropertyAnnotators\ApprovedByPropertyAnnotator;
use SMW\ApprovedRevs\PropertyAnnotators\ApprovedDatePropertyAnnotator;
use SMW\ApprovedRevs\PropertyAnnotators\ApprovedRevPropertyAnnotator;

/**
 * @private
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class PropertyAnnotator {

	use LoggerAwareTrait;

	/**
	 * @var ServicesFactory
	 */
	private $servicesFactory;

	/**
	 * @var PropertyAnnotator[]
	 */
	private $propertyAnnotators = [];

	/**
	 * @since 1.0
	 *
	 * @param ServicesFactory $servicesFactory
	 */
	public function __construct( ServicesFactory $servicesFactory ) {
		$this->servicesFactory = $servicesFactory;
	}

	/**
	 * @since 1.0
	 *
	 * @param SemanticData $semanticData
	 */
	public function addAnnotation( SemanticData $semanticData ) {

		$time = microtime( true );

		if ( !$this->canAnnotate( $semanticData->getSubject() ) ) {
			return;
		}

		if ( $this->propertyAnnotators === [] ) {
			$this->initPropertyAnnotators();
		}

		foreach ( $this->propertyAnnotators as $propertyAnnotator ) {
			$propertyAnnotator->addAnnotation( $semanticData );
		}

		$this->logger->info(
			[ 'SemanticApprovedRevs', 'procTime:{procTime}' ],
			[ 'procTime' => round( ( microtime( true ) - $time ), 5 ) ]
		);
	}

	private function canAnnotate( $subject ) {

		if ( $subject === null || $subject->getTitle() === null || $subject->getTitle()->isSpecialPage() ) {
			return false;
		}

		return true;
	}

	private function initPropertyAnnotators() {
		$this->propertyAnnotators[] = $this->servicesFactory->newApprovedByPropertyAnnotator();
		$this->propertyAnnotators[] = $this->servicesFactory->newApprovedStatusPropertyAnnotator();
		$this->propertyAnnotators[] = $this->servicesFactory->newApprovedDatePropertyAnnotator();
		$this->propertyAnnotators[] = $this->servicesFactory->newApprovedRevPropertyAnnotator();
	}

}
