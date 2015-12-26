<?php

namespace org\autoset\santorini\util;

class HashMap
{
	protected $_attributes = array();

	public function __construct($k = null, $v = null)
	{
		if (!is_null($k))
			$this->put($k, $v);
	}

	public function clear()
	{
		$this->_attributes = array();
	}

	public function put($k, $v)
	{
		$this->_attributes[$k] = $v;
	}

	public function get($k)
	{
		return isset($this->_attributes[$k]) ? $this->_attributes[$k] : null;
	}

	public function isEmpty()
	{
		return sizeof($this->_attributes) == 0;
	}

	public function size()
	{
		return sizeof($this->_attributes);
	}

	// This method is only exists for santorini framework.
	public function getAttributes()
	{
		return $this->_attributes;
	}

} 

?>