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

class Response{

	private const TEMPLATEENGINE_SMARTY = "smarty";
	private const TEMPLATEENGINE_TWIG = "twig";

	private static $_viewData = [];

	public static $view = null;
	public static $viewParent = null;

	/**
	 * code
	 * @param $code = null 
	 */
	public static function code($code = null){
		if($code){
			http_response_code($code);
		}
		else{
			return http_response_code();
		}
	}

	/**
	 * url
	 * @param string $urls
	 */
	 public static function url($urls = null, $fullPath = false){

		if(!$urls){
			$urls = RequestRouting::$_params["root"];
		}

		$url = "";
		if($fullPath){
			$url = RequestRouting::$_params["url"];
		}

		$url .= RequestRouting::$_params["phpSelf"] .$urls;

		return $url;
	}

	/**
	 * redirect
	 * @param string $url = null
	 */
	public static function redirect($url = null){

		if(substr($url,0,1) == "/"){
			$url = self::url($url);
		}

		header('location: '.$url);
		exit;
	}

	/**
	 * back
	 */
	public static function back(){

		if(!isset($_SERVER["HTTP_REFERER"])){
			return;
		}

		$uri = $_SERVER['HTTP_REFERER'];

		header("Location: ".$uri);
		exit;
	}

	/**
	 * sendData
	 * @param string $values
	 */
	public static function sendData($values){
		foreach($values as $key => $value){
			self::$_viewData[$key] = $value;
		}
	}

	/**
	 * template
	 * @param string $templateName
	 * @param Array $sendViewData
	 * @param boolean $outputBufferd
	 */
	public static function template($templateName = null, $sendViewData = null, $outputBufferd = false){
		
		$params = RequestRouting::$_params;
		
		$TemplatePath = $params["paths"]["rendering"] . "/" . RLD_PATH_NAME_TEMPLATE . "/" . $templateName . RLD_VIEW_EXTENSION;

		if(!file_exists($TemplatePath)){
			echo "<pre>Template file not found. \n Path : '".$TemplatePath."'\n</pre>";
			return;
		}

		if($sendViewData){
			self::sendData($sendViewData);
		}

		return self::_template($TemplatePath, $outputBufferd);
	}

	/**
	 * parentTemplate
	 * @param string $templateName
	 * @param Array $sendViewData
	 * @param boolean $outputBufferd
	 */
	public static function parentTemplate($templateName = null, $sendViewData = null, $outputBufferd = false){

		$params = RequestRouting::$_params;
		
		$TemplatePath = RLD_PATH_RENDERING_TEMPLATE . "/" . $templateName . RLD_VIEW_EXTENSION;

		if(!file_exists($TemplatePath)){
			echo "<pre>Template file not found. \n Path : '".$TemplatePath."'\n</pre>";
			return;
		}

		if($sendViewData){
			self::sendData($sendViewData);
		}

		return self::_template($TemplatePath, $outputBufferd);
	}

	/**
	 * _template
	 * @param string $TemplatePath
	 * @param boolean $outputBufferd
	 */
	private static function _template($TemplatePath, $outputBufferd){

		$templateEngine = Config::get("config.templateEngine");

		if($templateEngine===self::TEMPLATEENGINE_SMARTY){
			return self::_requireEngineSmarty($TemplatePath, $outputBufferd);
		}
		else if($templateEngine===self::TEMPLATEENGINE_TWIG){
			return self::_requireEngineTwig($TemplatePath, $outputBufferd);
		}

		return self::_require($TemplatePath, $outputBufferd);
	}

	/**
	 * content
	 * @param Array $sendViewData
	 * @param boolean $outputBufferd
	 */
	public static function content($sendViewData = null, $outputBufferd = false){

		$params = RequestRouting::$_params;

		if(isset($params["controller"]) && isset($params["action"])){
			$viewPath = $params["paths"]["rendering"] . "/" .RLD_PATH_NAME_VIEW. "/". $params["controller"] . "/". $params["action"] . RLD_VIEW_EXTENSION;
		}
		
		if(!empty(self::$view)){
			$viewPath = $params["paths"]["rendering"] . "/" .RLD_PATH_NAME_VIEW. "/". self::$view . RLD_VIEW_EXTENSION;
		}
		if(!empty(self::$viewParent)){
			$viewPath = $params["paths"]["rendering"] . "/" .RLD_PATH_NAME_VIEW. "/". self::$viewParent . RLD_VIEW_EXTENSION;
		}

		if(!$viewPath){
			return;
		}

		if($sendViewData){
			self::sendData($sendViewData);
		}

		return self::_view($viewPath, $outputBufferd);
	}

	/**
	 * viewExists
	 * @param String $viewName
	 * @return Boolean
	 */
	public static function viewExists($viewName){

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
	 * @param Array $sendViewData
	 * @param boolean $outputBufferd
	 */
	public static function view($viewName, $sendViewData = null, $outputBufferd = false){

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

		if($sendViewData){
			self::sendData($sendViewData);
		}

		return self::_view($viewPath, $outputBufferd);
	}

	/**
	 * parentViewExists
	 * @param String $viewName
	 * @return Boolean
	 */
	public static function parentViewExists($viewName){

		$params = RequestRouting::$_params;

		$viewPath = RLD_PATH_RENDERING_VIEW . "/" . $viewName . RLD_VIEW_EXTENSION;
		$viewPath = str_replace("//","/",$viewPath);

		if(!file_exists($viewPath)){
			return false;
		}

		return true;
	}
	
	/**
	 * parentView
	 * @param string $viewName
	 * @param Array $sendViewData
	 * @param boolean $outputBufferd
	 */
	public static function parentView($viewName, $sendViewData = null, $outputBufferd=false){

		$viewPath = RLD_PATH_RENDERING_VIEW . "/" . $viewName . RLD_VIEW_EXTENSION;
		$viewPath = str_replace("//","/",$viewPath);

		if(!file_exists($viewPath)){
			echo "<pre>[ViewError] View file not found. \n Path : '".$viewPath."'\n</pre>";
			return;
		}

		if($sendViewData){
			self::sendData($sendViewData);
		}

		return self::_view($viewPath, $outputBufferd);
	}

	/**
	 * _view
	 * @param string $viewPath
	 * @param boolean $outputBufferd
	 */
	private static function _view($viewPath, $outputBufferd){

		$templateEngine = Config::get("config.templateEngine");

		if($templateEngine === self::TEMPLATEENGINE_SMARTY){
			return self::_requireEngineSmarty($viewPath, $outputBufferd);
		}
		else if($templateEngine === self::TEMPLATEENGINE_TWIG){
			return self::requireEngineTwig($viewPath, $outputBufferd);
		}
		else{
			return self::_require($viewPath, $outputBufferd);
		}
	}

	/**
	 * viewPart
	 * @param string $viewPartName
	 * @param Array $sendViewData
	 * @param boolean $outputBufferd
	 */
	public static function viewPart($viewPartName, $sendViewData = null, $outputBufferd = false){

		$params= RequestRouting::$_params;

		$viewPartPath = $params["paths"]["rendering"] . "/" . RLD_PATH_NAME_VIEWPART . "/" . $viewPartName . RLD_VIEW_EXTENSION;
		$viewPartPath = str_replace("\\","/",$viewPartPath);

		if(!file_exists($viewPartPath)){
			echo "<pre>[ViewPartError] ViewPart file not found. \n Path : '".$viewPartPath."'\n</pre>";
			return;
		}

		if($sendViewData){
			self::sendData($sendViewData);
		}

		return self::_viewPart($viewPartPath, $outputBufferd);
	}

	/**
	 * parentViewPart
	 * @param string $viewPartName
	 * @param Array $sendViewData
	 * @param boolean $outputBufferd
	 */
	public function parentViewPart($viewPartName, $sendViewData = null, $outputBufferd = false){

		$viewPartPath = RLD_PATH_RENDERING_VIEWPART . "/" . $viewPartName . RLD_VIEW_EXTENSION;
		$viewPartPath = str_replace("\\","/",$viewPartPath);

		if(!file_exists($viewPartPath)){
			echo "<pre>[ViewPartError] ViewPart file not found. \n Path : '".$viewPartPath."'\n</pre>";
			return;
		}

		if($sendViewData){
			self::sendData($sendViewData);
		}

		return self::_viewPart($viewPartPath, $outputBufferd);

	}

	/**
	 * _viewPart
	 * @param string $viewPartName
	 * @param boolean $outputBufferd
	 */
	private static function _viewPart($viewPartPath ,$outputBufferd){

		$templateEngine = Config::get("config.templateEngine");

		if($templateEngine === self::TEMPLATEENGINE_SMARTY){
			return self::_requireEngineSmarty($viewPartPath,$outputBufferd);
		}
		else if($templateEngine === self::TEMPLATEENGINE_TWIG){
			return self::_requireEngineTwig($viewPartPath,$outputBufferd);
		}
		else{
			return self::_require($viewPartPath,$outputBufferd);
		}
	}

	/**
	 * hookReceive
	 * @param $hookName
	 * @param $hookMethod
	 * @param $aregments = null
	 */
	public static function hookReceive($hookName, $hookMethod, $aregments = null){

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
	private static function _require($path, $outputBufferd){

		if($outputBufferd){
			ob_start();
		}

		if(self::$_viewData){
			foreach(self::$_viewData as $key => $value){
				$$key = $value;
			}
		}

		require $path;

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
	private static function _requireEngineSmarty($loadFilePath,$outputBufferd){


	}

	/**
	 * _requireEngineTwig
	 * @param $loadFilePath
	 * @param $outputBufferd
	 */
	private static function _requireEngineTwig($loadFilePath,$outputBufferd){


	}

}