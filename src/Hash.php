<?php
/**
 * ===================================================
 * 
 * PHP FW - Mk3 -
 * 
 * Object class for initial operation.
 * 
 * URL : 
 * Copylight : Masato-Nakatsuji 2023.
 * 
 * ===================================================
 */

namespace Mk3\Core;

class Hash{

	/**
	 * get
	 * @param array $target
	 * @param string $name
	 */
	public static function get($target, $name){

		$names=explode(".",$name);

		foreach($names as $n_){

			$target=(array)$target;
			
			if(empty($target[$n_])){
				return null;
			}

			$target=$target[$n_];
		}

		return $target;

	}

	/**
	 * exists
	 * @param array $target
	 * @param string $name
	 */
	public static function exists($target,$name){

		$check=self::get($target, $name);
		if($check){
			return true;
		}

		return false;
	}

}