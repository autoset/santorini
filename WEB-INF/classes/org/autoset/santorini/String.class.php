<?php

namespace org\autoset\santorini;

class String
{ 
	public $value;

	public function __construct($value)
	{ 
		$this->value = $value; 
	} 

	public function __toString()
	{ 
		return "$this->value"; 
	} 

	public function toUpperCase()
	{ 
		return strtoupper($this->value); 
	} 
} 

?>