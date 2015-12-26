<?php

namespace org\autoset\santorini\database;

use org\autoset\santorini\exception\DataAccessException;

class SqliteDriver
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
		$sqlite = isset($arrTmp[1]) ? $arrTmp[1] : 'sqlite';
		$dbFile = isset($arrTmp[2]) ? $arrTmp[2] : '/WEB-INF/sqlite/neat.db';

		if ($jdbc != 'jdbc' && $cubrid != 'sqlite')
			die("데이터베이스 스키마가 드라이버와 일치하지 않습니다.");

		$this->_host = DIR_ROOT.'/'.$dbFile;

		if (!file_exists($this->_host))
			die("데이터베이스 파일이 존재하지 않습니다.");

		$this->_conn = new \SQLite3($this->_host);

		$this->_connected = $this->_conn ? true : false;

		return $this->_connected;
	}

	public function disconnect()
	{
		$this->_conn->close();

		$this->_connected = false;
	}

	public function isConnected()
	{
		return $this->_connected;
	}

	public function getVersion()
	{
		$res = SQLite3::version();
		return $res['versionString'];
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
		return ;
	}

	public function completeTrans($autoComplete = true)
	{
		return ;
	}

	public function commitTrans($bCommit=true)
	{
		return ;
	}

	public function rollbackTrans()
	{
		return ;
	}

	public function hasFailedTrans()
	{
		return ;
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

		$result = $this->_conn->query($sql);

		if ($this->getErrorNo() > 0)
			$result = false;

		if ($this->debug && !$result)
			echo '<fieldset style="margin:5px;"><legend><b><span style="color:red">Error '.$this->getErrorNo().'</span></b></legend><xmp> '.$this->getError().'</xmp></fieldset>';

		if ($this->debug)
			echo '</fieldset>';

		if ($this->isTransMode() && !$result)
			$this->_transFailedCnt++;

		return $result;
	}

	public function execute($sql, $params = array())
	{
		$result = $this->_execute($sql, $params);
		$this->_smartFreeResult($result);
	}
	
	function getFoundRows()
	{
		return false;
	}

	public function getInsertId()
	{
		return $this->_conn->lastInsertRowID();
	}

	public function getAffectedRows()
	{
		return $this->_conn->changes();
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

		$arrDat = array();

		while ($row = $result->fetchArray(SQLITE3_ASSOC))
		{
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

		$dat = $result->fetchArray();

		$this->_smartFreeResult($result);

		return $dat[0];
	}

	public function getCol($sql, $params = array())
	{
		$result = $this->_Execute($sql, $params);

		if (!$result)
			return array();

		$arrDat = array();

		while ($row = $result->fetchArray())
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

		$arrDat = $result->fetchArray(SQLITE3_ASSOC);

		$this->_smartFreeResult($result);

		return $arrDat;
	}

	public function getErrorNo()
	{
		return $this->_conn->lastErrorCode();
	}

	public function getError()
	{
		return $this->_conn->lastErrorMsg();
	}

	public function isTransMode()
	{
		return $this->_transMode;
	}

	private function logging($sql)
	{
		return ;

		$path = DIR_ROOT;

		if (!file_exists($path))
			mkdir($path,0777,true);

		$fp = fopen($path.'/SqliteDriver.log','a+');
		fwrite($fp, '['.$this->_host."]\t".date('Y-m-d H:i:s')."\t".($this->IsTransMode() ? 'TM' : 'NT')."\t".preg_replace("#\s{1,}#", " ", $sql).PHP_EOL);
		fclose($fp);
	}
} 

?>