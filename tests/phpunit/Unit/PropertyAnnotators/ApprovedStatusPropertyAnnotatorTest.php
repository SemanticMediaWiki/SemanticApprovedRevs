<?php

namespace SMW\ApprovedRevs\Tests\PropertyAnnotators;

use SMW\ApprovedRevs\PropertyAnnotators\ApprovedStatusPropertyAnnotator;
use SMWDIBlob;

/**
 * @covers \SMW\ApprovedRevs\PropertyAnnotators\ApprovedStatusPropertyAnnotator
 * @group semantic-approved-revs
 *
 * @license GPL-2.0-or-later
 * @since 1.0
 */
class ApprovedStatusPropertyAnnotatorTest extends \PHPUnit\Framework\TestCase {

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
				new SMWDIBlob( "checkme" ) );

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
