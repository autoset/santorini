<?php

namespace org\autoset\santorini\http;


class Cookie
{
	private $_name = null;
	private $_value = null;
	private $_domain = null;
	private $_path = null;
	private $_maxAge = null;

	public function __construct($name, $value)
	{
		$this->setName($name);

		$this->setValue($value);
	}

	public function getName()
	{
		return $this->_name;
	}

	public function getValue()
	{
		return $this->_value;
	}

	public function getMaxAge()
	{
		return is_null($this->_maxAge) ? -1 : $this->_maxAge;
	}

	public function getDomain()
	{
		return $this->_domain;
	}

	public function getPath()
	{
		return $this->_path;
	}

	private function setName($name)
	{
		$this->_name = $name;
	}

	public function setValue($value)
	{
		$this->_value = $value;
	}

	public function setMaxAge($maxAge)
	{
		$this->_maxAge = $maxAge;
	}

	public function setDomain($domain)
	{
		$this->_domain = $domain;
	}

	public function setPath($path)
	{
		$this->_path = $path;
	}

}


?>