<?php

namespace org\autoset\santorini\view;


use org\autoset\santorini\http\HttpServletRequest;
use org\autoset\santorini\http\HttpServletResponse;


class InternalResourceViewResolver extends UrlBasedViewResolver
{
	private $_prefix;
	private $_suffix;
	private $_order;

	public function __set($name, $value)
	{
		$this->{'_'.$name} = $value;
	}

	public function getOrder()
	{
		return (int)$this->_order;
	}

	public function display(HttpServletRequest &$req, HttpServletResponse &$resp, &$viewName, $model = null)
	{
		$viewPath = DIR_ROOT.'/'.$this->_prefix.$viewName.$this->_suffix;
		
		if (!file_exists($viewPath))
			return false;

		include_once($viewPath);
		return true;
	}

} 

?>