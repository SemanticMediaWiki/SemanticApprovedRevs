<?php

namespace SMW\ApprovedRevs;

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class PropertyRegistry {

	const SAR_PROP_APPROVED_REV = '__sar_approved_rev';
	const SAR_PROP_APPROVED_BY = '__sar_approved_by';
	const SAR_PROP_APPROVED_DATE = '__sar_approved_date';
	const SAR_PROP_APPROVED_STATUS = '__sar_approved_status';

	/**
	 * @since 1.0
	 *
	 * @param PropertyRegistry $propertyRegistry
	 */
	public function register( $propertyRegistry ) {

		$defs = [
			self::SAR_PROP_APPROVED_REV => [
				'label' => 'Approved revision',
				'type'  => '_num',
				'alias' => 'semantic-approvedrevs-property-approved-rev',
				'desc' => 'semantic-approvedrevs-property-approved-rev-desc',
				'visbility' => false
			],
			self::SAR_PROP_APPROVED_BY => [
				'label' => 'Approved by',
				'type'  => '_wpg',
				'alias' => 'semantic-approvedrevs-property-approved-by',
				'desc' => 'semantic-approvedrevs-property-approved-by-desc',
				'visbility' => false
			],
			self::SAR_PROP_APPROVED_DATE => [
				'label' => 'Approved date',
				'type'  => '_dat',
				'alias' => 'semantic-approvedrevs-property-approved-date',
				'desc' => 'semantic-approvedrevs-property-approved-date-desc',
				'visbility' => false
			],
			self::SAR_PROP_APPROVED_STATUS => [
				'label' => 'Approval status',
				'type'  => '_txt',
				'alias' => 'semantic-approvedrevs-property-approved-status',
				'desc' => 'semantic-approvedrevs-property-approved-status-desc',
				'visbility' => false
			]
		];

		foreach ( $defs as $key => $definition ) {

			$propertyRegistry->registerProperty(
				$key,
				$definition['type'],
				$definition['label'],
				$definition['visbility']
			);

			$propertyRegistry->registerPropertyAlias(
				$key,
				wfMessage( $definition['alias'] )->text()
			);

			$propertyRegistry->registerPropertyAliasByMsgKey(
				$key,
				$definition['alias']
			);

			$propertyRegistry->registerPropertyDescriptionMsgKeyById(
				$key,
				$definition['desc']
			);
		}
	}

}
