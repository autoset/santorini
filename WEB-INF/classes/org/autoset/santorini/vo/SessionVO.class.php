<?php

namespace org\autoset\santorini\vo;

/**
 * <pre>
 * 기능 : 세션VO
 * </pre>
 */
class SessionVO
{
	/**
	 * <pre>세션 식별자</pre>
	 * @type String
	 */ 
	private $_sessionId;

	/**
	 * <pre>사용자 식별자</pre>
	 * @type String
	 */ 
	private $_loginId;

	/**
	 * <pre>사용자 일련번호</pre>
	 * @type Integer
	 */ 
	private $_userId;

	/**
	 * <pre>사용자 명</pre>
	 * @type Integer
	 */ 
	private $_userName;

	/**
	 * <pre>마지막 접근 일시</pre>
	 * @type String
	 */ 
	private $_lastAccessDt;

	/**
	 * <pre>세션 데이터</pre>
	 * @type String
	 */ 
	private $_sessionData;

	/**
	 * <pre>입력 일시</pre>
	 * @type String
	 */ 
	private $_inputDt;

	/**
	 * <pre>IP주소</pre>
	 * @type String
	 */ 
	private $_ipAddr;

	private $_loginVia;

	private $_companyCd;

	/**
	 * <pre>세션 식별자의 getter</pre>
	 * @returnType String
	 * @return the _sessionId
	 */ 
	public function getSessionId() {
		return $this->_sessionId;
	}

	/**
	 * <pre>세션 식별자의 setter</pre>
	 * @param sessionId the _sessionId to set
	 */
	public function setSessionId($sessionId) {
		$this->_sessionId = $sessionId;
	}

	/**
	 * <pre>사용자 식별자의 getter</pre>
	 * @returnType String
	 * @return the _loginId
	 */ 
	public function getLoginId() {
		return $this->_loginId;
	}

	/**
	 * <pre>사용자 식별자의 setter</pre>
	 * @param loginId the _loginId to set
	 */
	public function setLoginId($loginId) {
		$this->_loginId = $loginId;
	}

	/**
	 * <pre>사용자 일련번호의 getter</pre>
	 * @returnType Integer
	 * @return the _userId
	 */ 
	public function getUserId() {
		return $this->_userId;
	}

	/**
	 * <pre>사용자 일련번호의 setter</pre>
	 * @param userId the _userId to set
	 */
	public function setUserId($userId) {
		$this->_userId = $userId;
	}

	/**
	 * <pre>사용자 명의 getter</pre>
	 * @returnType Integer
	 * @return the _userName
	 */ 
	public function getUserName() {
		return $this->_userName;
	}

	/**
	 * <pre>사용자 일련번호명의 setter</pre>
	 * @param userName the _userName to set
	 */
	public function setUserName($userName) {
		$this->_userName = $userName;
	}

	/**
	 * <pre>마지막 접근 일시의 getter</pre>
	 * @returnType String
	 * @return the _lastAccessDt
	 */ 
	public function getLastAccessDt() {
		return $this->_lastAccessDt;
	}

	/**
	 * <pre>마지막 접근 일시의 setter</pre>
	 * @param lastAccessDt the _lastAccessDt to set
	 */
	public function setLastAccessDt($lastAccessDt) {
		$this->_lastAccessDt = $lastAccessDt;
	}

	/**
	 * <pre>세션 데이터의 getter</pre>
	 * @returnType String
	 * @return the _sessionData
	 */ 
	public function getSessionData() {
		return $this->_sessionData;
	}

	/**
	 * <pre>세션 데이터의 setter</pre>
	 * @param sessionData the _sessionData to set
	 */
	public function setSessionData($sessionData) {
		$this->_sessionData = $sessionData;
	}

	/**
	 * <pre>입력 일시의 getter</pre>
	 * @returnType String
	 * @return the _inputDt
	 */ 
	public function getInputDt() {
		return $this->_inputDt;
	}

	/**
	 * <pre>입력 일시의 setter</pre>
	 * @param inputDt the _inputDt to set
	 */
	public function setInputDt($inputDt) {
		$this->_inputDt = $inputDt;
	}

	/**
	 * <pre>IP주소의 getter</pre>
	 * @returnType String
	 * @return the _ipAddr
	 */ 
	public function getIpAddr() {
		return $this->_ipAddr;
	}

	/**
	 * <pre>IP주소의 setter</pre>
	 * @param ipAddr the _ipAddr to set
	 */
	public function setIpAddr($ipAddr) {
		$this->_ipAddr = $ipAddr;
	}


	public function getLoginVia()
	{
		return $this->_loginVia;
	}

	public function setLoginVia($loginVia)
	{
		return $this->_loginVia = $loginVia;
	}


	/**
	 * <pre>회사 코드의 getter</pre>
	 * @returnType String
	 * @return the _companyCode
	 */ 
	public function getCompanyCd() {
		return $this->_companyCd;
	}

	/**
	 * <pre>회사 코드의 setter</pre>
	 * @param companyCode the _companyCode to set
	 */
	public function setCompanyCd($companyCode) {
		$this->_companyCd = $companyCode;
	}

}
