<?php

namespace SMW\ApprovedRevs\Tests\Integration;

use ApprovedRevs;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;
use SMW\DataItems\Property;
use SMW\DataItems\WikiPage;
use SMW\Tests\SMWIntegrationTestCase;
use Wikimedia\Rdbms\IDBAccessObject;

/**
 * Annotations of a page that was just created must be stored, regardless of
 * whether a revision has been approved yet.
 *
 * @group SemanticApprovedRevs
 * @group Database
 * @group medium
 *
 * @covers \SMW\ApprovedRevs\Hooks
 * @covers \SMW\ApprovedRevs\ApprovedRevsHandler
 */
class NewPageAnnotationsTest extends SMWIntegrationTestCase {

	private const PROPERTY = 'SarNewPageProperty';

	private $pageCreator;
	private $pageDeleter;
	private array $titles = [];

	protected function setUp(): void {
		parent::setUp();

		// Re-register SMW hook handlers
		// TODO: find a way to have the test use the Wiki "as it is" without having to re-register manually
		\SemanticMediaWiki::onExtensionFunction();
		\SMW\ApprovedRevs\Hooks::onExtensionFunction();

		$utilityFactory = $this->testEnvironment->getUtilityFactory();
		$this->pageCreator = $utilityFactory->newPageCreator();
		$this->pageDeleter = $utilityFactory->newPageDeleter();

		// The annotation is only set through a template, as in the reported
		// scenario
		$this->pageCreator
			->createPage( $this->newTitle( 'Template:SarNewPageAnnotation' ) )
			->doEdit( '[[' . self::PROPERTY . '::{{{1}}}]]' );
	}

	protected function tearDown(): void {
		$this->testEnvironment->flushPages( $this->titles );

		parent::tearDown();
	}

	public function testAnnotationsOfNewPageAreStoredBeforeApproval() {
		$title = $this->createPageWithAnnotation( 'NewPageUnapproved', 'First' );

		$this->assertEmpty( ApprovedRevs::getApprovedRevID( $title ) );

		$this->assertSame(
			[ 'First' ],
			$this->getAnnotationValues( $title )
		);
	}

	public function testAnnotationsOfNewPageRemainAfterApproval() {
		$title = $this->createPageWithAnnotation( 'NewPageApproved', 'First' );

		$this->approveLatestRevision( $title );

		$this->assertSame(
			[ 'First' ],
			$this->getAnnotationValues( $title )
		);
	}

	public function testAnnotationsStayAtApprovedRevisionAfterFurtherEdit() {
		$title = $this->createPageWithAnnotation( 'NewPageEdited', 'First' );

		$this->approveLatestRevision( $title );

		$this->pageCreator
			->createPage( $title )
			->doEdit( '{{SarNewPageAnnotation|Second}}' );

		$this->testEnvironment->executePendingDeferredUpdates();

		$this->assertSame(
			[ 'First' ],
			$this->getAnnotationValues( $title )
		);
	}

	private function newTitle( string $text ): Title {
		$title = MediaWikiServices::getInstance()->getTitleFactory()->newFromText( $text );
		$this->titles[] = $title;

		return $title;
	}

	private function createPageWithAnnotation( string $name, string $value ): Title {
		$title = $this->newTitle( 'SarNewPageAnnotations/' . $name );

		$this->pageCreator
			->createPage( $title )
			->doEdit( '{{SarNewPageAnnotation|' . $value . '}}' );

		$this->testEnvironment->executePendingDeferredUpdates();

		return $title;
	}

	private function approveLatestRevision( Title $title ): void {
		ApprovedRevs::setApprovedRevID(
			$title,
			$title->getLatestRevID( IDBAccessObject::READ_LATEST ),
			$this->getTestSysop()->getUser()
		);

		$this->testEnvironment->executePendingDeferredUpdates();
	}

	/**
	 * @return string[]
	 */
	private function getAnnotationValues( Title $title ): array {
		$semanticData = $this->getStore()->getSemanticData( WikiPage::newFromTitle( $title ) );
		$values = [];

		foreach ( $semanticData->getPropertyValues( new Property( self::PROPERTY ) ) as $dataItem ) {
			$values[] = $dataItem->getDBKey();
		}

		return $values;
	}
}
