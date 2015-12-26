<?php

namespace org\autoset\santorini\util;

class StringBuilder
{
	private $_arr = array();

	public function append($v)
	{
		$this->_arr[] = $v;
	}

	public function __toString()
	{
		return sizeof($this->_arr) > 0 ? implode('', $this->_arr) : null;
	}

	public function toString()
	{
		return $this->__toString();
	}

} 

?>