<?php

namespace example\common\interceptor;

use org\autoset\santorini\http\HttpServletRequest;
use org\autoset\santorini\http\HttpServletResponse;

use org\autoset\santorini\handler\HandlerInterceptorAdapter;
use org\autoset\santorini\ApplicationContext;

use example\common\exception\HttpSessionRequiredException;
use example\common\exception\ApiSessionRequiredException;
use example\common\exception\InvalidParameterException;

use example\common\util\UserHelperUtil;

class LoginCheckIntercepter extends HandlerInterceptorAdapter
{
	public function preHandle(HttpServletRequest &$request, HttpServletResponse &$response, &$handler)
	{
		$annoPagePolicy = $handler->getAnnotation('PagePolicy');
		$annoRequestMapping = $handler->getAnnotation('RequestMapping');

		// 어노테이션이 정의된 경우에만!
		if ($annoPagePolicy != null)
		{
			if ($annoPagePolicy->requiredLogin)
			{
				// 세션에서 사용자 ID 획득해 VO에 셋팅
				if (!UserHelperUtil::isLoggedIn($request))
				{
					throw new HttpSessionRequiredException('로그인 후 이용하세요.');
					return false;
				}
			}
		}

		return true;
	}

	public function postHandle(HttpServletRequest &$req, HttpServletResponse &$resp)
	{

	}

	public function afterCompletion(HttpServletRequest &$req, HttpServletResponse &$resp, &$handler)
	{

	}
}

