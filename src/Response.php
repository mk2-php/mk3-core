<?php
/**
 * ===================================================
 * 
 * PHP FW "Reald"
 * ResponseData
 * 
 * Object class for initial operation.
 * 
 * URL : 
 * Copylight : Masato-Nakatsuji 2023.
 * 
 * ===================================================
 */

namespace Reald\Core;

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
		$this->context = $context;
	}

	/**
	 * code
	 * @param $code = null 
	 */
	public function code($code = null){
		if($code){
			http_response_code($code);
			return $this;
		}
		else{
			return http_response_code();
		}
	}

	/**
	 * url
	 * @param string $urls
	 */
	 public function url($urls = null){

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
	 * @param string $values
	 */
	public function setData($values){
		foreach($values as $colum=>$value){
			ResponseData::set($colum,$value);
		}
		return $this;
	}

	/**
	 * template
	 * @param string $templateName
	 * @param boolean $outputBufferd
	 */
	public function template($templateName = null, $outputBufferd = false){
		
		$params = RequestRouting::$_params;
		
		$TemplatePath = $params["paths"]["rendering"] . "/" . RLD_PATH_NAME_TEMPLATE . "/" . $templateName . RLD_VIEW_EXTENSION;

		if(!file_exists($TemplatePath)){
			echo "<pre>Template file not found. \n Path : '".$TemplatePath."'\n</pre>";
			return;
		}

		return $this->_template($TemplatePath, $outputBufferd);
	}

	/**
	 * parentTemplate
	 * @param string $templateName
	 * @param boolean $outputBufferd
	 */
	public function parentTemplate($templateName = null, $outputBufferd = false){

		$params = RequestRouting::$_params;
		
		$TemplatePath = RLD_PATH_RENDERING_TEMPLATE . "/" . $templateName . RLD_VIEW_EXTENSION;

		if(!file_exists($TemplatePath)){
			echo "<pre>Template file not found. \n Path : '".$TemplatePath."'\n</pre>";
			return;
		}
		
		return $this->_template($TemplatePath, $outputBufferd);
	}

	/**
	 * _template
	 * @param string $TemplatePath
	 * @param boolean $outputBufferd
	 */
	private function _template($TemplatePath, $outputBufferd){

		$templateEngine = Config::get("config.templateEngine");

		if($templateEngine===self::TEMPLATEENGINE_SMARTY){
			return $this->_requireEngineSmarty($TemplatePath,$outputBufferd);
		}
		else if($templateEngine===self::TEMPLATEENGINE_TWIG){
			return $this->_requireEngineTwig($TemplatePath,$outputBufferd);
		}

		return $this->_require($TemplatePath,$outputBufferd);
	}

	/**
	 * content
	 * @param boolean $outputBufferd
	 */
	public function content($outputBufferd = false){

		$params=RequestRouting::$_params;

		if(!empty($this->context->view)){
			$viewPath = $params["paths"]["rendering"] . "/" .RLD_PATH_NAME_VIEW. "/". $this->context->view . RLD_VIEW_EXTENSION;
		}
		else{
			$viewPath = $params["paths"]["rendering"] . "/" .RLD_PATH_NAME_VIEW. "/". $params["controller"] . "/". $params["action"] . RLD_VIEW_EXTENSION;
		}

		return $this->_view($viewPath, $outputBufferd);
	}

	/**
	 * viewExists
	 * @param String $viewName
	 * @return Boolean
	 */
	public function viewExists($viewName){

		$params = RequestRouting::$_params;

		if(substr($viewName,0,1) == "/"){
			$viewPath = $viewName;
		}
		else{
			$viewPath = $params["paths"]["rendering"] . "/" .RLD_PATH_NAME_VIEW. "/". $viewName . RLD_VIEW_EXTENSION;
		}

		$viewPath = str_replace("//","/",$viewPath);

		if(!file_exists($viewPath)){
			return false;
		}

		return true;
	}

	/**
	 * view
	 * @param string $viewName
	 * @param boolean $outputBufferd
	 */
	public function view($viewName, $outputBufferd = false){

		$params = RequestRouting::$_params;

		if(substr($viewName,0,1) == "/"){
			$viewPath = $viewName;
		}
		else{
			$viewPath = $params["paths"]["rendering"] . "/" .RLD_PATH_NAME_VIEW. "/". $viewName . RLD_VIEW_EXTENSION;
		}

		$viewPath = str_replace("//","/",$viewPath);

		if(!file_exists($viewPath)){
			return "<pre>[ViewError] View file not found. \n Path : '".$viewPath."'\n</pre>";
		}

		return $this->_view($viewPath, $outputBufferd);
	}

	/**
	 * parentView
	 * @param string $viewName
	 * @param boolean $outputBufferd
	 */
	public function parentView($viewName, $outputBufferd=false){

		$viewPath = RLD_PATH_RENDERING_VIEW . "/" . $viewName . RLD_VIEW_EXTENSION;
		$viewPath = str_replace("//","/",$viewPath);

		if(!file_exists($viewPath)){
			echo "<pre>[ViewError] View file not found. \n Path : '".$viewPath."'\n</pre>";
			return;
		}

		return $this->_view($viewPath, $outputBufferd);
	}

	/**
	 * _view
	 * @param string $viewPath
	 * @param boolean $outputBufferd
	 */
	private function _view($viewPath, $outputBufferd){

		$templateEngine = Config::get("config.templateEngine");

		if($templateEngine === self::TEMPLATEENGINE_SMARTY){
			return $this->_requireEngineSmarty($viewPath,$outputBufferd);
		}
		else if($templateEngine === self::TEMPLATEENGINE_TWIG){
			return $this->_requireEngineTwig($viewPath,$outputBufferd);
		}
		else{
			return $this->_require($viewPath, $outputBufferd);
		}
	}

	/**
	 * viewPart
	 * @param string $viewPartName
	 * @param boolean $outputBufferd
	 */
	public function viewPart($viewPartName, $outputBufferd = false){

		$params= RequestRouting::$_params;

		$viewPartPath = $params["paths"]["rendering"] . "/" . RLD_PATH_NAME_VIEWPART . "/" . $viewPartName . RLD_VIEW_EXTENSION;
		$viewPartPath = str_replace("\\","/",$viewPartPath);

		if(!file_exists($viewPartPath)){
			echo "<pre>[ViewPartError] ViewPart file not found. \n Path : '".$viewPartPath."'\n</pre>";
			return;
		}
		
		return $this->_viewPart($viewPartPath, $outputBufferd);
	}

	/**
	 * parentViewPart
	 * @param string $viewPartName
	 * @param boolean $outputBufferd
	 */
	public function parentViewPart($viewPartName, $outputBufferd = false){

		$viewPartPath = RLD_PATH_RENDERING_VIEWPART . "/" . $viewPartName . RLD_VIEW_EXTENSION;
		$viewPartPath = str_replace("\\","/",$viewPartPath);

		if(!file_exists($viewPartPath)){
			echo "<pre>[ViewPartError] ViewPart file not found. \n Path : '".$viewPartPath."'\n</pre>";
			return;
		}
		
		return $this->_viewPart($viewPartPath, $outputBufferd);

	}

	/**
	 * _viewPart
	 * @param string $viewPartName
	 * @param boolean $outputBufferd
	 */
	private function _viewPart($viewPartPath ,$outputBufferd){

		$templateEngine = Config::get("config.templateEngine");

		if($templateEngine === self::TEMPLATEENGINE_SMARTY){
			return $this->_requireEngineSmarty($viewPartPath,$outputBufferd);
		}
		else if($templateEngine === self::TEMPLATEENGINE_TWIG){
			return  $this->_requireEngineTwig($viewPartPath,$outputBufferd);
		}
		else{
			return $this->_require($viewPartPath,$outputBufferd);
		}
	}

	/**
	 * hookReceive
	 * @param $hookName
	 * @param $hookMethod
	 * @param $aregments = null
	 */
	public function hookReceive($hookName, $hookMethod, $aregments = null){

		$containerPath =  RLD_ROOT . RLD_PATH_SEPARATE. RLD_CONTAINER . RLD_PATH_SEPARATE ."*";

		$getContainer = glob($containerPath);

		foreach($getContainer as $gc_){

			$hookPath = $gc_ . RLD_PATH_SEPARATE . RLD_DEFNS . RLD_PATH_SEPARATE . RLD_PATH_NAME_HOOK . RLD_PATH_SEPARATE . ucfirst($hookName) . RLD_PATH_NAME_HOOK . ".php";

			if(!file_exists($hookPath)){
				continue;
			}

			require $hookPath;

			$hookClassName = RLD_PATH_SEPARATE_NAMESPACE. str_replace(RLD_PATH_SEPARATE, RLD_PATH_SEPARATE_NAMESPACE, str_replace(RLD_ROOT . RLD_PATH_SEPARATE, "", $gc_)) . RLD_PATH_SEPARATE_NAMESPACE . RLD_DEFNS . RLD_PATH_SEPARATE_NAMESPACE . RLD_PATH_NAME_HOOK . RLD_PATH_SEPARATE_NAMESPACE . ucfirst($hookName) . RLD_PATH_NAME_HOOK;

			if(!class_exists($hookClassName)){
				continue;
			}

			$hook = new $hookClassName();

			if(!method_exists($hook, $hookMethod)){
				continue;
			}

			if($aregments){
				$hookRes = $hook->{$hookMethod}(...$aregments);
			}
			else{
				$hookRes = $hook->{$hookMethod}();
			}

			return $hookRes;
		}
	}

	/**
	 * _require
	 * @param string $path
	 * @param boolean $outputBufferd
	 */
	private function _require($path, $outputBufferd){

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

	/**
	 * _requireEngineSmarty
	 * @param $loadFilePath
	 * @param $outputBufferd
	 */
	private function _requireEngineSmarty($loadFilePath,$outputBufferd){


	}

	/**
	 * _requireEngineTwig
	 * @param $loadFilePath
	 * @param $outputBufferd
	 */
	private function _requireEngineTwig($loadFilePath,$outputBufferd){


	}

}