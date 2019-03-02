<?php

namespace SMW\ApprovedRevs\Tests;

use SMW\ApprovedRevs\ApprovedRevsHandler;

/**
 * @covers \SMW\ApprovedRevs\ApprovedRevsHandler
 * @group semantic-approved-revs
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class ApprovedRevsHandlerTest extends \PHPUnit_Framework_TestCase {

	private $approvedRevsFacade;

	protected function setUp() {

		$this->approvedRevsFacade = $this->getMockBuilder( '\SMW\ApprovedRevs\ApprovedRevsFacade' )
			->disableOriginalConstructor()
			->setMethods( [ 'getApprovedRevID' ] )
			->getMock();
	}

	public function testCanConstruct() {

		$this->assertInstanceOf(
			ApprovedRevsHandler::class,
			new ApprovedRevsHandler( $this->approvedRevsFacade )
		);
	}

	public function testIsApprovedUpdate_True() {

		$this->approvedRevsFacade->expects( $this->once() )
			->method( 'getApprovedRevID' )
			->will( $this->returnValue( 42 ) );

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new ApprovedRevsHandler(
			$this->approvedRevsFacade
		);

		$this->assertTrue(
			$instance->isApprovedUpdate( $title, 42 )
		);
	}

	public function testIsApprovedUpdate_False() {

		$this->approvedRevsFacade->expects( $this->once() )
			->method( 'getApprovedRevID' )
			->will( $this->returnValue( 42 ) );

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new ApprovedRevsHandler(
			$this->approvedRevsFacade
		);

		$this->assertFalse(
			$instance->isApprovedUpdate( $title, 1001 )
		);
	}

}
