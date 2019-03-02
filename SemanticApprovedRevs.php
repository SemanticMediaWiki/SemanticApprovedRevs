<?php

use SMW\ApprovedRevs\Hooks;

/**
 * Extension ...
 *
 * @see https://github.com/SemanticMediaWiki/SemanticApprovedRevs
 *
 * @defgroup SemanticApprovedRevs Semantic Approved Revs
 */
SemanticApprovedRevs::load();

/**
 * @codeCoverageIgnore
 */
class SemanticApprovedRevs {

	/**
	 * @since 1.0
	 *
	 * @note It is expected that this function is loaded before LocalSettings.php
	 * to ensure that settings and global functions are available by the time
	 * the extension is activated.
	 */
	public static function load() {
		if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
			include_once __DIR__ . '/vendor/autoload.php';
		}
	}

	/**
	 * @since 1.0
	 * @see https://www.mediawiki.org/wiki/Manual:Extension.json/Schema#callback
	 */
	public static function initExtension( $credits = [] ) {
		// See https://phabricator.wikimedia.org/T151136
		define( 'SMW_APPROVED_REVS_VERSION', isset( $credits['version'] ) ? $credits['version'] : 'UNKNOWN' );

		$GLOBALS['wgMessagesDirs']['SemanticApprovedRevs'] = __DIR__ . '/i18n';
	}

	/**
	 * @since 1.0
	 */
	public static function onExtensionFunction() {

		if ( !defined( 'SMW_VERSION' ) ) {
			if ( PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg' ) {
				die( "\nThe 'Semantic Approved Revs' extension requires the 'Semantic MediaWiki' extension to be installed and enabled.\n" );
			} else {
				die(
					'<b>Error:</b> The <a href="https://github.com/SemanticMediaWiki/SemanticApprovedRevs/">Semantic Approved Revs</a> extension' .
					' requires the <a href="https://www.semantic-mediawiki.org/wiki/Semantic_MediaWiki">Semantic MediaWiki</a> extension to be installed and enabled.<br />'
				);
			}
		}

		// We expected to check for APPROVED_REVS_VERSION but the extension and
		// its `extension.json` doesn't set the constant so we have to rely on
		// active class loading (which is an anti-pattern) to check whether the
		// extension is enabled or not!
		if ( !class_exists( 'ApprovedRevs' ) ) {
			if ( PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg' ) {
				die( "\nThe 'Semantic Approved Revs' extension requires the 'Approved Revs' extension to be installed and enabled.\n" );
			} else {
				die(
					'<b>Error:</b> The <a href="https://github.com/SemanticMediaWiki/SemanticApprovedRevs/">Semantic Approved Revs</a> extension' .
					' requires the <a href="https://www.mediawiki.org/wiki/Extension:Approved_Revs">Approved Revs</a> extension to be installed and enabled.<br />'
				);
			}
		}

		if ( defined( 'SESP_VERSION' ) && version_compare( SESP_VERSION, '2.1.0', '<' ) && ( $prop = Hooks::hasPropertyDefCollisions( $GLOBALS ) ) !== false ) {
			die(
				"\nPlease remove the `$prop` property (defined by the SemanticExtraSpecialProperties extension) and switch to the new SESP version 2.1" .
				" to avoid collision with the 'Semantic Approved Revs' list of properties.\n"
			);
		}

		$hooks = new Hooks();
		$hooks->register();
	}

}
