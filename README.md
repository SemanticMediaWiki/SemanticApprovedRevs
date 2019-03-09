# Semantic Approved Revs
[![Build Status](https://travis-ci.org/SemanticMediaWiki/SemanticApprovedRevs.svg?branch=master)](https://travis-ci.org/SemanticMediaWiki/SemanticApprovedRevs)
[![Code Coverage](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticApprovedRevs/badges/coverage.png?s=c5563fd91abeb49b37a6ef999198530b6796dd3c)](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticApprovedRevs/)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticApprovedRevs/badges/quality-score.png?s=9cc8ce493f63f5c2c22db71b2061b4b8c21f43ba)](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticApprovedRevs/)
[![Latest Stable Version](https://poser.pugx.org/mediawiki/semantic-approved-revs/version.png)](https://packagist.org/packages/mediawiki/semantic-approved-revs)
[![Packagist download count](https://poser.pugx.org/mediawiki/semantic-approved-revs/d/total.png)](https://packagist.org/packages/mediawiki/semantic-approved-revs)

Semantic Approved Revs (a.k.a. SAR) is an extension to [Semantic MediaWiki][smw] and complements the Approved Revs extension to control the storage of approved revision content. The extension provides:

- Control of Semantic MediaWiki related updates to match those of the `Approved Revs` hereby ensuring that only data of the approved revision is used for the storage
- Provides additional properties (`Approved by`, `Approved date`, `Approved revision`, and `Approval status`) to accompany the approval process

This short [video](https://youtu.be/cl9XmzKQ2Ec) demonstrates the interaction between the Semantic MediaWiki, Semantic Approved Revs, and Approved Revs extension.

## Requirements

- PHP 7.0 or later
- MediaWiki 1.31 or later
- Semantic MediaWiki 3.1 or later
- ApprovedRevs extension 0.8 or later

## Installation

The recommended way to install Semantic Approved Revs is by using [Composer][composer].

1. Either execute `composer require mediawiki/semantic-approved-revs:~1.0` from your MediaWiki installation directory or add an entry to MediaWiki's "composer.local.json" file with:
```json
{
	"require": {
		"mediawiki/semantic-approved-revs": "~1.0"
	}
}
```
2. Afterwards run `composer update --no-dev` and edit your LocalSettings.php and add the line
```php
   wfLoadExtension( 'SemanticApprovedRevs' );
```
3. Navigate to _Special:Version_ on your wiki and verify that the extension   has been successfully installed.

## Usage

Not additional customizing is necessary.

## Contribution and support

If you have remarks, questions, or suggestions, please send them to semediawiki-users@lists.sourceforge.net. You can subscribe to this list [here](http://sourceforge.net/mailarchive/forum.php?forum_name=semediawiki-user).

If you want to contribute work to the project please subscribe to the
developers mailing list and have a look at the [contribution guildline](/CONTRIBUTING.md). A list of people who have made contributions in the past can be found [here][contributors].

* [File an issue](https://github.com/SemanticMediaWiki/SemanticApprovedRevs/issues)
* [Submit a pull request](https://github.com/SemanticMediaWiki/SemanticApprovedRevs/pulls)
* Ask a question on [the mailing list](https://semantic-mediawiki.org/wiki/Mailing_list)

### Tests

This extension provides unit and integration tests that are run by a [continues integration platform][travis]
but can also be executed using `composer test` from the extension base directory.

## License

[GNU General Public License 2.0 or later][licence]

[composer]: https://getcomposer.org/
[licence]: https://www.gnu.org/copyleft/gpl.html
[mwcomposer]: https://www.mediawiki.org/wiki/Composer
[smw]: https://www.semantic-mediawiki.org/wiki/Semantic_MediaWiki
[travis]: https://travis-ci.org/SemanticMediaWiki/SemanticApprovedRevs
[mw-testing]: https://www.mediawiki.org/wiki/Manual:PHP_unit_testing
[mw-update]: https://www.mediawiki.org/wiki/Manual:Update.php
[mw-localsettings]: https://www.mediawiki.org/wiki/Localsettings
[contributors]: https://github.com/SemanticMediaWiki/SemanticApprovedRevs/graphs/contributors
[semver]: http://semver.org/
