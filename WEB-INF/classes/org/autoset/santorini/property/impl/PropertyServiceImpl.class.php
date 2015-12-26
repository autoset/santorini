<?php

namespace org\autoset\santorini\property\impl;

use org\autoset\santorini\property\PropertyService;

class PropertyServiceImpl implements PropertyService
{
	private $_properties = array();

	private $_extFileName = array();

	public function __construct()
	{
		
	}

	public function __destruct()
	{
	}

	public function __set($name, $value)
	{
		$this->{'_'.$name} = $value;
	}

	public function init()
	{
		foreach ($this->_extFileName as $fileInfo)
		{
			$lines = file(DIR_ROOT.'/'.$fileInfo['filename']);

			foreach ($lines as $line)
			{
				$line = trim($line);

				if (empty($line) || substr($line,0,1) == "#" || substr($line,0,1) == ";" || strpos($line,'=') === false)
					continue;

				list($key,$value) = explode('=',$line,2);

				$this->_properties[trim($key)] = trim($value);
			}
		}
	}

	public function getString($id)
	{
		return isset($this->_properties[$id]) ? str_replace('${ROOT}', DIR_ROOT, $this->_properties[$id]) : null;
	}

	public function getInt($id)
	{
		return isset($this->_properties[$id]) ? (int)$this->_properties[$id] : null;
	}

}

?>