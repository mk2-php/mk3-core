<?php
/**
 * ===================================================
 * 
 * PHP FW "Reald"
 * Request
 * 
 * Object class for initial operation.
 * 
 * URL : 
 * Copylight : Masato-Nakatsuji 2023.
 * 
 * ===================================================
 */

namespace Reald\Core;

class Request{

	public const METHOD_QUERY = "GET";
	public const METHOD_POST = "POST";
	public const METHOD_PUT = "PUT";
	public const METHOD_DELETE = "DELETE";

	private static $_request;

	/**
	 * data
	 */
	public static function data(){
	
		$method = $_SERVER['REQUEST_METHOD'];

		if($method == self::METHOD_QUERY){
			return self::query();
		}
		else if($method == self::METHOD_POST){
			return self::post();
		}
		else if($method == self::METHOD_PUT){
			return self::put();
		}
		else if($method == self::METHOD_DELETE){
			return self::delete();
		}
	}

	/**
	 * params
	 * @param string $name = null
	 */
	public static function params($name=null){

		if($name){
			if(!empty(Routings::$_data[$name])){
				return Routings::$_data[$name];
			}
		}
		else{
			return Routings::$_data;
		}
	}

	/**
	 * query
	 * @param $name = null
	 */
	public static function query(){
		self::_get(self::METHOD_QUERY);
		return new RequestControl(self::METHOD_QUERY);
	}

	/**
	 * post
	 * @param $name = null
	 */
	public static function post($name = null){
		self::_get(self::METHOD_POST);
		return new RequestControl(self::METHOD_POST);
	}

	/**
	 * put
	 */
	public static function put($name = null){
		self::_get(self::METHOD_PUT);
		return new RequestControl(self::METHOD_PUT);
	}

	/**
	 * delete
	 */
	public static function delete($name = null){
		self::_get(self::METHOD_DELETE);
		return new RequestControl(self::METHOD_DELETE);
	}

	public static function getData($type, $name = null){

		if(!isset(self::$_request[$type])){
			return;
		}
		
		if($name){

			if(!isset(self::$_request[$type][$name])){
				return;
			}
	
			return self::$_request[$type][$name];
		}
		else{
			return self::$_request[$type];
		}
	}

	public static function setData($type, $name, $value){

		if(!isset(self::$_request[$type])){
			self::$_request[$type] = [];
		}

		self::$_request[$type][$name] = $value;	
	}

	public static function deleteData($type, $name){

		if($name){
			unset(self::$_request[$type][$name]);
		}
		else{
			unset(self::$_request[$type]);
		}
	}

	private static function _get($type){

		if(!isset(self::$_request[$type])){
			
			$request = null;
			$mediaType = null;
			
			if(!empty($_SERVER['CONTENT_TYPE'])){
				$content_type = explode(';',trim(strtolower($_SERVER['CONTENT_TYPE'])));
				$mediaType = $content_type[0];	
			}
	
			if (
				$_SERVER['REQUEST_METHOD'] == $type && 
				$mediaType == 'application/json'
			){
				if($type == self::METHOD_QUERY){
					$request = $_GET;
				}
				else{
					// Correspondence in case of json format.
					$request = json_decode(file_get_contents('php://input'), true);
				}
			}
			else{
				if($type==self::METHOD_QUERY){
					$request = $_GET;
				}
				else if($type == self::METHOD_POST){
					$request = $_POST;
				}
				else if($type == self::METHOD_PUT){
					$request = $_PUT;
				}
				else if($type == self::METHOD_DELETE){
					$request = $_DELETE;
				}

				self::_getOnFiles($request);
			}
	
			self::$_request[$type] = $request;	
		}
	}

	/**
	 * _getOnFiles
	 * 
	 * ???!
	 * @param Array &$post 
	 */
	private static function _getOnFiles(&$request){

		if($_FILES){

			foreach($_FILES as $field => $f_){

				$exists = true;
				$buff = [];

				foreach($f_ as $column => $ff_){
					if(is_array($ff_)){

						foreach($ff_ as $index => $fff_){
							if(empty($buff[$index])){
								$buff[$index] = [];
							}

							$buff[$index][$column] = $fff_;
						}
					}
					else{
						$buff[0] = [];
						$buff[0][$column] = $ff_;
					}
				}

				if(count($buff) == 1){
					if($buff[0]["error"] == 4){
						$buff = null;
					}
				}

				$request[$field] = $buff;
			}
		}
		
	}
}

class RequestControl{
		
	private $type;

	public function __construct($type){
		$this->type = $type;
	}

	public function __get($name){
		return Request::getData($this->type, $name);
	}

	public function __set($name, $value){
		return Request::setData($this->type, $name, $value);
	}

	public function all(){
		return Request::getData($this->type);
	}

	public function refresh($values){

		if(gettype($values) == "object"){
			$values = $values->toArray();
		}

		foreach($values as $key => $value){
			Request::setData($this->type, $key, $value);
		}
	}

	public function toArray(){
		return $this->all();
	}

	/**
	 * exists
	 * @param $name = null
	 */
	public function exists($name = null){
		return (boolean)Request::getData($this->type, $name);
	}

	/**
	 * delete
	 * @param $name = null
	 */
	public function delete($name = null){
		if($name){
			return Request::deleteData($this->type, $name);
		}
		else{
			return Request::deleteData($this->type);
		}
	}
}