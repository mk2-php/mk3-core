<?php

namespace Reald\Services;

use Reald\Core\Request;

// use mk2\backpack_token\TokenBackpack;

class FormUi{

	private $methodMode;
	private $_errorValues = null;

	/**
	 * __construct
	 */
	public function __construct(){

        /*
		if(!empty($this->alternativeTokenBackpack)){
			$this->TokenBackpack = new $this->alternativeTokenBackpack();
		}
		else{
			$this->TokenBackpack = new TokenBackpack();
		}
        */
	}

	/**
	 * setError
	 * @param $errorValue
	 */
	public function setError($errorValues){

		if(gettype($errorValues) == "object"){
			$errorValues = $errorValues->toArray();
		}

		$this->_errorValues = $errorValues;
	}

	/**
	 * verify
	 */
    /*
	public function verify(){

		$requestData = $this->Request->data()->get();
		
		if(
			empty($requestData["_tname"]) ||
			empty($requestData["_token"])
		){
			return false;
		}

		$juge = $this->TokenBackpack->verify($requestData["_tname"],$requestData["_token"]);

		if(!$juge){
			return false;
		}

		$this->Request->data()->delete([
			"_tname",
			"_token"
		]);

		return true;
	}*/

	/**
	 * start
	 * @param Array $option Form tag attribute option
	 */
	public function start($option = null){

		if(empty($option["method"])){
			$option["method"] = "post";
		}

		if(!empty($option["onFile"])){
			$option["enctype"] = "multipart/form-data";
		}

		$str  = '<form'.$this->_convertOptionString($option).'>';

		$this->methodMode = $option["method"];

		return $str;
	}

	/**
	 * end
	 */
	public function end(){
		$this->methodMode = null;
		echo '</form>';
	}

	/**
	 * input
	 * @param String $name name attribute
	 * @param Array $option input tag attribute options.
	 */
	public function input($name,$option = null){

		if($name){
			$option["name"] = $this->_convertName($name);
		}

		if(empty($option["type"])){
			$option["type"] = "text";
		}

		if(!(
			$option["type"] == "radio" || 
			$option["type"] == "checkbox"
		)){
			if($this->_existRequest()){

				$getValue = $this->_getValue($name);

				if(isset($getValue)){
					if(empty($option["valueLocked"])){
						$option["value"] = $getValue;
					}
				}	
			}
		}

		$str = '<input'.$this->_convertOptionString($option).'>';

		return $str;
	}

	/**
	 * hidden
	 * @param String $name name attribute
	 * @param String $value default value
	 * @param Array $option input tag attribute options.
     * @return String tag output
	 */
	public function hidden($name, $value, $option = null){

		if(!$option){
			$option = [];
		}

		$option["type"] = "hidden";
		$option["value"] = $value;

		return $this->input($name, $option);
	}

	/**
	 * password
	 * @param String $name
	 * @param Array $option
     * @return String tag output
	 */
	public function password($name, $option = null){

		if(!$option){
			$option = [];
		}

		$option["type"] = "password";

		return $this->input($name,$option);
	}

	/**
	 * number
	 * @param String $name
	 * @param Array $option
     * @return String tag output
	 */
	public function number($name, $option = null){

		if(!$option){
			$option = [];
		}

		$option["type"] = "number";

		return $this->input($name,$option);
	}

	/**
	 * date
	 * @param String $name
	 * @param Array $option
     * @return String tag output
	 */
	public function date($name, $option = null){

		if(!$option){
			$option = [];
		}

		$option["type"] = "date";

		return $this->input($name,$option);
	}

	/**
	 * color
	 * @param String $name
	 * @param Array $option
     * @return String tag output
	 */
	public function color($name, $option = null){

		if(!$option){
			$option = [];
		}

		$option["type"] = "color";

		return $this->input($name,$option);
	}

	/**
	 * textarea
	 * @param String $name
	 * @param Array $option  =  null
	 */
	public function textarea($name,$option = null){
		
		$option["name"] = $this->_convertName($name);

		$value = null;
		if(!empty($option["value"])){
			$value = $option["value"];
			unset($option["value"]);
		}

		if($this->_existRequest()){
			$getValue = $this->_getValue($name);
			if(isset($getValue)){
				$value = $getValue;
			}
		}

		$str = '<textarea'.$this->_convertOptionString($option).'>'.$value.'</textarea>';

		return $str;
	}

	/**
	 * select
	 * @param $name
	 * @param $select
	 * @param $option  =  null
	 */
	public function select($name,$select,$option = null){

		$option["name"] = $this->_convertName($name);

		$value = null;
		if(isset($option["value"])){
			$value = (string)$option["value"];
			unset($option["value"]);
		}

		if($this->_existRequest()){
			$getValue = $this->_getValue($name);
			if(isset($getValue)){
				$value = $getValue;
			}
		}

		$optionTagStr = "";

		if(!empty($option["empty"])){
			$optionTagStr .= '<option value = "">'.$option["empty"].'</option>';
		}

		foreach($select as $key => $val){
			if(is_array($val)){
				$optionTagStr .=  '<optgroup label = "'.$key.'">';
				foreach($val as $key2 => $val2){
					$selected = "";
					if($value){
						if($value == $key2){
							$selected = 'selected';
						}	
					}
					else{
						if($value === "0" && (string)$key2 === "0"){
							$selected = 'selected';
						}
						else if($value === null && $key2 === null){
							$selected = 'selected';
						}
					}
					$optionTagStr .= '<option value = "'.$key2.'" '.$selected.'>'.$val2.'</option>';	
				}
				$optionTagStr .=  '</optgroup>';
			}
			else{
				$selected = "";
				if($value){
					if($value == $key){
						$selected = 'selected';
					}	
				}
				else{
					if($value === "0" && (string)$key === "0"){
						$selected = 'selected';
					}
					else if($value === null && $key === null){
						$selected = 'selected';
					}
				}
				$optionTagStr .= '<option value = "'.$key.'" '.$selected.'>'.$val.'</option>';	
			}
		}

		$str = '<select'.$this->_convertOptionString($option).'>'.$optionTagStr.'</select>';

		return $str;
	}

	/**
	 * radio
	 * @param $name
	 * @param $radio
	 * @param $option
	 * @param $labelOption
	 * @param $divStrStart
	 * @param $divStrEnd
	 */
	public function radio($name, $radio, $option = null, $labelOption = null, $divStrStart = null, $divStrEnd = null){

		$option["name"] = $this->_convertName($name);

		$value = null;
		if(isset($option["value"])){
			$value = $option["value"];
			unset($option["value"]);
		}

		if($this->_existRequest()){
			$getValue = $this->_getValue($name);
			if(isset($getValue)){
				$value = $getValue;
			}
		}
		
		$str = "";
		$ind = 0;
		foreach($radio as $key => $val){

			$radioId = 'radio.'.$name.'.'.$ind;

			$radioOpt = [
				"type" => "radio",
				"value" => $key,
				"id" => $radioId,
			];
			
			if($value !== null){
				if($value == $key){
					$radioOpt['checked'] = "checked";
				}	
			}

			if($option){
				foreach($option as $key => $o_){
					$radioOpt[$key] = $o_;
				}	
			}

			$buffStr = $this->input($name, $radioOpt);

			$labelStr = $this->_convertOptionString($labelOption);

			$buffStr .= '<label for = "'.$radioId.'" ' . $labelStr . '>'.$val.'</label>';

			if($divStrStart && $divStrEnd){
				$str .= $divStrStart. $buffStr . $divStrEnd;
			}
			else{
				$str .= $buffStr;
			}

			$ind++;
		}

		return $str;
	}

	/**
	 * agree
	 * @param $name
	 * @param $label
	 * @param $option  =  null
	 */
	public function agree($name, $label, $option = null){
		
		$id  =  "agree_" . $name;

		if(!$option){
			$option = [];
		}

		$option["id"] = $id;

		$value = null;
		if(isset($option["value"])){
			$value = $option["value"];
			unset($option["value"]);
		}

		if(intval($value)){
			$option["checked"] = "checked";			
		}

		if($this->_existRequest()){
			$getValue = $this->_getValue($name);
			if(isset($getValue)){
				if($getValue){
					$option["checked"] = "checked";
				}
			}
		}

		$option['type'] = "checkbox";
		$option['value'] = 1;

		$str  =  $this->hidden($name,0,[
			"valueLocked" => true,
		]);

		$str  .=  $this->input($name,$option);
		$str  .= '<label for = "'.$id.'">'.$label.'</label>';
		
		return $str;
	}

	/**
	 * checkbox
	 * @param $name
	 * @param $checkbox
	 * @param $option
	 * @param $labelOption
	 * @param $divStrStart
	 * @param $divStrEnd
	 */
	public function checkbox($name, $checkbox = null, $option = null, $labelOption = null, $divStrStart = null, $divStrEnd = null){

		if(!$checkbox){
			$checkbox = 1;
		}

		if(!is_array($checkbox)){
			$checkbox = [$checkbox => ""];
		}

		$searchName = $this->_convertName($name);
		if(empty($option["onecheck"])){
			$name = $this->_convertName($name)."[]";
		}

		$value = null;
		if(isset($option["value"])){
			$value = $option["value"];
			unset($option["value"]);
		}

		if($this->_existRequest()){
			$getValue = $this->_getValue($searchName);
			if($getValue){
				$value = $getValue;
			}
		}

		$str = "";
		$ind = 0;
		foreach($checkbox as $key => $val){

			$checkboxId = 'checkbox.'.$name.'.'.$ind;

			$checkboxOpt = [
				"type" => "checkbox",
				"value" => $key,
				"id" => $checkboxId,
			];

			if($value !== null){

				if(!is_array($value)){
					$value = [$value];
				}

				foreach($value as $v_){
					if($v_ == $key){
						$checkboxOpt['checked'] = "checked";
					}
				}
			}

			if($option){
				foreach($option as $key => $o_){
					$checkboxOpt[$key] = $o_;
				}	
			}

			$buffStr = $this->input($name, $checkboxOpt);

			$labelStr = $this->_convertOptionString($labelOption);
			
			$buffStr .= '<label for = "'.$checkboxId.'" '. $labelStr. '>'.$val.'</label>';

			if($divStrStart && $divStrEnd){
				$str .= $divStrStart. $buffStr . $divStrEnd;
			}
			else{
				$str .= $buffStr;
			}

			$ind++;
		}

		return $str;

	}

	/**
	 * checkboxOne
	 * @param $name
	 * @param $checkbox
	 * @param $option
	 * @param $labelOption
	 * @param $divStrStart
	 * @param $divStrEnd
	 */
	public function checkboxOne($name,  $checkbox = null, $option = null, $labelOption = null, $divStrStart = null, $divStrEnd = null){

		if(!$option){
			$option = [];
		}

		$option["onecheck"] = true;

		return $this->checkbox($name, $checkbox, $option, $labelOption, $divStrStart, $divStrEnd);
	}

	/**
	 * file
	 * @param $name
	 * @param $option  =  null
	 */
	public function file($name,$option = null){

		$option["type"] = "file";

		$name = $name . "[]";

		return $this->input($name,$option);
	}
	
	/**
	 * button
	 * @param $value
	 * @param $option  =  null
	 */
	public function button($value,$option = null){

		$option["type"] = "button";
		$option["value"] = $value;

		return $this->input(null,$option);
	}

	/**
	 * submit
	 * @param $value
	 * @param $option  =  null
	 */
	public function submit($value,$option = null){

		$option["type"] = "submit";
		$option["value"] = $value;

		return $this->input(null,$option);
	}

	/**
	 * reset
	 * @param $value
	 * @param $option  =  null
	 */
	public function reset($value,$option = null){

		$option["type"] = "reset";
		$option["value"] = $value;

		return $this->input(null,$option);
	}

	/**
	 * error
	 * @param string $name
	 * @param $option  =  null
	 */
	public function error($name, $option = null){

		if(!$option){
			$option = [];
		}

		if(!empty($this->_errorValues[$name])){

			$verror = $this->_errorValues[$name];

			if(is_array($verror)){
				$verror = join("<br>", $verror);
			}

			$optStr = $this->_convertOptionString($option);

			$str = '<div class="error" ' . $optStr .'>';
			
			if(!empty($option["allOutput"])){
				foreach($verror as $ind => $v_){
					$str .= $v_;
					if($ind){
						$str .= "<br>";
					}
				}	
			}
			else{
				$str .= $verror;
			}

			$str .= "</div>";

			return $str;
		}

	}

	/**
	 * tagToken
	 * @param string $tokenName
	 * @param $option  =  null
	 */
    /*
	public function tagToken($tokenName,$option = null){

		if(!$option){
			$option = [];
		}

		$token = $this->TokenBackpack->set($tokenName);

		$option["type"] = "hidden";
		$option["value"] = $tokenName;

		$str = $this->input("_tname",$option);

		$option["value"] = $token;
		$str .= $this->input("_token",$option);

		return $str;
	}*/

	/**
	 * _convertName
	 * @param $name
	 */
	private function _convertName($name){

		$names = explode(".",$name);

		if(count($names) == 1){
			return $name;
		}
		else{
			$newName = "";
			foreach($names as $ind => $n_){
				if($ind>0){
					$newName .= '['.$n_.']';
				}
				else{
					$newName .= $n_;
				}
			}

			return $newName;
		}

	}

	/**
	 * _convertOptionString
	 * @param $option  =  null
	 */
	private function _convertOptionString($option = null){

		if(!$option){
			return;
		}

		$str = "";
		foreach($option as $key => $val){
			$str .= ' '.$key.' = \''.$val.'\'';
		}

		return $str;
	}

	/**
	 * _existRequest
	 */
	private function _existRequest(){

		if($this->_getRequestData()){
			return true;
		}
		
		return false;
	}

	/**
	 * _getValue
	 * @param $name
	 */
	private function _getValue($name){
		
		$getData = $this->_getRequestData();

		$names = explode(".",$name);

		$value = null;
		foreach($names as $n_){
			if(isset($getData[$n_])){
				$value = $getData[$n_];
				$getData = $getData[$n_];
			}
			else{
				$value = null;
			}
		}

		return $value;
	}

	/**
	 * _getRequestData
	 */
	private function _getRequestData(){

		$getData = null;
        
		if(strtoupper($this->methodMode) == Request::METHOD_QUERY){
			$getData = Request::query()->all();
		}
		else if(strtoupper($this->methodMode) == Request::METHOD_POST){
			$getData = Request::post()->all();
		}
		else if(strtoupper($this->methodMode) == Request::METHOD_PUT){
			$getData = Request::put()->all();
		}
		else if(strtoupper($this->methodMode) == Request::METHOD_DELETE){
			$getData = Request::delete()->all();
		}

		return $getData;
	}
}