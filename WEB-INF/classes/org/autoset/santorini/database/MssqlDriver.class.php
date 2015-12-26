<?php

namespace org\autoset\santorini\database;

use Exception;
use org\autoset\santorini\exception\DataAccessException;

class MssqlDriver
{
	private $_conn = null;
	private $_connected = false;

	private $_transMode			= false;
	private $_transFailedCnt	= 0;

	public	$debug				= false;

	private $_host = null;

	public function __construct()
	{
		
	}

	public function __destruct()
	{
		if ($this->_connected)
			$this->disconnect();
	}

	public function connect($url, $username, $password)
	{
		if (!function_exists('sqlsrv_connect'))
			return false;

		$tmp = parse_url($url);

		if (!array_key_exists('scheme',$tmp) || $tmp['scheme'] != 'sqlserver')
			throw new DataAccessException("데이터베이스 스키마가 드라이버와 일치하지 않습니다.");

		$arrTmp = explode(';',$tmp['host']);
		$arrOptions = array();

		foreach ($arrTmp as $option)
		{
			if (trim($option) == '')
				continue;

			if (substr_count($option, '=') > 0)
			{
				list($k, $v) = explode('=', $option, 2);
				$arrOptions[$k] = $v;
			}
			else
			{
				$arrOptions['host'] = $option;
			}
		}

		if (!array_key_exists('DatabaseName', $arrOptions))
			throw new DataAccessException("데이터베이스 명이 누락되었습니다.");

		$characterSet = !array_key_exists('CharacterSet', $arrOptions) ? 'UTF-8' : $arrOptions['CharacterSet'];

		$this->_host = $arrOptions['host'];

		try
		{
			$this->_conn = sqlsrv_connect( $this->_host, array( "Database"=>$arrOptions['DatabaseName'], "UID"=>$username, "PWD"=>$password, "CharacterSet"=>$characterSet));
		}
		catch (Exception $ex)
		{
			throw $ex;
		}

		$this->_connected = $this->_conn ? true : false;

		return $this->_connected;
	}

	public function disconnect()
	{
		sqlsrv_close($this->_conn);

		$this->_connected = false;
	}

	public function isConnected()
	{
		return $this->_connected;
	}

	public function getVersion()
	{
		return sqlsrv_server_info($this->_connected);
	}

	private function _smartFreeResult(&$result)
	{
		if ($result)
			sqlsrv_free_stmt($result);
	}

	private function _prepareSql($sql, $params)
	{
		// 바인딩하기 전의 쿼리에서 탭, 공백 제거 
		// (문제가 될 수 있는데, 운영해보고 결정)
		$sql = preg_replace("#\s{1,}#s"," ",$sql);

		$arrTmp = explode('?', $sql);
		$nTmpCnt = sizeof($arrTmp);
		$arrDat = array();

		foreach ($arrTmp as $idx => $tmp)
		{
			if (!isset($params[$idx]))
				$arrDat[] = $tmp.($idx < $nTmpCnt - 1 && $params[$idx] === NULL ? 'NULL' : '');
			elseif (is_bool($params[$idx]))
				$arrDat[] = $tmp."'".($params[$idx] ? 1 : 0)."'";
			elseif (is_string($params[$idx]))
				$arrDat[] = $tmp."'".$this->_conn->real_escape_string($params[$idx])."'";
			else
				$arrDat[] = $tmp.$this->_conn->real_escape_string($params[$idx]);
		}

		unset($arrTmp);
		
		return implode('',$arrDat);
	}

	public function escapeString($s)
	{
		return $s;
	}

	public function startTrans()
	{
		sqlsrv_begin_transaction($this->_conn);

		$this->_transMode		= true;
		$this->_transFailedCnt	= 0;

		$this->Logging("++ StartTrans");
	}

	public function completeTrans($autoComplete = true)
	{
		if ($autoComplete && $this->_transFailedCnt === 0)
			sqlsrv_commit($this->_conn);
		else
			sqlsrv_rollback($this->_conn);
	}

	public function commitTrans($bCommit=true)
	{
		if ($bCommit)
			sqlsrv_commit($this->_conn);
		else
			sqlsrv_rollback($this->_conn);

		$this->_transMode		= false;
		$this->_transFailedCnt	= 0;

		$this->Logging("++ CommitTrans");
	}

	public function rollbackTrans()
	{
		sqlsrv_rollback($this->_conn);

		$this->_transMode		= false;
		$this->_transFailedCnt	= 0;

		$this->Logging("++ RollbackTrans");
	}

	public function hasFailedTrans()
	{
		return $this->_transFailedCnt > 0;
	}

	private function _execute($sql, $params = array())
	{
		if (!$this->isConnected())
		{
			throw new \org\autoset\santorini\exception\DataAccessException("데이터베이스에 연결되어 있지 않습니다.");
			return false;
		}

		$sql = trim($sql);

		if ($this->debug)
		{
			if (sizeof($params) > 0)
				$displaySql = $this->_prepareSql($sql, $params);
			else
				$displaySql = $sql;

			echo '<fieldset><legend><b>(sqlsrv) :</b></legend><xmp>'.$displaySql.'</xmp>';
			$displaySql = null;
		}

		$this->logging($sql);

		$result = sqlsrv_query( $this->_conn, $sql , $params , array( "Scrollable" => SQLSRV_CURSOR_STATIC ) );

		if ($this->getErrorNo() > 0)
			$result = false;

		if ($this->debug && !$result)
			echo '<fieldset style="margin:5px;"><legend><b><span style="color:red">Error '.$this->GetErrorNo().'</span></b></legend><xmp> '.$this->GetError().'</xmp></fieldset>';

		if ($this->debug)
			echo '</fieldset>';

		if ($this->isTransMode() && !$result)
			$this->_transFailedCnt++;

		if (!$result)
		{
			$this->logging("!!ERROR!!\t".$this->getError());
			throw new \Exception($this->getError());
		}

		return $result;
	}

	public function execute($sql, $params = array())
	{
		$result = sqlsrv_prepare( $this->_conn , $sql , $params );
		$this->_smartFreeResult($result);
	}
	
	function getFoundRows()
	{
		return false;//$this->getOne("SELECT FOUND_ROWS()");
	}

	public function getInsertId()
	{
		return false;//$this->_conn->insert_id;
	}

	public function getAffectedRows()
	{
		return $this->_conn->affected_rows;
	}

	// adodb.sf.net 라이브러리를 사용한 코드의 호환성을 위함.
	public function affected_Rows()
	{
		return sqlsrv_rows_affected($this->_conn);
	}

	public function getAll($sql, $params = array())
	{
		$result = $this->_execute($sql, $params);

		if (!$result)
			return array();

		if (sqlsrv_num_rows($result) === false)
			return array();

		$arrDat = array();

		while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC))
		{
			$arrDat[] = $row;
		}

		$this->_smartFreeResult($result);

		return $arrDat;
	}

	public function getOne($sql, $params = array())
	{
		$result = $this->_execute($sql, $params);

		if (!$result)
			return null;

		if (sqlsrv_num_rows($result) === false)
			return null;

		$dat = sqlsrv_fetch_array($result, SQLSRV_FETCH_NUMERIC);

		$this->_smartFreeResult($result);

		return $dat[0];
	}

	public function getCol($sql, $params = array())
	{
		$result = $this->_Execute($sql, $params);

		if (!$result)
			return array();

		if (sqlsrv_num_rows($result) === false)
			return array();

		$arrDat = array();

		while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_NUMERIC))
		{
			$arrDat[] = $row[0];
		}

		$this->_smartFreeResult($result);

		return $arrDat;
	}

	public function getRow($sql, $params = array())
	{
		$result = $this->_execute($sql, $params);

		if (!$result)
			return null;

		if (sqlsrv_num_rows($result) === false)
			return null;

		$arrDat = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);

		// adodb.sf.net의 lib를 사용하면서 필드가 모두 소문자로 왔던 것 때문에
		// 하위 호환성을 위한 코드
		$tmpRow = array();
		
		foreach ($arrDat as $k=>$v)
			$tmpRow[strtolower($k)] = $v;

		$arrDat = $tmpRow;
		unset($tmpRow);

		$this->_smartFreeResult($result);

		return $arrDat;
	}

	public function getErrorNo()
	{
		$errors = sqlsrv_errors();
		return isset($errors['code']) ? $errors['code'] : null;
	}

	public function getError()
	{
		$errors = sqlsrv_errors();
		return isset($errors['message']) ? $errors['message'] : null;
	}

	public function isTransMode()
	{
		return $this->_transMode;
	}

	private function logging($sql)
	{

		$path = DIR_ROOT;

		if (!file_exists($path))
			mkdir($path,0777,true);

		$fp = fopen($path.'/MsSQLDriver.log','a+');
		fwrite($fp, '['.$this->_host."]\t".date('Y-m-d H:i:s')."\t".($this->IsTransMode() ? 'TM' : 'NT')."\t".preg_replace("#\s{1,}#", " ", $sql).PHP_EOL);
		fclose($fp);
	}
} 

?>