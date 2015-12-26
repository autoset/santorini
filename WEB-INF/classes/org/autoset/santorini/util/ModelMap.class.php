<?php

namespace org\autoset\santorini\util;

class ModelMap extends LinkedHashMap
{

	public function addAttribute($k, $v)
	{
		$this->put($k, $v);
	}

	public function containsAttribute($k)
	{
		return array_key_exists($k, $this->_attributes);
	}


} 

?>