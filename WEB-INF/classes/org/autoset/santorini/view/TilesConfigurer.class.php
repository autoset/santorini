<?php

namespace org\autoset\santorini\view;

class TilesConfigurer
{
	private $_definitions = null;

	public function __set($name, $value)
	{
		$this->{'_'.$name} = $value;
	}

	public function getDefinitions()
	{
		return $this->_definitions;
	}
	
} 

?>