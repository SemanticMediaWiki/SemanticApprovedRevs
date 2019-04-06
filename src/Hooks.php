<?php

namespace SMW\ApprovedRevs;

use SMW\ApplicationFactory;
use Onoi\Cache\Cache;

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class Hooks {

	/**
	 * @var array
	 */
	private $handlers = [];

	/**
	 * @var Cache
	 */
	private $cache;

	/**
	 * @since 1.0
	 *
	 * @param array $config
	 */
	public function __construct( $config = [] ) {
		$this->registerHandlers( $config );
	}

	/**
	 * @since 1.0
	 *
	 * @param Cache $cache
	 */
	public function setCache( Cache $cache ) {
		$this->cache = $cache;
	}

	/**
	 * @since  1.0
	 */
	public static function hasPropertyCollisions( $var ) {

		if ( !isset( $var['sespgEnabledPropertyList'] ) ) {
			return false;
		}

		// SESP properties!
		$list = [
			'_APPROVED' => true,
			'_APPROVEDBY' => true,
			'_APPROVEDDATE' => true,
			'_APPROVEDSTATUS' => true
		];

		foreach ( $var['sespgEnabledPropertyList'] as $key ) {
			if ( isset( $list[$key] ) ) {
				return $key;
			}
		}

		return false;
	}

	/**
	 * @since 1.0
	 *
	 * @param array &$vars
	 */
	public static function initExtension( &$vars ) {

		/**
		 * @see https://www.semantic-mediawiki.org/wiki/Hooks#SMW::Config::BeforeCompletion
		 *
		 * @since 1.0
		 *
		 * @param array &$config
		 */
		$vars['wgHooks']['SMW::Config::BeforeCompletion'][] = function( &$config ) {

			if ( isset( $config['smwgImportFileDirs'] ) ) {
				$config['smwgImportFileDirs'] += [ 'sar' => __DIR__ . '/../data/import' ];
			}

			return true;
		};
	}

	/**
	 * @since  1.0
	 */
	public function register() {
		foreach ( $this->handlers as $name => $callback ) {
			\Hooks::register( $name, $callback );
		}
	}

	/**
	 * @since  1.0
	 */
	public function deregister() {
		foreach ( array_keys( $this->handlers ) as $name ) {

			\Hooks::clear( $name );

			// Remove registered `wgHooks` hooks that are not cleared by the
			// previous call
			if ( isset( $GLOBALS['wgHooks'][$name] ) ) {
				unset( $GLOBALS['wgHooks'][$name] );
			}
		}
	}

	/**
	 * @since  1.0
	 *
	 * @param string $name
	 *
	 * @return boolean
	 */
	public function isRegistered( $name ) {
		return \Hooks::isRegistered( $name );
	}

	/**
	 * @since  1.0
	 *
	 * @param string $name
	 *
	 * @return array
	 */
	public function getHandlers( $name ) {
		return \Hooks::getHandlers( $name );
	}

	/**
	 * @since 1.0
	 *
	 * @param Title $title
	 * @param integer $latestRevID
	 */
	public function onIsApprovedRevision( $title, $latestRevID ) {

		$approvedRevsHandler =  new ApprovedRevsHandler(
			new ApprovedRevsFacade()
		);

		return $approvedRevsHandler->isApprovedUpdate( $title, $latestRevID );
	}

	/**
	 * @since 1.0
	 *
	 * @param Title $title
	 * @param Revision|null &$revision
	 */
	public function onChangeRevision( $title, &$revision ) {

		$approvedRevsHandler =  new ApprovedRevsHandler(
			new ApprovedRevsFacade()
		);

		$approvedRevsHandler->doChangeRevision( $title, $revision );

		return true;
	}

	/**
	 * @since 1.0
	 *
	 * @param Title $title
	 * @param integer &$latestRevID
	 */
	public function onOverrideRevisionID( $title, &$latestRevID ) {

		$approvedRevsHandler =  new ApprovedRevsHandler(
			new ApprovedRevsFacade()
		);

		$approvedRevsHandler->doChangeRevisionID( $title, $latestRevID );

		return true;
	}

	/**
	 * @see https://www.semantic-mediawiki.org/wiki/Hooks#SMW::Property::initProperties
	 *
	 * @since 1.0
	 *
	 * @param ProertyRegistry $$registry
	 * @param integer &$latestRevID
	 */
	public function onInitProperties( $registry ) {

		$propertyRegistry = new PropertyRegistry();
		$propertyRegistry->register( $registry );

		return true;
	}

	/**
	 * @see https://www.semantic-mediawiki.org/wiki/Hooks#SMWStore::updateDataBefore
	 *
	 * @since 1.0
	 *
	 * @param ProertyRegistry $$registry
	 * @param integer &$latestRevID
	 */
	public function onUpdateDataBefore( $store, $semanticData ) {

		$propertyAnnotator = new PropertyAnnotator(
			new ServicesFactory()
		);

		$propertyAnnotator->setLogger(
			ApplicationFactory::getInstance()->getMediaWikiLogger( 'smw-approved-revs' )
		);

		$propertyAnnotator->addAnnotation( $semanticData );

		return true;
	}

	/**
	 * @see ??
	 *
	 * @since 1.0
	 *
	 * @param ParserOutput $output
	 * @param Title $title
	 * @param integer $rev_id
	 * @param string $content
	 */
	public function onApprovedRevsRevisionApproved( $output, $title, $rev_id, $content  ) {

		$ttl = 60 * 60; // 1hr

		if ( $this->cache === null ) {
			$this->cache = ApplicationFactory::getInstance()->getCache();
		}

		// Send an event to ParserAfterTidy and allow it to pass the preliminary
		// test even in cases where the content doesn't contain any SMW related
		// annotations. It is to ensure that when an agent switches to a blank
		// version (no SMW related annotations or categories) the update is carried
		// out and the store is able to remove any remaining annotations.
		$key = smwfCacheKey( 'smw:parseraftertidy', $title->getPrefixedDBKey() );
		$this->cache->save( $key, $rev_id, $ttl );

		return true;
	}

	/**
	 * @see ??
	 *
	 * @since 1.0
	 *
	 * @param Parser $parser
	 * @param Title $title
	 * @param integer $timestamp
	 * @param string $sha1
	 */
	public function onApprovedRevsFileRevisionApproved( $parser, $title, $timestamp, $sha1  ) {

		$ttl = 60 * 60; // 1hr

		if ( $this->cache === null ) {
			$this->cache = ApplicationFactory::getInstance()->getCache();
		}

		// @see onApprovedRevsRevisionApproved for the same reason
		$key = smwfCacheKey( 'smw:parseraftertidy', $title->getPrefixedDBKey() );
		$this->cache->save( $key, $sha1, $ttl );

		return true;
	}

	/**
	 * @see https://www.semantic-mediawiki.org/wiki/Hooks#...
	 *
	 * @since 1.0
	 *
	 * @param Title $title
	 * @param File &$file
	 */
	public function onChangeFile( $title, &$file ) {

		$approvedRevsHandler = new ApprovedRevsHandler(
			new ApprovedRevsFacade()
		);

		$approvedRevsHandler->doChangeFile( $title, $file );

		return true;
	}

	/**
	 * @see https://www.semantic-mediawiki.org/wiki/Hooks#...
	 *
	 * @since 1.0
	 *
	 * @param Title $title
	 * @param File &$file
	 */
	public function onChangeFileBeforeIngestProcessComplete( $title, &$file ) {

		$approvedRevsHandler =  new ApprovedRevsHandler(
			new ApprovedRevsFacade()
		);

		$approvedRevsHandler->doChangeFile( $title, $file );

		return true;
	}

	private function registerHandlers( $config ) {
		$this->handlers = [
			'ApprovedRevsRevisionApproved' => [ $this, 'onApprovedRevsRevisionApproved' ],
			'ApprovedRevsFileRevisionApproved' => [ $this, 'onApprovedRevsFileRevisionApproved' ],
			'SMW::RevisionGuard::IsApprovedRevision' => [ $this, 'onIsApprovedRevision' ],
			'SMW::RevisionGuard::ChangeRevision' => [ $this, 'onChangeRevision' ],
			'SMW::RevisionGuard::ChangeRevisionID' => [ $this, 'onOverrideRevisionID' ],
			'SMW::RevisionGuard::ChangeFile' => [ $this, 'onChangeFile' ],
			'SMW::Property::initProperties' => [ $this, 'onInitProperties' ],
			'SMWStore::updateDataBefore' => [ $this, 'onUpdateDataBefore' ],
		];
	}

}
