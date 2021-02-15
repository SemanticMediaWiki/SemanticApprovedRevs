<?php

namespace SMW\ApprovedRevs;

use ArrayIterator;
use DatabaseLogEntry;
use MWTimestamp;
use Title;
use User;
use Wikimedia\Rdbms\IDatabase;

class DatabaseLogReader {

	/**
	 * @var array
	 */
	private static $titleCache = [];

	/**
	 * @var DatabaseBase
	 */
	private $dbr;

	/**
	 * @var string
	 */
	private $query;

	/**
	 * @var string
	 */
	private $log;

	/**
	 * @var string
	 */
	private $dbKey;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @since 1.0
	 *
	 * @param DatabaseaBase $dbr injected connection
	 * @param Title|null $title
	 * @param string $type of log (default: approval)
	 */
	public function __construct( IDatabase $dbr, Title $title = null , $type = 'approval' ) {
		$this->dbr = $dbr;
		$this->dbKey = $title instanceof Title ? $title->getDBkey() : null;
		$this->type = $type;
	}

	/**
	 * @since 1.0
	 */
	public function clearCache() {
		self::$titleCache = [];
	}

	/**
	 * Fetch the query parameters for later calls
	 *
	 * @since 1.0
	 *
	 * @return array of parameters for SELECT call
	 */
	public function getQuery() {
		return $this->query;
	}

	/**
	 * @since 1.0
	 *
	 * @param Title|null $title
	 * @param string $type
	 *
	 * @return User
	 */
	public function getUserForLogEntry( Title $title = null, $type = 'approval' ) {

		$this->init( $title, $type );
		$logLine = $this->getLog()->current();

		if ( $logLine && $logLine->user_id ) {
			return User::newFromID( $logLine->user_id );
		}
	}

	/**
	 * @since 1.0
	 *
	 * @param Title|null $title
	 * @param string $type
	 *
	 * @return Timestamp
	 */
	public function getDateOfLogEntry( Title $title = null, $type = 'approval' ) {

		$this->init( $title, $type );
		$logLine = $this->getLog()->current();

		if ( $logLine && $logLine->log_timestamp ) {
			return new MWTimestamp( $logLine->log_timestamp );
		}
	}

	/**
	 * @since 1.0
	 *
	 * @param Title|null $title
	 * @param string $type
	 *
	 * @return string
	 */
	public function getStatusOfLogEntry( Title $title = null, $type = 'approval' ) {

		$this->init( $title, $type );
		$logLine = $this->getLog()->current();

		if ( $logLine && $logLine->log_action ) {
			return $logLine->log_action;
		}
	}

	/**
	 * Take care of loading from the cache or filling the query.
	 */
	private function init( $title, $type ) {

		$this->dbKey = $title instanceof Title ? $title->getDBkey() : null;
		$this->type = $type;

		if ( $this->query ) {
			return;
		}

		if ( !isset( self::$titleCache[ $this->dbKey . '#' . $this->type ] ) ) {
			$this->query = DatabaseLogEntry::getSelectQueryData();

			$this->query['conds'] = [
				'log_type' => $this->type,
				'log_title' => $this->dbKey
			];
			$this->query['options'] = [ 'ORDER BY' => 'log_timestamp desc' ];
			self::$titleCache[ $this->dbKey ] = $this;
		} else {
			$cache = self::$titleCache[ $this->dbKey . '#' . $this->type ];
			$this->query = $cache->getQuery();
			$this->log = $cache->getLog();
		}

	}

	/**
	 * Fetch the results using our conditions
	 *
	 * @return IResultWrapper
	 * @throws DBError
	 */
	private function getLog() {

		if ( $this->log !== null ) {
			return $this->log;
		}

		$query = $this->getQuery();

		$this->log = $this->dbr->select(
			$query['tables'],
			$query['fields'],
			$query['conds'],
			__METHOD__,
			$query['options'],
			$query['join_conds']
		);

		if ( $this->log === null ) {
			$this->log = new ArrayIterator(
				[ (object)[
					'user_id' => null,
					'log_timestamp' => null,
					'log_action' => null
				] ]
			);
		}

		return $this->log;
	}

}
