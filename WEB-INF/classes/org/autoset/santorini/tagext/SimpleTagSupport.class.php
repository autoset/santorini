<?php

namespace org\autoset\santorini\tagext;

use org\autoset\santorini\PageContext;

class SimpleTagSupport
{
	// NOTE: santorini ���� �߰��� (�±� �ٵ�)
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

	// NOTE: santorini ���� �߰���
	public function setTagBody($body)
	{
		$this->__tagBody = $body;
	}
	
	// NOTE: santorini ���� �߰���
	public function getTagBody()
	{
		return $this->__tagBody;
	}
}


?>