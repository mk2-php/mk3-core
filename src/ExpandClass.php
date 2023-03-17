<?php
/**
 * ===================================================
 * 
 * PHP FW - Mk3 -
 * ExpandClass
 * 
 * Element class Management object class,
 * 
 * URL : 
 * Copylight : Masato-Nakatsuji 2023.
 * 
 * ===================================================
 */

namespace Mk3\Core;

use Exception;

class ExpandClass{

	private const CLASSTYPE_PACK = "pack";
	private const CLASSTYPE_MIDDLEWARE = "Middleware";

    private $_classType;
	private $_context;
	private $extendNamespace;
    
	/**
	 * __construct
	 * @param $classType
	 * @param &$context
	 */
    public function __construct($classType,&$context){
        $this->_classType=$classType;
		$this->_context=$context;
		
		if($classType==self::CLASSTYPE_PACK){
			$this->extendNamespace = "mk3\pack_{className}\\";
		}
    	else if($classType==self::CLASSTYPE_MIDDLEWARE){
			$this->extendNamespace = "mk3\middleware_{className}\\";
		}
    }

	/**
	 * getFullClassName
	 * @param $className
	 */
	public function getFullClassName($className, $onError = false, $moduleOnParentFlg = false){

		$params= RequestRouting::$_params;

		if($moduleOnParentFlg){
			$classPath = ucfirst(MK3_DEFNS . "\\". $this->_classType ."\\" .ucfirst($className) . $this->_classType);
		}
		else{
			$classPath = $params["paths"]["namespace"] . "\\". $this->_classType ."\\" . ucfirst($className) . $this->_classType;
		}

		$classPath = str_replace("/", "\\" ,$classPath);

		if(!class_exists($classPath)){
			
			if($onError){
				throw New Exception("Not Found ". $this->_classType ." Class \"" . $classPath. "\".");
			}

			return;
		}

		return $classPath;
	}

	/**
	 * exists
	 * @params $className 
	 * @param $moduleOnParentFlg = false
	 */
	public function exists($className, $moduleOnParentFlg = false){

		$classPath = $this->getFullClassName($className);
		return false;
	}

	/**
	 * get
	 * @param $className
	 * @param $option = null
	 */
	public function get($className, $option = null){

		$fullClassName = $this->getFullClassName($className, true);
		return $this->_get($fullClassName, $option);
	}

	/**
	 * getParent
	 * @param $className
	 * @param $option = null
	 */
	public function getParent($className, $option = null){

		$fullClassName = $this->getFullClassName($className, true, true);
		return $this->_get($fullClassName, $option);
	}

	/**
	 * _get
	 * @param $fulClassName
	 * @param $option
	 */
	private function _get($fullClassName, $option){

		$classObject = new $fullClassName();

		$classObject->__parent = $this->_context;

		if($option){
			foreach($option as $field=>$value){
				$classObject->{$field}=$value;
			}
		}

		if(\method_exists($classObject,"handleBefore")){
			$classObject->handleBefore();
		}

		return $classObject;
	}

	/**
	 * my
	 */
	public function my(){

		if(class_exists("Mk3\Core\\".$this->_classType)){
			$namespace="Mk3\Core";
		}
		else{
			$namespace="\\".MK3_DEFNS."\\".$this->_classType;
		}

		$className=$namespace."\\".$this->_classType;

		$classObject=new $className();

		$classObject->__parent=$this->_context;

		if(\method_exists($classObject,"handleBefore")){
			$classObject->handleBefore();
		}

		return $classObject;
	}
}