<?php

namespace SMW\ApprovedRevs;

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class Hooks {

	/**
	 * @var array
	 */
	private $handlers = [];

	/**
	 * @since 1.0
	 *
	 * @param array $config
	 */
	public function __construct( $config = [] ) {
		$this->registerHandlers( $config );
	}

	/**
	 * @since  1.0
	 */
	public function register() {
		foreach ( $this->handlers as $name => $callback ) {
			\Hooks::register( $name, $callback );
		}
	}

	/**
	 * @since  1.0
	 */
	public function deregister() {
		foreach ( array_keys( $this->handlers ) as $name ) {

			\Hooks::clear( $name );

			// Remove registered `wgHooks` hooks that are not cleared by the
			// previous call
			if ( isset( $GLOBALS['wgHooks'][$name] ) ) {
				unset( $GLOBALS['wgHooks'][$name] );
			}
		}
	}

	/**
	 * @since  1.0
	 *
	 * @param string $name
	 *
	 * @return boolean
	 */
	public function isRegistered( $name ) {
		return \Hooks::isRegistered( $name );
	}

	/**
	 * @since  1.0
	 *
	 * @param string $name
	 *
	 * @return array
	 */
	public function getHandlers( $name ) {
		return \Hooks::getHandlers( $name );
	}

	/**
	 * @since 1.0
	 *
	 * @param Title $title
	 * @param integer $latestRevID
	 */
	public function onApprovedUpdate( $title, $latestRevID ) {

		$approvedRevsHandler =  new ApprovedRevsHandler(
			new ApprovedRevsFacade()
		);

		return $approvedRevsHandler->isApprovedUpdate( $title, $latestRevID );
	}

	/**
	 * @since 1.0
	 *
	 * @param Title $title
	 * @param Revision|null &$revision
	 */
	public function onChangeRevision( $title, &$revision ) {

		$approvedRevsHandler =  new ApprovedRevsHandler(
			new ApprovedRevsFacade()
		);

		$approvedRevsHandler->doChangeRevision( $title, $revision );

		return true;
	}

	/**
	 * @since 1.0
	 *
	 * @param Title $title
	 * @param integer &$latestRevID
	 */
	public function onOverrideRevisionID( $title, &$latestRevID ) {

		$approvedRevsHandler =  new ApprovedRevsHandler(
			new ApprovedRevsFacade()
		);

		$approvedRevsHandler->doChangeRevisionID( $title, $latestRevID );

		return true;
	}

	private function registerHandlers( $config ) {

		$this->handlers = [
			'SMW::LinksUpdate::ApprovedUpdate' => [ $this, 'onApprovedUpdate' ],
			'SMW::Parser::ChangeRevision' => [ $this, 'onChangeRevision' ],
			'SMW::Factbox::OverrideRevisionID' => [ $this, 'onOverrideRevisionID' ]
		];
	}

}
