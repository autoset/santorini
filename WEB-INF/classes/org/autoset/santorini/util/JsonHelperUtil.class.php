<?php

namespace org\autoset\santorini\util;

use org\autoset\santorini\vo\VirtualFormVO;

class JsonHelperUtil
{
	public static function String2VO($str)
	{
		$formVO = new VirtualFormVO;

		if (self::isEmpty($str))
			return $formVO;

		$jsonData = json_decode($str);

		foreach ($jsonData as $key => $value)
			$formVO->{'set'.ucfirst($key)}($value);

		return $formVO;
	}

    public static function isEmpty($str)
	{
        return $str == null || strlen($str) == 0;
    }

} 
