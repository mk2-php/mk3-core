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
		if(!empty($context->template)){
			$this->template = $context->template;
		}
		if(!empty($context->templateParent)){
			$this->templateParent = $context->templateParent;
		}
		
		if(!empty($this->template)){
			if(Config::exists("config.coreBlock.useResponse")){
				$this->Response->template($context->template);
			}
		}
		else if(!empty($this->templateParent)){
			if(Config::exists("config.coreBlock.useResponse")){
				$this->Response->parentTemplate($context->templateParent);
			}
		}
		else{
			if(Config::exists("config.coreBlock.useResponse")){
				$this->Response->view($context->view);
			}
		}

	}
}