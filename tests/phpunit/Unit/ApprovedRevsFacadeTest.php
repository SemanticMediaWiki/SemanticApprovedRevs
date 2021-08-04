<?php

namespace SMW\ApprovedRevs\Tests;

use SMW\ApprovedRevs\ApprovedRevsFacade;

/**
 * @covers \SMW\ApprovedRevs\ApprovedRevsFacade
 * @group semantic-approved-revs
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class ApprovedRevsFacadeTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$this->assertInstanceOf(
			ApprovedRevsFacade::class,
			new ApprovedRevsFacade()
		);
	}

	public function testGetApprovedRevID() {

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new ApprovedRevsFacade();

		$this->assertNull(
			$instance->getApprovedRevID( $title )
		);
	}

	public function testHasApprovedRevision() {

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new ApprovedRevsFacade();

		$this->assertIsBool(
			$instance->hasApprovedRevision( $title )
		);
	}

	public function testGetApprovedFileInfo() {

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new ApprovedRevsFacade();

		$this->assertIsArray(
			$instance->getApprovedFileInfo( $title )
		);

	}

	public function testClearApprovedFileInfo() {

		$this->assertTrue(
			property_exists( new \ApprovedRevs(), 'mApprovedFileInfo' )
		);

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new ApprovedRevsFacade();

		$this->assertIsArray(
			$instance->getApprovedFileInfo( $title )
		);

		$title->expects( $this->once() )
			->method( 'getDBkey' );

		$instance = new ApprovedRevsFacade();
		$instance->clearApprovedFileInfo( $title );
	}

}
