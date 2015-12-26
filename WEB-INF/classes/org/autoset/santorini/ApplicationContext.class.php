<?php

namespace org\autoset\santorini;

use Exception;

class ApplicationContext
{ 
	private static $_beanFactory = array();

	public static function getInstance()
	{
		return new ApplicationContext;
	}

	public static function setBeanFactory(&$arrBeanFactory)
	{
		self::$_beanFactory = $arrBeanFactory;

		foreach ($arrBeanFactory as $objBean)
		{
			if (method_exists($objBean, 'init'))
				$objBean->init();
		}
	}

	public static function &getBean($id)
	{
		if (array_key_exists($id, self::$_beanFactory))
			return self::$_beanFactory[$id];
		else
			throw new Exception('요청한 bean ID - '.$id.'(은)는 로드되지 않았습니다.');
	}

	public static function &getBeanByClass($classNm)
	{
		$classNm = str_replace(".", "\\", $classNm);
		$reflectionClass = new \ReflectionClass($classNm);
		$objTarget = $reflectionClass->newInstance();

		foreach (self::$_beanFactory as $id => $objBean)
		{
			if ($objBean instanceof $objTarget)
				return self::$_beanFactory[$id];
		}

		throw new Exception('요청한 bean class - '.$classNm.'(은)는 로드되지 않았습니다.');
	}

	public static function containsBean($id)
	{
		return array_key_exists($id, self::$_beanFactory);
	}

} 

?>