<?php
/**
 * ===================================================
 * 
 * PHP FW "Reald"
 * Controller
 * 
 * Basic Controller class.
 * 
 * URL : 
 * Copylight : Masato-Nakatsuji 2023.
 * 
 * ===================================================
 */

namespace Reald\Core;

class Controller{

	public $view = null;
	public $template = null;
	public $autoRender = false;

	public function __construct(){

		if(empty($this->view)){
			if(
				!empty(RequestRouting::$_params["controller"]) && 
				!empty(RequestRouting::$_params["action"])){	
					$this->view = RequestRouting::$_params["controller"]. "/". RequestRouting::$_params["action"];
			}
		}
	}

	/**
	 * setAutoRender
	 * @param $templateName 
	 */
	public function setAutoRender($juge){
		$this->autoRender = $juge;
		return $this;
	}

	/**
	 * setTemplate
	 * @param $templateName 
	 */
	public function setTemplate($templateName){
		$this->template = $templateName;
		return $this;
	}

	/**
	 * setTemplateParent
	 * @param $templateName 
	 */
	public function setTemplateParent($templateName){
		$this->templateParent = $templateName;
		return $this;
	}

	/**
	 * setView
	 * @param $view 
	 */
	public function setView($view){
		$this->view = $view;
		$this->viewParent = null;
		return $this;
	}

	public function setViewParent($view){
		$this->viewParent =$view;
		$this->view = null;
		return $this;
	}

	/**
	 * _rendering
	 */
	public function _rendering(){

		$useClass=Config::get("config.useClass");

		if(in_array("Render",$useClass)){

			$renderName="Render";
			$renderClassName='Reald\Core\\'.$renderName;
	
			if(!empty($this->RenderName)){
				$renderName=$this->RenderName."Render";
				$renderClassName = RLD_DEFNS_RENDER."\\".$renderName;
			}
	
			$render=new $renderClassName();

			if(method_exists($render,"handleBefore")){
				$render->handleBefore();
			}
			
			$render->render($this);

			if(method_exists($render,"handleAfter")){
				$render->handleAfter();
			}

		}
	}

	/**
	 * handleBefore
	 */
	public function handleBefore(){}

	/**
	 * handleAfter
	 * @param $output
	 */	
	public function handleAfter($output){}
	
}