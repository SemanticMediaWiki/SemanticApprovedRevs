<?php

namespace SMW\ApprovedRevs;

use ApprovedRevs;
use Title;

/**
 * The original `ApprovedRevs` consist of only static methods which are not mockable
 * or useful for any serious unit testing hence we use a facade to access an
 * instance.
 *
 * @license GPL-2.0-or-later
 * @since 1.0
 *
 * @author mwjames
 */
class ApprovedRevsFacade {

	/**
	 * @since 1.0
	 *
	 * @param Title $title
	 *
	 * @return int|null
	 */
	public function getApprovedRevID( Title $title ) {
		return ApprovedRevs::getApprovedRevID( $title );
	}

	/**
	 * @since 1.0
	 *
	 * @param Title $title
	 *
	 * @return bool
	 */
	public function hasApprovedRevision( Title $title ) {
		return ApprovedRevs::hasApprovedRevision( $title );
	}

	/**
	 * @since 1.0
	 *
	 * @param Title $title
	 *
	 * @return array{string|false, string|false}
	 */
	public function getApprovedFileInfo( Title $title ) {
		return ApprovedRevs::getApprovedFileInfo( $title );
	}

	/**
	 * @since 1.0
	 *
	 * @param Title $title
	 */
	public function clearApprovedFileInfo( Title $title ) {
		ApprovedRevs::clearApprovedFileInfo( $title );
	}

}
