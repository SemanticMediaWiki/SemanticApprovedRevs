<?php

namespace SMW\ApprovedRevs;

use Title;
use ApprovedRevs;

/**
 * The original `ApprovedRevs` consist of only static methods which are not mockable
 * or useful for any serious unit testing hence we use a facade to access an
 * instance.
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class ApprovedRevsFacade {

	/**
	 * @since  1.0
	 *
	 * @param Title $title
	 */
	public function getApprovedRevID( Title $title ) {
		return ApprovedRevs::getApprovedRevID( $title );
	}

}
