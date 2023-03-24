<?php
/**
 * ===================================================
 * 
 * PHP FW "Reald"
 * Startor.php
 * 
 * Object class for initial operation.
 * A class for starting the framework.
 * Starting from this class, the web system is constructed.
 * 
 * URL : 
 * Copylight : Masato-Nakatsuji 2023.
 * 
 * ===================================================
 */

namespace Reald\Core;

use Exception;
use Error;

define("RLD_BEGIN_MEMORY_USAGE", memory_get_peak_usage());

// autoload register
spl_autoload_register(function($className){

	if(count(explode(RLD_PATH_SEPARATE_NAMESPACE, $className)) > 1){
		$className = lcfirst($className);
	}

	$classPath = RLD_ROOT. RLD_PATH_SEPARATE . str_replace( RLD_PATH_SEPARATE_NAMESPACE , RLD_PATH_SEPARATE , $className ) . ".php";

	if(file_exists($classPath)){
		require_once $classPath;
		return;
	}
});

class Startor{

	private const MODE_CLI = "cli";
	private $middlewares = [];

	/**
	 * __construct
	 */
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
		require "Debug.php";
		require "Config.php";
		require "CoreBlock.php";
		require "Routing.php";
		require "RequestRouting.php";
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

		$configPath = RLD_PATH_CONFIG."/app.php";
		if(!file_exists($configPath)){
			throw new Exception('System configuration file "app.php" not found.'."\n".'Check if the file exists with the path below.'."\n".'Path : '.$configPath."\n");
		}

		$config = require($configPath);

		Config::set("config", $config);

		if(!empty($config["require"])){
			foreach($config["require"] as $cr_){
				if(!file_exists(RLD_PATH_CONFIG."/".$cr_.".php")){
					continue;
				}
				$requireName = pathinfo($cr_,PATHINFO_FILENAME);
				$buff = require(RLD_PATH_CONFIG."/".$cr_.".php");
				Config::set($cr_, $buff);
			}
		}

		
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

		$mainCommands = explode(" ",$argv[0]);
		$mainCommand = $mainCommands[0];

		if($mainCommand != "command"){
			require "Console/RldShell.php";
			new RldShell($argv);
			exit;
		}

		$cmdUrl = $argv[1];

		$this->routeParam = $this->Routing->searchCmd($cmdUrl);

		RequestRouting::$_params = $this->routeParam;	

		if(empty($this->routeParam["shell"])){
			throw new \Exception('The specified prepared command was not found.');			
		}

	}

	/**
	 * setRoutingWeb
	 */
	private function setRoutingWeb(){

		$this->routeParam = $this->Routing->search();

		RequestRouting::$_params = $this->routeParam;

		if(empty($this->routeParam["controller"])){
			http_response_code(404);
			throw new \Exception('The specified prepared page was not found.');			
		}

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
					if($m_[0] == RLD_PATH_SEPARATE_NAMESPACE){
						$middlewareName = ucfirst($m_)."Middleware";
					}
					else{
						$middlewareName = ucfirst(RLD_DEFNS_MIDDLEWARE)  .RLD_PATH_SEPARATE_NAMESPACE . ucfirst($m_)."Middleware";
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

				$middlewareName = $this->routeParam["paths"]["namespace"] . "\\" . RLD_PATH_NAME_MIDDLEWARE . "\\" .ucfirst($m_) . RLD_PATH_NAME_MIDDLEWARE;

				$mbuff = new $middlewareName;

				if(method_exists($mbuff, "handleBefore")){
					$buffer = $mbuff->handleBefore();
					$response[$m_] = $buffer;
				}

				$this->middlewares[] = $mbuff;
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

		$controllerName = $this->routeParam["paths"]["namespace"] . RLD_PATH_SEPARATE_NAMESPACE. 
			RLD_PATH_NAME_CONTROLLER . RLD_PATH_SEPARATE_NAMESPACE. 
			ucfirst($this->routeParam["controller"]). RLD_PATH_NAME_CONTROLLER
		;

		if(!class_exists($controllerName)){
			throw new \Exception('Missing "'.$controllerName.'" class not found.');
		}

		$controller=new $controllerName();

		Routings::$_data=$this->routeParam;

		$action=$this->routeParam["action"];

		if(!method_exists($controller,$action)){
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

		$shellName = $this->routeParam["paths"]["namespace"] . RLD_PATH_SEPARATE_NAMESPACE. 
			RLD_PATH_NAME_SHELL . RLD_PATH_SEPARATE_NAMESPACE. 
			ucfirst($this->routeParam["shell"]). RLD_PATH_NAME_SHELL
		;

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

		if(!empty($this->routeParam["request"])){
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

		$exceptionPath = $this->routeParam["paths"]["namespace"] .RLD_PATH_SEPARATE_NAMESPACE . RLD_PATH_NAME_EXCEPTION . RLD_PATH_SEPARATE_NAMESPACE . RLD_PATH_NAME_EXCEPTION_CLI;

		if(!class_exists($exceptionPath)){
			throw new \Exception('Missing Exception class not found.');
		}

		$exp = new $exceptionPath;

		$exp->handle($exception);
	}

	/**
	 * errorWeb
	 * @param Exception $exception
	 */
	private function errorWeb($exception){

		$exceptionPath = $this->routeParam["paths"]["namespace"] .RLD_PATH_SEPARATE_NAMESPACE . RLD_PATH_NAME_EXCEPTION . RLD_PATH_SEPARATE_NAMESPACE . RLD_PATH_NAME_EXCEPTION;
		
		if(!class_exists($exceptionPath)){
			throw new \Exception('Missing Exception class not found.');
		}

		$exp = new $exceptionPath;

		$exp->handle($exception);

		$exp->_rendering();
	}

}