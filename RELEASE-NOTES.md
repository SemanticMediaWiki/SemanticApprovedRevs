These are the release notes for the [Semantic Approved Revs](https://github.com/SemanticMediaWiki/SemanticApprovedRevs) (a.k.a SAR) MediaWiki extension.

## SAR 1.0.1

Released on 2026-10-06.

### Bug Fixes

* Fixed forced updates (`UpdateJob`, `rebuildData.php -page`) storing the semantic data of the latest revision instead of the approved one (#35)
* Fixed an error when Semantic MediaWiki passes a revision that is not yet stored, e.g. in a preview
* Fixed Semantic MediaWiki ignoring the approved revision ID of a page (e.g. in `RevisionGuard::getLatestRevID()`), and pages without an approved revision getting the revision ID 0 with ApprovedRevs master (#36)

## SAR 1.0.0

Released on 2026-10-06.

### Breaking Changes

* Removed the "Approved by", "Approved date", "Approved revision" and "Approval status" properties together with their property group and translations. They are provided by Semantic Extra Special Properties (`_APPROVEDBY`, `_APPROVEDDATE`, `_APPROVED` and `_APPROVEDSTATUS` in `$sespgEnabledPropertyList`); remove the stale `__sar_*` property values by rebuilding the data after switching (#30)
* Removed the check for colliding `$sespgEnabledPropertyList` entries of Semantic Extra Special Properties < 2.1

### Compatibility Changes

* Added support for Semantic MediaWiki 7 (tested with 7.3.1) by using its BagOStuff cache and MediaWiki's logger factory instead of the removed `ServicesFactory::getCache()` and `getMediaWikiLogger()`; SMW 5 and 6 keep working
* Restored MediaWiki 1.39 as the supported minimum (with Semantic MediaWiki 5.0 or later and ApprovedRevs 2.1.2 or later)
* Replaced deprecated MediaWiki APIs (`RepoGroup::singleton()`, `HookContainer::getHandlerCallbacks()` and direct `$wgHooks` access in the hook registration helpers) (#32, #33)

### Bug Fixes

* Fixed approving a revision failing on Semantic MediaWiki 7
* Fixed `SMW_APPROVED_REVS_VERSION` always being `UNKNOWN` instead of the version from `extension.json`
* Fixed annotations being created for pages without a valid title or approved revision
* Fixed the import of the property group definition using an outdated schema format
