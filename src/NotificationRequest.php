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

use InvalidArgumentException;
use JsonSchema\Constraints\Constraint;
use JsonSchema\Exception\ValidationException;
use JsonSchema\Validator;

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
	 * @param boolean                   $live  Informs about the origin of the notification.
	 * @param NotificationRequestItem[] $items A container object for the details included in the notification.
	 */
	public function __construct( $live, $items ) {
		$this->live  = $live;
		$this->items = $items;
	}

	/**
	 * Live.
	 *
	 * @return boolean True if live, false otherwise.
	 */
	public function is_live() {
		return $this->live;
	}

	/**
	 * Get items.
	 *
	 * @return NotificationRequestItem[]
	 */
	public function get_items() {
		return $this->items;
	}

	/**
	 * Create notification request from object.
	 *
	 * @param object $object Object.
	 * @return NotificationRequest
	 * @throws ValidationException Throws JSON schema validation exception when JSON is invalid.
	 */
	public static function from_object( $object ) {
		$validator = new Validator();

		$validator->validate(
			$object,
			(object) array(
				'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/notification-request.json' ),
			),
			Constraint::CHECK_MODE_EXCEPTIONS
		);

		$items = array();

		foreach ( $object->notificationItems as $o ) {
			if ( ! isset( $o->NotificationRequestItem ) ) {
				throw new InvalidArgumentException( 'Object must contain `NotificationRequestItem` property.' );
			}

			$items[] = NotificationRequestItem::from_object( $o->NotificationRequestItem );
		}

		return new self(
			filter_var( $object->live, FILTER_VALIDATE_BOOLEAN ),
			$items
		);
	}
}
