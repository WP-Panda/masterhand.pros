<?php
/**
 * Всякие удобности при разработке
 */

if ( ! function_exists( 'wpp_unvar_dump' ) ) :
	/**
	 * Приведение var_dump массива к пиздатому виду
	 * @notes спиздил тут - https://stackoverflow.com/questions/3531857/convert-var-dump-of-array-back-to-array-variable
	 */
	function wpp_unvar_dump( $str ) {

		if ( strpos( $str, "\n" ) === false ) {
			//Add new lines:
			$regex = array(
				'#(\\[.*?\\]=>)#',
				'#(string\\(|int\\(|float\\(|array\\(|NULL|object\\(|})#',
			);
			$str   = preg_replace( $regex, "\n\\1", $str );
			$str   = trim( $str );
		}
		$regex      = array(
			'#^\\040*NULL\\040*$#m',
			'#^\\s*array\\((.*?)\\)\\s*{\\s*$#m',
			'#^\\s*string\\((.*?)\\)\\s*(.*?)$#m',
			'#^\\s*int\\((.*?)\\)\\s*$#m',
			'#^\\s*bool\\(true\\)\\s*$#m',
			'#^\\s*bool\\(false\\)\\s*$#m',
			'#^\\s*float\\((.*?)\\)\\s*$#m',
			'#^\\s*\[(\\d+)\\]\\s*=>\\s*$#m',
			'#\\s*?\\r?\\n\\s*#m',
		);
		$replace    = array(
			'N',
			'a:\\1:{',
			's:\\1:\\2',
			'i:\\1',
			'b:1',
			'b:0',
			'd:\\1',
			'i:\\1',
			';'
		);
		$serialized = preg_replace( $regex, $replace, $str );
		$func       = create_function(
			'$match',
			'return "s:".strlen($match[1]).":\\"".$match[1]."\\"";'
		);
		$serialized = preg_replace_callback(
			'#\\s*\\["(.*?)"\\]\\s*=>#',
			$func,
			$serialized
		);
		$func       = create_function(
			'$match',
			'return "O:".strlen($match[1]).":\\"".$match[1]."\\":".$match[2].":{";'
		);
		$serialized = preg_replace_callback(
			'#object\\((.*?)\\).*?\\((\\d+)\\)\\s*{\\s*;#',
			$func,
			$serialized
		);
		$serialized = preg_replace(
			array( '#};#', '#{;#' ),
			array( '}', '{' ),
			$serialized
		);

		return unserialize( $serialized );
	}

endif;