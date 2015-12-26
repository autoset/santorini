<?php

namespace org\autoset\santorini\view;

use org\autoset\santorini\http\HttpServletRequest;
use org\autoset\santorini\http\HttpServletResponse;


class UrlBasedViewResolver
{
	private $_viewClassName = null;
	private $_viewClass = null;
	private $_order;

	public function __set($name, $value)
	{
		if ($name == 'viewClass')
		{
			$this->_viewClassName = $name;

			$value2 = str_replace('.','\\',$value);
			$this->_viewClass = new $value2;
			return ;
		}

		$this->{'_'.$name} = $value;
	}

	public function getOrder()
	{
		return (int)$this->_order;
	}

	public function display(HttpServletRequest &$req, HttpServletResponse &$resp, &$viewName, $model = null)
	{
		if ($this->_viewClass == null)
			return false;
		else
			return $this->_viewClass->display($req, $resp, $viewName, $model);
	}



} 

?>