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

class Render{

	/**
	 * render
	 * @param &$context
	 */
	public function render(&$context){

		if(!empty($context->view)){
			$this->view = $context->view;
			Response::$view = $context->view;
		}
		
		if(!empty($context->viewParent)){
			$this->viewParent = $context->viewParent;
			Response::$viewParent = $context->viewParent;
		}
		if(!empty($context->template)){
			$this->template = $context->template;
		}
		if(!empty($context->templateParent)){
			$this->templateParent = $context->templateParent;
		}
		

		if(!empty($this->templateParent)){
			Response::parentTemplate($context->templateParent);
		}
		else if(!empty($this->template)){
			Response::template($context->template);
		}
		else if(!empty($this->viewParent)){

			$juge = Response::parentViewExists($context->viewParent);

			if(!$juge){
				$getView = Response::parentView($context->viewParent);
				throw new \Exception(str_replace("<pre>","",$getView));
			}
				
			Response::parentView($context->viewParent);
		}
		else{

			$juge = Response::viewExists($context->view);

			if(!$juge){
				$getView = Response::view($context->view);
				throw new \Exception(str_replace("<pre>","",$getView));
			}
				
			Response::view($context->view);
		}
	}
}