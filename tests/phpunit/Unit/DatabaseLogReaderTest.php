<?php

namespace SMW\ApprovedRevs\Tests;

use SMW\ApprovedRevs\DatabaseLogReader;
use SMW\ApprovedRevs\ServicesFactory;

/**
 * @covers \SMW\ApprovedRevs\DatabaseLogReader
 * @group semantic-approved-revs
 *
 * @license GPL-2.0-or-later
 * @since 1.0
 *
 * @author mwjames
 */
class DatabaseLogReaderTest extends \PHPUnit\Framework\TestCase {

	private $servicesFactory;
	private $connection;

	protected function setUp(): void {
		parent::setUp();

		$this->servicesFactory = new ServicesFactory();

		$this->connection = $this->getMockBuilder( '\Wikimedia\Rdbms\Database' )
			->disableOriginalConstructor()
			->getMock();
	}

	public function testCanConstruct() {
		$this->assertInstanceOf(
			DatabaseLogReader::class,
			$this->servicesFactory->newDatabaseLogReader()
		);
	}

	public function testGetQuery() {
		$log = $this->servicesFactory->newDatabaseLogReader();

		$this->assertNull(
			$log->getQuery()
		);
	}

	public function testNoLog() {
		$log = $this->servicesFactory->newDatabaseLogReader();

		$this->assertNull(
			$log->getUserForLogEntry()
		);
	}

	public function testGetNull() {
		$title = \Title::newFromText( "none" );

		$log = $this->servicesFactory->newDatabaseLogReader( $title );

		$this->assertNull(
			$log->getUserForLogEntry()
		);

		$this->assertNull(
			$log->getDateOfLogEntry()
		);

		$this->assertNull(
			$log->getStatusOfLogEntry()
		);

		$query = $log->getQuery();

		$this->assertEquals(
			[ 'tables', 'fields', 'conds', 'options', 'join_conds' ],
			array_keys( $query )
		);
	}

	public function testGetLogAndQuery() {
		$title = \Title::newFromText( __METHOD__ );

		$row = new \stdClass;
		$row->user_id = 1;
		$row->log_timestamp = 5;
		$row->log_action = 'bloop';

		$this->connection->expects( $this->any() )
			->method( 'select' )
			->willReturn( new \ArrayIterator( [ $row ] ) );

		$this->servicesFactory->setConnection(
			$this->connection
		);

		$log = $this->servicesFactory->newDatabaseLogReader();

		$this->assertEquals(
			\User::newFromID( 1 ),
			$log->getUserForLogEntry( $title )
		);

		$this->assertEquals(
			new \MWTimestamp( 5 ),
			$log->getDateOfLogEntry( $title )
		);

		$this->assertEquals(
			'bloop',
			$log->getStatusOfLogEntry( $title )
		);

		$query = $log->getQuery();

		$this->assertEquals(
			[ 'tables', 'fields', 'conds', 'options', 'join_conds' ],
			array_keys( $query )
		);
	}

	public function testCache() {
		$title = \Title::newFromText( __METHOD__ );

		$row = new \stdClass;
		$row->user_id = 1;
		$row->log_timestamp = 5;
		$row->log_action = 'bloop';

		$this->connection->expects( $this->once() )
			->method( 'select' )
			->willReturn( new \ArrayIterator( [ $row ] ) );

		$this->servicesFactory->setConnection(
			$this->connection
		);

		$log = $this->servicesFactory->newDatabaseLogReader();
		$log->clearCache();

		$this->assertEquals(
			\User::newFromID( 1 ),
			$log->getUserForLogEntry( $title )
		);

		// Second call on same title instance should be made from cache
		$this->assertEquals(
			\User::newFromID( 1 ),
			$log->getUserForLogEntry( $title )
		);
	}

}
