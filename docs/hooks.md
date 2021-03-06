# Hooks

- [Actions](#actions)
- [Filters](#filters)

## Actions

### `pronamic_pay_adyen_checkout_head`

*Prints scripts or data in the head tag on the Adyen checkout page.*

This action can be used, for example, to register and print a custom style.

See the following link for an example:
https://github.com/wp-pay-gateways/adyen#pronamic_pay_adyen_checkout_head


**Changelog**

Version | Description
------- | -----------
`1.1` | 

Source: [./views/head.php](../views/head.php), [line 8](../views/head.php#L8-L20)

### `pronamic_pay_webhook_log_payment`

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$payment` |  | 

Source: [./src/NotificationsController.php](../src/NotificationsController.php), [line 157](../src/NotificationsController.php#L157-L157)

## Filters

### `pronamic_pay_adyen_checkout_configuration`

*Filters the Adyen checkout configuration.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$configuration` | `object` | Adyen checkout configuration.

**Changelog**

Version | Description
------- | -----------
`1.2.0` | 

Source: [./src/DropInGateway.php](../src/DropInGateway.php), [line 272](../src/DropInGateway.php#L272-L278)

### `pronamic_pay_adyen_payment_metadata`

*Filters the Adyen checkout configuration.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$metadata` | `array` | Payment request metadata.
`$payment` |  | 

**Changelog**

Version | Description
------- | -----------
`1.1.1` | 

Source: [./src/PaymentRequestHelper.php](../src/PaymentRequestHelper.php), [line 134](../src/PaymentRequestHelper.php#L134-L140)

### `pronamic_pay_adyen_config_object`

*Filters the Adyen config object.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$config_object` | `object` | Adyen config object.

**Changelog**

Version | Description
------- | -----------
`1.1` | 

Source: [./src/WebSdkGateway.php](../src/WebSdkGateway.php), [line 257](../src/WebSdkGateway.php#L257-L269)


<p align="center"><a href="https://github.com/pronamic/wp-documentor"><img src="https://cdn.jsdelivr.net/gh/pronamic/wp-documentor@main/logos/pronamic-wp-documentor.svgo-min.svg" alt="Pronamic WordPress Documentor" width="32" height="32"></a><br><em>Generated by <a href="https://github.com/pronamic/wp-documentor">Pronamic WordPress Documentor</a> <code>1.1.1</code></em><p>

