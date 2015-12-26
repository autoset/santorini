<?php

namespace org\autoset\santorini\tagext;

use org\autoset\santorini\PageContext;

class SimpleTagSupport
{
	// NOTE: santorini 에서 추가됨 (태그 바디)
	protected $__tagBody = "";

	public function doTag()
	{

	}

	public function getPhpContext()
	{
		return new PageContext();
	}

	public function getParsedContext()
	{
		ob_start();
		$this->doTag();
		$out = ob_get_contents();
		ob_end_clean();

		return $out;
	}

	// NOTE: santorini 에서 추가됨
	public function setTagBody($body)
	{
		$this->__tagBody = $body;
	}
	
	// NOTE: santorini 에서 추가됨
	public function getTagBody()
	{
		return $this->__tagBody;
	}
}


?>