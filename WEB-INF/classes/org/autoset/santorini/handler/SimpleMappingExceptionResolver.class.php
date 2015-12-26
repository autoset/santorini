<?php

namespace org\autoset\santorini\handler;

class SimpleMappingExceptionResolver
{ 
	private $_defaultErrorView = null;
	private $_exceptionMappings = array();

	public function __set($name, $value)
	{
		$this->{'_'.$name} = $value;
	}

	public function getExceptionMappings()
	{
		return $this->_exceptionMappings;
	}

	public function getDefaultErrorView()
	{
		return $this->_defaultErrorView;
	}

} 

?>