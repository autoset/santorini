<?php

namespace org\autoset\santorini\handler;

use org\autoset\santorinis\ApplicationContext;

use org\autoset\santorini\dao\SessionDAO;
use org\autoset\santorini\vo\SessionVO;

class SessionHandler
{
	private $_lifeTime;
	private $_sessionDAO;

	public function __construct()
	{
		$this->_sessionDAO = new SessionDAO;

		$this->_lifeTime = get_cfg_var("session.gc_maxlifetime");
	}

	public function open($save_path, $session_name)
	{
        return true;
    }

    public function close()
	{
		$this->gc(ini_get('session.gc_maxlifetime'));

        return true;
    }

	public function read($session_id)
	{
		$sessionVO = new SessionVO;
		$sessionVO->setSessionId($session_id);

		$sessionVO = $this->_sessionDAO->selectSessionData($sessionVO);

		if ($sessionVO == null)
		{
			$this->write($session_id, '');
			$sessionVO = new SessionVO;
		}

		return base64_decode($sessionVO->getSessionData());
	}

	public function write($session_id, $data)
	{
		$sessionVO = new SessionVO;
		$sessionVO->setSessionId($session_id);

		// 세션데이터에 \00 이 들어가는 경우가 있다. 그래서 base64인코딩.
		// 헌데, DB에서 데이터가 조회되도 안되니.. 뭐 보안측면에서 했다고 생각합시다.
		$sessionVO->setSessionData(base64_encode($data));
		$sessionVO->setIpAddr(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
		
		$resultVO = $this->_sessionDAO->selectSessionData($sessionVO);

		if ($resultVO == null)
			$this->_sessionDAO->insertSession($sessionVO);
		else
			$this->_sessionDAO->updateSession($sessionVO);

		return true;
    }

    public function destroy($session_id)
	{
		$sessionVO = new SessionVO;
		$sessionVO->setSessionId($session_id);

		$this->_sessionDAO->deleteSession($sessionVO);

		return true;
    }

    public function gc($timeout)
	{
		// NOTE : GC에 의해 delete 시 부하 가중됨. cron으로 대체해 주셈
		//$this->_sessionDAO->deleteGarbageCollector($timeout);

		return true;
    }
}

