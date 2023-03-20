<?php
/**
 * ===================================================
 * 
 * PHP FW - Mk3 -
 * Command
 * 
 * Object class for initial operation.
 * 
 * URL : 
 * Copylight : Masato-Nakatsuji 2023.
 * 
 * ===================================================
 */

namespace Mk3\Core;

class Command{

	/**
	 * text
	 * @param $output outpuut text
	 * @param $inline = false inline flg
	 */
	public function text($output,$inline=false){
		echo $this->_out($output,null,$inline);
		return $this;
	}

	/**
	 * blue
	 * @param $output outpuut text
	 * @param $inline = false inline flg
	 */
	public function blue($output,$inline=false){
		echo $this->_out($output,'0;34',$inline);
		return $this;
	}

	/**
	 * green
	 * @param $output outpuut text
	 * @param $inline = false inline flg
	 */
	public function green($output,$inline=false){
		echo $this->_out($output,'0;32',$inline);
		return $this;
	}

	/**
	 * cyan
	 * @param $output outpuut text
	 * @param $inline = false inline flg
	 */
	public function cyan($output,$inline=false){
		echo $this->_out($output,'0;36',$inline);
		return $this;
	}

	/**
	 * red
	 * @param $output outpuut text
	 * @param $inline = false inline flg
	 */	
	public function red($output,$inline=false){
		echo $this->_out($output,'0;31',$inline);
		return $this;
	}

	/**
	 * purple
	 * @param $output outpuut text
	 * @param $inline = false inline flg
	 */		
	public function purple($output,$inline=false){
		echo $this->_out($output,'0;35',$inline);
		return $this;
	}

	/**
	 * brown
	 * @param $output outpuut text
	 * @param $inline = false inline flg
	 */		
	public function brown($output,$inline=false){
		echo $this->_out($output,'0;33',$inline);
		return $this;
	}

	/**
	 * lightGray
	 * @param $output outpuut text
	 * @param $inline = false inline flg
	 */
	public function lightGray($output,$inline=false){
		echo $this->_out($output,'0;37',$inline);
		return $this;
	}

	/**
	 * darkGray
	 * @param $output outpuut text
	 * @param $inline = false inline flg
	 */
	public function darkGray($output,$inline=false){
		echo $this->_out($output,'1;30',$inline);
		return $this;
	}

	/**
	 * lightBlue
	 * @param $output outpuut text
	 * @param $inline = false inline flg
	 */	
	public function lightBlue($output,$inline=false){
		echo $this->_out($output,'1;34',$inline);
		return $this;
	}

	/**
	 * lightGreen
	 * @param $output outpuut text
	 * @param $inline = false inline flg
	 */	
	public function lightGreen($output,$inline=false){
		echo $this->_out($output,'1;32',$inline);
		return $this;
	}

	/**
	 * lightCyan
	 * @param $output outpuut text
	 * @param $inline = false inline flg
	 */	
	public function lightCyan($output,$inline=false){
		echo $this->_out($output,'1;36',$inline);
		return $this;
	}

	/**
	 * lightRed
	 * @param $output outpuut text
	 * @param $inline = false inline flg
	 */	
	public function lightRed($output,$inline=false){
		echo $this->_out($output,'1;31',$inline);
		return $this;
	}

	/**
	 * lightPurple
	 * @param $output outpuut text
	 * @param $inline = false inline flg
	 */	
	public function lightPurple($output,$inline=false){
		echo $this->_out($output,'1;35',$inline);
		return $this;
	}

	/**
	 * yellow
	 * @param $output outpuut text
	 * @param $inline = false inline flg
	 */	
	public function yellow($output,$inline=false){
		echo $this->_out($output,'1;33',$inline);
		return $this;
	}

	/**
	 * white
	 * @param $output outpuut text
	 * @param $inline = false inline flg
	 */	
	public function white($output,$inline=false){
		echo $this->_out($output,'1;37',$inline);
		return $this;
	}

	/**
	 * _out
	 * @param $output outpuut text
	 * @param $color color pallete
	 * @param $inline inline flg
	 */	
	private function _out($output,$color=null,$inline){

		$str=$output;
		if($color){
			$str="\033[".$color."m".$output."\033[0m";
		}

		if(!$inline){
			$str.="\n";
		}
		return $str;
	}

	/**
	 * input
	 * @param $output outpuut text
	 * @return input text
	 */	
	public function input($output){
		echo $output." : ";
		return trim(fgets(STDIN));
	}
	
}