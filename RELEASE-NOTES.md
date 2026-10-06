These are the release notes for the [Semantic Approved Revs](https://github.com/SemanticMediaWiki/SemanticApprovedRevs) (a.k.a SAR) MediaWiki extension.

## SAR 1.0.0

Released on TBD.

### Compatibility Changes

* Added support for Semantic MediaWiki 7 (tested with 7.3.1) by using its BagOStuff cache and MediaWiki's logger factory instead of the removed `ServicesFactory::getCache()` and `getMediaWikiLogger()`; SMW 5 and 6 keep working
* Restored MediaWiki 1.39 as the supported minimum (with Semantic MediaWiki 5.0 or later and ApprovedRevs 2.1.2 or later)
* Replaced deprecated MediaWiki APIs (`RepoGroup::singleton()`, `HookContainer::getHandlerCallbacks()` and direct `$wgHooks` access in the hook registration helpers)

### Bug Fixes

* Fixed the approved-by property never being set on MediaWiki 1.39
* Fixed the approved-by, approved-date, approved-status and approved-rev properties not being stored on Semantic MediaWiki 7, which removed the `SMWStore::updateDataBefore` hook
* Fixed approving a revision failing on Semantic MediaWiki 7
* Fixed the property group import (`sar.group.json`) being rejected by the Semantic MediaWiki schema validation, so the approved-by, approved-date, approved-status and approved-rev properties are grouped again
* Fixed `SMW_APPROVED_REVS_VERSION` always being `UNKNOWN` instead of the version from `extension.json`
