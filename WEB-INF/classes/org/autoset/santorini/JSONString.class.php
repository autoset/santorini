<?php

namespace org\autoset\santorini;

class JSONString
{ 
	public $value;

	public function __construct($value)
	{ 
		$this->value = $value; 
	} 

	public function __toString()
	{ 
		return $this->toString();
	} 

	public function toString()
	{ 
		return json_decode($this->value);
	} 

	public function toUpperCase()
	{ 
		return strtoupper($this->value); 
	} 
} 

?>