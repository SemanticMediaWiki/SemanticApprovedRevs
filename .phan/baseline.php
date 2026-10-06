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
	// PhanTypeMismatchProperty : 10+ occurrences
	// PhanNonClassMethodCall : 5 occurrences
	// PhanUndeclaredClassMethod : 5 occurrences
	// PhanUndeclaredMethod : 5 occurrences
	// PhanUndeclaredTypeParameter : 5 occurrences
	// PhanTypeMismatchReturn : 4 occurrences
	// PhanTypeMismatchPropertyProbablyReal : 3 occurrences
	// PhanTypeMismatchArgument : 2 occurrences
	// PhanTypeMismatchDimAssignment : 2 occurrences
	// PhanUndeclaredTypeProperty : 2 occurrences
	// PhanUndeclaredTypeReturnType : 2 occurrences
	// PhanImpossibleCondition : 1 occurrence
	// PhanTypeMismatchArgumentNullable : 1 occurrence
	// PhanTypeMismatchArgumentNullableInternal : 1 occurrence
	// PhanTypeMismatchArgumentProbablyReal : 1 occurrence
	// PhanTypeMismatchDeclaredParam : 1 occurrence
	// PhanUndeclaredProperty : 1 occurrence
	// PhanUndeclaredTypeThrowsType : 1 occurrence
	// PhanUnusedPrivateMethodParameter : 1 occurrence

	'file_suppressions' => [
		'src/ApprovedRevsHandler.php' => [
			'PhanUndeclaredProperty' => ['\\SMW\\ApprovedRevs\\ApprovedRevsHandler::doChangeFile']
		],
		'src/DatabaseLogReader.php' => [
			'PhanTypeMismatchDeclaredParam' => ['\\SMW\\ApprovedRevs\\DatabaseLogReader::__construct'],
			'PhanTypeMismatchDimAssignment' => ['\\SMW\\ApprovedRevs\\DatabaseLogReader::init'],
			'PhanTypeMismatchProperty' => ['\\SMW\\ApprovedRevs\\DatabaseLogReader::__construct', '\\SMW\\ApprovedRevs\\DatabaseLogReader::init'],
			'PhanTypeMismatchPropertyProbablyReal' => ['\\SMW\\ApprovedRevs\\DatabaseLogReader::getLog', '\\SMW\\ApprovedRevs\\DatabaseLogReader::init'],
			'PhanTypeMismatchReturn' => ['\\SMW\\ApprovedRevs\\DatabaseLogReader::getDateOfLogEntry', '\\SMW\\ApprovedRevs\\DatabaseLogReader::getLog', '\\SMW\\ApprovedRevs\\DatabaseLogReader::getQuery'],
			'PhanUndeclaredClassMethod' => ['\\SMW\\ApprovedRevs\\DatabaseLogReader::getDateOfLogEntry', '\\SMW\\ApprovedRevs\\DatabaseLogReader::getLog', '\\SMW\\ApprovedRevs\\DatabaseLogReader::getStatusOfLogEntry', '\\SMW\\ApprovedRevs\\DatabaseLogReader::getUserForLogEntry'],
			'PhanUndeclaredTypeProperty' => ['\\SMW\\ApprovedRevs\\DatabaseLogReader'],
			'PhanUndeclaredTypeReturnType' => ['\\SMW\\ApprovedRevs\\DatabaseLogReader::getDateOfLogEntry', '\\SMW\\ApprovedRevs\\DatabaseLogReader::getLog'],
			'PhanUndeclaredTypeThrowsType' => ['\\SMW\\ApprovedRevs\\DatabaseLogReader::getLog']
		],
		'src/Hooks.php' => [
			'PhanImpossibleCondition' => ['\\SMW\\ApprovedRevs\\Hooks::initExtension'],
			'PhanTypeMismatchArgument' => ['\\SMW\\ApprovedRevs\\Hooks::onChangeFile', '\\SMW\\ApprovedRevs\\Hooks::onInitProperties'],
			'PhanTypeMismatchArgumentNullableInternal' => ['\\SMW\\ApprovedRevs\\Hooks::onExtensionFunction'],
			'PhanUndeclaredClassMethod' => ['\\SMW\\ApprovedRevs\\Hooks::saveToCache'],
			'PhanUndeclaredMethod' => ['\\SMW\\ApprovedRevs\\Hooks::saveToCache'],
			'PhanUndeclaredTypeParameter' => ['\\SMW\\ApprovedRevs\\Hooks::onApprovedRevsFileRevisionApproved', '\\SMW\\ApprovedRevs\\Hooks::onApprovedRevsRevisionApproved', '\\SMW\\ApprovedRevs\\Hooks::onChangeFile', '\\SMW\\ApprovedRevs\\Hooks::onInitProperties', '\\SMW\\ApprovedRevs\\Hooks::setCache'],
			'PhanUndeclaredTypeProperty' => ['\\SMW\\ApprovedRevs\\Hooks'],
			'PhanUnusedPrivateMethodParameter' => ['\\SMW\\ApprovedRevs\\Hooks::registerHandlers']
		],
		'src/PropertyAnnotator.php' => [
			'PhanTypeMismatchArgumentProbablyReal' => ['\\SMW\\ApprovedRevs\\PropertyAnnotator::addAnnotation'],
			'PhanTypeMismatchProperty' => ['\\SMW\\ApprovedRevs\\PropertyAnnotator::initPropertyAnnotators']
		],
		'src/PropertyAnnotators/ApprovedByPropertyAnnotator.php' => [
			'PhanTypeMismatchProperty' => ['\\SMW\\ApprovedRevs\\PropertyAnnotators\\ApprovedByPropertyAnnotator::setApprovedBy']
		],
		'src/PropertyAnnotators/ApprovedDatePropertyAnnotator.php' => [
			'PhanNonClassMethodCall' => ['\\SMW\\ApprovedRevs\\PropertyAnnotators\\ApprovedDatePropertyAnnotator::newDITime'],
			'PhanTypeMismatchProperty' => ['\\SMW\\ApprovedRevs\\PropertyAnnotators\\ApprovedDatePropertyAnnotator::addAnnotation']
		],
		'src/PropertyAnnotators/ApprovedRevPropertyAnnotator.php' => [
			'PhanTypeMismatchArgumentNullable' => ['\\SMW\\ApprovedRevs\\PropertyAnnotators\\ApprovedRevPropertyAnnotator::addAnnotation']
		],
		'src/PropertyAnnotators/ApprovedStatusPropertyAnnotator.php' => [
			'PhanTypeMismatchProperty' => ['\\SMW\\ApprovedRevs\\PropertyAnnotators\\ApprovedStatusPropertyAnnotator::addAnnotation', '\\SMW\\ApprovedRevs\\PropertyAnnotators\\ApprovedStatusPropertyAnnotator::setApprovedStatus']
		],
		'src/PropertyRegistry.php' => [
			'PhanUndeclaredMethod' => ['\\SMW\\ApprovedRevs\\PropertyRegistry::register']
		],
		'src/ServicesFactory.php' => [
			'PhanTypeMismatchProperty' => ['\\SMW\\ApprovedRevs\\ServicesFactory::getConnection']
		],
	],
	// 'directory_suppressions' => ['src/directory_name' => ['PhanIssueName1', 'PhanIssueName2']] can be manually added if needed.
	// (directory_suppressions will currently be ignored by subsequent calls to --save-baseline, but may be preserved in future Phan releases)
];
