<?php

$cfg = require __DIR__ . '/../vendor/mediawiki/mediawiki-phan-config/src/config.php';

$cfg['baseline_path'] = __DIR__ . '/baseline.php';

$IP = getenv( 'MW_INSTALL_PATH' ) !== false
	? str_replace( '\\', '/', getenv( 'MW_INSTALL_PATH' ) )
	: '../..';

// Dependency extensions are made known to Phan but are not analysed themselves
$dependencyExtensions = [
	$IP . '/extensions/SemanticMediaWiki',
	$IP . '/extensions/ApprovedRevs',
];

$cfg['directory_list'] = array_merge(
	$cfg['directory_list'],
	[ 'src' ],
	$dependencyExtensions
);

$cfg['exclude_analysis_directory_list'] = array_merge(
	$cfg['exclude_analysis_directory_list'],
	[ 'vendor/' ],
	$dependencyExtensions
);

return $cfg;
