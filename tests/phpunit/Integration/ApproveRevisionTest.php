<?php

namespace SMW\ApprovedRevs\Tests\Integration;

use ApiTestCase;

/**
 * @group SemanticApprovedRevs
 * @group API
 * @group Database
 * @group medium
 *
 * @covers \SMW\ApprovedRevs\Hooks
 */
class ApproveRevisionTest extends ApiTestCase {

	public function testApprove() {
		// NOTE: this test is known not to work when using MySql (SMW problem with temporary tables)

		// Re-register SMW hook handlers
		// TODO: find a way to have the test use the Wiki "as it is".
		// Experiments trying to undo the changes of MediaWikiIntegrationTestCase->overrideMwServices()
		// have failed.
		\SemanticMediaWiki::onExtensionFunction();
		\SMW\ApprovedRevs\Hooks::onExtensionFunction();

		$page = $this->getExistingTestPage('ApproveRevisionApiTest');
		$revId = $page->getRevisionRecord()->getId();
		$tokens = $this->getTokenList($this->getTestSysop());

		$result = $this->doApiRequest( [
			'action' => 'approve',
			'revid' => $revId,
			'token' => $tokens['edittoken']
		] );

		$this->assertEquals("Revision was successfully approved.", $result[0]["approve"]["result"]);
	}
}
