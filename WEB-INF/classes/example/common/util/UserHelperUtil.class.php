<?php

namespace example\common\util;

use org\autoset\santorini\http\HttpServletRequest;
use org\autoset\santorini\vo\SessionVO;

class UserHelperUtil
{
	public static function isLoggedIn(HttpServletRequest $request)
	{
		return $request->getSession()->getAttribute('LOGGED_IN') === true;
	}

	public static function getUserInfo(HttpServletRequest $request)
	{
		$pui = $request->getSession()->getAttribute('USER_INFO');
		return $pui == null ? new SessionVO : $pui;
	}

	public static function setUserInfo(HttpServletRequest $request, SessionVO $sessionVO)
	{
		$request->getSession()->setAttribute('LOGGED_IN', $sessionVO->getUserId() !== null);
		$request->getSession()->setAttribute('USER_INFO', $sessionVO);
	}

} 

