<?php

namespace org\autoset\santorini;

class ModelAndView
{ 
	private $_viewName = null;
	private $_view = null;

	private $_model = null;

	public function __construct($viewName, $model = null)
	{ 
		if (is_string($viewName))
		{
			$this->_viewName = $viewName;
			eval("\$view = new \\org\\autoset\\santorini\\view\\{$viewName};");
			$this->_view = $view;
		}
		else
		{
			$this->_viewName = get_class($viewName);
			$this->_view = $viewName;
		}

		$this->_model = $model;
	}

	protected function getModelInternal()
	{
		return $this->_model;
	}

	public function getModel()
	{
		return $this->_model;
	}

	public function getView()
	{
		return $this->_view;
	}
} 

?>