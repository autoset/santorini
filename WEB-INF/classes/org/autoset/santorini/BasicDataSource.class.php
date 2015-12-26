<?php

namespace org\autoset\santorini;

class BasicDataSource
{
	private $_driverClassName = null;
	private $_driverClass = null;

	private $_url = null;
	private $_username = null;
	private $_password = null;

	public function __construct()
	{
		
	}

	public function __destruct()
	{
		$this->_driverClass = null;
	}

	public function __set($name, $value)
	{
		if ($name == 'driverClassName')
		{
			$value2 = str_replace('.','\\',$value);
			$this->_driverClass = new $value2;
		}

		$this->{'_'.$name} = $value;
	}

	public function init()
	{
		$this->_driverClass->connect($this->_url, $this->_username, $this->_password);
	}

	public function getDriverClassName()
	{
		return $this->_driverClassName;
	}

	public function &getConnection()
	{
		return $this->_driverClass;
	}

	public function getUrl()
	{
		return $this->_url;
	}

	public function setUrl($url)
	{
		$this->_url = $url;
	}

	public function getUsername()
	{
		return $this->_username;
	}

	public function setUsername($username)
	{
		$this->_username = $username;
	}

	public function getPassword()
	{
		return $this->_password;
	}

	public function setPassword($password)
	{
		$this->_password = $password;
	}

} 

?>