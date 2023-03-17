<?php
/**
 * ===================================================
 * 
 * PHP FW - Mk3 -
 * Render
 * 
 * Object class for initial operation.
 * 
 * URL : 
 * Copylight : Masato-Nakatsuji 2023.
 * 
 * ===================================================
 */

namespace Mk3\Core;

class Render extends CoreBlock{

	/**
	 * render
	 * @param &$context
	 */
	public function render(&$context){

		if(!empty($context->view)){
			$this->view = $context->view;
		}
		if(!empty($context->template)){
			$this->template = $context->template;
		}
		if(!empty($context->templateParent)){
			$this->templateParent = $context->templateParent;
		}
		
		if(!empty($this->template)){
			if(Config::exists("config.coreBlock.useResponse")){
				$this->Response->loadTemplate($context->template);
			}
		}
		else if(!empty($this->templateParent)){
			if(Config::exists("config.coreBlock.useResponse")){
				$this->Response->loadTemplateParent($context->templateParent);
			}
		}
		else{
			if(Config::exists("config.coreBlock.useResponse")){
				$this->Response->loadView($context->view);
			}
		}

	}
}