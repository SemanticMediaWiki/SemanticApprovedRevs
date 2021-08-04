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

	protected function setUp(): void {

		$this->approvedRevsFacade = $this->getMockBuilder( '\SMW\ApprovedRevs\ApprovedRevsFacade' )
			->disableOriginalConstructor()
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
			->method( 'hasApprovedRevision' )
			->will( $this->returnValue( true ) );

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

	public function testIsApprovedUpdate_True_WhenNoApprovedRevIsAvailable() {

		$this->approvedRevsFacade->expects( $this->once() )
			->method( 'hasApprovedRevision' )
			->will( $this->returnValue( false ) );

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
			->method( 'hasApprovedRevision' )
			->will( $this->returnValue( true ) );

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

	public function testIsApprovedUpdate_TrueNoApprovedRev() {

		$this->approvedRevsFacade->expects( $this->once() )
			->method( 'hasApprovedRevision' )
			->will( $this->returnValue( true ) );

		$this->approvedRevsFacade->expects( $this->once() )
			->method( 'getApprovedRevID' )
			->will( $this->returnValue( null ) );

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new ApprovedRevsHandler(
			$this->approvedRevsFacade
		);

		$this->assertTrue(
			$instance->isApprovedUpdate( $title, 1001 )
		);
	}

	public function testDoChangeRevision() {

		$this->approvedRevsFacade->expects( $this->once() )
			->method( 'getApprovedRevID' )
			->will( $this->returnValue( 42 ) );

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new ApprovedRevsHandler(
			$this->approvedRevsFacade
		);

		$rev = null;

		$instance->doChangeRevision( $title, $rev );
	}

	public function testDoChangeRevisionID() {

		$this->approvedRevsFacade->expects( $this->once() )
			->method( 'getApprovedRevID' )
			->will( $this->returnValue( 42 ) );

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new ApprovedRevsHandler(
			$this->approvedRevsFacade
		);

		$rev = null;

		$instance->doChangeRevisionID( $title, $rev );
	}

	public function testDoChangeFile_NoSha1() {

		$this->approvedRevsFacade->expects( $this->once() )
			->method( 'getApprovedFileInfo' )
			->will( $this->returnValue( [ '', false ] ) );

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new ApprovedRevsHandler(
			$this->approvedRevsFacade
		);

		$file = null;

		$this->assertTrue(
			$instance->doChangeFile( $title, $file )
		);
	}

	public function testDoChangeFile_FromLocalRepo() {

		$f = null;

		$this->approvedRevsFacade->expects( $this->once() )
			->method( 'getApprovedFileInfo' )
			->will( $this->returnValue( [ '1552165749', '2fd4e1c67a2d28fced849ee1bb76e7391b93eb12' ] ) );

		$file = $this->getMockBuilder( '\File' )
			->disableOriginalConstructor()
			->getMock();

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$db = $this->getMockBuilder( '\DatabaseBase' )
			->disableOriginalConstructor()
			->getMock();

		$localRepo = $this->getMockBuilder( '\LocalRepo' )
			->disableOriginalConstructor()
			->getMock();

		$localRepo->expects( $this->once() )
			->method( 'getReplicaDB' )
			->will( $this->returnValue( $db ) );

		$localRepo->expects( $this->once() )
			->method( 'findBySha1' )
			->with( $this->equalTo( '2fd4e1c67a2d28fced849ee1bb76e7391b93eb12' ) )
			->will( $this->returnValue( [ $file ] ) );

		$repoGroup = $this->getMockBuilder( '\RepoGroup' )
			->disableOriginalConstructor()
			->getMock();

		$repoGroup->expects( $this->once() )
			->method( 'getLocalRepo' )
			->will( $this->returnValue( $localRepo ) );

		$instance = new ApprovedRevsHandler(
			$this->approvedRevsFacade,
			$repoGroup
		);

		$instance->doChangeFile( $title, $f );

		$this->assertSame(
			'2fd4e1c67a2d28fced849ee1bb76e7391b93eb12',
			$f->file_sha1
		);
	}

}
