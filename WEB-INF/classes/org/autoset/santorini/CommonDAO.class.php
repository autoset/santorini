<?php

namespace org\autoset\santorini;

class CommonDAO
{
	private $_dataSourceBean = null;

	public function __construct()
	{
		$this->_dataSourceBean = ApplicationContext::getBeanByClass('org.autoset.santorini.BasicDataSource');
	}

	public function getDataSourceBean()
	{
		return $this->_dataSourceBean;
	}

	public function setDataSourceBean(&$obj)
	{
		$this->_dataSourceBean = $obj;
	}

	public function __call($methodName, $arguments)
	{
		$calledClass = get_called_class();

		$classNm = substr($calledClass, 0, strrpos($calledClass,"\\")+1);
		$classNm .= strtolower(DB_TYPE)."\\";
		$classNm .= substr($calledClass, strrpos($calledClass,"\\")+1);

		$reflectionClass = new \ReflectionClass($classNm);
		$reflectionMethod = new \ReflectionMethod($classNm, $methodName);

		return $reflectionMethod->invokeArgs($reflectionClass->newInstanceArgs(), $arguments);
	}

	// NOTE : DB 타입이 늘어나면 이곳을 확장해야 합니다.
	protected function getSequenceNo()
	{
		switch (strtolower(DB_TYPE))
		{
			case 'cubrid':
				//return (int)$this->getOne('SELECT SEQ_SN.NEXTVAL FROM db_root');
			case 'mysql':
				return (int)$this->getOne('SELECT LAST_INSERT_ID()');
			case 'mssql':
				return (int)$this->getOne('SELECT @@IDENTITY');
		}
	}

	// MySQL 전용
	public function getFoundRows()
	{
		return (int)$this->getOne('SELECT FOUND_ROWS()');
	}

	protected function prepareSqlMapBind($sql, &$vo)
	{
		if (is_null($vo))
			$vo = array();

		if (is_array($vo))
		{
			foreach ($vo as $name=>$value)
			{
				if (is_array($value) || is_object($value))
					continue;

				$sql = str_replace('$'.$name.'$',$this->_dataSourceBean->getConnection()->escapeString($value),$sql); // SECURITY NOTE: SQL Injection 위험이 존재

				if (is_null($value))
					$value = 'NULL';
				elseif (!is_numeric($value))
					$value = "'".$this->_dataSourceBean->getConnection()->escapeString($value)."'";
				elseif (is_numeric($value) && substr($value,0,1) == '0' && strlen($value) > 1)
					$value = "'".$this->_dataSourceBean->getConnection()->escapeString($value)."'";
				elseif (is_numeric($value) && strlen($value) > 5)
					$value = "'".$this->_dataSourceBean->getConnection()->escapeString($value)."'";


				$sql = str_replace('#'.$name.'#',$value,$sql);
			}
		}
		elseif ($vo instanceof \org\autoset\santorini\vo\VirtualFormVO)
		{
			$map = array();
			foreach ($vo as $k=>$v)
				$map[substr($k,1)] = $v;

			return $this->prepareSqlMapBind($sql, $map);
		}
		else
		{
			$reflection = new \ReflectionObject($vo);
			$props = $reflection->getProperties();

			$arrProps = array();
			foreach ($props as $prop)
			{
				$name = substr($prop->getName(),1);
				$method = $reflection->getMethod('get'.$name);
				$value = $method->invoke($vo);

				if (is_array($value))
					continue;

				if (is_string($value))
					$sql = str_replace('$'.$name.'$',$this->_dataSourceBean->getConnection()->escapeString($value),$sql); // SECURITY NOTE: SQL Injection 위험이 존재

				if (is_null($value))
					$value = 'NULL';
				elseif (!is_numeric($value))
					$value = "'".$this->_dataSourceBean->getConnection()->escapeString($value)."'";
				elseif (is_numeric($value) && substr($value,0,1) == '0' && strlen($value) > 1)
					$value = "'".$this->_dataSourceBean->getConnection()->escapeString($value)."'";
				elseif (is_numeric($value) && strlen($value) > 5)
					$value = "'".$this->_dataSourceBean->getConnection()->escapeString($value)."'";

				$sql = str_replace('#'.$name.'#',$value,$sql);
			}

			$reflection = null;
			$props = null;
			unset($reflection);
			unset($props);
		}

		return $sql;
	}

	protected function createResultMap($vo, $row)
	{
		$reflection = new \ReflectionObject($vo);
		$newVo = $reflection->newInstance();

		foreach ($row as $colName=>$colVal)
		{
			$arrTmp = explode('_',$colName);
			foreach ($arrTmp as $tK=>$tV)
			{
				if ($tK == 0)
					$arrTmp[$tK] = strtoLower($tV);
				else
					$arrTmp[$tK] = strtoupper(substr($tV,0,1)).strtoLower(substr($tV,1));
			}

			$newVo->{'set'.implode('',$arrTmp)}($colVal);
			//$newVo->{'set'.str_replace('_','',$colName)}($colVal);
		}

		return $newVo;
	}

	protected function escapeString($str)
	{
		return $this->_dataSourceBean->getConnection()->escapeString($str);
	}

	protected function insert($sql, $vo = null)
	{
		$this->execute($this->prepareSqlMapBind($sql, $vo));
	}

	protected function update($sql, $vo = null)
	{
		$this->execute($this->prepareSqlMapBind($sql, $vo));
	}

	protected function delete($sql, $vo = null)
	{
		$this->execute($this->prepareSqlMapBind($sql, $vo));
	}

	protected function selectByPk($sql, $vo = null, $returnType = null)
	{
		if (is_array($vo) && is_null($returnType))
		{
			$returnType = array();

			foreach ($vo as $k=>$v)
			{
				$returnType[strtolower(preg_replace("#([A-Z])#","_$1",$k))] = $k;
			}
		}
		elseif ($vo instanceof \org\autoset\santorini\vo\VirtualFormVO && is_null($returnType))
		{
			$returnType = new \org\autoset\santorini\vo\VirtualFormVO;

			foreach ($vo as $k=>$v)
			{
				$name = ucfirst(substr($k,1));
				$returnType->{'set'.$name} = $v;
			}
		}

		if (is_null($returnType))
		{
			$row = $this->getRow($this->prepareSqlMapBind($sql, $vo));
			return is_null($row) ? null : $this->createResultMap($vo, $row);
		}
		elseif (is_object($returnType))
		{
			$row = $this->getRow($this->prepareSqlMapBind($sql, $vo));
			return is_null($row) ? null : $this->createResultMap($returnType, $row);
		}
		elseif (is_array($returnType))
		{
			$row = $this->getRow($this->prepareSqlMapBind($sql, $vo));

			$arrTmp = array();

			foreach ($returnType as $column => $property)
			{
				$arrTmp[$property] = $row[$column];
			}

			return $arrTmp;
		}
		else
		{
			return $this->getOne($this->prepareSqlMapBind($sql, $vo));
		}
	}

	// php에서 list가 예약어라 selectList로 리네임.
	protected function selectList($sql, $vo = null, $returnType = null)
	{
		$result = $this->getAll($this->prepareSqlMapBind($sql, $vo));
		$dat = array();

		if (is_array($vo) && is_null($returnType))
		{
			$returnType = array();

			foreach ($vo as $k=>$v)
			{
				$returnType[strtolower(preg_replace("#([A-Z])#","_$1",$k))] = $k;
			}
		}
		elseif ($vo instanceof \org\autoset\santorini\vo\VirtualFormVO && is_null($returnType))
		{
			$returnType = new \org\autoset\santorini\vo\VirtualFormVO;

			foreach ($vo as $k=>$v)
			{
				$name = ucfirst(substr($k,1));
				$returnType->{'set'.$name} = $v;
			}
		}

		if (is_null($returnType))
		{
			foreach ($result as $row)
			{
				$dat[] = $this->createResultMap($vo, $row);
			}
		}
		elseif (is_object($returnType))
		{
			foreach ($result as $row)
			{
				$dat[] = $this->createResultMap($returnType, $row);
			}
		}
		elseif (is_array($returnType))
		{
			foreach ($result as $row)
			{
				$arrTmp = array();

				foreach ($returnType as $column => $property)
				{
					$arrTmp[$property] = $row[$column];
				}

				$dat[] = $arrTmp;
			}
		}

		$result = null;
		unset($result);

		return $dat;
	}

	/** 
	*/

	protected function execute($sql, $vo = array())
	{
		if ($this->_dataSourceBean->getConnection() == null || 
			!$this->_dataSourceBean->getConnection()->isConnected())
			throw new DataAccessException("데이터베이스 접속에 실패했습니다.");

		//$this->_dataSourceBean->getConnection()->debug = true;
		$this->_dataSourceBean->getConnection()->execute($sql, $vo);
	}

	protected function getAll($sql, $vo = array())
	{
		//$this->_dataSourceBean->getConnection()->debug = true;
		return $this->_dataSourceBean->getConnection()->getAll($sql, $vo);
	}

	protected function getRow($sql, $vo = array())
	{
		//$this->_dataSourceBean->getConnection()->debug = true;
		return $this->_dataSourceBean->getConnection()->getRow($sql, $vo);
	}

	protected function getOne($sql, $vo = array())
	{
		//$this->_dataSourceBean->getConnection()->debug = true;
		return $this->_dataSourceBean->getConnection()->getOne($sql, $vo);
	}

	protected function getCol($sql, $vo = array())
	{
		//$this->_dataSourceBean->getConnection()->debug = true;
		return $this->_dataSourceBean->getConnection()->getCol($sql, $vo);
	}
} 

?>