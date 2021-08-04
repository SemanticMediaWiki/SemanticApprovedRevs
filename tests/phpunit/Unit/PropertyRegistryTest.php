<?php

namespace SMW\ApprovedRevs\Tests;

use SMW\ApprovedRevs\PropertyRegistry;

/**
 * @covers \SMW\ApprovedRevs\PropertyRegistry
 * @group semantic-approved-revs
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class PropertyRegistryTest extends \PHPUnit_Framework_TestCase {

	private $propertyRegistry;

	protected function setUp(): void {

		$this->propertyRegistry = $this->getMockBuilder( '\SMW\PropertyRegistry' )
			->disableOriginalConstructor()
			->getMock();
	}

	public function testCanConstruct() {

		$this->assertInstanceOf(
			PropertyRegistry::class,
			new PropertyRegistry()
		);
	}

	public function testRegister( ) {

		$this->propertyRegistry->expects( $this->exactly( 4 ) )
			->method( 'registerProperty' )
			->withConsecutive(
				[ $this->equalTo('__sar_approved_rev') ],
				[ $this->equalTo('__sar_approved_by') ],
				[ $this->equalTo('__sar_approved_date') ],
				[ $this->equalTo('__sar_approved_status') ] );

		$this->propertyRegistry->expects( $this->exactly( 4 ) )
			->method( 'registerPropertyAlias' );

		$this->propertyRegistry->expects( $this->exactly( 4 ) )
			->method( 'registerPropertyAliasByMsgKey' );

		$this->propertyRegistry->expects( $this->exactly( 4 ) )
			->method( 'registerPropertyDescriptionMsgKeyById' );

		$instance = new PropertyRegistry();

		$instance->register( $this->propertyRegistry );
	}

}
