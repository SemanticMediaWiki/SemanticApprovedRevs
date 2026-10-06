<?php

namespace SMW\ApprovedRevs\Tests;

use SMW\ApprovedRevs\PropertyAnnotator;
use SMW\ApprovedRevs\PropertyAnnotators\ApprovedByPropertyAnnotator;
use SMW\ApprovedRevs\PropertyAnnotators\ApprovedDatePropertyAnnotator;
use SMW\ApprovedRevs\PropertyAnnotators\ApprovedRevPropertyAnnotator;
use SMW\ApprovedRevs\PropertyAnnotators\ApprovedStatusPropertyAnnotator;
use SMW\DIWikiPage;

/**
 * @covers \SMW\ApprovedRevs\PropertyAnnotator
 * @group semantic-approved-revs
 *
 * @license GPL-2.0-or-later
 * @since 1.0
 */
class PropertyAnnotatorTest extends \PHPUnit\Framework\TestCase {

	/**
	 * @var \SMW\ApprovedRevs\ServicesFactory|\PHPUnit\Framework\MockObject\MockObject
	 */
	private $servicesFactory;

	/**
	 * @var \Psr\Log\NullLogger|\PHPUnit\Framework\MockObject\MockObject
	 */
	private $logger;

	protected function setUp(): void {
		parent::setUp();

		$approvedByPropertyAnnotator = $this->getMockBuilder( ApprovedByPropertyAnnotator::class )
			->disableOriginalConstructor()
			->getMock();

		$approvedStatusPropertyAnnotator = $this->getMockBuilder( ApprovedStatusPropertyAnnotator::class )
			->disableOriginalConstructor()
			->getMock();

		$approvedDatePropertyAnnotator = $this->getMockBuilder( ApprovedDatePropertyAnnotator::class )
			->disableOriginalConstructor()
			->getMock();

		$approvedRevPropertyAnnotator = $this->getMockBuilder( ApprovedRevPropertyAnnotator::class )
			->disableOriginalConstructor()
			->getMock();

		$this->servicesFactory = $this->getMockBuilder( '\SMW\ApprovedRevs\ServicesFactory' )
			->disableOriginalConstructor()
			->getMock();

		$this->servicesFactory->expects( $this->any() )
			->method( 'newApprovedByPropertyAnnotator' )
			->willReturn( $approvedByPropertyAnnotator );

		$this->servicesFactory->expects( $this->any() )
			->method( 'newApprovedStatusPropertyAnnotator' )
			->willReturn( $approvedStatusPropertyAnnotator );

		$this->servicesFactory->expects( $this->any() )
			->method( 'newApprovedDatePropertyAnnotator' )
			->willReturn( $approvedDatePropertyAnnotator );

		$this->servicesFactory->expects( $this->any() )
			->method( 'newApprovedRevPropertyAnnotator' )
			->willReturn( $approvedRevPropertyAnnotator );

		$this->logger = $this->getMockBuilder( '\Psr\Log\NullLogger' )
			->disableOriginalConstructor()
			->getMock();
	}

	public function testCanConstruct() {
		$this->assertInstanceOf(
			PropertyAnnotator::class,
			new PropertyAnnotator( $this->servicesFactory )
		);
	}

	public function testAddAnnotation() {
		$this->logger->expects( $this->once() )
			->method( 'info' );

		$semanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMock();

		$semanticData->expects( $this->once() )
			->method( 'getSubject' )
			->willReturn( DIWikiPage::newFromText( 'Foo' ) );

		$annotator = new PropertyAnnotator(
			$this->servicesFactory
		);

		$annotator->setLogger( $this->logger );
		$annotator->addAnnotation( $semanticData );
	}

	public function testCanNotAnnotate() {
		$this->logger->expects( $this->never() )
			->method( 'info' );

		$subject = $this->getMockBuilder( '\SMW\DIWikiPage' )
			->disableOriginalConstructor()
			->getMock();

		$semanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMock();

		$semanticData->expects( $this->once() )
			->method( 'getSubject' )
			->willReturn( $subject );

		$annotator = new PropertyAnnotator(
			$this->servicesFactory
		);

		$annotator->setLogger( $this->logger );
		$annotator->addAnnotation( $semanticData );
	}

}
