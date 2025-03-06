<?php

namespace SMW\ApprovedRevs\Tests;

use SMW\ApprovedRevs\ApprovedRevsHandler;

/**
 * @covers \SMW\ApprovedRevs\ApprovedRevsHandler
 * @group semantic-approved-revs
 *
 * @license GPL-2.0-or-later
 * @since 1.0
 *
 * @author mwjames
 */
class ApprovedRevsHandlerTest extends \PHPUnit\Framework\TestCase {

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
			->willReturn( true );

		$this->approvedRevsFacade->expects( $this->once() )
			->method( 'getApprovedRevID' )
			->willReturn( 42 );

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
			->willReturn( false );

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
			->willReturn( true );

		$this->approvedRevsFacade->expects( $this->once() )
			->method( 'getApprovedRevID' )
			->willReturn( 42 );

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
			->willReturn( true );

		$this->approvedRevsFacade->expects( $this->once() )
			->method( 'getApprovedRevID' )
			->willReturn( null );

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
			->willReturn( 42 );

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
			->willReturn( 42 );

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
			->willReturn( [ '', false ] );

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
			->willReturn( [ '1552165749', '2fd4e1c67a2d28fced849ee1bb76e7391b93eb12' ] );

		$file = $this->getMockBuilder( '\File' )
			->disableOriginalConstructor()
			->getMock();

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$db = $this->getMockBuilder( '\Wikimedia\Rdbms\Database' )
			->disableOriginalConstructor()
			->getMock();

		$localRepo = $this->getMockBuilder( '\LocalRepo' )
			->disableOriginalConstructor()
			->getMock();

		$localRepo->expects( $this->once() )
			->method( 'getReplicaDB' )
			->willReturn( $db );

		$localRepo->expects( $this->once() )
			->method( 'findBySha1' )
			->with( '2fd4e1c67a2d28fced849ee1bb76e7391b93eb12' )
			->willReturn( [ $file ] );

		$repoGroup = $this->getMockBuilder( '\RepoGroup' )
			->disableOriginalConstructor()
			->getMock();

		$repoGroup->expects( $this->once() )
			->method( 'getLocalRepo' )
			->willReturn( $localRepo );

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
