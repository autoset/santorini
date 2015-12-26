<?php

namespace org\autoset\santorini;

use org\autoset\santorini\io\PrintWriter;

use org\autoset\santorini\http\HttpServlet;
use org\autoset\santorini\http\HttpServletRequest;
use org\autoset\santorini\http\HttpServletResponse;


class PageContext
{
	public function getOut()
	{
		return new PrintWriter();
	}

	public function getRequest()
	{
		return new HttpServletRequest; // TODO : 전역 HttpServletRequest에서 땡겨와야 함!
	}

	public function getResponse()
	{
		return new HttpServletResponse; // TODO : 전역 HttpServletResponse에서 땡겨와야 함!
	}

	public function getSession()
	{
		return $this->getRequest()->getSession();
	}
} 

?>