<?php

namespace Reald\Services;

use Reald\Services\Encrypt;

class Session{

	private $Encrypt;
	
    //Ex. RLD_PATH_APP_TEMPORARY."session";
	public $tmpPath = null;

    /**
     * Session unique name
     * 
     * when other systems are configured with the same "Reald"
     * It is recommended to change this value to prevent mixing of session information.
     */
	public $name = "rldss";

    // sessionId keep limit
	public $ssidLimit = 10800;	

    /**
     * session data encrypt algolizums
     * 
     * Encrypt session data early.
     * Encryption salt or password can be changed to make it even stronger.
     */
	public $encrypt = [
		"encAlgolizum" => "aes-256-cbc",
		"encSalt" => "mk2session123456789********************************",
		"encPassword" => "mk2sessionpassword123456789*****************************",
	];
	
	/**
	 * __construct
	 */
	public function __construct(){

        $this->Encrypt = new Encrypt;

		if(!empty($this->tmpPath)){
			if(!is_dir($this->tmpPath)){
				@mkdir($this->tmpPath,0775,true);
			}
			@session_save_path($this->tmpPath);
		}

		@session_start();
	}

	/**
	 * write
     * 
     * Register session information.
     * 
	 * @param String $name
	 * @param String $value
	 */
	public function write($name,$value){

		$source=$this->read();

		if($name){
			$source[$name] = $value;
		}
		else
		{
			$source = $value;
		}

		if(!empty($this->ssidLimit)){
			$nowUnix = date_format(date_create("now"),"YmdHis");
			$source["__limit"] = date_format(date_create("+".$this->ssidLimit." seconds"),"YmdHis");
		}

		if(!empty($this->encrypt)){
			$source = $this->Encrypt->encode($source,$this->encrypt);
		}

		$_SESSION[$this->name] = $source;

		return $this;
	}

	/**
	 * (private)_write
	 * @param $source
	 */
	private function _write($source){

		if(!empty($this->ssidLimit)){
			$nowUnix = date_format(date_create("now"),"YmdHis");
			$source["__limit"] = date_format(date_create("+".$this->ssidLimit." seconds"),"YmdHis");
		}

		if(!empty($this->encrypt)){
			$source = $this->Encrypt->encode($source,$this->encrypt);
		}

		$_SESSION[$this->name] = $source;
	}

	/**
	 * read
	 * @param string $name = null
	 */
	public function read($name = null){

		if(!empty($_SESSION[$this->name])){
			$source = $_SESSION[$this->name];
		}
		else
		{
			return null;
		}

		if(!empty($this->encrypt)){
			$source = $this->Encrypt->decode($source,$this->encrypt);
		}

		if(!empty($this->ssidLimit)){

			$before_time = 0;
			$getUnix = date_format(date_create("now"),"YmdHis");
			if(!empty($source["__limit"])){
				$before_time = date_format(date_create($source["__limit"]),"YmdHis");
			}

			if(intval($getUnix)>intval($before_time)){
				@session_regenerate_id(true);
				$this->_write($source);
				$source["__limit"] = date_format(date_create("+".$this->ssidLimit." seconds"),"YmdHis");
			}

		}

		if(empty($opt["on_limit"])){
			unset($source["__limit"]);
		}
		if(!empty($opt["on_ssid"])){
			$source["__ssid"] = session_id();
		}

		if($name){
			if(!empty($source[$name])){
				$output = $source[$name];
			}
			else
			{
				return null;
			}
		}
		else
		{
			$output = $source;
		}

		return $output;
	}

	/**
	 * flash
     * 
     * After getting the specified session data, it will be deleted automatically.
     * 
	 * @param String $name
     * @return String session data 
	 */
	public function flash($name){
		$output = $this->read($name);
		$this->delete($name);

		return $output;
	}

	/**
	 * delete
     * 
     * deletes the specified session data.
     * 
	 * @param String $name
	 */
	public function delete($name=null){

		$source = $this->read();

		if($name){
			if(!empty($source[$name])){
				unset($source[$name]);
			}
			$this->write(null,$source);
		}
		else
		{
			if(!empty($_SESSION[$this->name])){
				unset($_SESSION[$this->name]);
			}
		}
	}

	/**
	 * getLimit
     * 
     * Get session data retention period.

     * @return String Session data retention period
	 */
	public function getLimit(){
		$limit = $this->read("__limit",array(
			"on_limit"=>true,
		));
		return $limit;
	}

	/**
	 * getSSID
     * 
     * Get SSID for access to session information
     * 
     * @return String SSID
	 */
	public function getSSID(){
		return session_id();
	}

	/**
	 * changeSSID
     * 
     * Update SSID for access to session information
     * 
     * @return Void
	 */
	public function changeSSID(){
		session_regenerate_id(true);
	}

}