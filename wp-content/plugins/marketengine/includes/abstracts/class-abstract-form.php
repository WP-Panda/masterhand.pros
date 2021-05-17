<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Abstract Form Class
 *
 * MarketEngine Form Class extend to control form
 * @class       AE_Form
 * @version     1.0
 * @package     marketengine/abstracts
 * @author      Dakachi
 * @category    Abstract Class
 */
abstract class ME_Form {
	/**
	 * form name
	 * @var string
	*/
	public $name;
	
	/**
	 * form fields
	 * @var array
	*/
	public $fields;

	/**
	 * form data validate rules
	 * @var array
	*/
	public $rules;

	/**
	 * form template path
	 * @var string
	*/	
	public $template;
	
	/**
	 * use captcha or not
	 * @var bool
	*/

	public $captcha;

	/**
	 * Validate data 
	 *
	 * Validate data before accept and insert to database
	 *
	 * @since 1.0
	 *
	 * @param $
	 * @return void
	 */
	public function validate(){

	}

	public function render(){

	}
}