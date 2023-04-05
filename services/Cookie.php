<?php

namespace Reald\Services;

use Reald\Service\Encrypt;

class Cookie{

    public $encryptClass = "Reald\Services\Encrypt";

	public $name = "rld_cookie";
	public $limit = 30;
	public $encrypt =[ 
		"encAlgolizum" => "aes-256-cbc",
		"encSalt" => "rldcookiesalt123456789********************************************",
		"encPassword" => "rldcookiepassword123456789*****************************************",
	];
	public $path = "";
	public $domain = "";
	public $secure = "";

	/**
	 * __construct
	 */
	public function __construct(){
        $this->Encrypt = new $encryptClass;
	}

	/**
	 * write
	 * @param string $name
	 * @param $value
	 */
	public function write($name,$value,$option=array()){

		if(!empty($this->name)){
			$cookie_name = $this->name.$name;
		}
		else
		{
			$cookie_name = $name;
		}

		$value=$this->Encrypt->encode($value, $this->encrypt);

		if(is_array($value)){
			$value = json_encode($value);
		}
	
		if(!empty($option["limit"])){

			if($option["limit"] == "no"){
				$limit = 0;
			}
			else
			{
				$limit = time()+$option["limit"];
			}
		}
		else
		{
			$limit = time()+$this->limit;
		}

		if(!empty($option["path"])){
			$path = $option["path"];
		}
		else
		{
			if(!empty($this->path)){
				$path = $this->path;
			}
			else
			{
				$path = "/";
			}
		}

		if(!empty($option["domain"])){
			$domain = $option["domain"];
		}
		else
		{
			if(!empty($this->domain)){
				$domain = $this->domain;
			}
		}

		if(!empty($option["secure"])){
			$secure=$option["secure"];
		}
		else
		{
			if(!empty($this->secure)){
				$secure = $this->secure;
			}
		}

		setcookie($cookie_name, $value, @$limit, $path, @$domain, @$secure);
	}

	/**
	 * read
	 * @param string $name
	 */
	public function read($name=null){

		if(!empty($this->name)){
			$cookie_name=$this->name.$name;
		}
		else
		{
			$cookie_name=$name;
		}

		if(!empty($_COOKIE[$cookie_name])){
			$source=@$_COOKIE[$cookie_name];
		}
		else
		{
			return null;
		}

		$source=$this->Encrypt->decode($source,$this->encrypt);

		return $source;
	}

	/**
	 * delete
	 * @param string $name
	 * @param array $option = []
	 */
	public function delete($name,$option=[]){

		if(!empty($this->name)){
			$cookie_name=$this->name.$name;
		}
		else
		{
			$cookie_name=$name;
		}

		if(!empty($option["path"])){
			$path=$option["path"];
		}
		else
		{
			if(!empty($this->path)){
				$path=$this->path;
			}
			else
			{
				$path="/";
			}
		}

		if(!empty($option["domain"])){
			$domain=$option["domain"];
		}
		else
		{
			if(!empty($this->domain)){
				$domain=$this->domain;
			}
		}

		if(!empty($option["secure"])){
			$secure=$option["secure"];
		}
		else
		{
			if(!empty($this->secure)){
				$secure=$this->secure;
			}
		}

		setcookie($cookie_name,"",time()-1000,@$path,@$domain,@$secure);

		return;
	}

}