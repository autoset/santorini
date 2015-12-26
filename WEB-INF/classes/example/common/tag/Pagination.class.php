<?php

namespace example\common\tag;

use org\autoset\santorini\tagext\SimpleTagSupport;
use org\autoset\santorini\PageContext;

class Pagination extends SimpleTagSupport
{
	private $_paginationInfo;

	public function setPaginationInfo($paginationInfo)
	{
		$this->_paginationInfo = $paginationInfo;
	}

	public function doTag()
	{
		if ($this->_paginationInfo->getTotalPageCount() < 1)
			return ;

		$pageContext = $this->getPhpContext();
		$out = $pageContext->getOut();

		$beginPageIdx = (ceil($this->_paginationInfo->getCurrentPageNo() / $this->_paginationInfo->getPageSize()) - 1) * $this->_paginationInfo->getPageSize() + 1;
		$endPageIdx = $beginPageIdx + $this->_paginationInfo->getPageSize() - 1;

		if ($endPageIdx > $this->_paginationInfo->getTotalPageCount())
			$endPageIdx = $this->_paginationInfo->getTotalPageCount();

		$out->println('<ul class="pagination pagination-sm no-margin pull-left">');
	
		if ($this->_paginationInfo->getCurrentPageNo() > $this->_paginationInfo->getPageSize())
		{
			$out->println('<li><a href="#" data-page-idx="1">처음</a></li>');
			$out->println('<li><a href="#" data-page-idx="'.($beginPageIdx-1).'">«</a></li>');
		}

		for ($i = $beginPageIdx; $i <= $endPageIdx; $i++)
		{
			if ($i == $this->_paginationInfo->getCurrentPageNo())
				$out->println('<li class="active"><a href="#" data-page-idx="'.$i.'">'.$i.'</a></li>');
			else
				$out->println('<li><a href="#" data-page-idx="'.$i.'">'.$i.'</a></li>');
		}

		if ($endPageIdx + 1 < $this->_paginationInfo->getTotalPageCount())
			$out->println('<li><a href="#" data-page-idx="'.($endPageIdx + 1).'">»</a></li>');

		if ($beginPageIdx != (ceil($this->_paginationInfo->getTotalPageCount() / $this->_paginationInfo->getPageSize()) - 1) * $this->_paginationInfo->getPageSize() + 1)
			$out->println('<li><a href="#" data-page-idx="'.$this->_paginationInfo->getTotalPageCount().'">마지막</a></li>');

		$out->println('</ul>');
	}
}

?>