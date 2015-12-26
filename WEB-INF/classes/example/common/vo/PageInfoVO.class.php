<?php

namespace example\common\vo;

/**
 * <pre>
 * 기능 : 페이지 정보
 * </pre>
 */
class PageInfoVO
{
	/**
	 * <pre>페이지 제목</pre>
	 * @type String
	 */ 
	private $_title;

	/**
	 * <pre>페이지 설명</pre>
	 * @type String
	 */ 
	private $_description;


	/**
	 * <pre>페이지 제목의 getter</pre>
	 * @returnType String
	 * @return the _title
	 */ 
	public function getTitle() {
		return $this->_title;
	}

	/**
	 * <pre>페이지 제목의 setter</pre>
	 * @param title the _title to set
	 */
	public function setTitle($title) {
		$this->_title = $title;
	}


	/**
	 * <pre>페이지 설명의 getter</pre>
	 * @returnType String
	 * @return the _description
	 */ 
	public function getDescription() {
		return $this->_description;
	}

	/**
	 * <pre>페이지 설명의 setter</pre>
	 * @param description the _description to set
	 */
	public function setDescription($description) {
		$this->_description = $description;
	}


}
