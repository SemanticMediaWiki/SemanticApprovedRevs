<?php

namespace SMW\ApprovedRevs\Tests\PropertyAnnotators;

use SMW\ApprovedRevs\PropertyAnnotators\ApprovedRevPropertyAnnotator;
use SMWDINumber as DINumber;

/**
 * @covers \SMW\ApprovedRevs\PropertyAnnotators\ApprovedRevPropertyAnnotator
 * @group semantic-approved-revs
 *
 * @license GPL-2.0-or-later
 * @since 1.0
 */
class ApprovedRevPropertyAnnotatorTest extends \PHPUnit\Framework\TestCase {

	private $approvedRevsFacade;

	protected function setUp(): void {
		parent::setUp();

		$this->approvedRevsFacade = $this->getMockBuilder( '\SMW\ApprovedRevs\ApprovedRevsFacade' )
			->disableOriginalConstructor()
			->getMock();
	}

	public function testCanConstruct() {
		$this->assertInstanceOf(
			ApprovedRevPropertyAnnotator::class,
			new ApprovedRevPropertyAnnotator( $this->approvedRevsFacade )
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
				new DINumber( 42 ) );

		$annotator = new ApprovedRevPropertyAnnotator(
			$this->approvedRevsFacade
		);

		$annotator->setApprovedRev( 42 );
		$annotator->addAnnotation( $semanticData );
	}

	public function testRemoval() {
		$semanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMock();

		$semanticData->expects( $this->once() )
			->method( 'removeProperty' );

		$annotator = new ApprovedRevPropertyAnnotator(
			$this->approvedRevsFacade
		);

		$annotator->setApprovedRev( false );
		$annotator->addAnnotation( $semanticData );
	}

}
