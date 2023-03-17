<?php
/**
 * ===================================================
 * 
 * PHP FW - Mk3 -
 * Routing
 * 
 * Object class for initial operation.
 * 
 * URL : 
 * Copylight : Masato-Nakatsuji 2023.
 * 
 * ===================================================
 */

namespace Mk3\Core;

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

		$routingList=$this->convertRouting(self::TYPE_SHELL);

		$response=$this->searchRouting(self::TYPE_SHELL, ["root"=>$commandLine], $routingList);

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

		$routings=Config::get("config.routing.".$type);

		$routings["release"] = $this->convertRoutingPageScope($routings["release"]);
		$routings["release"] = $this->convertRoutingModules($routings["release"]);

		return $routings;
	}

	/**
	* convertRoutingPageScope
	* @param Array $pages
	*/
	private function convertRoutingPageScope($pages){

		$buffer=[];
		foreach($pages as $scope => $rp_){

			if(is_array($rp_)){
				foreach($rp_ as $url => $rpp_){

					if($scope == "/"){
						$buffer[$url] = $rpp_;
					}
					else{
						if($url == "/"){
							$buffer[$scope] = $rpp_;
						}
						else{
							$buffer[$scope.$url]=$rpp_;
						}
					}
				}	
			}
			else{
				$buffer[$scope]=$rp_;
			}
		}
		return $buffer;
	}

	/**
	* convertRoutingModules
	* @param Array $pages
	*/
	private function convertRoutingModules($pages){

		foreach($pages as $url => $rp_){

			if(strpos($rp_, MK3_CONTAINER . "=") === false){
				continue;
			}

			$moduleName = str_replace(MK3_CONTAINER . "=", "", $rp_);

			$routingFilePath = MK3_ROOT . MK3_PATH_SEPARATE . MK3_CONTAINER . MK3_PATH_SEPARATE . lcfirst($moduleName) . "/routing/pages.php";

			if(!file_exists($routingFilePath)){
				unset($pages[$url]);
				continue;
			}

			$getRouting = require($routingFilePath);

			$getRouting["release"] = $this->convertRoutingPageScope($getRouting["release"]);

			foreach($getRouting["release"] as $url2nd => $gr_){
				if($url2nd == "/"){
					$pages[$url] = MK3_CONTAINER. "=" . $moduleName . "," . $gr_;
				}
				else{
					$pages[$url . $url2nd] = MK3_CONTAINER ."=" . $moduleName . "," .$gr_;
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
			'method'=>$_SERVER["REQUEST_METHOD"],
			'port'=>$_SERVER['SERVER_PORT'],
			'remoteIp'=>$remoteIp,
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
		if(!empty($routingList["release"])){
			foreach($routingList["release"] as $url=>$route){

				$url0=str_replace("*","{:?}/{:?}/{:?}/{:?}/{:?}/{:?}/{:?}/{:?}/{:?}/{:?}/{:?}",$url);

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
				$output=$routingList["release"][$url];
				if(!empty($passParams[$url])){
					$confirmPassParams=$passParams[$url];
				}
				else{
					$confirmPassParams=null;
				}
			}
		}

		if(is_array($output)){
			$output2=null;

			$method=strtolower($rootParams["method"]);

			if(!empty($output[$method])){
				$output2=$output[$method];
			}
			else{
				$output2=$output;
			}

			if($output2){
				$output=$output2;
			}
			else{
				$output=null;
			}
		}

		if($output){
			
			$output = $this->convertResponse($type,$output,$confirmPassParams);

			if(!$output){
				return;
			}

			$output=array_merge($output,$rootParams);
			return $output;	
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

		$errorExceptionName=get_class($exception);

		if(!empty($routingList["error"])){
			$errorList=$routingList["error"];
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
			$confirmErrorRoute=$errorRoute['Exception'];
			if(!empty($errorRoute[$errorExceptionName])){
				$confirmErrorRoute=$errorRoute[$errorExceptionName];
			}
	
			$confirmErrorRoute=$this->convertResponse($type,$confirmErrorRoute);

			foreach($defaultRootParam as $key=>$val){
				$confirmErrorRoute[$key]=$val;
			}
			return $confirmErrorRoute;
	
		}

	}

	/**
	 * convertResponse
	 * @param $type
	 * @param $result
	 * @param $passParams = null
	 */
	private function convertResponse($type,$result,$passParams=null){

		if(is_array($result)){
			if(!empty($result["middleware"])){
				$middleware = $result["middleware"];
			}
			if(!empty($result[0])){
				$result = $result[0];
			}
		}

		if(is_array($result)){
			return null;
		}

		$module = null;
		if(strpos($result, MK3_CONTAINER . "=") > -1){
			$module = substr($result, strpos($result, MK3_CONTAINER . "=") + strlen(MK3_CONTAINER . "="));
			$module = substr($module, 0, strpos($module,","));
			$result = str_replace(MK3_CONTAINER . "=" . $module. ",","",$result);
		}

		$result = explode("@",$result);

		if($type==self::TYPE_PAGES){
			$classType="controller";
		}
		else if($type==self::TYPE_SHELL){
			$classType="shell";
		}

		$output=[
			$classType => $result[0],
			"action" => $result[1],
		];

		$output["request"]=$passParams;

		if(!empty($middleware)){
			$output["middleware"]=$middleware;
		}
		
		if(strpos($result[0], MK3_PATH_SEPARATE_NAMESPACE . ucfirst(MK3_CONTAINER)) > -1){
			// get module name
			$moduleName=explode(MK3_PATH_SEPARATE_NAMESPACE, $result[0]);
			$output["module"]=$moduleName[2];
		}

		if($module){

			$output["paths"] = [
				"namespace" => MK3_PATH_SEPARATE_NAMESPACE . ucfirst(MK3_CONTAINER) . MK3_PATH_SEPARATE_NAMESPACE . ucfirst($module). MK3_PATH_SEPARATE_NAMESPACE . ucfirst(MK3_DEFNS),
				"rendering" => MK3_ROOT. MK3_PATH_SEPARATE . MK3_CONTAINER . MK3_PATH_SEPARATE . $module. MK3_PATH_SEPARATE. MK3_PATH_NAME_RENDERING,
			];
		}
		else{

			$output["paths"] = [
				"namespace" => str_replace(MK3_PATH_SEPARATE, MK3_PATH_SEPARATE_NAMESPACE, ucfirst(MK3_DEFNS)),
				"rendering" => MK3_PATH_RENDERING,
			];
		}

		return $output;
	}

	
}