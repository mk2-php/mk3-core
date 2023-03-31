<?php

namespace Reald\Services;

use Reald\Core\Request;

class BootstrapFormUi extends FormUi{

	public function input($name, $option = null){

		if(!$option){
			$option = [];
		}

		if(empty($option["type"])){
			$option["type"] = "text";
		}
			
		if(!(
			$option["type"] == "radio" || 
			$option["type"] == "checkbox" ||
			$option["type"] == "submit" ||
			$option["type"] == "button" ||
			$option["type"] == "reset" || 
			$option["type"] == "image"
		)){
			if(empty($option["class"])){
				$option["class"] = "";
			}
			$option["class"] .= " form-control";
		}
		
		
		return parent::input($name, $option);
	}
	
	public function select($name,$select,$option = null){

		if(!$option){
			$option = [];
		}

		if(empty($option["class"])){
			$option["class"] = "";
		}
		$option["class"] .= " form-control";

		return parent::select($name, $select, $option);
	}

	public function textarea($name, $option = null){
		if(!$option){
			$option = [];
		}

		if(empty($option["type"])){
			$option["type"] = "text";
		}
			
			if(!(
				$option["type"] == "radio" || 
				$option["type"] == "checkbox"
			)){
				if(empty($option["class"])){
					$option["class"] = "";
				}
				$option["class"] .= " form-control";
			}
		
		
		return parent::textarea($name, $option);
	}

	public function radio($name, $radio, $option = null, $labelOption = null, $divStrStart = null, $divStrEnd = null){
		if(!$option){
			$option = [];
		}
		if(!$labelOption){
			$labelOption = [];
		}

		if(empty($option["class"])){
			$option["class"] = "";
		}
		$option["class"] .= " form-check-input"; 

		if(empty($labelOption["class"])){
			$labelOption["class"] = "";
		}
		$labelOption["class"] .= " form-check-label"; 

		$divStrStart = "<div class=\"form-check-inline\">";
		$divStrEnd = "</div>";

		return parent::radio($name, $radio, $option, $labelOption, $divStrStart, $divStrEnd);
	}


	public function checkbox($name, $checkbox, $option = null, $labelOption = null, $divStrStart = null, $divStrEnd = null){
		if(!$option){
			$option = [];
		}
		if(!$labelOption){
			$labelOption = [];
		}

		if(empty($option["class"])){
			$option["class"] = "";
		}
		$option["class"] .= " form-check-input"; 

		if(empty($labelOption["class"])){
			$labelOption["class"] = "";
		}
		$labelOption["class"] .= " form-check-label"; 

		$divStrStart = "<div class=\"form-check-inline\">";
		$divStrEnd = "</div>";

		return parent::checkbox($name, $checkbox, $option, $labelOption, $divStrStart, $divStrEnd);
	}

}