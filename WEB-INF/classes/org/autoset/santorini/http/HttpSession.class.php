<?php

namespace org\autoset\santorini\http;


class HttpSession
{
	private $_session = array();

	public function __construct()
	{
		header('P3P: CP="CAO PSA OUR"');

		@session_start();

		if ($this->getId() == '')
			session_regenerate_id(true);

		$this->_session = &$_SESSION;
	}


	private function changeSessionName($sessionName)
	{
		$cookieInfo = session_get_cookie_params();

		if ( (empty($cookieInfo['domain'])) && (empty($cookieInfo['secure'])) )
		{
			setcookie(session_name(), $sessionName, $cookieInfo['lifetime'], $cookieInfo['path']);
		}
		elseif (empty($cookieInfo['secure']))
		{
			setcookie(session_name(), $sessionName, $cookieInfo['lifetime'], $cookieInfo['path'], $cookieInfo['domain']);
		}
		else
		{
			setcookie(session_name(), $sessionName, $cookieInfo['lifetime'], $cookieInfo['path'], $cookieInfo['domain'], $cookieInfo['secure']);
		}
	}

	public function getAttribute($name)
	{
		return isset($this->_session[$name]) ? $this->_session[$name] : null;
	}

	public function getAttributeNames()
	{
		return array_keys($this->_session);
	}

	public function getId()
	{
		return session_id();
	}

	public function setId($sessionId)
	{
		session_id($sessionId);
	}

	public function getName()
	{
		return session_name();
	}

	public function getLastAccessedTime()
	{
		return 0;
	}

	public function setAttribute($name, $value)
	{
		$this->_session[$name] = $value;
	}

	public function removeAttribute($name)
	{
		$this->_session[$name] = null;
	}

}


?>