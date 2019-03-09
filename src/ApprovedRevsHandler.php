<?php

namespace SMW\ApprovedRevs;

use Title;
use Revision;

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class ApprovedRevsHandler {

	/**
	 * @var ApprovedRevsFacade
	 */
	private $approvedRevsFacade;

	/**
	 * @since 1.0
	 *
	 * @param ApprovedRevsFacade $approvedRevsFacade
	 */
	public function __construct( ApprovedRevsFacade $approvedRevsFacade ) {
		$this->approvedRevsFacade = $approvedRevsFacade;
	}

	/**
	 * @since  1.0
	 *
	 * @param Title $title
	 * @param integer $latestRevID
	 *
	 * @return boolean
	 */
	public function isApprovedUpdate( Title $title, $latestRevID ) {

		if ( !$this->approvedRevsFacade->hasApprovedRevision( $title ) ) {
			return true;
		}

		if ( ( $approvedRevID = $this->approvedRevsFacade->getApprovedRevID( $title ) ) !== null ) {
			return $approvedRevID == $latestRevID;
		}

		return true;
	}

	/**
	 * @since  1.0
	 *
	 * @param Title $title
	 * @param Revision|null &$revision
	 */
	public function doChangeRevision( Title $title, &$revision ) {

		// Forcibly change the revision to match what ApprovedRevs sees as
		// approved
		if ( ( $approvedRevID = $this->approvedRevsFacade->getApprovedRevID( $title ) ) !== null ) {
			$approvedRev = Revision::newFromId( $approvedRevID );

			if ( $approvedRev instanceof Revision ) {
				$revision = $approvedRev;
			}
		}
	}

	/**
	 * @since  1.0
	 *
	 * @param Title $title
	 * @param integer &$revisionID
	 */
	public function doChangeRevisionID( Title $title, &$revisionID ) {

		if ( ( $approvedRevID = $this->approvedRevsFacade->getApprovedRevID( $title ) ) !== null ) {
			$revisionID = $approvedRevID;
		}
	}

}
