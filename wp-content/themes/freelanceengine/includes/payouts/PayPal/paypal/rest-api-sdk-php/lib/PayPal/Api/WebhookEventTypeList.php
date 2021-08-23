<?php

namespace PayPal\Api;

use PayPal\Common\PayPalModel;

/**
 * Class WebhookEventTypeList
 *
 * List of webhook events.
 *
 * @package PayPal\Api
 *
 * @property \PayPal\Api\WebhookEventType[] event_types
 */
class WebhookEventTypeList extends PayPalModel {
	/**
	 * Append EventTypes to the list.
	 *
	 * @param \PayPal\Api\WebhookEventType $webhookEventType
	 *
	 * @return $this
	 */
	public function addEventType( $webhookEventType ) {
		if ( ! $this->getEventTypes() ) {
			return $this->setEventTypes( array( $webhookEventType ) );
		} else {
			return $this->setEventTypes(
				array_merge( $this->getEventTypes(), array( $webhookEventType ) )
			);
		}
	}

	/**
	 * A list of webhook events.
	 *
	 * @return \PayPal\Api\WebhookEventType[]
	 */
	public function getEventTypes() {
		return $this->event_types;
	}

	/**
	 * A list of webhook events.
	 *
	 * @param \PayPal\Api\WebhookEventType[] $event_types
	 *
	 * @return $this
	 */
	public function setEventTypes( $event_types ) {
		$this->event_types = $event_types;

		return $this;
	}

	/**
	 * Remove EventTypes from the list.
	 *
	 * @param \PayPal\Api\WebhookEventType $webhookEventType
	 *
	 * @return $this
	 */
	public function removeEventType( $webhookEventType ) {
		return $this->setEventTypes(
			array_diff( $this->getEventTypes(), array( $webhookEventType ) )
		);
	}

}
