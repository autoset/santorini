<?php

namespace org\autoset\santorini\database;

use org\autoset\santorini\exception\DataAccessException;

class CubridDriver
{
	private $_conn = null;
	private $_connected = false;
	private	$_resultMode		= null;

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
		$arrTmp = explode(':', $url, 8);

		$jdbc = isset($arrTmp[0]) ? $arrTmp[0] : 'jdbc';
		$cubrid = isset($arrTmp[1]) ? $arrTmp[1] : 'cubrid';
		$host = isset($arrTmp[2]) ? $arrTmp[2] : '127.0.0.1';
		$port = isset($arrTmp[3]) ? $arrTmp[3] : '33000';
		$dbName = isset($arrTmp[4]) ? $arrTmp[4] : 'demo';
		$userId = isset($arrTmp[5]) ? $arrTmp[5] : 'dba';
		$password = isset($arrTmp[6]) ? $arrTmp[6] : '';
		$property = isset($arrTmp[7]) ? $arrTmp[7] : '';

		if ($jdbc != 'jdbc' && $cubrid != 'cubrid')
			throw new DataAccessException("데이터베이스 스키마가 드라이버와 일치하지 않습니다.");

		$this->_host = $host;

		$this->_conn = @cubrid_connect($this->_host, $port, $dbName, $userId, $password);

		$this->_connected = $this->_conn ? true : false;

		return $this->_connected;
	}

	public function disconnect()
	{
		return ;

		@cubrid_disconnect($this->_conn);

		$this->_connected = false;
	}

	public function isConnected()
	{
		return $this->_connected;
	}

	public function getVersion()
	{
		return cubrid_version();
	}

	private function _smartFreeResult(&$result)
	{
		//if ($result && $this->_resultMode == MYSQLI_USE_RESULT)
		//	$result->close();
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
				$arrDat[] = $tmp."'".$this->escapeString($params[$idx])."'";
			else
				$arrDat[] = $tmp.$this->escapeString($params[$idx]);
		}

		unset($arrTmp);
		
		return implode('',$arrDat);
	}

	public function escapeString($s)
	{
		return str_replace("'","''",$s); // cubrid_real_escape_string
	}

	public function startTrans()
	{
		cubrid_set_autocommit($this->_conn, CUBRID_AUTOCOMMIT_FALSE);

		$this->_transMode		= true;
		$this->_transFailedCnt	= 0;

		$this->Logging("++ StartTrans");
	}

	public function completeTrans($autoComplete = true)
	{
		if ($autoComplete && $this->_transFailedCnt === 0)
			$this->commitTrans();
		else
			$this->rollbackTrans();
	}

	public function commitTrans($bCommit=true)
	{
		if ($bCommit)
			cubrid_commit($this->_conn);
		else
			cubrid_rollback($this->_conn);

		$this->_transMode		= false;
		$this->_transFailedCnt	= 0;

		cubrid_set_autocommit($this->_conn, CUBRID_AUTOCOMMIT_TRUE);

		$this->Logging("++ CommitTrans");
	}

	public function rollbackTrans()
	{
		cubrid_rollback($this->_conn);

		$this->_transMode		= false;
		$this->_transFailedCnt	= 0;

		cubrid_set_autocommit($this->_conn, CUBRID_AUTOCOMMIT_TRUE);

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

		//$this->_resultMode = MYSQLI_STORE_RESULT;//(strtolower(substr($sql,0,6)) == 'select') ? MYSQLI_USE_RESULT : MYSQLI_STORE_RESULT;

		if (sizeof($params) > 0)
			$sql = $this->_prepareSql($sql, $params);

		if ($this->debug)
			echo '<fieldset><legend><b>(mysqli) :</b></legend><xmp>'.$sql.'</xmp>';

		$this->logging($sql);

		$result = @cubrid_execute($this->_conn, $sql);//, CUBRID_ASYNC);//, $this->_resultMode);

		if ($this->getErrorNo() > 0)
			$result = false;

		if ($this->debug && !$result)
			echo '<fieldset style="margin:5px;"><legend><b><span style="color:red">Error '.$this->getErrorNo().'</span></b></legend><xmp> '.$this->getError().'</xmp></fieldset>';

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
		$result = $this->_execute($sql, $params);
		$this->_smartFreeResult($result);
	}
	
	function getFoundRows()
	{
		return false;//$this->getOne("SELECT FOUND_ROWS()");
	}

	public function getInsertId()
	{
		return false;//cubrid_insert_id('');
	}

	public function getAffectedRows()
	{
		return cubrid_affected_rows($this->_conn);
	}

	// adodb.sf.net 라이브러리를 사용한 코드의 호환성을 위함.
	public function affected_Rows()
	{
		return $this->getAffectedRows();
	}

	public function getAll($sql, $params = array())
	{
		$result = $this->_Execute($sql, $params);

		if (!$result)
			return array();

		if (cubrid_num_rows($result) < 1)
			return array();

		$arrDat = array();

		while ($row = cubrid_fetch_assoc($result))
		{
			/*
			// adodb.sf.net의 library를 사용하면서 필드가 모두 소문자로 왔던 것 때문에
			// 기존 코드들의 호환성을 위한 코드(아.. 싫다.. 이짓 안하면 0.01 더 빨라질텐데..)
			$tmpRow = array();
			
			foreach ($row as $k=>$v)
				$tmpRow[strtolower($k)] = $v;

			$row = $tmpRow;
			unset($tmpRow);
			*/
			$arrDat[] = $row;
		}

		$this->_smartFreeResult($result);

		return $arrDat;
	}

	public function getOne($sql, $params = array())
	{
		$result = $this->_Execute($sql, $params);

		if (!$result)
			return null;

		if (cubrid_num_rows($result) < 1)
			return null;

		$dat = cubrid_fetch_row($result);

		$this->_smartFreeResult($result);

		return $dat[0];
	}

	public function getCol($sql, $params = array())
	{
		$result = $this->_Execute($sql, $params);

		if (!$result)
			return array();

		if (cubrid_num_rows($result) < 1)
			return array();

		$arrDat = array();

		while ($row = cubrid_fetch_row($result))
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

		if (cubrid_num_rows($result) < 1)
			return null;

		$arrDat = cubrid_fetch_assoc($result);

		/*
		// adodb.sf.net의 lib를 사용하면서 필드가 모두 소문자로 왔던 것 때문에
		// 하위 호환성을 위한 코드
		$tmpRow = array();
		
		foreach ($arrDat as $k=>$v)
			$tmpRow[strtolower($k)] = $v;

		$arrDat = $tmpRow;
		unset($tmpRow);
		*/

		$this->_smartFreeResult($result);

		return $arrDat;
	}

	public function getErrorNo()
	{
		return cubrid_error_code();
	}

	public function getError()
	{
		return cubrid_error_msg();
	}

	public function isTransMode()
	{
		return $this->_transMode;
	}

	private function logging($sql)
	{
		//return ;

		$path = DIR_ROOT;

		if (!file_exists($path))
			mkdir($path,0777,true);

		$fp = fopen($path.'/CubridDriver.log','a+');
		fwrite($fp, '['.$this->_host."]\t".date('Y-m-d H:i:s')."\t".($this->IsTransMode() ? 'TM' : 'NT')."\t".preg_replace("#\s{1,}#", " ", $sql).PHP_EOL);
		fclose($fp);
	}
} 

?>