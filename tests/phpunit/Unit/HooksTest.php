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

		$this->callOnSMWLinksUpdateApprovedUpdate( $instance );
		$this->callOnSMWDataUpdaterSkipUpdate( $instance );
		$this->callOnSMWParserChangeRevision( $instance );
		$this->callOnSMWFactboxOverrideRevisionID( $instance );
		$this->callOnSMWInitProperties( $instance );
		$this->callOnSMWStoreUpdateDataBefore( $instance );
	}

	public function callOnSMWLinksUpdateApprovedUpdate( $instance ) {

		$handler = 'SMW::LinksUpdate::ApprovedUpdate';

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

	public function callOnSMWDataUpdaterSkipUpdate( $instance ) {

		$handler = 'SMW::DataUpdater::SkipUpdate';

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

	public function callOnSMWParserChangeRevision( $instance ) {

		$handler = 'SMW::Parser::ChangeRevision';

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

	public function callOnSMWFactboxOverrideRevisionID( $instance ) {

		$handler = 'SMW::Factbox::OverrideRevisionID';

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

	private function assertThatHookIsExcutable( $hooks, $arguments ) {

		if ( is_callable( $hooks ) ) {
			$hooks = [ $hooks ];
		}

		foreach ( $hooks as $hook ) {

			$this->assertInternalType(
				'boolean',
				call_user_func_array( $hook, $arguments )
			);
		}
	}

}
