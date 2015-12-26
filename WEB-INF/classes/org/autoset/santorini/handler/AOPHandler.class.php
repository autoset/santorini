<?php

namespace org\autoset\santorini\handler;

use ReflectionObject;

use org\autoset\santorini\ApplicationContext;

class AOPHandler
{
	private $_ro = null;
	private $_class = null;

	public function __construct(&$oClass)
	{
		$this->_class = &$oClass;
		$this->_ro = new ReflectionObject($this->_class);
	}

	public function &__call($name, $arguments)
	{
		$method = $this->_ro->getMethod($name);

		// NOTE : 서비스 클래스에서만 사용한다고 가정하고. 
		// 트랜잭션을 묶어주는 역할을 합니다.
		$dataSourceBean = ApplicationContext::getBean('dataSource');

		$dataSourceBean->getConnection()->startTrans();

		try
		{
			$result = $method->invokeArgs($this->_class, $arguments);
		}
		catch (Exception $ex)
		{
			$dataSourceBean->getConnection()->rollbackTrans();
			throw $ex;
			return ;
		}

		$dataSourceBean->getConnection()->commitTrans();

		return $result;
	}
}

?>