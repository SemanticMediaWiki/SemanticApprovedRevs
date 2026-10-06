<?php
/**
 * This is an automatically generated baseline for Phan issues.
 * When Phan is invoked with --load-baseline=path/to/baseline.php,
 * The pre-existing issues listed in this file won't be emitted.
 *
 * This file can be updated by invoking Phan with --save-baseline=path/to/baseline.php
 * (can be combined with --load-baseline)
 */
return [
	// # Issue statistics:
	// PhanTypeMismatchArgumentProbablyReal : 9 occurrences
	// PhanTypeMismatchArgument : 6 occurrences
	// PhanPluginDuplicateAdjacentStatement : 1 occurrence
	// PhanUndeclaredClassMethod : 1 occurrence
	// PhanUndeclaredMethod : 1 occurrence
	// PhanUndeclaredTypeParameter : 1 occurrence
	// PhanUndeclaredTypeProperty : 1 occurrence

	'file_suppressions' => [
		'src/Hooks.php' => [
			'PhanUndeclaredClassMethod' => ['\\SMW\\ApprovedRevs\\Hooks::saveToCache'],
			'PhanUndeclaredMethod' => ['\\SMW\\ApprovedRevs\\Hooks::saveToCache'],
			'PhanUndeclaredTypeParameter' => ['\\SMW\\ApprovedRevs\\Hooks::setCache'],
			'PhanUndeclaredTypeProperty' => ['\\SMW\\ApprovedRevs\\Hooks']
		],
		'tests/phpunit/Unit/ApprovedRevsHandlerTest.php' => [
			'PhanTypeMismatchArgument' => ['\\SMW\\ApprovedRevs\\Tests\\ApprovedRevsHandlerTest::testDoChangeFile_FromLocalRepo', '\\SMW\\ApprovedRevs\\Tests\\ApprovedRevsHandlerTest::testDoChangeFile_NoSha1', '\\SMW\\ApprovedRevs\\Tests\\ApprovedRevsHandlerTest::testDoChangeRevisionID'],
			'PhanTypeMismatchArgumentProbablyReal' => ['\\SMW\\ApprovedRevs\\Tests\\ApprovedRevsHandlerTest::testDoChangeFile_FromLocalRepo']
		],
		'tests/phpunit/Unit/DatabaseLogReaderTest.php' => [
			'PhanPluginDuplicateAdjacentStatement' => ['\\SMW\\ApprovedRevs\\Tests\\DatabaseLogReaderTest::testCache']
		],
		'tests/phpunit/Unit/HooksTest.php' => [
			'PhanTypeMismatchArgument' => ['\\SMW\\ApprovedRevs\\Tests\\HooksTest::callOnApprovedRevsFileRevisionApproved', '\\SMW\\ApprovedRevs\\Tests\\HooksTest::callOnApprovedRevsRevisionApproved'],
			'PhanTypeMismatchArgumentProbablyReal' => ['\\SMW\\ApprovedRevs\\Tests\\HooksTest::newCacheMock']
		],
		'tests/phpunit/Unit/PropertyAnnotators/ApprovedByPropertyAnnotatorTest.php' => [
			'PhanTypeMismatchArgumentProbablyReal' => ['\\SMW\\ApprovedRevs\\Tests\\PropertyAnnotators\\ApprovedByPropertyAnnotatorTest::testAddAnnotation']
		],
		'tests/phpunit/Unit/PropertyAnnotators/ApprovedDatePropertyAnnotatorTest.php' => [
			'PhanTypeMismatchArgument' => ['\\SMW\\ApprovedRevs\\Tests\\PropertyAnnotators\\ApprovedDatePropertyAnnotatorTest::testAddAnnotation'],
			'PhanTypeMismatchArgumentProbablyReal' => ['\\SMW\\ApprovedRevs\\Tests\\PropertyAnnotators\\ApprovedDatePropertyAnnotatorTest::testAddAnnotation']
		],
		'tests/phpunit/Unit/PropertyAnnotators/ApprovedRevPropertyAnnotatorTest.php' => [
			'PhanTypeMismatchArgumentProbablyReal' => ['\\SMW\\ApprovedRevs\\Tests\\PropertyAnnotators\\ApprovedRevPropertyAnnotatorTest::testAddAnnotation']
		],
		'tests/phpunit/Unit/PropertyAnnotators/ApprovedStatusPropertyAnnotatorTest.php' => [
			'PhanTypeMismatchArgumentProbablyReal' => ['\\SMW\\ApprovedRevs\\Tests\\PropertyAnnotators\\ApprovedStatusPropertyAnnotatorTest::testAddAnnotation']
		],
	],
	// 'directory_suppressions' => ['src/directory_name' => ['PhanIssueName1', 'PhanIssueName2']] can be manually added if needed.
	// (directory_suppressions will currently be ignored by subsequent calls to --save-baseline, but may be preserved in future Phan releases)
];
