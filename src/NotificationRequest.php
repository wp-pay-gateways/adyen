<?php
/**
 * Notification request
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Notification request
 *
 * @link    https://docs.adyen.com/developers/api-reference/notifications-api#notificationrequest
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class NotificationRequest {
	/**
	 * Informs about the origin of the notification:
	 *
	 * - `true`: the notification originated from the live environment.
	 * - `false`: the notification originated from the test environment.
	 *
	 * @var boolean
	 */
	private $live;

	/**
	 * A container object for the details included in the notification.
	 *
	 * @var array
	 */
	private $items;

	/**
	 * Construct notification request.
	 *
	 * @param boolean $live  Informs about the origin of the notification.
	 * @param array   $items A container object for the details included in the notification.
	 */
	public function __construct( $live, $items ) {
		$this->live  = $live;
		$this->items = $items;
	}

	/**
	 * Create notification request from object.
	 *
	 * @param object $object Object.
	 * @return OrderAnnounceResponse
	 * @throws InvalidArgumentException Throws invalid argument exception when object does not contains the required properties.
	 */
	public static function from_object( $object ) {
		if ( ! isset( $object->live ) ) {
			throw new InvalidArgumentException( 'Object must contain `live` property.' );
		}

		if ( ! isset( $object->notificationItems ) ) {
			throw new InvalidArgumentException( 'Object must contain `notificationItems` property.' );
		}

		if ( ! is_array( $object->notificationItems ) ) {
			throw new InvalidArgumentException( 'Object property `notificationItems` must be an array.' );			
		}

		$items = array();

		foreach ( $object->notificationItems as $o ) {
			$items[] = NotificationRequestItem::from_object( $o );
		}

		return new self(
			filter_var( $object->redirectUrl, FILTER_VALIDATE_BOOLEAN ),
			$items
		);
	}
}
