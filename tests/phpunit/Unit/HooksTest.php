<?php

namespace SMW\ApprovedRevs\Tests;

use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;
use SMW\ApprovedRevs\Hooks;
use Wikimedia\ObjectCache\BagOStuff;

/**
 * @covers \SMW\ApprovedRevs\Hooks
 * @group semantic-approved-revs
 *
 * @license GPL-2.0-or-later
 * @since 1.0
 *
 * @author mwjames
 */
class HooksTest extends \PHPUnit\Framework\TestCase {

	public function testCanConstruct() {
		$config = [];

		$this->assertInstanceOf(
			Hooks::class,
			new Hooks()
		);
	}

	public function testRegister() {
		$instance = new Hooks();
		$instance->deregister();
		$instance->register();

		$this->callOnApprovedRevsRevisionApproved( $instance );
		$this->callOnApprovedRevsFileRevisionApproved( $instance );

		$this->callOnSMWRevisionGuardIsApprovedRevision( $instance );
		$this->callOnSMWRevisionGuardChangeRevision( $instance );
		$this->callOnSMWRevisionGuardChangeRevisionID( $instance );
		$this->callOnSMWInitProperties( $instance );
		$this->callOnSMWStoreUpdateDataBefore( $instance );
		$this->callOnSMWConfigBeforeCompletion( $instance );
		$this->callOnSMWRevisionGuardChangeFile( $instance );
	}

	/**
	 * SMW 7 replaced the Onoi cache with a MediaWiki BagOStuff.
	 *
	 * @return \PHPUnit\Framework\MockObject\MockObject&BagOStuff
	 */
	private function newCacheMock() {
		$isOnoi = interface_exists( '\Onoi\Cache\Cache' );

		$cache = $this->getMockBuilder( $isOnoi ? '\Onoi\Cache\Cache' : BagOStuff::class )
			->disableOriginalConstructor()
			->getMock();

		$cache->expects( $this->once() )
			->method( $isOnoi ? 'save' : 'set' )
			// @phan-suppress-next-line PhanTypeMismatchArgumentProbablyReal PHPUnit with() typing
			->with( $this->stringContains( 'smw:parseraftertidy' ) );

		// @phan-suppress-next-line PhanTypeMismatchReturn The mocked class depends on the SMW version
		return $cache;
	}

	public function callOnApprovedRevsRevisionApproved( $instance ) {
		$handler = 'ApprovedRevsRevisionApproved';

		$title = $this->getMockBuilder( Title::class )
			->disableOriginalConstructor()
			->getMock();

		$cache = $this->newCacheMock();

		$instance->setCache( $cache );

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$output = '';
		$rev_id = 42;
		$content = '';

		$this->assertThatHookIsExcutable(
			$instance->getHandlers( $handler ),
			[ $output, $title, $rev_id, $content ]
		);
	}

	public function callOnApprovedRevsFileRevisionApproved( $instance ) {
		$handler = 'ApprovedRevsFileRevisionApproved';

		$title = $this->getMockBuilder( Title::class )
			->disableOriginalConstructor()
			->getMock();

		$cache = $this->newCacheMock();

		$instance->setCache( $cache );

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$parser = '';
		$timestamp = 42;
		$sha1 = '1001';

		$this->assertThatHookIsExcutable(
			$instance->getHandlers( $handler ),
			[ $parser, $title, $timestamp, $sha1 ]
		);
	}

	public function callOnSMWRevisionGuardIsApprovedRevision( $instance ) {
		$handler = 'SMW::RevisionGuard::IsApprovedRevision';

		$title = $this->getMockBuilder( Title::class )
			->disableOriginalConstructor()
			->getMock();

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$rev = 0;

		$this->assertThatHookIsExcutable(
			$instance->getHandlers( $handler ),
			[ $title, $rev ]
		);
	}

	public function callOnSMWRevisionGuardChangeRevision( $instance ) {
		$handler = 'SMW::RevisionGuard::ChangeRevision';

		$title = $this->getMockBuilder( Title::class )
			->disableOriginalConstructor()
			->getMock();

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$revision = null;

		$this->assertThatHookIsExcutable(
			$instance->getHandlers( $handler ),
			[ $title, &$revision ]
		);
	}

	public function callOnSMWRevisionGuardChangeRevisionID( $instance ) {
		$handler = 'SMW::RevisionGuard::ChangeRevisionID';

		$title = $this->getMockBuilder( Title::class )
			->disableOriginalConstructor()
			->getMock();

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$latestRevID = 0;

		$this->assertThatHookIsExcutable(
			$instance->getHandlers( $handler ),
			[ $title, &$latestRevID ]
		);
	}

	public function callOnSMWInitProperties( $instance ) {
		$handler = 'SMW::Property::initProperties';

		$propertyRegistry = $this->getMockBuilder( '\SMW\PropertyRegistry' )
			->disableOriginalConstructor()
			->getMock();

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$this->assertThatHookIsExcutable(
			$instance->getHandlers( $handler ),
			[ $propertyRegistry ]
		);
	}

	public function callOnSMWStoreUpdateDataBefore( $instance ) {
		$handler = 'SMW::Store::BeforeDataUpdateComplete';

		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$semanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMock();

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$this->assertThatHookIsExcutable(
			$instance->getHandlers( $handler ),
			[ $store, $semanticData ]
		);
	}

	public function callOnSMWConfigBeforeCompletion( $instance ) {
		$handler = 'SMW::Config::BeforeCompletion';

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$config = [
			'smwgImportFileDirs' => []
		];

		// Registered via `wgHooks` in `Hooks::initExtension`, hence run it through
		// the container instead of `Hooks::getHandlers`
		$this->assertTrue(
			MediaWikiServices::getInstance()->getHookContainer()->run( $handler, [ &$config ] )
		);

		$this->assertArrayHasKey(
			'sar',
			$config['smwgImportFileDirs']
		);
	}

	public function callOnSMWRevisionGuardChangeFile( $instance ) {
		$handler = 'SMW::RevisionGuard::ChangeFile';

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$title = $this->getMockBuilder( Title::class )
			->disableOriginalConstructor()
			->getMock();

		$file = null;

		$this->assertThatHookIsExcutable(
			$instance->getHandlers( $handler ),
			[ $title, &$file ]
		);
	}

	private function assertThatHookIsExcutable( $hooks, $arguments ) {
		if ( is_callable( $hooks ) ) {
			$hooks = [ $hooks ];
		}

		foreach ( $hooks as $hook ) {

			$this->assertIsBool(
				call_user_func_array( $hook, $arguments )
			);
		}
	}

}
