<?php

namespace SMW\ApprovedRevs;

use Title;
use Revision;
use RepoGroup;
use OldLocalFile;
use File;

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
	 * @var RepoGroup
	 */
	private $repoGroup;

	/**
	 * @since 1.0
	 *
	 * @param ApprovedRevsFacade $approvedRevsFacade
	 * @param RepoGroup|null $repoGroup
	 */
	public function __construct( ApprovedRevsFacade $approvedRevsFacade, RepoGroup $repoGroup = null ) {
		$this->approvedRevsFacade = $approvedRevsFacade;
		$this->repoGroup = $repoGroup;
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

	/**
	 * @since  1.0
	 *
	 * @param Title $title
	 * @param File &$file
	 */
	public function doChangeFile( Title $title, &$file ) {

		list( $timestamp, $file_sha1 ) = $this->approvedRevsFacade->getApprovedFileInfo( $title );

		if ( $file_sha1 === false ) {
			return true;
		}

		if ( $this->repoGroup === null ) {
			$this->repoGroup = RepoGroup::singleton();
		}

		$localRepo = $this->repoGroup->getLocalRepo();

		// Retrievalable from the archive?
		$file = OldLocalFile::newFromKey( $file_sha1, $localRepo, $timestamp );

		// Try the local repo!
		if ( $file === false ) {
			$files = $localRepo->findBySha1( $file_sha1 );
			$file = end( $files );
		}

		if ( $file instanceof File ) {
			$file->file_sha1 = $file_sha1;
		}
	}

}
