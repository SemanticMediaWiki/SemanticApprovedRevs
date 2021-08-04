<?php

namespace SMW\ApprovedRevs\Tests;

use SMW\ApprovedRevs\Hooks;

/**
 * @covers \SMW\ApprovedRevs\Hooks
 * @group semantic-approved-revs
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class HooksTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$config =  [];

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

	public function callOnApprovedRevsRevisionApproved( $instance ) {

		$handler = 'ApprovedRevsRevisionApproved';

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$cache = $this->getMockBuilder( '\Onoi\Cache\Cache' )
			->disableOriginalConstructor()
			->getMock();

		$cache->expects( $this->once() )
			->method( 'save' )
			->with( $this->stringContains( 'smw:parseraftertidy' ) );

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

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$cache = $this->getMockBuilder( '\Onoi\Cache\Cache' )
			->disableOriginalConstructor()
			->getMock();

		$cache->expects( $this->once() )
			->method( 'save' )
			->with( $this->stringContains( 'smw:parseraftertidy' ) );

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

		$title = $this->getMockBuilder( '\Title' )
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

		$title = $this->getMockBuilder( '\Title' )
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

		$title = $this->getMockBuilder( '\Title' )
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

		$handler = 'SMWStore::updateDataBefore';

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

		$this->assertThatHookIsExcutable(
			$instance->getHandlers( $handler ),
			[ &$config ]
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

		$title = $this->getMockBuilder( '\Title' )
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
