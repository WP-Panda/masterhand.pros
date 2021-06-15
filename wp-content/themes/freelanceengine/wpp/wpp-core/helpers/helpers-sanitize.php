<?php
/**
 * File Description
 *
 * @author  WP Panda
 *
 * @package auto.calk
 * @since   1.0.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wpp_only_numbers' ) ):

	/**
	 * Allow only numbers
	 * Получение только цифр
	 *
	 * @param $string
	 *
	 * @return string
	 */

	function wpp_only_numbers( $string ) {
		return preg_replace( '/\D/', '', $string );
	}

endif;

if ( ! function_exists( 'wpp_sanitize_link' ) ):

	/**
	 *
	 * Link not empty
	 * Проверка ссылки на пустоту
	 *
	 * @param null|string $link - target url
	 *
	 * @return string
	 */

	function wpp_sanitize_link( $link = null ) {

		if ( ! empty( $link ) ) {
			$link = esc_url( $link );
		}

		if ( empty( $link ) ) {
			$link = 'javascript:void(0);';
		}

		return $link;

	}

endif;

if ( ! function_exists( 'wpp_email_link' ) ) :
	/**
	 * Создание и проверка ссылки на элекктропочту
	 */
	function wpp_email_link( $mail ) {

		$error_args = [
			__( 'Email is Empty' ),
			__( 'Email is Not Valid' )
		];

		$errors = [];

		if ( empty( $mail ) ) {
			$errors[] = $error_args[0];
		}

		if ( empty( sanitize_email( $mail ) ) ) {
			$errors[] = $error_args[1];
		}

		if ( ! empty( $errors ) ) {
			$error_str = '';
			foreach ( $errors as $error ) {
				$error_str .= sprintf( '<p>%s</p>', $error );
			}

			return $error_str;
		}

		return apply_filters( 'wpp_email_link_filter', sprintf( '<a href="mailto:%1$s" title="">%1$s</a>', $mail ) );


	}

endif;

if ( ! function_exists( 'wpp_phone_link' ) ) :

	/**
	 * Генерация ссылки номера телефона
	 */
	function wpp_phone_link( $phone ) {

		if ( empty( $phone ) ) {
			return '<p>Phone is Empty</p>';
		}

		if ( empty( wpp_only_numbers( $phone ) ) ) {
			return '<p>Phone is Not Valid</p>';
		}

		return apply_filters( 'wpp_phone_link_filter', sprintf( '<a href="tel:%1$s" title=""><b>%2$s</b></a>', wpp_only_numbers( $phone ), $phone ) );


	}

endif;