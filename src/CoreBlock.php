<?php
/**
 * ===================================================
 * 
 * PHP FW - Mk3 -
 * CoreBlock
 * 
 * Element class base object class.
 * 
 * URL : 
 * Copylight : Masato-Nakatsuji 2023.
 * 
 * ===================================================
 */

namespace Mk3\Core;

class CoreBlock{

	/**
	 * __consturct
	 */
	public function __construct(){

		$useClass=Config::get("config.useClass");
		foreach($useClass as $c_){
			$this->{$c_}=new ExpandClass($c_,$this);
		}
		
		if(Config::exists("config.coreBlock.useRequest")){
			// Load Reuqest Class
			$this->Request=new Request();
		}
		if(Config::exists("config.coreBlock.useResponse")){
			// Load Response Class
			$this->Response=new Response($this);
		}
		if(php_sapi_name()=="cli"){

			if(Config::exists("config.coreBlock.useCommand")){
				// Set Command Class (CLI Mode Only)
				require_once "Command.php";
				$this->Command=new Command($this);
			}
		}
	}

	/**
	 * require
	 * @param $path
	 * @return string $path
	 */
	public function require($path){

		$getData= ResponseData::get();

		foreach($getData as $field=>$value){
			$$field=$value;
		}

		require $path;
	}
}