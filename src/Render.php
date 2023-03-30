<?php
/**
 * ===================================================
 * 
 * PHP FW "Reald"
 * Render
 * 
 * Object class for initial operation.
 * 
 * URL : 
 * Copylight : Masato-Nakatsuji 2023.
 * 
 * ===================================================
 */

namespace Reald\Core;

class Render extends CoreBlock{

	/**
	 * render
	 * @param &$context
	 */
	public function render(&$context){

		if(!empty($context->view)){
			$this->view = $context->view;
		}
		
		if(!empty($context->viewParent)){
			$this->viewParent = $context->viewParent;
		}
		if(!empty($context->template)){
			$this->template = $context->template;
		}
		if(!empty($context->templateParent)){
			$this->templateParent = $context->templateParent;
		}
		
		if(Config::exists("config.coreBlock.useResponse")){
			if(!empty($this->templateParent)){
				$this->Response->parentTemplate($context->templateParent);
			}
			else if(!empty($this->template)){
				$this->Response->template($context->template);
			}
			else if(!empty($this->viewParent)){

				$juge = $this->Response->parentViewExists($context->viewParent);

				if(!$juge){
					$getView = $this->Response->parentView($context->viewParent);
					throw new \Exception(str_replace("<pre>","",$getView));
				}
				
				$this->Response->parentView($context->viewParent);

			}
			else{

				$juge = $this->Response->viewExists($context->view);

				if(!$juge){
					$getView = $this->Response->view($context->view);
					throw new \Exception(str_replace("<pre>","",$getView));
				}
				
				$this->Response->view($context->view);
			}
		}



	}
}