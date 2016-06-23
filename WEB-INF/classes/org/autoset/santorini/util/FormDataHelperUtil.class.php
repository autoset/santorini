<?php

namespace org\autoset\santorini\util;

use org\autoset\santorini\vo\VirtualFormVO;
use org\autoset\santorini\http\HttpServletRequest;

class FormDataHelperUtil
{
	public static function Param2VO(HttpServletRequest $request)
	{
		$formVO = new VirtualFormVO;

		$paramNames = $request->getParameterNames();

		foreach ($paramNames as $paramName)
		{
			$formVO->{'set'.ucfirst($paramName)}($request->getMixedParameter($paramName, false));
		}

		return $formVO;
	}

} 
