<?php

namespace SMW\ApprovedRevs\Tests;

use SMW\ApprovedRevs\PropertyAnnotator;
use SMW\DIProperty;
use SMW\DIWikiPage;

/**
 * @covers \SMW\ApprovedRevs\PropertyAnnotator
 * @group semantic-approved-revs
 *
 * @license GNU GPL v2+
 * @since 1.0
 */
class PropertyAnnotatorTest extends \PHPUnit_Framework_TestCase {

	private $servicesFactory;
	private $logger;

	protected function setUp(): void {
		parent::setUp();

		$approvedByPropertyAnnotator = $this->getMockBuilder( '\SMW\ApprovedRevs\PropertyAnnotators\ApprovedByPropertyAnnotator' )
			->disableOriginalConstructor()
			->getMock();

		$approvedStatusPropertyAnnotator = $this->getMockBuilder( '\SMW\ApprovedRevs\PropertyAnnotators\ApprovedStatusPropertyAnnotator' )
			->disableOriginalConstructor()
			->getMock();

		$approvedDatePropertyAnnotator = $this->getMockBuilder( '\SMW\ApprovedRevs\PropertyAnnotators\ApprovedDatePropertyAnnotator' )
			->disableOriginalConstructor()
			->getMock();

		$approvedRevPropertyAnnotator = $this->getMockBuilder( '\SMW\ApprovedRevs\PropertyAnnotators\ApprovedRevPropertyAnnotator' )
			->disableOriginalConstructor()
			->getMock();

		$this->servicesFactory = $this->getMockBuilder( '\SMW\ApprovedRevs\ServicesFactory' )
			->disableOriginalConstructor()
			->getMock();

		$this->servicesFactory->expects( $this->any() )
			->method( 'newApprovedByPropertyAnnotator' )
			->will( $this->returnValue( $approvedByPropertyAnnotator ) );

		$this->servicesFactory->expects( $this->any() )
			->method( 'newApprovedStatusPropertyAnnotator' )
			->will( $this->returnValue( $approvedStatusPropertyAnnotator ) );

		$this->servicesFactory->expects( $this->any() )
			->method( 'newApprovedDatePropertyAnnotator' )
			->will( $this->returnValue( $approvedDatePropertyAnnotator ) );

		$this->servicesFactory->expects( $this->any() )
			->method( 'newApprovedRevPropertyAnnotator' )
			->will( $this->returnValue( $approvedRevPropertyAnnotator ) );

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
			->will( $this->returnValue( DIWikiPage::newFromText( 'Foo' ) ) );

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
			->will( $this->returnValue( $subject ) );

		$annotator = new PropertyAnnotator(
			$this->servicesFactory
		);

		$annotator->setLogger( $this->logger );
		$annotator->addAnnotation( $semanticData );
	}

}
