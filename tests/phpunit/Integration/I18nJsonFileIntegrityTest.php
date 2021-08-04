<?php

namespace SMW\ApprovedRevs\Tests\Integration;

use SMW\Tests\Utils\UtilityFactory;

/**
 * @group semantic-approved-revs
 * @group medium
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class I18nJsonFileIntegrityTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider i18nFileProvider
	 */
	public function testI18NJsonDecodeEncode( $file ) {

		$jsonFileReader = UtilityFactory::getInstance()->newJsonFileReader( $file );

		$this->assertIsInt(
			$jsonFileReader->getModificationTime()
		);

		$this->assertIsArray(
			$jsonFileReader->read()
		);
	}

	public function i18nFileProvider() {

		$provider = [];
		$location = $GLOBALS['wgMessagesDirs']['SemanticApprovedRevs'];

		if ( !is_array( $location ) ) {
			$location = [ $location ];
		}
		foreach ( $location as $oneDir ) {
			$bulkFileProvider = UtilityFactory::getInstance()->newBulkFileProvider( $oneDir );
			$bulkFileProvider->searchByFileExtension( 'json' );

			foreach ( $bulkFileProvider->getFiles() as $file ) {
				$provider[] = [ $file ];
			}
		}
		return $provider;
	}

}
