<?php
/**
 * ===================================================
 * 
 * PHP FW "Reald"
 * Routing
 * 
 * Object class for initial operation.
 * 
 * URL : 
 * Copylight : Masato-Nakatsuji 2023.
 * 
 * ===================================================
 */

namespace Reald\Core;

class Routings{
	public static $_data=null;
}

class Routing{

	private const TYPE_PAGES = "pages";
	private const TYPE_SHELL = "shell";

	/**
	 * seasrch
	 */
	public function search(){

		$routingList=$this->convertRouting(self::TYPE_PAGES);

		$rootParams=$this->getRoute();
		
		$response = $this->searchRouting(self::TYPE_PAGES, $rootParams,$routingList);

		return $response;

	}

	/**
	 * searchCmd
	 * @param string $commandLine
	 */
	public function searchCmd($commandLine){

		$routingList = $this->convertRouting(self::TYPE_SHELL);

		$rootParams = $this->getRouteCLI($commandLine);

		$response = $this->searchRouting(self::TYPE_SHELL, $rootParams, $routingList);

		return $response;
	}
	
	/**
	* searchErrorClass
	* @param Exception $exception
	* @param Array $rootParams
	*/
	public function searchErrorClass($exception, $rootParams){

		$routingList=$this->convertRouting(self::TYPE_PAGES);

		$rootParams=$this->getRoute();

		$response=$this->getRouteErrorClass(self::TYPE_PAGES, $rootParams, $exception,$rootParams,$routingList);

		return $response;

	}

	/**
	* searchErrorClassCmd
	* @param Exception $exception
	* @param Array $rootParams
	*/
	public function searchErrorClassCmd($exception, $rootParams){

		$routingList=$this->convertRouting(self::TYPE_SHELL);

		$response=$this->getRouteErrorClass(self::TYPE_SHELL,null, $exception,$rootParams,$routingList);

		return $response;

	}

	/**
	* convertRouting
	* @param string $type
	*/
	private function convertRouting($type){

		$routings = Config::get("config.routing.".$type);

		$routings = $this->convertRoutingPageScope($routings);
		$routings = $this->convertRoutingOptions($routings, $type);

		return $routings;
	}

	/**
	* convertRoutingPageScope
	* @param Array $pages
	*/
	private function convertRoutingPageScope($pages){

		$buffer=[];
		foreach($pages as $url => $rp_){

			$urls = explode("|", $url);

			if(count($urls) == 1){

				if(is_array($rp_)){
					
					$sub = $this->convertRoutingPageScope($rp_);

					foreach($sub as $subUrl => $s_){
						if($subUrl == "/"){
							$subUrl = "";
						}
						$buffer[$url . $subUrl] = $s_;
					}
				}
				else{

					$buffer[$url] = [
						"_" => $rp_,
					];
				}
			}
			else{

				$method = $urls[0];
				$url = $urls[1];

				if(is_array($rp_)){
					
					$sub = $this->convertRoutingPageScope($rp_);

					foreach($sub as $subUrl => $s_){
						if($subUrl == "/"){
							$subUrl = "";
						}
						
						if(empty($buffer[$url . $subUrl])){
							$buffer[$url . $subUrl] = [];
						}

						$buffer[$url . $subUrl][$method] = $s_["_"];
					}
				}
				else{

					if(empty($buffer[$url])){
						$buffer[$url] = [];
					}

					$buffer[$url][$method] = $rp_;
				}
			}
		}

		return $buffer;
	}
	
	/**
	 * convertRoutingParse
	 * @param $pages
	 * @param $type
	 * @param $parentRoute = null
	 */
	private function convertRoutingParse($pages, $type, $parentRoute = null){

		foreach($pages as $url => $r_){

			foreach($r_ as $method => $rp_){

				$rp_ = explode("|", $rp_);

				if($type == self::TYPE_SHELL){
					$shell = null;
				}
				else if($type == self::TYPE_PAGES){
					$controller = null;
				}

				$action = null;

				$container = null;
				if(!empty($parentRoute["container"])){
					$container = $parentRoute["container"];
				}

				$middleware = null;
				if(!empty($parentRoute["middleware"])){
					$middleware = $parentRoute["middleware"];
				}

				foreach($rp_ as $rpp_){

					$rpp_ = explode(":", $rpp_);

					$rpp_[0] = trim($rpp_[0]);
					$rpp_[1] = trim($rpp_[1]);

					if($rpp_[0] == "action"){
						$action = $rpp_[1];
						continue;
					}
					else if($rpp_[0] == lcfirst(RLD_CONTAINER)){
						$container = $rpp_[1];
						continue;
					}
					else if($rpp_[0] == lcfirst(RLD_PATH_NAME_MIDDLEWARE)){
						if($middleware){
							$middleware = array_merge($middleware, explode(",", $rpp_[1]));
						}
						else{
							$middleware = explode(",", $rpp_[1]);
						}
						continue;
					}

					if($type == self::TYPE_SHELL){
						if($rpp_[0] == "shell"){
							$shell = $rpp_[1];
						}
					}
					else if($type == self::TYPE_PAGES){
						if($rpp_[0] == "controller"){
							$controller = $rpp_[1];
						}
					}
				}

				$buffer = [];
				
				if($type == self::TYPE_SHELL){
					$buffer["shell"] = $shell;
				}
				else if($type == self::TYPE_PAGES){
					$buffer["controller"] = $controller;
				}
				$buffer["action"] = $action;
				$buffer["container"] = $container;
				$buffer["middleware"] = $middleware;

				if($container){
					$buffer["paths"] = [
						"namespace" => RLD_PATH_SEPARATE_NAMESPACE . ucfirst(RLD_CONTAINER) . RLD_PATH_SEPARATE_NAMESPACE . ucfirst($container). RLD_PATH_SEPARATE_NAMESPACE . ucfirst(RLD_DEFNS),
						"rendering" => RLD_ROOT. RLD_PATH_SEPARATE . RLD_CONTAINER . RLD_PATH_SEPARATE . $container. RLD_PATH_SEPARATE. RLD_PATH_NAME_RENDERING,
					];
				}
				else{
					$buffer["paths"] = [
						"namespace" => str_replace(RLD_PATH_SEPARATE, RLD_PATH_SEPARATE_NAMESPACE, ucfirst(RLD_DEFNS)),
						"rendering" => RLD_PATH_RENDERING,
					];
				}

				$pages[$url][$method] = $buffer;
			}

		}

		return $pages;
	}

	/**
	* convertRoutingOptions
	* @param Array $pages
	*/
	private function convertRoutingOptions($pages, $type){

		$pages = $this->convertRoutingParse($pages, $type);
		
		foreach($pages as $url => $r_){

			foreach($r_ as $method => $rp_){

				if(!$rp_["container"]){
					continue;
				}

				if($type == self::TYPE_SHELL){
					$mode = "shell";
				}
				else if($type == self::TYPE_PAGES){
					$mode = "pages";
				}

				$containerRoutingFilePath = RLD_ROOT . RLD_PATH_SEPARATE . RLD_CONTAINER . RLD_PATH_SEPARATE . $rp_["container"] . RLD_PATH_SEPARATE . RLD_PATH_NAME_CONFIG . RLD_PATH_SEPARATE . "routing_". $mode .".php";
		
				if(!file_exists($containerRoutingFilePath)){
					continue;
				}
	
				$getRouting = require($containerRoutingFilePath);
	
				$getRouting = $this->convertRoutingPageScope($getRouting);
				$getRouting = $this->convertRoutingParse($getRouting, $type, $rp_);
	
				foreach($getRouting as $url2nd => $g__){
	
					foreach($g__ as $method2 => $gr_){

						$gr_["container"] = $rp_["container"];
					
						/*
						if($rp_["middleware"]){
							if(empty($gr_["middleware"])){
								$gr_["middleware"] = [];
							}

							Debug::out($gr_["middleware"]);

							//$gr_["middleware"] = array_merge($gr_["middleware"], $rp_["middleware"]);
						}
		*/

						if($url2nd == "/"){
							$pages[$url][$method2] = $gr_;
						}
						else{
							$pages[$url . $url2nd][$method2] = $gr_;
						}	
					}
				}
			}
		}

		return $pages;
	}

	/**
	* getRoute
	*/
	private function getRoute(){

		$phpself=dirname($_SERVER["PHP_SELF"]);
		$requestUrl=$_SERVER["REQUEST_URI"];

		if($phpself=="/"){
			$phpself="";
		}

		$root=str_replace($phpself,"",$requestUrl);
		$root=explode("?",$root);
		$root=$root[0];

		$host=$_SERVER["HTTP_HOST"];
		$protocol="http";
		if(!empty($_SERVER["HTTPS"])){
			$protocol="https";
		}

		$remoteIp=$_SERVER["REMOTE_ADDR"];
		if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$remoteIp=$_SERVER["HTTP_X_FORWARDED_FOR"];
		}

		$path=$phpself.$root;
		$url=$protocol.'://'.$host;

		$response=[
			'root'=>$root,
			'path'=>$path,
			'url'=>$url,
			'host'=>$host,
			"phpSelf"=>$phpself,
			'protocol'=>$protocol,
			'method' => $_SERVER["REQUEST_METHOD"],
			'port' => $_SERVER['SERVER_PORT'],
			'remoteIp' => $remoteIp,
			"paths"=>[
				"namespace" => RLD_DEFNS,
				"rendering" => RLD_PATH_RENDERING,
			]
		];

		return $response;
	}
	/**
	* getRouteCLI
	*/
	private function getRouteCLI($commandLine){

		$response=[
			'root'=>$commandLine,
			"paths"=>[
				"namespace" => RLD_DEFNS,
				"rendering" => RLD_PATH_RENDERING,
			]
		];

		return $response;
	}

	/**
	* searchRouting
	* @param string $type
	* @param Array $rootParams
	* @param Array $routingList
	*/
	private function searchRouting($type, $rootParams, $routingList){

		$root = $rootParams["root"];

		$roots=explode("/", $root);
		if(!end($roots)){
			array_pop($roots);
		}
		array_shift($roots);

		$passParams=[];
		$matrixA=[];
		$matrixB=[];
		if(!empty($routingList)){
			foreach($routingList as $url => $route){

				$url0 = str_replace("*","{:?}/{:?}/{:?}/{:?}/{:?}/{:?}/{:?}/{:?}/{:?}/{:?}/{:?}",$url);

				$urls=explode("/",$url0);
				array_shift($urls);
				
				$jugeA=true;
				foreach($urls as $ind=>$u_){
					if(empty($roots[$ind])){
						$roots[$ind]="";
					}
					if($u_!==$roots[$ind]){
						if(
							strpos($u_,"{")>0 ||
							strpos($u_,"?}")>0
						){
							if($roots[$ind]){
								if(empty($passParams[$url])){
									$passParams[$url]=[];
								}
								$passParams[$url][]=$roots[$ind];
							}
						}
						else if(
							strpos($u_,"{")>0 ||
							strpos($u_,"}")>0
						){
							if($roots[$ind]){
								if(empty($passParams[$url])){
									$passParams[$url]=[];
								}
								$passParams[$url][]=$roots[$ind];
							}

							if(!$roots[$ind]){
								$jugeA=false;
							}
						}

						else{
							$jugeA=false;
						}
					}
				}

				$jugeB=true;
				foreach($roots as $ind=>$r_){
					if(empty($urls[$ind])){
						$urls[$ind]="";
					}
					if($urls[$ind]!==$r_){
						if(
							strpos($urls[$ind],"{")>0 ||
							strpos($urls[$ind],"?}")>0
						){

						}
						else if(
							strpos($urls[$ind],"{")>0 ||
							strpos($urls[$ind],"}")>0
						){
							if(!$r_){
								$jugeB=false;
							}
						}
						else{
							$jugeB=false;
						}
					}
				}

				$matrixA[$url]=$jugeA;
				$matrixB[$url]=$jugeB;

			}
		}

		$output=null;

		$confirmPassParams=null;
		foreach($matrixA as $url=>$ma_){
			if($ma_ && $ma_==$matrixB[$url]){
				$output=$routingList[$url];
				if(!empty($passParams[$url])){
					$confirmPassParams=$passParams[$url];
				}
				else{
					$confirmPassParams=null;
				}
			}
		}

		$output2 = null;

		if(is_array($output)){

			if($type == self::TYPE_PAGES){

				foreach($output as $method => $o_){
					if($method == "_"){
						$output2 = $o_;
					}
					else{
						if(strtolower($rootParams["method"]) == strtolower($method)){
							$output2 = $o_;
							break;
						}	
					}
				}
			}
			else if($type == self::TYPE_SHELL){
				$output2 = $output["_"];
			}
		}
		
		if($output2){
			$output2 = array_merge($rootParams, $output2);
			return $output2;	
		}
		else{	
			return $rootParams;	
		}
	}

	/**
	 * getRouteErrorClass
	 * @param $type
	 * @param $defaultRootParam
	 * @param $exception
	 * @param $rootParams
	 * @param $routingList
	 */
	private function getRouteErrorClass($type, $defaultRootParam, $exception, $rootParams, $routingList){

		$errorExceptionName = get_class($exception);

		if(!empty($routingList["error"])){
			$errorList = $routingList["error"];
		}

		if(!empty($routingList["errorScope"])){

			$errorRoute=$errorList["/"];

			$roots=explode("/",$rootParams["root"]);
			array_shift($roots);
			
			foreach($errorList as $scope=>$e_){
				$scopes=explode("/",$scope);
				array_shift($scopes);

				$juge=true;
				foreach($scopes as $ind=>$s_){
					if(empty($roots[$ind])){
						$roots[$ind]="";
					}
					if($s_!=$roots[$ind]){
						$juge=false;
					}
				}

				if($juge){
					$errorRoute=$e_;
				}
			}
		}
		else{	
			if(!empty($errorList)){
				$errorRoute=$errorList;
			}
		}

		if(!empty($errorRoute)){

			$confirmErrorRoute = $errorRoute['Exception'];

			if(!empty($errorRoute[$errorExceptionName])){
				$confirmErrorRoute=$errorRoute[$errorExceptionName];
			}
	
			if($defaultRootParam){
				foreach($defaultRootParam as $key => $val){
					$confirmErrorRoute[$key] = $val;
				}	
			}

			return $confirmErrorRoute;
		}
	}	
}