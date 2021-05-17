<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

abstract class ME_Shipping {
	public $name;
	public $order;
	public function __construct($order) {

	}

	abstract function caculate_fee();	
}

class ME_Simple_Shipping extends ME_Shipping {
	public function __construct($order) {
		$this->name = "simple";
		$this->order = $order;
	}
	public function caculate_fee() {
		return 10;
	}
}

/**
 * Retrieve a shipping class base on name
 * 
 * @param string $name The shipping method name
 * @param object $order 
 * 
 * @since 1.0
 *
 * @return ME_Shipping object
 */
function marketengine_get_shipping_class($name, $order) {
	$class_name = 'ME_' . ucfirst($name) . '_Shipping';
	if(class_exists($class_name)) {
		return new $class_name($order);
	}
	return new ME_Simple_Shipping($order);
}