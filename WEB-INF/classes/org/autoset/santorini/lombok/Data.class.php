<?php

namespace org\autoset\santorini\lombok;

use Exception;

class Data {

	public function __construct($fromObject = null) {

		if (is_object($fromObject)) {
			$objVars = get_object_vars($fromObject);
			foreach ($objVars as $k => $v) {
				$this->{'set'.ucfirst($k)}($v);
			}
		}

	}

	public function __call($name, $arguments) {
		if (preg_match("#^get(.*)#i", $name, $match)) {
			$propertyName = lcfirst($match[1]);
			if (property_exists($this, $propertyName)) {
				return $this->{$propertyName};
			} else {
				throw new Exception("Undefined property[".$propertyName."] called.");
			}
		} elseif (preg_match("#^set(.*)#i", $name, $match)) {
			$propertyName = lcfirst($match[1]);
			if (property_exists($this, $propertyName)) {
				$this->{$propertyName} = $arguments[0];
			} else {
				throw new Exception("Undefined property[".$propertyName."] called.");
			}
		}
	}

}
