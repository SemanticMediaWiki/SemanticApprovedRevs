<?php

namespace SMW\ApprovedRevs;

use SMW\ApprovedRevs\PropertyAnnotators\ApprovedByPropertyAnnotator;
use SMW\ApprovedRevs\PropertyAnnotators\ApprovedDatePropertyAnnotator;
use SMW\ApprovedRevs\PropertyAnnotators\ApprovedRevPropertyAnnotator;
use SMW\ApprovedRevs\PropertyAnnotators\ApprovedStatusPropertyAnnotator;
use Title;
use Wikimedia\Rdbms\Database;

/**
 * @license GPL-2.0-or-later
 * @since 1.0
 *
 * @author mwjames
 */
class ServicesFactory {

	/**
	 * @var Database
	 */
	private $connection;

	/**
	 * @since 1.0
	 */
	public function setConnection( Database $connection ) {
		$this->connection = $connection;
	}

	/**
	 * @since 1.3
	 *
	 * @return Database
	 */
	public function getConnection() {
		if ( $this->connection === null ) {
			$this->connection = wfGetDB( DB_REPLICA );
		}

		return $this->connection;
	}

	/**
	 * @since 1.0
	 *
	 * @return ApprovedRevsFacade
	 */
	public function newApprovedRevsFacade() {
		return new ApprovedRevsFacade();
	}

	/**
	 * @since 1.0
	 *
	 * @param null|Title $title to get the DBLogReader
	 * @param string $type which log entries to get (default: approval)
	 * @return DatabaseLogReader
	 */
	public function newDatabaseLogReader( ?Title $title = null, $type = 'approval' ) {
		return new DatabaseLogReader( $this->getConnection(), $title, $type );
	}

	/**
	 * @since 1.0
	 *
	 * @return ApprovedByPropertyAnnotator
	 */
	public function newApprovedByPropertyAnnotator() {
		return new ApprovedByPropertyAnnotator( $this->newDatabaseLogReader() );
	}

	/**
	 * @since 1.0
	 *
	 * @return ApprovedStatusPropertyAnnotator
	 */
	public function newApprovedStatusPropertyAnnotator() {
		return new ApprovedStatusPropertyAnnotator( $this->newDatabaseLogReader() );
	}

	/**
	 * @since 1.0
	 *
	 * @return ApprovedDatePropertyAnnotator
	 */
	public function newApprovedDatePropertyAnnotator() {
		return new ApprovedDatePropertyAnnotator( $this->newDatabaseLogReader() );
	}

	/**
	 * @since 1.0
	 *
	 * @return ApprovedRevPropertyAnnotator
	 */
	public function newApprovedRevPropertyAnnotator() {
		return new ApprovedRevPropertyAnnotator( $this->newApprovedRevsFacade() );
	}

}
