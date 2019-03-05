<?php
/**
 * Settings
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Core\GatewaySettings;

/**
 * Settings
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class Settings extends GatewaySettings {
	/**
	 * Webhook username meta key.
	 *
	 * @var string
	 */
	const WEBHOOK_USERNAME_META_KEY = '_pronamic_gateway_adyen_webhook_username';

	/**
	 * Webhook password meta key.
	 *
	 * @var string
	 */
	const WEBHOOK_PASSWORD_META_KEY = '_pronamic_gateway_adyen_webhook_password';

	/**
	 * Constructs and initialize settings.
	 */
	public function __construct() {
		add_filter( 'pronamic_pay_gateway_sections', array( $this, 'sections' ) );
		add_filter( 'pronamic_pay_gateway_fields', array( $this, 'fields' ) );
	}

	/**
	 * Sections.
	 *
	 * @param array $sections Sections.
	 * @return array
	 */
	public function sections( array $sections ) {
		$sections['adyen'] = array(
			'title'   => __( 'Adyen', 'pronamic_ideal' ),
			'methods' => array( 'adyen' ),
		);

		// Transaction feedback.
		$sections['adyen_feedback'] = array(
			'title'       => __( 'Transaction feedback', 'pronamic_ideal' ),
			'methods'     => array( 'adyen' ),
			'description' => sprintf(
				'%s %s',
				__(
					'The URLs below need to be copied to the payment provider dashboard to receive automatic transaction status updates.',
					'pronamic_ideal'
				),
				__(
					'Set the user name and password below and in the webhook authentication settings in the Adyen dashboard for increased security (recommended).',
					'pronamic_ideal'
				)
			),
		);

		return $sections;
	}

	/**
	 * Fields.
	 *
	 * @param array $fields Fields.
	 * @return array
	 */
	public function fields( array $fields ) {
		// API Key.
		$fields[] = array(
			'filter'   => FILTER_SANITIZE_STRING,
			'section'  => 'adyen',
			'meta_key' => '_pronamic_gateway_adyen_api_key',
			'title'    => _x( 'API Key', 'adyen', 'pronamic_ideal' ),
			'type'     => 'textarea',
			'classes'  => array( 'code' ),
			'methods'  => array( 'adyen' ),
			'tooltip'  => __( 'API key as mentioned in the payment provider dashboard', 'pronamic_ideal' ),
		);

		// Live API URL prefix.
		$fields[] = array(
			'filter'   => FILTER_SANITIZE_STRING,
			'section'  => 'adyen',
			'meta_key' => '_pronamic_gateway_adyen_live_url_prefix',
			'title'    => _x( 'API Live URL Prefix', 'adyen', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'regular-text', 'code' ),
			'methods'  => array( 'adyen' ),
			'tooltip'  => __( 'The unique prefix for the live API URL, as mentioned at <strong>Account » API URLs</strong> in the Adyen dashboard', 'pronamic_ideal' ),
		);

		// Merchant Account.
		$fields[] = array(
			'filter'   => FILTER_SANITIZE_STRING,
			'section'  => 'adyen',
			'meta_key' => '_pronamic_gateway_adyen_merchant_account',
			'title'    => _x( 'Merchant Account', 'adyen', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'regular-text', 'code' ),
			'methods'  => array( 'adyen' ),
			'tooltip'  => __( 'The merchant account identifier, with which you want to process the transaction', 'pronamic_ideal' ),
		);

		// Transaction feedback.
		$fields[] = array(
			'section' => 'adyen',
			'methods' => array( 'adyen' ),
			'title'   => __( 'Transaction feedback', 'pronamic_ideal' ),
			'type'    => 'description',
			'html'    => sprintf(
				'<span class="dashicons dashicons-warning"></span> %s',
				__(
					'Receiving payment status updates needs additional configuration, if not yet completed.',
					'pronamic_ideal'
				)
			),
		);

		// Webhook URL.
		$fields[] = array(
			'section'  => 'adyen_feedback',
			'title'    => __( 'Webhook URL', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'large-text', 'code' ),
			'value'    => get_rest_url( null, NotificationsController::REST_ROUTE_NAMESPACE . '/notifications' ),
			'readonly' => true,
			'tooltip'  => sprintf(
				/* translators: %s: Adyen */
				__(
					'Copy the Webhook URL to the %s dashboard to receive automatic transaction status updates.',
					'pronamic_ideal'
				),
				__( 'Adyen', 'pronamic_ideal' )
			),
		);

		// Webhook authentication username.
		$fields[] = array(
			'filter'   => FILTER_SANITIZE_STRING,
			'section'  => 'adyen_feedback',
			'meta_key' => self::WEBHOOK_USERNAME_META_KEY,
			'title'    => _x( 'User Name', 'adyen', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'regular-text', 'code' ),
			'methods'  => array( 'adyen' ),
			'tooltip'  => __(
				'The webhook authentication user name, as mentioned at <strong>Account » Server communication</strong> in the Adyen dashboard',
				'pronamic_ideal'
			),
		);

		// Webhook authentication password.
		$fields[] = array(
			'filter'   => FILTER_SANITIZE_STRING,
			'section'  => 'adyen_feedback',
			'meta_key' => self::WEBHOOK_PASSWORD_META_KEY,
			'title'    => _x( 'Password', 'adyen', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'regular-text', 'code' ),
			'methods'  => array( 'adyen' ),
			'tooltip'  => __(
				'The webhook authentication password, as mentioned at <strong>Account » Server communication</strong> in the Adyen dashboard',
				'pronamic_ideal'
			),
		);

		return $fields;
	}

	/**
	 * Save post.
	 *
	 * @param array $data Data to save.
	 *
	 * @return array
	 */
	public function save_post( $data ) {
		// Trim settings.
		$fields = array(
			self::WEBHOOK_USERNAME_META_KEY,
			self::WEBHOOK_PASSWORD_META_KEY,
		);

		foreach ( $fields as $field ) {
			if ( ! isset( $data[ $field ] ) ) {
				continue;
			}

			$data[ $field ] = trim( $data[ $field ] );
		}

		return $data;
	}
}
