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

		if(php_sapi_name() == "cli"){
			echo "\n";
			echo "[Debug]: ".$firstTrace["file"]." (".$firstTrace["line"].")";
			print_r($value);
		}
		else{
			echo "<pre>";
			echo "<strong>Debug:".$firstTrace["file"]."(".$firstTrace["line"].")</strong><br>";
			print_r($value);
			echo "</pre>";	
		}
	}

	/**
	 * trace
	 */
	public static function trace(){

		$backTrace = debug_backtrace();

		if(php_sapi_name() == "cli"){
			echo "[Trace]\n";
		}
		else{
			echo "<pre><<strong>[Trace]</strong>\n";
		}
		foreach($backTrace as $b_){
			echo "# " . $b_["file"] . "(" . $b_["line"] . ") ". $b_["function"] ."()\n";
		}

		if(php_sapi_name() == "cli"){
			echo "\n";
		}
		else{
			echo "</pre>";
		}
	}

	/**
	 * memory
	 */
	public static function memory(){
		echo memory_get_peak_usage();
	}

	/**
	 * useMemory
	 */
	public static function useMemory(){
		echo memory_get_peak_usage() - MK3_BEGIN_MEMORY_USAGE;
	}

}