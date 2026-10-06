<?php

namespace SMW\ApprovedRevs\Tests;

use MediaWiki\Revision\RevisionLookup;
use MediaWiki\Revision\RevisionStoreRecord;
use MediaWiki\Title\Title;
use MediaWikiIntegrationTestCase;
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
class ApprovedRevsHandlerTest extends MediaWikiIntegrationTestCase {

	/**
	 * @var \SMW\ApprovedRevs\ApprovedRevsFacade|\PHPUnit\Framework\MockObject\MockObject
	 */
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

		$title = $this->getMockBuilder( Title::class )
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

		$title = $this->getMockBuilder( Title::class )
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

		$title = $this->getMockBuilder( Title::class )
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

		$title = $this->getMockBuilder( Title::class )
			->disableOriginalConstructor()
			->getMock();

		$instance = new ApprovedRevsHandler(
			$this->approvedRevsFacade
		);

		$this->assertTrue(
			$instance->isApprovedUpdate( $title, 1001 )
		);
	}

	public function testDoChangeRevisionID() {
		$this->approvedRevsFacade->expects( $this->once() )
			->method( 'getApprovedRevID' )
			->willReturn( 42 );

		$title = $this->getMockBuilder( Title::class )
			->disableOriginalConstructor()
			->getMock();

		$instance = new ApprovedRevsHandler(
			$this->approvedRevsFacade
		);

		$rev = null;

		$instance->doChangeRevisionID( $title, $rev );

		$this->assertSame( 42, $rev );
	}

	public function testDoChangeRevisionID_KeepsLatestWhenNothingIsApproved() {
		$this->approvedRevsFacade->expects( $this->once() )
			->method( 'getApprovedRevID' )
			->willReturn( null );

		$title = $this->getMockBuilder( Title::class )
			->disableOriginalConstructor()
			->getMock();

		$instance = new ApprovedRevsHandler(
			$this->approvedRevsFacade
		);

		$rev = 1001;

		$instance->doChangeRevisionID( $title, $rev );

		$this->assertSame( 1001, $rev );
	}

	public function testDoChangeRevision_ReplacesRevisionWithApprovedOne() {
		$approvedRev = $this->getMockBuilder( RevisionStoreRecord::class )
			->disableOriginalConstructor()
			->getMock();

		$revisionLookup = $this->createMock( RevisionLookup::class );
		$revisionLookup->expects( $this->once() )
			->method( 'getRevisionById' )
			// @phan-suppress-next-line PhanTypeMismatchArgumentProbablyReal PHPUnit with() typing
			->with( 42 )
			->willReturn( $approvedRev );

		$this->setService( 'RevisionLookup', $revisionLookup );

		$this->approvedRevsFacade->expects( $this->once() )
			->method( 'getApprovedRevID' )
			->willReturn( 42 );

		$title = $this->getMockBuilder( Title::class )
			->disableOriginalConstructor()
			->getMock();

		$instance = new ApprovedRevsHandler(
			$this->approvedRevsFacade
		);

		$rev = null;

		$instance->doChangeRevision( $title, $rev );

		$this->assertSame( $approvedRev, $rev );
	}

	public function testDoChangeRevision_KeepsRevisionWhenNothingIsApproved() {
		$this->approvedRevsFacade->expects( $this->once() )
			->method( 'getApprovedRevID' )
			->willReturn( null );

		$title = $this->getMockBuilder( Title::class )
			->disableOriginalConstructor()
			->getMock();

		$instance = new ApprovedRevsHandler(
			$this->approvedRevsFacade
		);

		$latest = $this->getMockBuilder( RevisionStoreRecord::class )
			->disableOriginalConstructor()
			->getMock();

		$rev = $latest;

		$instance->doChangeRevision( $title, $rev );

		$this->assertSame( $latest, $rev );
	}

	public function testDoChangeFile_NoSha1() {
		$this->approvedRevsFacade->expects( $this->once() )
			->method( 'getApprovedFileInfo' )
			->willReturn( [ '', false ] );

		$title = $this->getMockBuilder( Title::class )
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

		$title = $this->getMockBuilder( Title::class )
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
			// @phan-suppress-next-line PhanTypeMismatchArgumentProbablyReal PHPUnit with() typing
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

	public function testDoChangeFile_UsesRepoGroupServiceByDefault() {
		$sha1 = '2fd4e1c67a2d28fced849ee1bb76e7391b93eb12';

		$this->approvedRevsFacade->expects( $this->once() )
			->method( 'getApprovedFileInfo' )
			->willReturn( [ '1552165749', $sha1 ] );

		$file = $this->getMockBuilder( '\File' )
			->disableOriginalConstructor()
			->getMock();

		$db = $this->getMockBuilder( '\Wikimedia\Rdbms\Database' )
			->disableOriginalConstructor()
			->getMock();

		$localRepo = $this->getMockBuilder( '\LocalRepo' )
			->disableOriginalConstructor()
			->getMock();

		$localRepo->method( 'getReplicaDB' )
			->willReturn( $db );

		$localRepo->expects( $this->once() )
			->method( 'findBySha1' )
			// @phan-suppress-next-line PhanTypeMismatchArgumentProbablyReal PHPUnit with() typing
			->with( $sha1 )
			->willReturn( [ $file ] );

		$repoGroup = $this->getMockBuilder( '\RepoGroup' )
			->disableOriginalConstructor()
			->getMock();

		$repoGroup->expects( $this->once() )
			->method( 'getLocalRepo' )
			->willReturn( $localRepo );

		$this->setService( 'RepoGroup', $repoGroup );

		$title = $this->getMockBuilder( Title::class )
			->disableOriginalConstructor()
			->getMock();

		$f = null;

		( new ApprovedRevsHandler( $this->approvedRevsFacade ) )->doChangeFile( $title, $f );

		$this->assertSame( $file, $f );
	}

}
