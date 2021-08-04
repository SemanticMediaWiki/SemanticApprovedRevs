<?php

namespace SMW\ApprovedRevs\Tests\PropertyAnnotators;

use SMW\ApprovedRevs\PropertyAnnotators\ApprovedStatusPropertyAnnotator;
use SMW\DIProperty;
use SMWDIString as DIString;

/**
 * @covers \SMW\ApprovedRevs\PropertyAnnotators\ApprovedStatusPropertyAnnotator
 * @group semantic-approved-revs
 *
 * @license GNU GPL v2+
 * @since 1.0
 */
class ApprovedStatusPropertyAnnotatorTest extends \PHPUnit_Framework_TestCase {

	private $databaseLogReader;

	protected function setUp(): void {
		parent::setUp();

		$this->databaseLogReader = $this->getMockBuilder( '\SMW\ApprovedRevs\DatabaseLogReader' )
			->disableOriginalConstructor()
			->getMock();
	}

	public function testCanConstruct() {

		$this->assertInstanceOf(
			ApprovedStatusPropertyAnnotator::class,
			new ApprovedStatusPropertyAnnotator( $this->databaseLogReader )
		);
	}

	public function testAddAnnotation() {

		$semanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMock();

		$semanticData->expects( $this->once() )
			->method( 'addPropertyObjectValue' )
			->with(
				$this->anyThing(),
				$this->equalTo( new DIString( "checkme" ) ) );

		$annotator = new ApprovedStatusPropertyAnnotator(
			$this->databaseLogReader
		);

		$annotator->setApprovedStatus( "checkme" );
		$annotator->addAnnotation( $semanticData );
	}

	public function testRemoval() {

		$semanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMock();

		$semanticData->expects( $this->once() )
			->method( 'removeProperty' );

		$annotator = new ApprovedStatusPropertyAnnotator(
			$this->databaseLogReader
		);

		$annotator->setApprovedStatus( false );
		$annotator->addAnnotation( $semanticData );
	}

}
