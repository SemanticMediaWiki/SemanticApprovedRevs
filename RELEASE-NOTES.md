These are the release notes for the [Semantic Approved Revs](https://github.com/SemanticMediaWiki/SemanticApprovedRevs) (a.k.a SAR) MediaWiki extension.

## SAR 1.0.0

Released on TBD.

### Breaking Changes

* Removed the "Approved by", "Approved date", "Approved revision" and "Approval status" properties together with their property group and translations. They are provided by Semantic Extra Special Properties (`_APPROVEDBY`, `_APPROVEDDATE`, `_APPROVED` and `_APPROVEDSTATUS` in `$sespgEnabledPropertyList`); remove the stale `__sar_*` property values by rebuilding the data after switching (#30)
* Removed the check for colliding `$sespgEnabledPropertyList` entries of Semantic Extra Special Properties < 2.1

### Compatibility Changes

* Added support for Semantic MediaWiki 7 (tested with 7.3.1) by using its BagOStuff cache and MediaWiki's logger factory instead of the removed `ServicesFactory::getCache()` and `getMediaWikiLogger()`; SMW 5 and 6 keep working
* Restored MediaWiki 1.39 as the supported minimum (with Semantic MediaWiki 5.0 or later and ApprovedRevs 2.1.2 or later)
* Replaced deprecated MediaWiki APIs (`RepoGroup::singleton()`, `HookContainer::getHandlerCallbacks()` and direct `$wgHooks` access in the hook registration helpers)

### Bug Fixes

* Fixed approving a revision failing on Semantic MediaWiki 7
* Fixed `SMW_APPROVED_REVS_VERSION` always being `UNKNOWN` instead of the version from `extension.json`
