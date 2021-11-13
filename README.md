# Semantic Approved Revs
[![CI](https://github.com/SemanticMediaWiki/SemanticApprovedRevs/actions/workflows/main.yaml/badge.svg)](https://github.com/SemanticMediaWiki/SemanticApprovedRevs/actions/workflows/main.yaml)
[![codecov](https://codecov.io/gh/SemanticMediaWiki/SemanticApprovedRevs/branch/master/graph/badge.svg?token=77DNOQPTNF)](https://codecov.io/gh/SemanticMediaWiki/SemanticApprovedRevs)
[![Latest Stable Version](https://poser.pugx.org/mediawiki/semantic-approved-revs/version.png)](https://packagist.org/packages/mediawiki/semantic-approved-revs)
[![Packagist download count](https://poser.pugx.org/mediawiki/semantic-approved-revs/d/total.png)](https://packagist.org/packages/mediawiki/semantic-approved-revs)

Semantic Approved Revs (a.k.a. SAR) is a [Semantic MediaWiki][smw] extension and a complement to the Approved Revs extension to help store data related to an approved revision. The extension provides:

- Control over Semantic MediaWiki related updates to only store data for an approved revision (managed by extension Approved Revs)
- Additional properties ("Approved by", "Approved date", "Approved revision" and "Approval status") to accompany the approval process

This short [video](https://youtu.be/cl9XmzKQ2Ec) demonstrates the interaction between the Semantic MediaWiki, Semantic Approved Revs, and the Approved Revs extension.


## Requirements

- PHP 7.0 or later
- MediaWiki 1.31 or later
- Semantic MediaWiki 3.1 or later
- ApprovedRevs extension 0.8 or later


## Installation

The recommended way to install  Semantic Approved Revs is using [Composer](https://getcomposer.org) with
[MediaWiki's built-in support for Composer](https://www.mediawiki.org/wiki/Composer).

Note that the required extensions Semantic MediaWiki and Scribunto must be installed first according to
the installation instructions provided.

### Step 1

Change to the base directory of your MediaWiki installation. If you do not have a "composer.local.json" file yet,
create one and add the following content to it:

```json
{
	"require": {
		"mediawiki/semantic-approved-revs": "~1.0"
	}
}
```

If you already have a "composer.local.json" file add the following line to the end of the "require"
section in your file:

    "mediawiki/semantic-approved-revs": "~1.0"

Remember to add a comma to the end of the preceding line in this section.

### Step 2

Run the following command in your shell:

    php composer.phar update --no-dev

Note if you have Git installed on your system add the `--prefer-source` flag to the above command.

### Step 3

Add the following line to the end of your "LocalSettings.php" file:

    wfLoadExtension( 'SemanticApprovedRevs' );


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
