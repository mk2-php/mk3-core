<?php
/**
 * ===================================================
 * 
 * PHP FW - Mk3 -
 * ResponseData
 * 
 * Object class for initial operation.
 * 
 * URL : 
 * Copylight : Masato-Nakatsuji 2023.
 * 
 * ===================================================
 */

namespace Mk3\Core;

use Exception;

class ResponseData{

	private static $_data=[];

	/**
	 * get
	 * @param $name = null
	 */
	public static function get($name=null){

		if($name){
			if(!empty(self::$_data[$name])){
				return self::$data[$name];
			}
		}
		else{
			return self::$_data;
		}

	}

	/**
	 * set
	 * @param $name
	 * @param $value
	 */
	public static function set($name,$value){
		self::$_data[$name]=$value;
	}

}

class Response{

	private const TEMPLATEENGINE_SMARTY = "smarty";
	private const TEMPLATEENGINE_TWIG = "twig";

	/**
	 * __construct
	 * @param &$context
	 */
	public function __construct(&$context){
		$this->context=$context;
	}

	/**
	 * getCode
	 */
	public function getCode(){
		return http_response_code();
	}

	/**
	 * getCode
	 * @params int $code
	 */
	public function setCode($code){
		http_response_code($code);
		return $this;
	}

	/**
	 * url
	 * @param string $urls
	 */
	 public function url($urls=null){

		if(is_string($urls)){

			if($urls[0]=="/"){
				return $urls;
			}
			else if($urls[0]=="@"){
				if(!RequestRouting::$_params["phpSelf"]){
					return "/";
				}
				return RequestRouting::$_params["phpSelf"];
			}
			else{
				return RequestRouting::$_params["phpSelf"]."/".$urls;
			}

		}
		else{

			if(!$urls){
				return RequestRouting::$_params["path"];
			}

			$url="";
			if(!empty($urls["controller"])){
				$url.=$urls["controller"]."/";
			}
			else{
				$url.=RequestRouting::$_params["controller"]."/";
			}

			if(!empty($urls["action"])){
				if($urls["action"]!="index"){
					$url.=$urls["action"]."/";
				}
			}

			if(!empty($urls["pass"])){
				if(!is_array($urls["pass"])){
					$urls["pass"]=[$urls["pass"]];
				}
				foreach($urls["pass"] as $p_){
					$url.=$p_."/";
				}
			}

			if(!empty($urls["query"])){
				if(!is_array($urls["query"])){
					$urls["query"]=[$urls["query"]];
				}
				$query="?";
				$ind=0;
				foreach($urls["query"] as $field=>$value){
					if($ind){
						$query.="&";
					}
					$query.=$field."=".$value;
					$ind++;
				}

				$url.=$query;
			}

			return RequestRouting::$_params["path"].$url;

		}

	}

	/**
	 * homeUrl
	 */
	public function homeUrl(){
		return $this->url("@");
	}

	/**
	 * redirect
	 * @param string $urls = null
	 */
	public function redirect($urls = null){
		$url=$this->url($urls);
		header('location: '.$url);
		exit;
	}

	/**
	 * back
	 */
	public function back(){
		$uri = $_SERVER['HTTP_REFERER'];
		header("Location: ".$uri);
		exit;
	}

	/**
	 * setData
	 * @param string $name
	 * @param string $value
	 */
	public function setData($name,$value){
		ResponseData::set($name,$value);
		return $this;
	}

	/**
	 * setDatas
	 * @param string $values
	 */
	public function setDatas($values){
		foreach($values as $colum=>$value){
			ResponseData::set($colum,$value);
		}
		return $this;
	}

	/**
	 * loadTemplate
	 * @param string $templateName
	 * @param boolean $outputBufferd
	 */
	public function loadTemplate($templateName = null, $outputBufferd = false){
		
		$params = RequestRouting::$_params;
		
		$TemplatePath = $params["paths"]["rendering"] . "/" . MK3_PATH_NAME_TEMPLATE . "/" . $templateName . MK3_VIEW_EXTENSION;

		if(!file_exists($TemplatePath)){
			echo "<pre>Template file not found. \n Path : '".$TemplatePath."'\n</pre>";
			return;
		}

		return $this->_loadTemplate($TemplatePath, $outputBufferd);
	}

	/**
	 * loadTemplateParent
	 * @param string $templateName
	 * @param boolean $outputBufferd
	 */
	public function loadTemplateParent($templateName = null, $outputBufferd = false){

		$params = RequestRouting::$_params;
		
		$TemplatePath = MK3_PATH_RENDERING_TEMPLATE . "/" . $templateName . MK3_VIEW_EXTENSION;

		if(!file_exists($TemplatePath)){
			echo "<pre>Template file not found. \n Path : '".$TemplatePath."'\n</pre>";
			return;
		}
		
		return $this->_loadTemplate($TemplatePath, $outputBufferd);
	}

	/**
	 * _loadTemplate
	 * @param string $TemplatePath
	 * @param boolean $outputBufferd
	 */
	private function _loadTemplate($TemplatePath, $outputBufferd){

		$templateEngine = Config::get("config.templateEngine");

		if($templateEngine===self::TEMPLATEENGINE_SMARTY){
			return $this->requireEngineSmarty($TemplatePath,$outputBufferd);
		}
		else if($templateEngine===self::TEMPLATEENGINE_TWIG){
			return $this->requireEngineTwig($TemplatePath,$outputBufferd);
		}

		return $this->require($TemplatePath,$outputBufferd);
	}

	/**
	 * loadContent
	 * @param boolean $outputBufferd
	 */
	public function loadContent($outputBufferd = false){

		$params=RequestRouting::$_params;

		if(!empty($this->context->view)){
			$viewPath = $params["paths"]["rendering"] . "/" .MK3_PATH_NAME_VIEW. "/". $this->context->view . MK3_VIEW_EXTENSION;
		}
		else{
			$viewPath = $params["paths"]["rendering"] . "/" .MK3_PATH_NAME_VIEW. "/". $params["controller"] . "/". $params["action"] . MK3_VIEW_EXTENSION;
		}

		return $this->_loadView($viewPath, $outputBufferd);
	}

	/**
	 * loadView
	 * @param string $viewName
	 * @param boolean $outputBufferd
	 */
	public function loadView($viewName, $outputBufferd = false){
/*
		if($viewName){
			$viewPath = $params["paths"]["rendering"] . "/" . MK3_PATH_NAME_VIEW. "/". $viewName . MK3_VIEW_EXTENSION;	
		}
		else{
			$params=RequestRouting::$_params;

			if(!empty($this->context->view)){
				$viewPath = $params["paths"]["rendering"] . "/" .MK3_PATH_NAME_VIEW. "/". $this->context->view . MK3_VIEW_EXTENSION;
			}
			else{
				$viewPath = $params["paths"]["rendering"] . "/" .MK3_PATH_NAME_VIEW. "/". $params["controller"] . "/". $params["action"] . MK3_VIEW_EXTENSION;
			}				
		}
*/		
		$params=RequestRouting::$_params;

		$viewPath = $params["paths"]["rendering"] . "/" .MK3_PATH_NAME_VIEW. "/". $viewName . MK3_VIEW_EXTENSION;
		$viewPath = str_replace("//","/",$viewPath);

		if(!file_exists($viewPath)){
			echo "<pre>[ViewError] View file not found. \n Path : '".$viewPath."'\n</pre>";
			return;
		}

		return $this->_loadView($viewPath, $outputBufferd);
	}

	/**
	 * loadViewParent
	 * @param string $viewName
	 * @param boolean $outputBufferd
	 */
	public function loadViewParent($viewName, $outputBufferd=false){

		$viewPath = MK3_PATH_RENDERING_VIEW . "/" . $viewName . MK3_VIEW_EXTENSION;
		$viewPath = str_replace("//","/",$viewPath);

		if(!file_exists($viewPath)){
			echo "<pre>[ViewError] View file not found. \n Path : '".$viewPath."'\n</pre>";
			return;
		}

		return $this->_loadView($viewPath, $outputBufferd);
	}

	/**
	 * _loadView
	 * @param string $viewPath
	 * @param boolean $outputBufferd
	 */
	private function _loadView($viewPath, $outputBufferd){

		$templateEngine=Config::get("config.templateEngine");

		if($templateEngine === self::TEMPLATEENGINE_SMARTY){
			return $this->requireEngineSmarty($viewPath,$outputBufferd);
		}
		else if($templateEngine === self::TEMPLATEENGINE_TWIG){
			return  $this->requireEngineTwig($viewPath,$outputBufferd);
		}
		else{
			return $this->require($viewPath,$outputBufferd);
		}
	}

	/**
	 * loadViewPart
	 * @param string $viewPartName
	 * @param boolean $outputBufferd
	 */
	public function loadViewPart($viewPartName, $outputBufferd = false){

		$params= RequestRouting::$_params;

		$viewPartPath = $params["paths"]["rendering"] . "/" . MK3_PATH_NAME_VIEWPART . "/" . $viewPartName . MK3_VIEW_EXTENSION;
		$viewPartPath = str_replace("\\","/",$viewPartPath);

		if(!file_exists($viewPartPath)){
			echo "<pre>[ViewPartError] ViewPart file not found. \n Path : '".$viewPartPath."'\n</pre>";
			return;
		}
		
		return $this->_loadViewPart($viewPartPath, $outputBufferd);
	}

	/**
	 * loadViewPartParent
	 * @param string $viewPartName
	 * @param boolean $outputBufferd
	 */
	public function loadViewPartParent($viewPartName, $outputBufferd = false){

		$viewPartPath = MK3_PATH_RENDERING_VIEWPART . "/" . $viewPartName . MK3_VIEW_EXTENSION;
		$viewPartPath = str_replace("\\","/",$viewPartPath);

		if(!file_exists($viewPartPath)){
			echo "<pre>[ViewPartError] ViewPart file not found. \n Path : '".$viewPartPath."'\n</pre>";
			return;
		}
		
		return $this->_loadViewPart($viewPartPath, $outputBufferd);

	}

	/**
	 * _loadViewPart
	 * @param string $viewPartName
	 * @param boolean $outputBufferd
	 */
	private function _loadViewPart($viewPartPath ,$outputBufferd){

		$templateEngine = Config::get("config.templateEngine");

		if($templateEngine === self::TEMPLATEENGINE_SMARTY){
			return $this->requireEngineSmarty($viewPartPath,$outputBufferd);
		}
		else if($templateEngine === self::TEMPLATEENGINE_TWIG){
			return  $this->requireEngineTwig($viewPartPath,$outputBufferd);
		}
		else{
			return $this->require($viewPartPath,$outputBufferd);
		}
	}

	/**
	 * require
	 * @param string $path
	 * @param boolean $outputBufferd
	 */
	private function require($path, $outputBufferd){

		if($outputBufferd){
			ob_start();
		}

		$this->context->require($path);

		if($outputBufferd){
			$contents = ob_get_contents();
			ob_end_clean();
	
			return $contents;	
		}

	}

}