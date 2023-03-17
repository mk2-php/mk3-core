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

use Exception;
use Error;

define("MK3_BEGIN_MEMORY_USAGE", memory_get_peak_usage());

// autoload register
spl_autoload_register(function($className){

	if(count(explode(MK3_PATH_SEPARATE_NAMESPACE, $className)) > 1){
		$className = lcfirst($className);
	}

	$classPath = MK3_ROOT. MK3_PATH_SEPARATE . str_replace( MK3_PATH_SEPARATE_NAMESPACE , MK3_PATH_SEPARATE , $className ) . ".php";

	if(file_exists($classPath)){
		require_once $classPath;
		return;
	}
});

class Startor{

	private const MODE_CLI = "cli";
	private $middlewares = [];

	public function __construct(){

		try{

			// load kernel Script (Version1)
			$this->loadKernelV1();
			
			// config data loading..
			$this->loadConfig();

			// load kernel Script (Version2)
			$this->loadKernelV2();

			// use class load.
			$this->useClass();

			// set routing
			$this->setRouting();

			// set Controller/Shell
			$this->setControllerOrShell();

		}catch(Exception $e){
			$this->error($e);
		}
		catch(Error $e){
			$this->error($e);
		}

	}

	/**
	 * loadKernelV1
	 */
	private function loadKernelV1(){

		require "construct.php";
		require "Hash.php";
		require "Debug.php";
		require "Config.php";
		require "CoreBlock.php";
		require "Routing.php";
		require "RequestRouting.php";
		require "ExpandClass.php";

	}
	
	/**
	 * loadKernelV2
	 */
	private function loadKernelV2(){

		if(Config::exists("config.coreBlock.useRequest")){
			require "Request.php";
		}
		if(Config::exists("config.coreBlock.useResponse")){
			require "Response.php";
		}

	}

	/**
	 * loadConfig
	 */
	private function loadConfig(){

		$configPath=MK3_PATH_CONFIG."/app.php";
		if(!file_exists($configPath)){
			throw new Exception('System configuration file "app.php" not found.'."\n".'Check if the file exists with the path below.'."\n".'Path : '.$configPath."\n");
		}

		$config=require($configPath);

		if(!empty($config["require"])){
			foreach($config["require"] as $cr_){
				if(!file_exists(MK3_PATH_CONFIG."/".$cr_.".php")){
					continue;
				}
				$requireName=pathinfo($cr_,PATHINFO_FILENAME);
				$buff=require(MK3_PATH_CONFIG."/".$cr_.".php");
				Config::set($cr_,$buff);
			}
		}

		Config::set("config",$config);
		
	}

	/**
	 * useClass
	 */
	private function useClass(){

		$useClass=Config::get("config.useClass");
		foreach($useClass as $c_){
			if(file_exists($c_.".php")){
				require $c_.".php";
			}
		}

	}

	/**
	 * setRouting
	 */
	private function setRouting(){

		$this->Routing = new Routing();

		if(php_sapi_name()==self::MODE_CLI){
			$this->setRoutingCLI();
		}
		else{
			$this->setRoutingWeb();
		}

	}

	/**
	 * setRoutingWeb
	 */
	private function setRoutingCLI(){

		$argv=$_SERVER["argv"];
		array_shift($argv);
		if(empty($argv[0])){
			$argv[0]="top";
		}
		$mainCommands=explode(":",$argv[0]);
		$mainCommand=$mainCommands[0];
		if($mainCommand!="command"){
			require "Mk3Shell/Mk3shell.php";
			new Mk3shell($argv);
			exit;
		}

		$cmdUrl=$mainCommands[1];
		$this->routeParam = $this->Routing->searchCmd($cmdUrl);

		if(empty($this->routeParam["shell"])){
			http_response_code(404);
			throw new \Exception('The specified prepared command was not found.');			
		}

		RequestRouting::$_params = $this->routeParam;			

	}

	/**
	 * setRoutingWeb
	 */
	private function setRoutingWeb(){

		$this->routeParam = $this->Routing->search();

		if(Config::exists("config.defaultRouting.controller")){

			if($this->routeParam["root"]=="/"){
				if(empty($this->routeParam["controller"])){
					$this->routeParam["controller"]=Config::get("config.defaultRouting.controller");
				}
				if(empty($this->routeParam["action"])){
					$this->routeParam["action"]=Config::get("config.defaultRouting.action");	
				}
			}
		}

		if(empty($this->routeParam["controller"])){
			http_response_code(404);
			throw new \Exception('The specified prepared page was not found.');			
		}

		RequestRouting::$_params=$this->routeParam;
	}

	/**
	 * setControllerOrShell
	 */
	private function setControllerOrShell(){

		if(php_sapi_name()==self::MODE_CLI){
			$this->setShell();
		}
		else{
			$this->setController();
		}

	}

	/**
	 * loadMiddlewareBefore
	 */
	private function loadMiddlewareBefore(){

		if(php_sapi_name()==self::MODE_CLI){
			$type="shell";
		}
		else{
			$type="pages";
		}		

		// load middleware (global)
		if(Config::exists("config.useClass.Middleware")){
			if(Config::exists("config.middleware.".$type)){

				$mList=Config::get("config.middleware.".$type);

				foreach($mList as $m_){
					if($m_[0] == MK3_PATH_SEPARATE_NAMESPACE){
						$middlewareName=ucfirst($m_)."Middleware";
					}
					else{
						$middlewareName=ucfirst(MK3_DEFNS_MIDDLEWARE)  .MK3_PATH_SEPARATE_NAMESPACE . ucfirst($m_)."Middleware";
					}

					$mbuff=new $middlewareName;

					if(method_exists($mbuff,"handleBefore")){
						$mbuff->handleBefore();
					}

					$this->middlewares[]=$mbuff;
				}
			}
		}

		$response=[];

		// load middleware (local)
		if(!empty($this->routeParam["middleware"])){
			foreach($this->routeParam["middleware"] as $m_){
				if($m_[0] == MK3_PATH_SEPARATE_NAMESPACE){
					$middlewareName=ucfirst($m_)."Middleware";
				}
				else{
					$middlewareName=ucfirst(MK3_DEFNS_MIDDLEWARE) . MK3_PATH_SEPARATE_NAMESPACE . ucfirst($m_)."Middleware";
				}

				$mbuff=new $middlewareName;

				if(method_exists($mbuff,"handleBefore")){
					$buffer = $mbuff->handleBefore();
					$response[$m_]=$buffer;
				}

				$this->middlewares[]=$mbuff;
			}
		}

		return $response;
	}

	/**
	 * loadMiddlewareAfter
	 * @param $input
	 */
	private function loadMiddlewareAfter($input){

		if(!empty($this->middlewares)){
			foreach($this->middlewares as $m_){
				if(method_exists($m_,"handleAfter")){
					$m_->handleAfter($input);
				}
			}
		}

	}

	/**
	 * setController
	 */
	private function setController(){

		$controllerName = $this->routeParam["paths"]["namespace"] . MK3_PATH_SEPARATE_NAMESPACE. 
			MK3_PATH_NAME_CONTROLLER . MK3_PATH_SEPARATE_NAMESPACE. 
			ucfirst($this->routeParam["controller"]). MK3_PATH_NAME_CONTROLLER
		;

		if(!class_exists($controllerName)){
			http_response_code(404);
			throw new \Exception('Missing "'.$controllerName.'" class not found.');
		}

		$controller=new $controllerName();

		Routings::$_data=$this->routeParam;

		$action=$this->routeParam["action"];

		if(!method_exists($controller,$action)){
			http_response_code(404);
			throw new \Exception('"'.$action.'" action does not exist in "'.$controllerName.'" class.');
		}

		// middleware before action...
		$middlewareResponse = $this->loadMiddlewareBefore();
		if($middlewareResponse){
			$controller->middlewareResponse=$middlewareResponse;
		}

		if(method_exists($controller,"handleBefore")){
			$controller->beforeResponse = $controller->handleBefore();
		}

		if(!empty($this->routeParam["request"])){
			$output=$controller->{$action}(...$this->routeParam["request"]);
		}
		else{
			$output=$controller->{$action}();
		}

		if(!empty($controller->autoRender)){
			$controller->_rendering();
		}

		if(method_exists($controller,"handleAfter")){
			$buff = $controller->handleAfter($output);
			if($buff){
				$output = $buff;
			}
		}

		// middleware after action..
		$this->loadMiddlewareAfter($output);

	}

	/**
	 * setShell
	 */
	public function setShell(){

		if($this->routeParam["shell"][0] == MK3_PATH_SEPARATE_NAMESPACE){
			$this->routeParam["shell"] = substr($this->routeParam["shell"],1);
			$shellName = ucfirst($this->routeParam["shell"])."Shell";
		}
		else{
			$shellName = ucfirst(MK3_DEFNS_SHELL) . MK3_PATH_SEPARATE_NAMESPACE . ucfirst($this->routeParam["shell"])."Shell";
		}

		if(!class_exists($shellName)){
			http_response_code(404);
			throw new \Exception('Missing "'.$shellName.'" class not found.');
		}

		$shell=new $shellName();

		$action=$this->routeParam["action"];

		if(!method_exists($shell,$action)){
			http_response_code(404);
			throw new \Exception('"'.$action.'" action does not exist in "'.$shellName.'" class.');
		}

		if(method_exists($shell,"handleBefore")){
			$shell->beforeResponse = $shell->handleBefore();
		}

		if($this->routeParam["request"]){
			$output=$shell->{$action}(...$this->routeParam["request"]);
		}
		else{
			$output=$shell->{$action}();
		}

		if(method_exists($shell,"handleAfter")){
			$shell->handleAfter($output);
		}
	}

	/**
	 * error
	 * @param Exception/Error $exception
	 */
	private function error($exception){

		if(php_sapi_name()==self::MODE_CLI){
			$this->errorCLI($exception);
		}
		else{
			$this->errorWeb($exception);
		}
	}

	/**
	 * errorCLI
	 * @param Exception $exception
	 */
	private function errorCLI($exception){

		$errorRoute = $this->Routing->searchErrorClassCmd($exception, $this->routeParam);

		try{

			$shellName  =ucfirst(MK3_DEFNS_SHELL) . MK3_PATH_SEPARATE_NAMESPACE . ucfirst($errorRoute["shell"])."Shell";

			if(!class_exists($shellName)){
				throw new \Exception('Missing "'.$shellName.'" class not found.');
			}

			$shell=new $shellName();

			if(method_exists($shell,"handleBefore")){
				$shell->beforeResponse = $shell->handleBefore($exception);
			}

			$output = $shell->{$errorRoute["action"]}($exception);

			if(method_exists($shell,"handleAfter")){
				$shell->handleAfter($output,$exception);
			}

		}catch(\Exception $e){
			echo "\n";
			echo "\033[0;31m";
			echo $exception."\n";
			echo "\n";
			echo $e."\n";
			echo "\033[0m";
			echo "\n";
		}

	}

	/**
	 * errorWeb
	 * @param Exception $exception
	 */
	private function errorWeb($exception){

		if(empty($this->Routing)){
			echo $exception;
			return;
		}

		$errorRoute = $this->Routing->searchErrorClass($exception, $this->routeParam);

		try{

			if(!$errorRoute){
				throw new \Exception('Missing Error Exception class not found.');
			}
						
			$controllerName = $errorRoute["paths"]["namespace"] . MK3_PATH_SEPARATE_NAMESPACE . 
				MK3_PATH_NAME_CONTROLLER . MK3_PATH_SEPARATE_NAMESPACE . 
				ucfirst($errorRoute["controller"]). MK3_PATH_NAME_CONTROLLER
			;

			if(!class_exists($controllerName)){
				throw new \Exception('Missing "'.$controllerName.'" class not found.');
			}

			$controller=new $controllerName();

			if(method_exists($controller,"handleBefore")){
				$controller->beforeResponse = $controller->handleBefore($exception);
			}

			$output=$controller->{$errorRoute["action"]}($exception);
			
			RequestRouting::$_params = $errorRoute;

			if(!empty($controller->autoRender)){
				$controller->_rendering();
			}

			if(method_exists($controller,"handleAfter")){
				$controller->handleAfter($output,$exception);
			}

		}catch(\Exception $e){
			echo "<pre>";
			echo $e;
			echo "</pre>";
		}

	}

}