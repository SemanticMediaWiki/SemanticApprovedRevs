<?php

namespace SMW\ApprovedRevs\Tests;

use SMW\ApprovedRevs\ServicesFactory;

/**
 * @covers \SMW\ApprovedRevs\ServicesFactory
 * @group semantic-approved-revs
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class ServicesFactoryTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$this->assertInstanceOf(
			ServicesFactory::class,
			new ServicesFactory()
		);
	}

	public function testGetConnection( ) {

		$connection = $this->getMockBuilder( '\DatabaseBase' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new ServicesFactory();

		$instance->setConnection(
			$connection
		);

		$this->assertSame(
			$connection,
			$instance->getConnection()
		);
	}

	public function testCanConstructApprovedRevsFacade( ) {

		$instance = new ServicesFactory();

		$this->assertInstanceOf(
			'\SMW\ApprovedRevs\ApprovedRevsFacade',
			$instance->newApprovedRevsFacade()
		);
	}

	public function testCanConstructDatabaseLogReader() {

		$connection = $this->getMockBuilder( '\DatabaseBase' )
			->disableOriginalConstructor()
			->getMock();

		$appFactory = new ServicesFactory();
		$appFactory->setConnection( $connection );

		$dbLogReader = $appFactory->newDatabaseLogReader( null );
		$this->assertNull( $dbLogReader->getStatusOfLogEntry() );
	}

	/**
	 * @dataProvider propertyAnnotatorsProvider
	 */
	public function testCanConstructPropertyAnnotators( $name, $instanceOf ) {

		$instance = new ServicesFactory();

		$this->assertInstanceOf(
			$instanceOf,
			call_user_func_array( [ $instance, $name ], [] )
		);
	}

	public function propertyAnnotatorsProvider() {

		yield [
			'newApprovedByPropertyAnnotator',
			'\SMW\ApprovedRevs\PropertyAnnotators\ApprovedByPropertyAnnotator'
		];

		yield [
			'newApprovedStatusPropertyAnnotator',
			'\SMW\ApprovedRevs\PropertyAnnotators\ApprovedStatusPropertyAnnotator'
		];

		yield [
			'newApprovedDatePropertyAnnotator',
			'\SMW\ApprovedRevs\PropertyAnnotators\ApprovedDatePropertyAnnotator'
		];

		yield [
			'newApprovedRevPropertyAnnotator',
			'\SMW\ApprovedRevs\PropertyAnnotators\ApprovedRevPropertyAnnotator'
		];
	}

}
