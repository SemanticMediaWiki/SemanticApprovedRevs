<?php

namespace SMW\ApprovedRevs\Tests\Integration;

use ApprovedRevs;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;
use SMW\ApprovedRevs\ApprovedRevsFacade;
use SMW\Services\ServicesFactory;
use SMW\Tests\SMWIntegrationTestCase;
use Wikimedia\Rdbms\IDBAccessObject;

/**
 * SMW only accepts an integer from the ChangeRevisionID hook, so the revision
 * ID reported for an approved page must be an int, whichever way ApprovedRevs
 * hands it out.
 *
 * @group SemanticApprovedRevs
 * @group Database
 * @group medium
 *
 * @covers \SMW\ApprovedRevs\Hooks
 * @covers \SMW\ApprovedRevs\ApprovedRevsHandler
 * @covers \SMW\ApprovedRevs\ApprovedRevsFacade
 */
class LatestRevisionIdTest extends SMWIntegrationTestCase {

	/** @var \SMW\Tests\Utils\PageCreator */
	private $pageCreator;

	private array $titles = [];

	protected function setUp(): void {
		parent::setUp();

		$this->forgetCachedApprovedRevisionIds();

		// Edits must not approve themselves, whoever the editor is
		$this->setMwGlobals( 'egApprovedRevsAutomaticApprovals', false );

		// Re-register SMW hook handlers
		// TODO: find a way to have the test use the Wiki "as it is" without having to re-register manually
		\SemanticMediaWiki::onExtensionFunction();
		\SMW\ApprovedRevs\Hooks::onExtensionFunction();

		$this->pageCreator = $this->testEnvironment->getUtilityFactory()->newPageCreator();
	}

	protected function tearDown(): void {
		$this->testEnvironment->flushPages( $this->titles );

		parent::tearDown();
	}

	public function testLatestRevisionIdIsTheApprovedOneWhenLaterEditsAreNotApproved() {
		$title = $this->newTitle( 'SarLatestRevisionId/Approved' );

		$approvedRevId = $this->createRevision( $title, 'Step 2' );

		ApprovedRevs::setApprovedRevID( $title, $approvedRevId, $this->getTestSysop()->getUser() );
		$this->testEnvironment->executePendingDeferredUpdates();

		$latestRevId = $this->createRevision( $title, 'Step 3' );

		$this->assertNotSame( $approvedRevId, $latestRevId );

		$this->forgetCachedApprovedRevisionIds();

		$this->assertSame(
			$approvedRevId,
			ServicesFactory::getInstance()->singleton( 'RevisionGuard' )->getLatestRevID( $title )
		);
	}

	public function testLatestRevisionIdIsKeptWhenNothingIsApproved() {
		$title = $this->newTitle( 'SarLatestRevisionId/Unapproved' );

		$this->createRevision( $title, 'Step 1' );
		$latestRevId = $this->createRevision( $title, 'Step 2' );

		$this->assertSame(
			$latestRevId,
			ServicesFactory::getInstance()->singleton( 'RevisionGuard' )->getLatestRevID( $title )
		);
	}

	public function testApprovedRevisionIdOfFacadeIsAnInteger() {
		$title = $this->newTitle( 'SarLatestRevisionId/Facade' );

		$approvedRevId = $this->createRevision( $title, 'Step 1' );

		ApprovedRevs::setApprovedRevID( $title, $approvedRevId, $this->getTestSysop()->getUser() );
		$this->testEnvironment->executePendingDeferredUpdates();

		$this->forgetCachedApprovedRevisionIds();

		$this->assertSame( $approvedRevId, ( new ApprovedRevsFacade() )->getApprovedRevID( $title ) );
	}

	/**
	 * ApprovedRevs caches the ID it was given when approving, but a job or
	 * maintenance script runs in a new process and reads it from the database.
	 * Both caches are keyed by page ID, which tests reuse, so they are also
	 * reset before each test.
	 */
	private function forgetCachedApprovedRevisionIds(): void {
		foreach ( [ 'mApprovedRevIDForPage', 'mApprovablePages' ] as $name ) {
			$cache = new \ReflectionProperty( ApprovedRevs::class, $name );
			$cache->setAccessible( true );
			$cache->setValue( null, [] );
		}
	}

	private function newTitle( string $text ): Title {
		$title = MediaWikiServices::getInstance()->getTitleFactory()->newFromText( $text )
			?? throw new \InvalidArgumentException( "Invalid title: $text" );
		$this->titles[] = $title;

		return $title;
	}

	private function createRevision( Title $title, string $text ): int {
		$this->pageCreator->createPage( $title )->doEdit( $text );
		$this->testEnvironment->executePendingDeferredUpdates();

		return $title->getLatestRevID( IDBAccessObject::READ_LATEST );
	}
}
