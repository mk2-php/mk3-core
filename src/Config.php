<?php
/**
 * ===================================================
 * 
 * PHP FW - Mk3 -
 * 
 * Initial setting class..
 * 
 * URL : 
 * Copylight : Masato-Nakatsuji 2023.
 * 
 * ===================================================
 */

namespace Mk3\Core;

class Config{

	private static $_data=null;

	/**
	 * set
	 * @param $name = null
	 * @param $value
	 */
	public static function set($name=null,$value){

		if($name){
			self::$_data[$name]=$value;
		}
		else{
			self::$_data=$value;
		}

	}

	/**
	 * get
	 * @param $name
	 * @return $getData
	 */
	public static function get($name=null){

		if(!$name){
			return self::$_data;
		}

		$names=explode(".",$name);
		$getData=self::$_data;
		foreach($names as $ind=>$n_){

			if(!empty($getData[$n_])){
				$getData=$getData[$n_];
			}
			else if(in_array($n_,$getData)){
				return true;
			}
			else{
				return null;
			}
		}
		
		return $getData;
	}

	/**
	 * exists
	 * @param $name
	 * @return boolean true/false
	 */
	public static function exists($name){
		if(self::get($name)){
			return true;
		}
		else{
			return false;
		}
	}

	/**
	 * require
	 * @param $pathName
	 * @return require
	 */
	public static function require($pathName){
		if(file_exists(MK3_PATH_CONFIG."/".$pathName)){
			return require MK3_PATH_CONFIG."/".$pathName;
		}
	}
}