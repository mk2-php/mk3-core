<?php
/**
 * ===================================================
 * 
 * PHP FW - Mk3 -
 * 
 * Element class base object class.
 * 
 * URL : 
 * Copylight : Masato-Nakatsuji 2023.
 * 
 * ===================================================
 */

namespace Mk3\Core;

class Debug{

	/**
	 * out
	 * @param $value
	 */
	public static function out($value){
		$trace=debug_backtrace();
		$firstTrace=$trace[0];

		echo "<pre>";
		echo "<strong>Debug:".$firstTrace["file"]."(".$firstTrace["line"].")</strong><br>";
		print_r($value);
		echo "</pre>";
	}

}