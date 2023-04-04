<?php

namespace Reald\Services;

class Encrypt{

	public const MODE_SERIALIZE = 1;
	public const MODE_JSON = 1;

	public $mode = self::MODE_SERIALIZE;

	public $encAlgolizum = "aes-256-cbc";
	public $encSalt = "ABCDEFG123456**************************";
	public $encPassword = "password123456789******************";
	
	public $hashAlgolizum = "sha256";
	public $hashSalt = "123456789ABC*****************";
	public $hashStretch = 5;


	/**
	 * encode
	 * @param any $data
	 * @param Array $option = null
	 */
	public function encode($data, $option = null){

		$data = $this->_convert_encode($data);

		$option = $this->_setOption($option);

		$ivLength = openssl_cipher_iv_length($option["encAlgolizum"]);
		$iv = substr($option["encSalt"],1,$ivLength);
		$options = 0;

		//encodeing...
		$encrypted = openssl_encrypt($data, $option["encAlgolizum"], $option["encPassword"], $options, $iv);

		if(!empty($option["binaryOutput"])){
			$encrypted = base64_decode($encrypted);
		}

		return $encrypted;
	}

	/**
	 * encAuto
	 * @param Array $data
	 */
	public function encAuto($data){

		$data = $this->_convert_encode($data);

		$option = $this->_setOption();

		$option["encSalt"] = $this->hash(uniqId().date("YmdHis"));

		$option["encPassword"] = $this->hash($option["encSalt"] . $option["encPassword"]);

		$res=$this->encode($data, $option);

		return [
			"result"=>$res,
			"salt"=>$option["encSalt"],
			"password"=>$option["encPassword"],
		];

	}

	/**
	 * decode
	 * @param aby $data
	 * @param Array $option = null
	 */
	public function decode($data,$option = null){

		$option = $this->_setOption($option);

		$ivLength = openssl_cipher_iv_length($option["encAlgolizum"]);
		$iv = substr($option["encSalt"],1,$ivLength);
		$options = 0;

		if(!empty($option["binaryOutput"])){
			$data = base64_encode($data);
		}

		//decode
		$decrypted = openssl_decrypt($data, $option["encAlgolizum"], $option["encPassword"], $options, $iv);

		$output = $this->_convert_decode($decrypted);
		
		return $output;
	}

	/**
	 * hash
	 * @param any $data
	 * @param Array $option
	 */
	public function hash($data,$option = null){

		$algolizum = $this->hashAlgolizum;
		if(!empty($option["algolizum"])){
			$algolizum=$option["algolizum"];
		}

		$salt = $this->hashSalt;
		if(!empty($option["salt"])){
			$salt = $option["salt"];
		}

		$stretch = $this->hashStretch;
		if(!empty($option["stretch"])){
			$stretch = $option["stretch"];
		}

		$hash = $this->_convert_encode($data);

		for($n = 0 ; $n < $stretch ; $n++){
			$hash = hash($algolizum,$hash.$salt);
		}

		return $hash;
	}
	
	/**
	 * (private) _setOption
	 */
	public function _setOption($option=null){

		if(empty($option["encAlgolizum"])){
			$option["encAlgolizum"] = $this->encAlgolizum;
		}

		if(empty($option["encSalt"])){
			$option["encSalt"] = $this->encSalt;
		}

		if(empty($option["encPassword"])){
			$option["encPassword"] = $this->encPassword;
		}

		return $option;
	}

	/**
	 * _convert_encode
	 * @param any $data;
	 * @return $res
	 */
	private function _convert_encode($data){

		if($this->mode == self::MODE_SERIALIZE){
			$res = serialize($data);
		}
		else if($this->mode == self::MODE_JSON){
			$res = json_encode($data);
		}

		return $res;
	}

	/**
	 * _convert_decode
	 * @param any $data;
	 * @return $res
	 */
	private function _convert_decode($data){

		if($this->mode == self::MODE_SERIALIZE){
			$res = unserialize($data);
		}
		else if($this->mode == self::MODE_JSON){
			$res = json_decode($data, true);
		}

		return $res;
	}
}