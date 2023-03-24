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

class Controller extends CoreBlock{

	public $view = null;
	public $template = null;
	public $autoRender = false;

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
		return $this;
	}

	/**
	 * _rendering
	 */
	public function _rendering(){
/*
		if(empty($this->view)){
			$this->view = RequestRouting::$_params["controller"]. "/". RequestRouting::$_params["action"];
			if(!empty(RequestRouting::$_params["module"])){
				$_view = "modules/". 
					lcfirst(RequestRouting::$_params["module"]) . "/" . 
					RLD_PATH_NAME_RENDERING . "/" .
					RLD_PATH_NAME_VIEW . 
					substr($this->view, strlen("/modules/". lcfirst(RequestRouting::$_params["module"]).RLD_PATH_SEPARATE. RLD_DEFNS . RLD_PATH_SEPARATE . RLD_PATH_NAME_CONTROLLER));

				$this->view = $_view;
			}
		}
*/
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

			if(!empty($this->UI)){
				$render->UI = $this->UI;
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