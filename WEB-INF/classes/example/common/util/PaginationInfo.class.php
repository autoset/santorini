<?php

namespace example\common\util;

class PaginationInfo
{
	/**
	 * <pre>현재 페이지</pre>
	 * @type Integer
	 */ 
	private $_currentPageNo;

	/**
	 * <pre>페이지 당 표시 건 수</pre>
	 * @type Integer
	 */ 
	private $_recordCountPerPage;

	/**
	 * <pre>페이징 표시 갯수</pre>
	 * @type Integer
	 */ 
	private $_pageSize;

	/**
	 * <pre>총 데이터 건 수</pre>
	 * @type Integer
	 */ 
	private $_totalRecordCount;

	/**
	 * <pre>총 페이지 수</pre>
	 * @type Integer
	 */ 
	private $_totalPageCount;

	/**
	 * <pre>현재 페이지의 getter</pre>
	 * @returnType Integer
	 * @return the _currentPageNo
	 */ 
	public function getCurrentPageNo() {
		return $this->_currentPageNo;
	}

	/**
	 * <pre>현재 페이지의 setter</pre>
	 * @param currentPageNo the _currentPageNo to set
	 */
	public function setCurrentPageNo($currentPageNo) {
		$this->_currentPageNo = $currentPageNo;
	}

	/**
	 * <pre>페이지 당 표시 건 수의 getter</pre>
	 * @returnType Integer
	 * @return the _recordCountPerPage
	 */ 
	public function getRecordCountPerPage() {
		return $this->_recordCountPerPage;
	}

	/**
	 * <pre>페이지 당 표시 건 수의 setter</pre>
	 * @param recordCountPerPage the _recordCountPerPage to set
	 */
	public function setRecordCountPerPage($recordCountPerPage) {
		$this->_recordCountPerPage = $recordCountPerPage;
	}

	/**
	 * <pre>페이징 표시 갯수의 getter</pre>
	 * @returnType Integer
	 * @return the _pageSize
	 */ 
	public function getPageSize() {
		return $this->_pageSize;
	}

	/**
	 * <pre>페이징 표시 갯수의 setter</pre>
	 * @param pageSize the _pageSize to set
	 */
	public function setPageSize($pageSize) {
		$this->_pageSize = $pageSize;
	}

	/**
	 * <pre>총 데이터 건 수의 getter</pre>
	 * @returnType Integer
	 * @return the _totalRecordCount
	 */ 
	public function getTotalRecordCount() {
		return $this->_totalRecordCount;
	}

	/**
	 * <pre>총 데이터 건 수의 setter</pre>
	 * @param totalRecordCount the _totalRecordCount to set
	 */
	public function setTotalRecordCount($totalRecordCount) {
		$this->_totalRecordCount = $totalRecordCount;

		$this->setTotalPageCount(ceil($totalRecordCount / $this->getRecordCountPerPage()));
	}

	/**
	 * <pre>총 페이지 수의 getter</pre>
	 * @returnType Integer
	 * @return the _totalPageCount
	 */ 
	public function getTotalPageCount() {
		return $this->_totalPageCount;
	}

	/**
	 * <pre>총 페이지 수의 setter</pre>
	 * @param totalPageCount the _totalPageCount to set
	 */
	public function setTotalPageCount($totalPageCount) {
		$this->_totalPageCount = $totalPageCount > 0 ? $totalPageCount : 1;
	}


}

?>