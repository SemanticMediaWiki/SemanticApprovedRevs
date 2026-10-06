<?php

namespace SMW\ApprovedRevs;

use File;
use MediaWiki\MediaWikiServices;
use MediaWiki\Revision\RevisionStoreRecord;
use MediaWiki\Title\Title;
use OldLocalFile;
use RepoGroup;

/**
 * @license GPL-2.0-or-later
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
	public function __construct( ApprovedRevsFacade $approvedRevsFacade, ?RepoGroup $repoGroup = null ) {
		$this->approvedRevsFacade = $approvedRevsFacade;
		$this->repoGroup = $repoGroup;
	}

	/**
	 * @since  1.0
	 *
	 * @param Title $title
	 * @param int $latestRevID
	 *
	 * @return bool
	 */
	public function isApprovedUpdate( Title $title, $latestRevID ) {
		if ( !$this->approvedRevsFacade->hasApprovedRevision( $title ) ) {
			return true;
		}

		$approvedRevID = $this->approvedRevsFacade->getApprovedRevID( $title );

		if ( $approvedRevID !== null ) {
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
		$approvedRevID = $this->approvedRevsFacade->getApprovedRevID( $title );

		if ( $approvedRevID !== null ) {
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
	 * @param int|null &$revisionID
	 */
	public function doChangeRevisionID( Title $title, &$revisionID ) {
		$approvedRevID = $this->approvedRevsFacade->getApprovedRevID( $title );

		if ( $approvedRevID !== null ) {
			$revisionID = $approvedRevID;
		}
	}

	/**
	 * @since  1.0
	 *
	 * @param Title $title
	 * @param File|false|null &$file
	 *
	 * @return true|null
	 */
	public function doChangeFile( Title $title, &$file ) {
		// It has been observed that when running `runJobs.php` with `--wait`
		// the `ApprovedRevs` instance holds an outdated cache entry therefore
		// clear the static before trying to get the info
		$this->approvedRevsFacade->clearApprovedFileInfo( $title );

		[ $timestamp, $file_sha1 ] = $this->approvedRevsFacade->getApprovedFileInfo(
			$title
		);

		if ( $file_sha1 === false ) {
			return true;
		}

		if ( $this->repoGroup === null ) {
			$this->repoGroup = MediaWikiServices::getInstance()->getRepoGroup();
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
			// file_sha1 is set ad hoc here and is not a declared property of File
			// @phan-suppress-next-line PhanUndeclaredProperty
			$file->file_sha1 = $file_sha1;
		}
	}

}
