<?php

namespace example\common\vo;


/**
 * <pre>
 * 기능 : VO
 * </pre>
 */
class BaseVO
{
	/**
	 * <pre>총 데이터 건 수</pre>
	 * @type Integer
	 */ 
	protected $_totalCnt;

	/**
	 * <pre>총 페이지 수</pre>
	 * @type Integer
	 */ 
	protected $_totalPageCnt;

	/**
	 * <pre>페이지 당 데이터 건 수</pre>
	 * @type Integer
	 */ 
	protected $_pageUnit;

	/**
	 * <pre>페이징 이동 링크 갯수</pre>
	 * @type Integer
	 */ 
	protected $_pageSize = 10;

	/**
	 * <pre>조회 할 페이지 인덱스</pre>
	 * @type Integer
	 */ 
	protected $_pageIndex;

	/**
	 * <pre>조회 할 시작 인덱스</pre>
	 * @type Integer
	 */ 
	protected $_beginIndex;

	/**
	 * <pre>조회 할 마지막 인덱스</pre>
	 * @type Integer
	 */ 
	protected $_endIndex;

	/**
	 * <pre>Row Number</pre>
	 * @type Integer
	 */ 
	protected $_rn;

	/**
	 * <pre>입력자</pre>
	 * @type String
	 */ 
	protected $_inputUserSn;

	/**
	 * <pre>입력자명</pre>
	 * @type String
	 */ 
	protected $_inputUserNm;

	/**
	 * <pre>입력자ID</pre>
	 * @type String
	 */ 
	protected $_inputUserId;


	/**
	 * <pre>입력 일시</pre>
	 * @type String
	 */ 
	protected $_inputDt;

	/**
	 * <pre>수정자</pre>
	 * @type String
	 */ 
	protected $_updateUserSn;

	/**
	 * <pre>수정 일시</pre>
	 * @type String
	 */ 
	protected $_updateDt;

	/**
	 * <pre>수정 횟수</pre>
	 * @type Integer
	 */ 
	protected $_updateCnt;

	/**
	 * <pre>삭제자</pre>
	 * @type String
	 */ 
	protected $_deleteUserSn;

	/**
	 * <pre>삭제 일시</pre>
	 * @type String
	 */ 
	protected $_deleteDt;

	/**
	 * <pre>삭제 여부</pre>
	 * @type String
	 */ 
	protected $_deleteYn;

	/**
	 * <pre>검색어</pre>
	 * @type String
	 */ 
	protected $_searchKeyword;

	/**
	 * <pre>검색 항목</pre>
	 * @type String
	 */ 
	protected $_searchItem;

	/**
	 * <pre>역방향 순번</pre>
	 * @type Integer
	 */ 
	protected $_reversedRn;

	/**
	 * <pre>정렬 컬럼</pre>
	 * @type String
	 */ 
	protected $_dataSortColumn;

	/**
	 * <pre>정렬 방식</pre>
	 * @type String
	 */ 
	protected $_dataSortOrder;

	/**
	 * <pre>역방향 순번의 getter</pre>
	 * @returnType Integer
	 * @return the _reversedRn
	 */ 
	public function getReversedRn() {
		return $this->_reversedRn;
	}

	/**
	 * <pre>역방향 순번의 setter</pre>
	 * @param reversedRn the _reversedRn to set
	 */
	public function setReversedRn($reversedRn) {
		$this->_reversedRn = $reversedRn;
	}


	/**
	 * <pre>총 데이터 건 수의 getter</pre>
	 * @returnType String
	 * @return the _totalCnt
	 */ 
	public function getTotalCnt() {
		return $this->_totalCnt;
	}

	/**
	 * <pre>총 데이터 건 수의 setter</pre>
	 * @param totalCnt the _totalCnt to set
	 */
	public function setTotalCnt($totalCnt) {
		$this->_totalCnt = $totalCnt;

		$this->setTotalPageCnt(ceil($this->_totalCnt / $this->getPageUnit()));
	}
	
	/**
	 * <pre>총 페이지 수의 getter</pre>
	 * @returnType String
	 * @return the _totalPageCnt
	 */ 
	public function getTotalPageCnt() {
		return $this->_totalPageCnt;
	}

	/**
	 * <pre>총 페이지 수의 setter</pre>
	 * @param totalPageCnt the _totalPageCnt to set
	 */
	public function setTotalPageCnt($totalPageCnt) {
		$this->_totalPageCnt = $totalPageCnt;
	}
	
	/**
	 * <pre>페이지 당 데이터 건 수의 getter</pre>
	 * @returnType String
	 * @return the _pageUnit
	 */ 
	public function getPageUnit() {
		return $this->_pageUnit;
	}

	/**
	 * <pre>페이지 당 데이터 건 수의 setter</pre>
	 * @param pageUnit the pageUnit to set
	 */
	public function setPageUnit($pageUnit) {
		$this->_pageUnit = $pageUnit;
	}
	
	/**
	 * <pre>페이징 이동 링크 갯수의 getter</pre>
	 * @returnType String
	 * @return the _pageUnit
	 */ 
	public function getPageSize() {
		return $this->_pageSize;
	}

	/**
	 * <pre>페이징 이동 링크 갯수의 setter</pre>
	 * @param pageUnit the pageUnit to set
	 */
	public function setPageSize($pageSize) {
		$this->_pageSize = $pageSize;
	}

	/**
	 * <pre>조회 할 페이지 인덱스의 getter</pre>
	 * @returnType String
	 * @return the _pageIndex
	 */ 
	public function getPageIndex() {
		return $this->_pageIndex;
	}

	/**
	 * <pre>조회 할 페이지 인덱스의 setter</pre>
	 * @param pageIndex the pageIndex to set
	 */
	public function setPageIndex($pageIndex) {
		$this->_pageIndex = $pageIndex;
	}

	/**
	 * <pre>조회 할 시작 인덱스의 getter</pre>
	 * @returnType String
	 * @return the _beginIndex
	 */ 
	public function getBeginIndex() {
		return $this->_beginIndex;
	}

	/**
	 * <pre>조회 할 시작 인덱스의 setter</pre>
	 * @param beginIndex the beginIndex to set
	 */
	public function setBeginIndex($beginIndex) {
		$this->_beginIndex = $beginIndex;

		$this->setEndIndex($this->_beginIndex + $this->getPageUnit() - 1);
	}

	/**
	 * <pre>조회 할 마지막 인덱스의 getter</pre>
	 * @returnType Integer
	 * @return the _endIndex
	 */ 
	public function getEndIndex() {
		return $this->_endIndex;
	}

	/**
	 * <pre>조회 할 마지막 인덱스의 setter</pre>
	 * @param endIndex the _endIndex to set
	 */
	public function setEndIndex($endIndex) {
		$this->_endIndex = $endIndex;
	}

	/**
	 * <pre>Row Number의 getter</pre>
	 * @returnType Integer
	 * @return the _rn
	 */ 
	public function getRn() {
		return $this->_rn;
	}

	/**
	 * <pre>Row Number의 setter</pre>
	 * @param rn the _rn to set
	 */
	public function setRn($rn) {
		$this->_rn = $rn;
	}

	/**
	 * <pre>입력자의 getter</pre>
	 * @returnType String
	 * @return the _inputUserSn
	 */ 
	public function getInputUserSn() {
		return $this->_inputUserSn;
	}

	/**
	 * <pre>입력자의 setter</pre>
	 * @param inputUserSn the inputUserSn to set
	 */
	public function setInputUserSn($inputUserSn) {
		$this->_inputUserSn = $inputUserSn;
	}

	/**
	 * <pre>입력자명의 getter</pre>
	 * @returnType String
	 * @return the _inputUserNm
	 */ 
	public function getInputUserNm() {
		return $this->_inputUserNm;
	}

	/**
	 * <pre>입력자명의 setter</pre>
	 * @param inputUserNm the inputUserNm to set
	 */
	public function setInputUserNm($inputUserNm) {
		$this->_inputUserNm = $inputUserNm;
	}

	/**
	 * <pre>입력자ID의 getter</pre>
	 * @returnType String
	 * @return the _inputUserId
	 */ 
	public function getInputUserId() {
		return $this->_inputUserId;
	}

	/**
	 * <pre>입력자ID의 setter</pre>
	 * @param inputUserId the _inputUserId to set
	 */
	public function setInputUserId($inputUserId) {
		$this->_inputUserId = $inputUserId;
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
	 * @param inputDt the inputDt to set
	 */
	public function setInputDt($inputDt) {
		$this->_inputDt = $inputDt;
	}

	/**
	 * <pre>수정자의 getter</pre>
	 * @returnType String
	 * @return the _updateUserSn
	 */ 
	public function getUpdateUserSn() {
		return $this->_updateUserSn;
	}

	/**
	 * <pre>수정자의 setter</pre>
	 * @param updateUserSn the updateUserSn to set
	 */
	public function setUpdateUserSn($updateUserSn) {
		$this->_updateUserSn = $updateUserSn;
	}

	/**
	 * <pre>수정 일시의 getter</pre>
	 * @returnType String
	 * @return the _updateDt
	 */ 
	public function getUpdateDt() {
		return $this->_updateDt;
	}

	/**
	 * <pre>수정 일시의 setter</pre>
	 * @param updateDt the updateDt to set
	 */
	public function setUpdateDt($updateDt) {
		$this->_updateDt = $updateDt;
	}

	/**
	 * <pre>수정 횟수의 getter</pre>
	 * @returnType int
	 * @return the _updateCnt
	 */ 
	public function getUpdateCnt() {
		return $this->_updateCnt;
	}

	/**
	 * <pre>수정 횟수의 setter</pre>
	 * @param updateCnt the updateCnt to set
	 */
	public function setUpdateCnt($updateCnt) {
		$this->_updateCnt = $updateCnt;
	}

	/**
	 * <pre>삭제자의 getter</pre>
	 * @returnType String
	 * @return the _deleteUserSn
	 */ 
	public function getDeleteUserSn() {
		return $this->_deleteUserSn;
	}

	/**
	 * <pre>삭제자의 setter</pre>
	 * @param deleteUserSn the deleteUserSn to set
	 */
	public function setDeleteUserSn($deleteUserSn) {
		$this->_deleteUserSn = $deleteUserSn;
	}

	/**
	 * <pre>삭제 일시의 getter</pre>
	 * @returnType String
	 * @return the _deleteDt
	 */ 
	public function getDeleteDt() {
		return $this->_deleteDt;
	}

	/**
	 * <pre>삭제 일시의 setter</pre>
	 * @param deleteDt the deleteDt to set
	 */
	public function setDeleteDt($deleteDt) {
		$this->_deleteDt = $deleteDt;
	}

	/**
	 * <pre>삭제 여부의 getter</pre>
	 * @returnType String
	 * @return the _deleteDt
	 */ 
	public function getDeleteYn() {
		return $this->_deleteYn;
	}

	/**
	 * <pre>삭제 여부의 setter</pre>
	 * @param deleteDt the deleteDt to set
	 */
	public function setDeleteYn($deleteYn) {
		$this->_deleteYn = $deleteYn;
	}

	/**
	 * <pre>검색어의 getter</pre>
	 * @returnType String
	 * @return the _searchKeyword
	 */ 
	public function getSearchKeyword() {
		return $this->_searchKeyword;
	}

	/**
	 * <pre>검색어의 setter</pre>
	 * @param searchKeyword the _searchKeyword to set
	 */
	public function setSearchKeyword($searchKeyword) {
		$this->_searchKeyword = $searchKeyword;
	}

	/**
	 * <pre>검색 항목의 getter</pre>
	 * @returnType String
	 * @return the _searchItem
	 */ 
	public function getSearchItem() {
		return $this->_searchItem;
	}

	/**
	 * <pre>검색 항목의 setter</pre>
	 * @param searchItem the _searchItem to set
	 */
	public function setSearchItem($searchItem) {
		$this->_searchItem = $searchItem;
	}

	/**
	 * <pre>정렬 컬럼의 getter</pre>
	 * @returnType String
	 * @return the _dataSortColumn
	 */ 
	public function getDataSortColumn() {
		return $this->_dataSortColumn;
	}

	/**
	 * <pre>정렬 컬럼의 setter</pre>
	 * @param dataSortColumn the _dataSortColumn to set
	 */
	public function setDataSortColumn($dataSortColumn) {
		$this->_dataSortColumn = $dataSortColumn;
	}

	/**
	 * <pre>정렬 방식의 getter</pre>
	 * @returnType String
	 * @return the _dataSortOrder
	 */ 
	public function getDataSortOrder() {
		return $this->_dataSortOrder;
	}

	/**
	 * <pre>정렬 방식의 setter</pre>
	 * @param dataSortOrder the _dataSortOrder to set
	 */
	public function setDataSortOrder($dataSortOrder) {
		$this->_dataSortOrder = $dataSortOrder;
	}

}
