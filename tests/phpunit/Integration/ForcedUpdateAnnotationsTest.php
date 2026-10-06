<?php

namespace SMW\ApprovedRevs\Tests\Integration;

use ApprovedRevs;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;
use SMW\DataItems\Property;
use SMW\DataItems\WikiPage;
use SMW\MediaWiki\Jobs\UpdateJob;
use SMW\Services\ServicesFactory;
use SMW\Tests\SMWIntegrationTestCase;
use Wikimedia\Rdbms\IDBAccessObject;

/**
 * When SMW re-parses a page outside of a normal edit (forced update job,
 * rebuildData.php), it must store the annotations of the approved revision,
 * not those of the latest one.
 *
 * @group SemanticApprovedRevs
 * @group Database
 * @group medium
 *
 * @covers \SMW\ApprovedRevs\Hooks
 * @covers \SMW\ApprovedRevs\ApprovedRevsHandler
 */
class ForcedUpdateAnnotationsTest extends SMWIntegrationTestCase {

	private const PROPERTY = 'SarForcedUpdateProperty';

	/** @var \SMW\Tests\Utils\PageCreator */
	private $pageCreator;

	private array $titles = [];

	protected function setUp(): void {
		parent::setUp();

		// Re-register SMW hook handlers
		// TODO: find a way to have the test use the Wiki "as it is" without having to re-register manually
		\SemanticMediaWiki::onExtensionFunction();
		\SMW\ApprovedRevs\Hooks::onExtensionFunction();

		$this->pageCreator = $this->testEnvironment->getUtilityFactory()->newPageCreator();

		$this->pageCreator
			->createPage( $this->newTitle( 'Template:SarForcedUpdateAnnotation' ) )
			->doEdit( '[[' . self::PROPERTY . '::{{{1}}}]]' );
	}

	protected function tearDown(): void {
		$this->testEnvironment->flushPages( $this->titles );

		parent::tearDown();
	}

	public function testForcedUpdateStoresAnnotationsOfApprovedRevision() {
		$title = $this->newTitle( 'SarForcedUpdate/Page' );

		$this->pageCreator
			->createPage( $title )
			->doEdit( '{{SarForcedUpdateAnnotation|Step 2}}' );
		$this->testEnvironment->executePendingDeferredUpdates();

		ApprovedRevs::setApprovedRevID(
			$title,
			$title->getLatestRevID( IDBAccessObject::READ_LATEST ),
			$this->getTestSysop()->getUser()
		);
		$this->testEnvironment->executePendingDeferredUpdates();

		// Further edit that is not approved
		$this->pageCreator
			->createPage( $title )
			->doEdit( '{{SarForcedUpdateAnnotation|Step 3}}' );
		$this->testEnvironment->executePendingDeferredUpdates();

		$this->assertSame( [ 'Step_2' ], $this->getAnnotationValues( $title ) );

		$job = ServicesFactory::getInstance()->newJobFactory()->newUpdateJob(
			$title,
			[ UpdateJob::FORCED_UPDATE => true ]
		);
		$job->run();
		$this->testEnvironment->executePendingDeferredUpdates();

		$this->assertSame( [ 'Step_2' ], $this->getAnnotationValues( $title ) );
	}

	private function newTitle( string $text ): Title {
		$title = MediaWikiServices::getInstance()->getTitleFactory()->newFromText( $text )
			?? throw new \InvalidArgumentException( "Invalid title: $text" );
		$this->titles[] = $title;

		return $title;
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
