<?php

namespace org\autoset\santorini\dao;

use org\autoset\santorini\CommonDAO;
use org\autoset\santorini\vo\SessionVO;

class SessionDAO extends CommonDAO
{

	public function selectSessionData(SessionVO $formVO)
	{
		$sql =<<<SQL
			SELECT session_data
			FROM session
			WHERE session_id = #sessionId#
SQL;
		return $this->selectByPk($sql, $formVO);
	}

	public function insertSession(SessionVO $formVO)
	{
		$sql =<<<SQL
			INSERT INTO session
			(
				session_id
				, login_id
				, user_id
				, last_access_dt
				, session_data
				, input_dt
				, ip_addr
			)
			VALUES
			(
				#sessionId#
				, #loginId#
				, #userId#
				, NOW()
				, #sessionData#
				, NOW()
				, #ipAddr#
			)
SQL;
		$this->insert($sql, $formVO);
	}

	public function updateSession(SessionVO $formVO)
	{
		$sql =<<<SQL
			UPDATE session
			SET
				last_access_dt = NOW()
				, session_data = #sessionData#
			WHERE
				session_id = #sessionId#
SQL;
		$this->update($sql, $formVO);
	}

	public function deleteSession(SessionVO $formVO)
	{
		$sql =<<<SQL
			DELETE FROM session
			WHERE session_id = #sessionId#
SQL;
		$this->delete($sql, $formVO);
	}

	public function deleteGarbageCollector($timeout)
	{
		$map = array();
		$map['timeout'] = $timeout;

		$sql =<<<SQL
			DELETE FROM session
			WHERE UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(last_access_dt) > #timeout#
SQL;

		$this->delete($sql, $map);
	}

	public function updateSessionUserInfo($map)
	{
		$sql =<<<SQL
			UPDATE session
			SET
				login_id = #loginId#
				, user_id = #userId#
			WHERE
				session_id = #sessionId#
SQL;
		$this->update($sql, $map);
	}

}

?>