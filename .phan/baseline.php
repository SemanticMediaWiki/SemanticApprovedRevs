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
	// PhanTypeMismatchArgumentNullable : 1 occurrence
	// PhanTypeMismatchArgumentNullableInternal : 1 occurrence
	// PhanTypeMismatchArgumentProbablyReal : 1 occurrence
	// PhanUndeclaredClassMethod : 1 occurrence
	// PhanUndeclaredMethod : 1 occurrence
	// PhanUndeclaredProperty : 1 occurrence
	// PhanUndeclaredTypeParameter : 1 occurrence
	// PhanUndeclaredTypeProperty : 1 occurrence

	'file_suppressions' => [
		'src/ApprovedRevsHandler.php' => [
			'PhanUndeclaredProperty' => ['\\SMW\\ApprovedRevs\\ApprovedRevsHandler::doChangeFile']
		],
		'src/Hooks.php' => [
			'PhanTypeMismatchArgumentNullableInternal' => ['\\SMW\\ApprovedRevs\\Hooks::onExtensionFunction'],
			'PhanUndeclaredClassMethod' => ['\\SMW\\ApprovedRevs\\Hooks::saveToCache'],
			'PhanUndeclaredMethod' => ['\\SMW\\ApprovedRevs\\Hooks::saveToCache'],
			'PhanUndeclaredTypeParameter' => ['\\SMW\\ApprovedRevs\\Hooks::setCache'],
			'PhanUndeclaredTypeProperty' => ['\\SMW\\ApprovedRevs\\Hooks']
		],
		'src/PropertyAnnotator.php' => [
			'PhanTypeMismatchArgumentProbablyReal' => ['\\SMW\\ApprovedRevs\\PropertyAnnotator::addAnnotation']
		],
		'src/PropertyAnnotators/ApprovedRevPropertyAnnotator.php' => [
			'PhanTypeMismatchArgumentNullable' => ['\\SMW\\ApprovedRevs\\PropertyAnnotators\\ApprovedRevPropertyAnnotator::addAnnotation']
		],
	],
	// 'directory_suppressions' => ['src/directory_name' => ['PhanIssueName1', 'PhanIssueName2']] can be manually added if needed.
	// (directory_suppressions will currently be ignored by subsequent calls to --save-baseline, but may be preserved in future Phan releases)
];
