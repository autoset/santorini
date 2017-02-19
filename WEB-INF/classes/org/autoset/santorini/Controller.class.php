<?php

namespace org\autoset\santorini;

use Exception;

use org\autoset\santorini\http\HttpServlet;
use org\autoset\santorini\http\HttpServletRequest;
use org\autoset\santorini\http\HttpServletResponse;

use org\autoset\santorini\view\InternalResourceViewResolver;
use org\autoset\santorini\util\ModelMap;
use org\autoset\santorini\ModelAndView;

class Controller extends HttpServlet
{
	public $model = null;

	private $_viewResolverMap = null;

	private $_req = null;
	private $_resp = null;

	public function service(HttpServletRequest &$req, HttpServletResponse &$resp)
	{
		$this->_req = &$req;
		$this->_resp = &$resp;

		$ret = $this->{self::$__parent__->getCalledMethodName()}($req, $resp);

		// 인터셉터 후처리
		if (sizeof(self::$__parent__->getInterceptorMap()) > 0)
		{
			foreach (self::$__parent__->getInterceptorMap() as $interceptorBean)
			{
				if (method_exists($interceptorBean, 'postHandle'))
					$interceptorBean->postHandle($req, $resp, $this->model);
			}
		}

		$this->_viewResolverMap = self::$__parent__->getViewResolverMap();
		
		if ($ret instanceof ModelAndView)
		{
			$ret->getView()->display($req, $resp, $ret->getModel());
		}
		elseif (is_string($ret))
		{
			foreach ($this->_viewResolverMap as $order=>$viewResolver)
			{
				if (!$viewResolver->display($req, $resp, $ret, $this->model))
					continue;
			}
		}
	}

	public function getRequestParam($name,$required=false,$defaultValue=null)
	{
		if (strpos($name,'[]') === false)
			$val = $this->_req->getParameter($name);
		else
			$val = $this->_req->getParameterValues(substr($name,0,-2));

		if ($val == null)
			$val = $defaultValue;

		if ($required && $val == null)
			throw new Exception('필수 파라미터 '.$name.'의 값이 존재하지 않습니다.');

		return $val;
	}

	public function getRequestBody()
	{
		return $this->_req->getInputStream();
	}

	public function getPathVar($itemIdx)
	{
		if (is_string($itemIdx)) {
			return self::$__parent__->getPathVarByName($itemIdx);
		} else {
			return self::$__parent__->getPathVarByIdx($itemIdx);
		}
	}

}
