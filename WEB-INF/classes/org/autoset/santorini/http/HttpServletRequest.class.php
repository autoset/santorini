<?php

namespace org\autoset\santorini\http;

//use org\autoset\santorini\String;

class HttpServletRequest
{
	public function __construct()
	{

	}

	/*
     * <pre>
     * 서버가 사용하는 인증 이름
     * </pre>
     * 
     * @return String
	*/
	public function getAuthType()
	{
		return "null";
	}

	/*
     * <pre>
     * 쿠키 리턴
     * </pre>
     * 
     * @return Cookie[]
	*/
	public function getCookies()
	{
		$arr = array();

		foreach ($_COOKIE as $name => $value)
			$arr[] = new Cookie($name, $value);

		return $arr;
	}

	/*
     * <pre>
     * 헤더 리턴
     * </pre>
     * 
     * @return String
	*/
	public function getHeader($name)
	{
		$name = str_replace(array(' ','-'),'_',strtoupper($name));
		return $_SERVER['HTTP_'.$name];
	}

	/*
     * <pre>
     * 메서드 리턴
     * </pre>
     * 
     * @return String
	*/
	public function getMethod()
	{
		return strtoupper($_SERVER['REQUEST_METHOD']);
	}

	/*
     * <pre>
     * URL에서 추가적인 패스 정보 리턴
	 * </pre>
     * 
     * @return String
	*/
	public function getPathInfo()
	{
		return isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : null;
	}

	/*
     * <pre>
     * URL에서 추가적인 패스 정보 리턴
	 * </pre>
     * 
     * @return String
	*/
	public function getPathTranslated()
	{
		return isset($_SERVER['PATH_TRANSLATED']) ? $_SERVER['PATH_TRANSLATED'] : '';
	}

	/*
     * <pre>
     * URL에서 추가적인 패스 정보 리턴
	 * </pre>
     * 
     * @return String
	*/
	public function getRemoteUser()
	{
		return isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null;
	}

	/*
     * <pre>
     * 세션 ID 리턴
	 * </pre>
     * 
     * @return String
	*/
	public function getRequestedSessionId()
	{
		return session_name();
	}

	/*
     * <pre>
     * URI값
	 * </pre>
     * 
     * @return String
	*/
	public function getRequestURI()
	{
		return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
	}

	/*
     * <pre>
     * 현재 세션
	 * </pre>
     * 
     * @return HttpSession
	*/
	public function getSession()
	{
		return new HttpSession;
	}

	/*
     * <pre>
     * 
	 * </pre>
     * 
     * @return Object
	*/
	public function getParameter($name)
	{
		return $this->getMixedParameter($name, true);
	}

	/*
     * <pre>
     * 
	 * </pre>
     * 
     * @return Object
	*/
	public function getMixedParameter($name, $singleReturn = true)
	{
		$value = null;

		switch ($this->getMethod())
		{
			case 'GET':
				$value = isset($_GET[$name]) ? $_GET[$name] : null;
				break;
			case 'POST':

				if (isset($_POST[$name]))
				{
					// PHP 7.0.0 이상에서는 그대로 수용
					if (PHP_VERSION_ID >= 70000)
					{
						$value = $_POST[$name];
					}
					else
					{
						$value = $this->__decodeUnicodeUrl( $this->__smartStripSlashes( $_POST[$name] ) );
					}
				}
				else
				{
					if (isset($_FILES[$name]))
					{
						if (is_array($_FILES[$name]['tmp_name']))
						{
							$value = MultipartFiles::getData($_FILES[$name]);
						}
						else
						{
							$value = new MultipartFile($_FILES[$name]);
						}
					}
					else
					{
						if (isset($_GET[$name]))
						{
							return $_GET[$name];
						}
						else
						{
							$value = null;
						}
					}
				}
				break;
		}

		if ($singleReturn == true && is_array($value))
			return $value[0];
		else
			return $value;
	}

	/*
     * <pre>
     * 
	 * </pre>
     * 
     * @return Object
	*/
	private function __decodeUnicodeUrl($str)
	{
		$res = '';

		$i = 0;

		$max = strlen($str) - 6;
		
		while ($i <= $max)
		{
			$character = $str[$i];

			if ($character == '%' && $str[$i + 1] == 'u')
			{
				$value = hexdec(substr($str, $i + 2, 4));
				$i += 6;

				if ($value < 0x0080) // 1 byte: 0xxxxxxx
					$character = chr($value);
				else if ($value < 0x0800) // 2 bytes: 110xxxxx 10xxxxxx
					$character = chr((($value & 0x07c0) >> 6) | 0xc0) . chr(($value & 0x3f) | 0x80);
				else // 3 bytes: 1110xxxx 10xxxxxx 10xxxxxx
					$character = chr((($value & 0xf000) >> 12) | 0xe0) . chr((($value & 0x0fc0) >> 6) | 0x80) . chr(($value & 0x3f) | 0x80);
			}
			else
			{
				$i++;
			}

			$res .= $character;
		}

		return $res . substr($str, $i);
	}

	/*
     * <pre>
     * 
	 * </pre>
     * 
     * @return Object
	*/
	private function __smartStripSlashes($str)
	{
		if (get_magic_quotes_gpc())
			if (is_array($str))
				return array_map(array($this, '__smartStripSlashes'), $str);
			else
				return stripslashes($str);
		else
			return $str;
	}

	/*
     * <pre>
     * 
	 * </pre>
     * 
     * @return Object
	*/
	public function getParameterValues($name)
	{
		$value = null;

		switch ($this->getMethod())
		{
			case 'GET':
				$value = isset($_GET[$name]) ? $_GET[$name] : null;
				break;
			case 'POST':
				$value = isset($_POST[$name]) ? $_POST[$name] : null;
				break;
		}

		if (is_array($value))
		{
			$value = array_map(array($this, '__smartStripSlashes'), $value);
			$value = array_map(array($this, '__decodeUnicodeUrl'), $value);

			return $value;
		}
		else
		{
			return array($value);
		}
	}

	/*
     * <pre>
     * 
	 * </pre>
     * 
     * @return Object
	*/
	public function getParameterNames()
	{
		switch ($this->getMethod())
		{
			case 'GET':
				return isset($_GET) ? array_keys($_GET) : array();
				break;
			case 'POST':
				$names = array();
			
				if (isset($_POST))
				{
					$names = array_keys($_POST);
				}
			
				if (isset($_FILES))
				{
					$names = array_merge($names, array_keys($_FILES));
				}
				
				return $names;
				break;
		}
	}

	/*
     * <pre>
     * 
	 * </pre>
     * 
     * @return Object
	*/
	function getRemoteAddr()
	{
		return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
	}

	function isSecure()
	{
		return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'];
	}

	function getInputStream()
	{
		return file_get_contents("php://input");
	}

	function getContentType()
	{
		$contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : null;

		if (is_null($contentType))
			return null;

		if (($pos = strpos($contentType, ';')) !== false)
		{
			return substr($contentType, 0, $pos);
		}
	}
}

