<?php

namespace SMW\ApprovedRevs;

use File;
use MediaWiki\Logger\LoggerFactory;
use MediaWiki\MediaWikiServices;
use MediaWiki\Parser\Parser;
use MediaWiki\Parser\ParserOutput;
use MediaWiki\Revision\RevisionStoreRecord;
use MediaWiki\Title\Title;
use SMW\SemanticData;
use SMW\Services\ServicesFactory as ApplicationFactory;
use SMW\Store;
use Wikimedia\ObjectCache\BagOStuff;

/**
 * @license GPL-2.0-or-later
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
	 * Onoi\Cache\Cache only exists up to SMW 6; remove it from the type when support for SMW < 7 is dropped.
	 *
	 * @var \Onoi\Cache\Cache|BagOStuff|null
	 * @suppress PhanUndeclaredTypeProperty
	 */
	private $cache;

	/**
	 * @since 1.0
	 *
	 * @param array $config
	 */
	public function __construct( $config = [] ) {
		$this->registerHandlers();
	}

	/**
	 * Onoi\Cache\Cache only exists up to SMW 6; remove it from the type when support for SMW < 7 is dropped.
	 *
	 * @since 1.0
	 *
	 * @param \Onoi\Cache\Cache|BagOStuff $cache
	 * @suppress PhanUndeclaredTypeParameter
	 */
	public function setCache( $cache ) {
		$this->cache = $cache;
	}

	/**
	 * @since  1.0
	 *
	 * @param array $var
	 *
	 * @return string|false
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
	 * @param array $credits the extension credits as registered from `extension.json`
	 */
	public static function initExtension( array $credits ) {
		$version = 'UNKNOWN';

		// See https://phabricator.wikimedia.org/T151136
		if ( isset( $credits['version'] ) ) {
			$version = $credits['version'];
		}

		define( 'SMW_APPROVED_REVS_VERSION', $version );

		/**
		 * @see https://www.semantic-mediawiki.org/wiki/Hooks#SMW::Config::BeforeCompletion
		 *
		 * @since 1.0
		 *
		 * @param array &$config
		 */
		$GLOBALS['wgHooks']['SMW::Config::BeforeCompletion'][] = static function ( &$config ) {
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
		$hookContainer = MediaWikiServices::getInstance()->getHookContainer();
		foreach ( $this->handlers as $name => $callback ) {
			$hookContainer->register( $name, $callback );
		}
	}

	/**
	 * @since  1.0
	 */
	public function deregister() {
		foreach ( array_keys( $this->handlers ) as $name ) {
			MediaWikiServices::getInstance()->getHookContainer()->clear( $name );
		}
	}

	/**
	 * @since  1.0
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public function isRegistered( $name ) {
		return MediaWikiServices::getInstance()->getHookContainer()->isRegistered( $name );
	}

	/**
	 * @since  1.0
	 *
	 * @param string $name
	 *
	 * @return array
	 */
	public function getHandlers( $name ) {
		return isset( $this->handlers[$name] ) ? [ $this->handlers[$name] ] : [];
	}

	/**
	 * @since 1.0
	 *
	 * @param Title $title
	 * @param int $latestRevID
	 *
	 * @return bool
	 */
	public function onIsApprovedRevision( $title, $latestRevID ) {
		$approvedRevsHandler = new ApprovedRevsHandler(
			new ApprovedRevsFacade()
		);

		return $approvedRevsHandler->isApprovedUpdate( $title, $latestRevID );
	}

	/**
	 * @since 1.0
	 *
	 * @param Title $title
	 * @param ?RevisionStoreRecord $record
	 *
	 * @return bool
	 */
	public function onChangeRevision( $title, ?RevisionStoreRecord $record ) {
		$approvedRevsHandler = new ApprovedRevsHandler(
			new ApprovedRevsFacade()
		);

		$approvedRevsHandler->doChangeRevision( $title, $record );

		return true;
	}

	/**
	 * @since 1.0
	 *
	 * @param Title $title
	 * @param int &$latestRevID
	 *
	 * @return bool
	 */
	public function onOverrideRevisionID( $title, &$latestRevID ) {
		$approvedRevsHandler = new ApprovedRevsHandler(
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
	 * @param \SMW\PropertyRegistry $registry
	 *
	 * @return bool
	 */
	public function onInitProperties( $registry ) {
		$propertyRegistry = new PropertyRegistry();
		$propertyRegistry->register( $registry );

		return true;
	}

	/**
	 * @see https://www.semantic-mediawiki.org/wiki/Hooks#SMW::Store::BeforeDataUpdateComplete
	 *
	 * @since 1.0
	 *
	 * @param Store $store
	 * @param SemanticData $semanticData
	 *
	 * @return bool
	 */
	public function onUpdateDataBefore( $store, $semanticData ) {
		$propertyAnnotator = new PropertyAnnotator(
			new ServicesFactory()
		);

		$propertyAnnotator->setLogger(
			LoggerFactory::getInstance( 'smw-approved-revs' )
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
	 * @param int $rev_id
	 * @param string $content
	 *
	 * @return bool
	 */
	public function onApprovedRevsRevisionApproved( $output, $title, $rev_id, $content ) {
		// 1hr
		$ttl = 60 * 60;

		// Send an event to ParserAfterTidy and allow it to pass the preliminary
		// test even in cases where the content doesn't contain any SMW related
		// annotations. It is to ensure that when an agent switches to a blank
		// version (no SMW related annotations or categories) the update is carried
		// out and the store is able to remove any remaining annotations.
		$key = smwfCacheKey( 'smw:parseraftertidy', $title->getPrefixedDBKey() );
		$this->saveToCache( $key, $rev_id, $ttl );

		return true;
	}

	/**
	 * @see ??
	 *
	 * @since 1.0
	 *
	 * @param Parser $parser
	 * @param Title $title
	 * @param int $timestamp
	 * @param string $sha1
	 *
	 * @return bool
	 */
	public function onApprovedRevsFileRevisionApproved( $parser, $title, $timestamp, $sha1 ) {
		// 1hr
		$ttl = 60 * 60;

		// @see onApprovedRevsRevisionApproved for the same reason
		$key = smwfCacheKey( 'smw:parseraftertidy', $title->getPrefixedDBKey() );
		$this->saveToCache( $key, $sha1, $ttl );

		return true;
	}

	/**
	 * @see https://www.semantic-mediawiki.org/wiki/Hooks#...
	 *
	 * @since 1.0
	 *
	 * @param Title $title
	 * @param File|false|null &$file
	 *
	 * @return bool
	 */
	public function onChangeFile( $title, &$file ) {
		$approvedRevsHandler = new ApprovedRevsHandler(
			new ApprovedRevsFacade()
		);

		$approvedRevsHandler->doChangeFile( $title, $file );

		return true;
	}

	/**
	 * SMW 7 dropped onoi/cache in favour of a MediaWiki BagOStuff; SMW 5 and 6
	 * only provide the Onoi cache. Remove the fallback when support for SMW < 7
	 * is dropped.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param int $ttl
	 * @suppress PhanUndeclaredMethod getCache() only exists up to SMW 6
	 * @suppress PhanUndeclaredClassMethod Onoi\Cache\Cache only exists up to SMW 6
	 */
	private function saveToCache( string $key, $value, int $ttl ): void {
		if ( $this->cache === null ) {
			$smw = ApplicationFactory::getInstance();
			$this->cache = method_exists( $smw, 'getObjectCache' ) ? $smw->getObjectCache() : $smw->getCache();
		}

		if ( $this->cache instanceof BagOStuff ) {
			$this->cache->set( $key, $value, $ttl );
		} else {
			$this->cache->save( $key, $value, $ttl );
		}
	}

	private function registerHandlers() {
		$this->handlers = [
			'ApprovedRevsRevisionApproved' => [ $this, 'onApprovedRevsRevisionApproved' ],
			'ApprovedRevsFileRevisionApproved' => [ $this, 'onApprovedRevsFileRevisionApproved' ],
			'SMW::RevisionGuard::IsApprovedRevision' => [ $this, 'onIsApprovedRevision' ],
			'SMW::RevisionGuard::ChangeRevision' => [ $this, 'onChangeRevision' ],
			'SMW::RevisionGuard::ChangeRevisionID' => [ $this, 'onOverrideRevisionID' ],
			'SMW::RevisionGuard::ChangeFile' => [ $this, 'onChangeFile' ],
			'SMW::Property::initProperties' => [ $this, 'onInitProperties' ],
			'SMW::Store::BeforeDataUpdateComplete' => [ $this, 'onUpdateDataBefore' ],
		];
	}

	/**
	 * @since 1.0
	 */
	public static function onExtensionFunction() {
		if ( !defined( 'SMW_VERSION' ) ) {
			if ( PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg' ) {
				die(
					"\nThe 'Semantic Approved Revs' extension requires the 'Semantic MediaWiki' extension" .
					" to be installed and enabled.\n"
				);
			} else {
				die(
					'<b>Error:</b> The <a href="https://github.com/SemanticMediaWiki/SemanticApprovedRevs/">' .
					'Semantic Approved Revs</a> extension' .
					' requires the <a href="https://www.semantic-mediawiki.org/wiki/Semantic_MediaWiki">' .
					'Semantic MediaWiki</a> extension to be installed and enabled.<br />'
				);
			}
		}

		// We expected to check for APPROVED_REVS_VERSION but the extension and
		// its `extension.json` doesn't set the constant so we have to rely on
		// active class loading (which is an anti-pattern) to check whether the
		// extension is enabled or not!
		if ( !class_exists( 'ApprovedRevs' ) ) {
			if ( PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg' ) {
				die(
					"\nThe 'Semantic Approved Revs' extension requires the 'Approved Revs' extension" .
					" to be installed and enabled.\n"
				);
			} else {
				die(
					'<b>Error:</b> The <a href="https://github.com/SemanticMediaWiki/SemanticApprovedRevs/">' .
					'Semantic Approved Revs</a> extension' .
					' requires the <a href="https://www.mediawiki.org/wiki/Extension:Approved_Revs">' .
					'Approved Revs</a> extension to be installed and enabled.<br />'
				);
			}
		}

		if ( defined( 'SESP_VERSION' ) && version_compare( (string)SESP_VERSION, '2.1.0', '<' ) ) {
			$prop = self::hasPropertyCollisions( $GLOBALS );

			if ( $prop !== false ) {
				die(
					"\nPlease remove the `$prop` property (defined by the SemanticExtraSpecialProperties extension)" .
					" and switch to the new SESP version 2.1" .
					" to avoid collision with the 'Semantic Approved Revs' list of properties.\n"
				);
			}
		}

		$hooks = new Hooks();
		$hooks->register();
	}

}
