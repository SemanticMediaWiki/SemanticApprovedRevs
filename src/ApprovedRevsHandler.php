<?php

namespace SMW\ApprovedRevs;

use File;
use MediaWiki\MediaWikiServices;
use OldLocalFile;
use RepoGroup;
use MediaWiki\Revision\RevisionStoreRecord;
use Title;

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
	 * @param ?RevisionStoreRecord &$revision
	 */
	public function doChangeRevision( Title $title, ?RevisionStoreRecord &$revision ) {

		// Forcibly change the revision to match what ApprovedRevs sees as
		// approved
		if ( ( $approvedRevID = $this->approvedRevsFacade->getApprovedRevID( $title ) ) !== null ) {
			$approvedRev = MediaWikiServices::getInstance()
					   ->getRevisionLookup()->getRevisionById( $approvedRevID );
			if ( $approvedRev instanceof RevisionStoreRecord ) {
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

		// It has been observed that when running `runJobs.php` with `--wait`
		// the `ApprovedRevs` instance holds an outdated cache entry therefore
		// clear the static before trying to get the info
		$this->approvedRevsFacade->clearApprovedFileInfo( $title );

		list( $timestamp, $file_sha1 ) = $this->approvedRevsFacade->getApprovedFileInfo(
			$title
		);

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
