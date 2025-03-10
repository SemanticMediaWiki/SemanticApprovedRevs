<?php

namespace SMW\ApprovedRevs\Tests\PropertyAnnotators;

use SMW\ApprovedRevs\PropertyAnnotators\ApprovedByPropertyAnnotator;
use SMW\DIWikiPage;
use User;

/**
 * @covers \SMW\ApprovedRevs\PropertyAnnotators\ApprovedByPropertyAnnotator
 * @group semantic-approved-revs
 *
 * @license GPL-2.0-or-later
 * @since 1.0
 */
class ApprovedByPropertyAnnotatorTest extends \PHPUnit\Framework\TestCase {

	private $databaseLogReader;

	protected function setUp(): void {
		parent::setUp();

		$this->databaseLogReader = $this->getMockBuilder( '\SMW\ApprovedRevs\DatabaseLogReader' )
			->disableOriginalConstructor()
			->getMock();
	}

	public function testCanConstruct() {
		$this->assertInstanceOf(
			ApprovedByPropertyAnnotator::class,
			new ApprovedByPropertyAnnotator( $this->databaseLogReader )
		);
	}

	public function testAddAnnotation() {
		$user = User::newFromName( "UnitTest" );

		$semanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMock();

		$semanticData->expects( $this->once() )
			->method( 'addPropertyObjectValue' )
			->with(
				$this->anyThing(),
				DIWikiPage::newFromTitle( $user->getUserPage() ) );

		$annotator = new ApprovedByPropertyAnnotator(
			$this->databaseLogReader
		);

		$annotator->setApprovedBy( $user );
		$annotator->addAnnotation( $semanticData );
	}

	public function testRemoval() {
		$semanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMock();

		$semanticData->expects( $this->once() )
			->method( 'removeProperty' );

		$annotator = new ApprovedByPropertyAnnotator(
			$this->databaseLogReader
		);

		$annotator->setApprovedBy( false );
		$annotator->addAnnotation( $semanticData );
	}

}
