<?php

namespace SMW\ApprovedRevs\Tests\PropertyAnnotators;

use MWTimestamp;
use SMW\ApprovedRevs\PropertyAnnotators\ApprovedDatePropertyAnnotator;
use SMWDITime as DITime;

/**
 * @covers \SMW\ApprovedRevs\PropertyAnnotators\ApprovedDatePropertyAnnotator
 * @group semantic-approved-revs
 *
 * @license GPL-2.0-or-later
 * @since 1.0
 */
class ApprovedDatePropertyAnnotatorTest extends \PHPUnit\Framework\TestCase {

	private $databaseLogReader;

	protected function setUp(): void {
		parent::setUp();

		$this->databaseLogReader = $this->getMockBuilder( '\SMW\ApprovedRevs\DatabaseLogReader' )
			->disableOriginalConstructor()
			->getMock();
	}

	public function testCanConstruct() {
		$this->assertInstanceOf(
			ApprovedDatePropertyAnnotator::class,
			new ApprovedDatePropertyAnnotator( $this->databaseLogReader )
		);
	}

	protected static function getDITime( MWTimestamp $time ) {
		return new DITime(
				DITime::CM_GREGORIAN,
				$time->format( 'Y' ),
				$time->format( 'm' ),
				$time->format( 'd' ),
				$time->format( 'H' ),
				$time->format( 'i' )
		);
	}

	public function testAddAnnotation() {
		$now = new MWTimestamp( wfTimestampNow() );
		$time = self::getDITime( $now );

		$semanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMock();

		$semanticData->expects( $this->once() )
			->method( 'addPropertyObjectValue' )
			->with(
				$this->anyThing(),
				$time
			);

		$annotator = new ApprovedDatePropertyAnnotator(
			$this->databaseLogReader
		);

		$annotator->setApprovedDate( $now );
		$annotator->addAnnotation( $semanticData );
	}

	public function testRemoval() {
		$semanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMock();

		$semanticData->expects( $this->once() )
			->method( 'removeProperty' );

		$annotator = new ApprovedDatePropertyAnnotator(
			$this->databaseLogReader
		);

		$annotator->setApprovedDate( false );
		$annotator->addAnnotation( $semanticData );
	}

}
