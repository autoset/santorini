<?php

namespace org\autoset\santorini\handler;

class InterceptorHandler
{ 
	private $_className;
	private $_methodName;
	private $_annotations;

	public function __construct($arrClassInfo)
	{
		$this->_className = $arrClassInfo['className'];
		$this->_methodName = $arrClassInfo['methodName'];
		$this->_annotations = $arrClassInfo['annotation'];
	}
	
	public function getAnnotation($className)
	{
		if (isset($this->_annotations[$className]))
			return $this->_annotations[$className];
		else
			return null;
	}

	public function getAnnotations()
	{
		return $this->_annotations;
	}
}

?>